<?php
/**
 * =============================================================================
 * FLAIR FACILITIES: GOOGLE REVIEW MANAGEMENT SYSTEM
 * =============================================================================
 *
 * Description: Handles the entire Google Business Profile review sync system,
 * including OAuth2, API calls, database interaction, and cron jobs.
 *
 */

// =============================================================================
// SECTION 0: ADMIN MENU
// =============================================================================

add_action('admin_menu', function() {
    add_menu_page(
        'Review Management',
        'Reviews',
        'manage_options',
        'flairltd_review_management',
        'flairltd_reviews_management_page_html',
        'dashicons-star-filled',
        30
    );
});

add_action('admin_init', function() {
    register_setting('flairltd_review_settings_group', 'flairltd_google_client_id');
    register_setting('flairltd_review_settings_group', 'flairltd_google_client_secret');
    register_setting('flairltd_review_settings_group', 'flairltd_google_account_id');
    register_setting('flairltd_review_settings_group', 'flairltd_google_location_id');
    register_setting('flairltd_review_settings_group', 'flairltd_reviews_word_mappings');
});

// =============================================================================
// SECTION 1: REVIEW MANAGEMENT PAGE & API LOGIC
// =============================================================================

/**
 * Generates the Google OAuth2 authorization URL.
 */
if ( ! function_exists( 'flairltd_reviews_get_google_auth_url' ) ) {
    function flairltd_reviews_get_google_auth_url() {
        $client_id = get_option('flairltd_google_client_id');
        $redirect_uri = admin_url('admin.php?page=flairltd_review_management');
        $scope = 'https://www.googleapis.com/auth/business.manage';
        $state = wp_create_nonce('flairltd_google_oauth');
        set_transient('flairltd_google_oauth_state', $state, 600);

        $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);
        return $auth_url;
    }
}

/**
 * Handles the callback from Google after user authorization to get the refresh token.
 */
if ( ! function_exists( 'flairltd_reviews_handle_google_oauth_callback' ) ) {
    function flairltd_reviews_handle_google_oauth_callback($code) {
        $client_id = get_option('flairltd_google_client_id');
        $client_secret = get_option('flairltd_google_client_secret');
        $redirect_uri = admin_url('admin.php?page=flairltd_review_management');
        $token_url = 'https://oauth2.googleapis.com/token';

        $response = wp_remote_post($token_url, [
            'body' => [
                'code' => $code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
            ]
        ]);

        if (is_wp_error($response)) {
            wp_die('Error exchanging authorization code: ' . $response->get_error_message());
        }

        $token_data = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($token_data['access_token'])) {
            update_option('flairltd_google_access_token', $token_data['access_token']);
            update_option('flairltd_google_token_expires_at', time() + $token_data['expires_in']);
        }

        if (isset($token_data['refresh_token'])) {
            update_option('flairltd_google_refresh_token', $token_data['refresh_token']);
            delete_option('flairltd_google_token_invalid');
        } else {
            $existing_refresh = get_option('flairltd_google_refresh_token');
            if (empty($existing_refresh)) {
                wp_die('Error: Refresh token not received on first authorization. Please ensure you:<br>1. Revoke access in your Google Account settings at <a href="https://myaccount.google.com/permissions" target="_blank">https://myaccount.google.com/permissions</a><br>2. Try authorizing again<br><br>Full Response: <pre>' . print_r($token_data, true) . '</pre>');
            }
        }
        
        delete_transient('flairltd_google_oauth_state');
    }
}

/**
 * Gets a valid access token for API calls, using the refresh token if the current one is expired.
 */
if ( ! function_exists( 'flairltd_reviews_get_google_access_token' ) ) {
    function flairltd_reviews_get_google_access_token() {
        if (get_option('flairltd_google_token_invalid')) {
            return new WP_Error('token_invalid', 'Refresh token is invalid. Please re-authorize in the Review Management settings.');
        }

        $access_token = get_option('flairltd_google_access_token');
        $expires_at = get_option('flairltd_google_token_expires_at', 0);
        
        if ( time() > ($expires_at - 60) ) {
            $refresh_token = get_option('flairltd_google_refresh_token');
            if (empty($refresh_token)) {
                return new WP_Error('no_refresh_token', 'Refresh token is missing. Please re-authorize in the Review Management settings.');
            }

            $client_id = get_option('flairltd_google_client_id');
            $client_secret = get_option('flairltd_google_client_secret');
            $token_url = 'https://oauth2.googleapis.com/token';

            $response = wp_remote_post($token_url, [
                'body' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token',
                ],
                'timeout' => 15,
            ]);

            if (is_wp_error($response)) {
                return new WP_Error('token_refresh_network_error', 'Network error while refreshing token: ' . $response->get_error_message() . '. Will retry on next attempt.');
            }

            $http_code = wp_remote_retrieve_response_code($response);
            $token_data = json_decode(wp_remote_retrieve_body($response), true);

            if ($http_code === 200 && isset($token_data['access_token'])) {
                $access_token = $token_data['access_token'];
                update_option('flairltd_google_access_token', $access_token);
                update_option('flairltd_google_token_expires_at', time() + $token_data['expires_in']);
                delete_option('flairltd_google_token_invalid');
                return $access_token;
            } else {
                if (isset($token_data['error']) && $token_data['error'] === 'invalid_grant') {
                    update_option('flairltd_google_token_invalid', true);
                    return new WP_Error('token_revoked', 'Refresh token has been revoked. Please re-authorize in the Review Management settings. Error: ' . print_r($token_data, true));
                }
                return new WP_Error('token_refresh_failed', 'Failed to refresh token (HTTP ' . $http_code . '). Will retry on next attempt. Response: ' . print_r($token_data, true));
            }
        }
        
        return $access_token;
    }
}

/**
 * Clears the "token invalid" flag. Call this before starting a new OAuth flow.
 */
if ( ! function_exists( 'flairltd_reviews_clear_token_invalid_flag' ) ) {
    function flairltd_reviews_clear_token_invalid_flag() {
        delete_option('flairltd_google_token_invalid');
        delete_option('flairltd_token_failure_email_sent');
    }
}

/**
 * Sends an email notification to the site admin when token authorization is needed.
 */
if ( ! function_exists( 'flairltd_reviews_send_token_failure_email' ) ) {
    function flairltd_reviews_send_token_failure_email( $error_message ) {
        $last_email_sent = get_option('flairltd_token_failure_email_sent');
        if ($last_email_sent && (time() - $last_email_sent) < 86400) {
            return;
        }
        
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $auth_url = admin_url('admin.php?page=flairltd_review_management');
        
        $subject = "[{$site_name}] Google Reviews: Re-Authorization Required";
        
        $message = "Hello,\n\n";
        $message .= "The Google Business Profile review sync system on {$site_name} requires your attention.\n\n";
        $message .= "The Google API refresh token has been revoked or has expired, and the system can no longer fetch reviews automatically.\n\n";
        $message .= "ERROR DETAILS:\n";
        $message .= "{$error_message}\n\n";
        $message .= "ACTION REQUIRED:\n";
        $message .= "Please log in to your WordPress admin panel and re-authorize the Google Business Profile connection:\n";
        $message .= "{$auth_url}\n\n";
        $message .= "STEPS TO FIX:\n";
        $message .= "1. Go to: Review Management (in WordPress admin)\n";
        $message .= "2. Click the 'Re-Authorize with Google' button\n";
        $message .= "3. Grant permission when prompted by Google\n\n";
        $message .= "Once re-authorized, the daily review sync will resume automatically.\n\n";
        $message .= "This is an automated message from the Flair Facilities Review Management System.\n";
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        $sent = wp_mail($admin_email, $subject, $message, $headers);
        
        if ($sent) {
            update_option('flairltd_token_failure_email_sent', time());
        }
        
        return $sent;
    }
}

/**
 * Fetches all Google reviews using pagination until an existing review is found.
 */
if ( ! function_exists( 'flairltd_reviews_fetch_google_reviews' ) ) {
    function flairltd_reviews_fetch_google_reviews() {
        global $wpdb;
        $account_id = get_option('flairltd_google_account_id');
        $location_id = get_option('flairltd_google_location_id');

        if (empty($account_id) || empty($location_id)) {
            return 'Error: Account ID or Location ID is not set.';
        }

        $access_token = flairltd_reviews_get_google_access_token();
        if (is_wp_error($access_token)) {
            return $access_token->get_error_message();
        }

        $all_reviews = [];
        $page_token = null;
        $max_pages = 20;
        $page_count = 0;
        $existing_review_found = false;

        do {
            $api_url = "https://mybusiness.googleapis.com/v4/accounts/{$account_id}/locations/{$location_id}/reviews";
            
            $query_args = ['pageSize' => 50];
            if ($page_token) {
                $query_args['pageToken'] = $page_token;
            }
            $api_url = add_query_arg($query_args, $api_url);

            $response = wp_remote_get($api_url, [
                'headers' => ['Authorization' => 'Bearer ' . $access_token],
                'timeout' => 30
            ]);

            if (is_wp_error($response)) {
                return 'API request failed: ' . $response->get_error_message();
            }

            $http_code = wp_remote_retrieve_response_code($response);
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if ($http_code !== 200) {
                return "API Error (HTTP {$http_code}): " . print_r($data, true);
            }

            if ( $page_count === 0 && isset($data['totalReviewCount']) ) {
                if ( is_numeric($data['averageRating']) ) {
                    update_option('flairltd_reviews_average_rating', floatval($data['averageRating']));
                }
                if ( is_numeric($data['totalReviewCount']) ) {
                    update_option('flairltd_reviews_total_count', intval($data['totalReviewCount']));
                }
            }

            if (!empty($data['reviews'])) {
                foreach($data['reviews'] as $review) {
                     $existing_review = $wpdb->get_var($wpdb->prepare(
                        "SELECT id FROM {$wpdb->prefix}flairltd_reviews WHERE review_id = %s",
                        $review['reviewId']
                    ));
                    if ($existing_review) {
                        $existing_review_found = true;
                        break;
                    }
                    $all_reviews[] = $review;
                }
            }

            if ($existing_review_found) {
                break;
            }

            $page_token = $data['nextPageToken'] ?? null;
            $page_count++;

        } while ($page_token && $page_count < $max_pages);

        return $all_reviews;
    }
}

/**
 * Inserts an array of formatted reviews into the database.
 */
if ( ! function_exists( 'flairltd_reviews_insert_reviews' ) ) {
    function flairltd_reviews_insert_reviews($reviews) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'flairltd_reviews';
        $newly_inserted_reviews = [];

        foreach ($reviews as $review) {
             $existing_review = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE review_id = %s",
                $review['reviewId']
            ));
            if ($existing_review) {
                continue;
            }
            
            $wpdb->insert(
                $table_name,
                [
                    'review_id' => $review['reviewId'],
                    'reviewer_display_name' => $review['reviewer']['displayName'] ?? '',
                    'reviewer_profile_photo_url' => $review['reviewer']['profilePhotoUrl'] ?? '',
                    'star_rating' => $review['starRating'] ?? 'ZERO',
                    'comment' => $review['comment'] ?? '',
                    'create_time' => gmdate('Y-m-d H:i:s', strtotime($review['createTime'])),
                    'update_time' => gmdate('Y-m-d H:i:s', strtotime($review['updateTime'])),
                    'reply_comment' => $review['reviewReply']['comment'] ?? null,
                    'reply_update_time' => isset($review['reviewReply']) ? gmdate('Y-m-d H:i:s', strtotime($review['reviewReply']['updateTime'])) : null,
                ]
            );

            if ($wpdb->insert_id) {
                $newly_inserted_reviews[] = (object) [
                    'id' => $wpdb->insert_id,
                    'comment' => $review['comment'] ?? ''
                ];
            }
        }
        return $newly_inserted_reviews;
    }
}

function flairltd_reviews_management_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    if ( isset($_GET['code']) && isset($_GET['state']) && get_transient('flairltd_google_oauth_state') === $_GET['state'] ) {
        flairltd_reviews_clear_token_invalid_flag();
        flairltd_reviews_handle_google_oauth_callback($_GET['code']);
        wp_redirect(admin_url('admin.php?page=flairltd_review_management'));
        exit;
    }

    $client_id = get_option('flairltd_google_client_id');
    $client_secret = get_option('flairltd_google_client_secret');
    $account_id = get_option('flairltd_google_account_id');
    $location_id = get_option('flairltd_google_location_id');
    $refresh_token = get_option('flairltd_google_refresh_token');
    $word_mappings = get_option('flairltd_reviews_word_mappings', '');

    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p>Manage your Google Business Profile API connection to automatically sync reviews to your website.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'flairltd_review_settings_group' ); ?>

            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px; border: 1px solid #ccd0d4;">
                <h2>Step 1: Enter API Credentials & Location IDs</h2>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="flairltd_google_client_id">Client ID</label></th>
                            <td><input name="flairltd_google_client_id" type="text" id="flairltd_google_client_id" value="<?php echo esc_attr( $client_id ); ?>" class="large-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="flairltd_google_client_secret">Client Secret</label></th>
                            <td><input name="flairltd_google_client_secret" type="password" id="flairltd_google_client_secret" value="<?php echo esc_attr( $client_secret ); ?>" class="large-text" placeholder="Value is saved, but not shown for security"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="flairltd_google_account_id">Account ID</label></th>
                            <td><input name="flairltd_google_account_id" type="text" id="flairltd_google_account_id" value="<?php echo esc_attr( $account_id ); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="flairltd_google_location_id">Location ID</label></th>
                            <td><input name="flairltd_google_location_id" type="text" id="flairltd_google_location_id" value="<?php echo esc_attr( $location_id ); ?>" class="regular-text"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px; border: 1px solid #ccd0d4;">
                <h2>Word-to-URL Mappings</h2>
                <p>Enter one mapping per line. Format: <code>word|https://example.com/page1,https://example.com/page2</code></p>
                <p>If a review contains the word, it will be associated with all pages at the URLs listed for that word.</p>
                 <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="flairltd_reviews_word_mappings">Mappings</label></th>
                            <td><textarea name="flairltd_reviews_word_mappings" id="flairltd_reviews_word_mappings" class="large-text" rows="10"><?php echo esc_textarea($word_mappings); ?></textarea></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php submit_button( 'Save All Review Settings' ); ?>
        </form>

        <?php if ( $client_id && $client_secret ) : ?>
            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px; border: 1px solid #ccd0d4;">
                <h2>Step 2: Authorize Your Account</h2>
                <?php if ( $refresh_token ) : ?>
                    <p style="color:green; font-weight:bold;">&#x2705; System is authorized. You can re-authorize if you need to switch accounts.</p>
                <?php else: ?>
                    <p>Click the button below to grant this website permission to access your Google Business Profile reviews. You will only need to do this once.</p>
                <?php endif; ?>
                <p><a href="<?php echo esc_url(flairltd_reviews_get_google_auth_url()); ?>" class="button button-primary"><?php echo $refresh_token ? 'Re-Authorize with Google' : 'Authorize with Google'; ?></a></p>
            </div>
        <?php endif; ?>
        
        <?php if ( $refresh_token ) : ?>
            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px; border: 1px solid #ccd0d4;">
                 <h2>Step 3: Find Your Account & Location IDs</h2>
                 <p>If you don't know your IDs, use this tool... Copy the numeric IDs from the results and paste them into the fields in Step 1.</p>
                 <form method="post" action="">
                     <input type="hidden" name="flairltd_find_ids" value="true">
                     <?php wp_nonce_field( 'flairltd_find_ids_nonce', 'flairltd_find_ids_nonce_field' ); ?>
                     <?php submit_button( 'Find My IDs Now', 'secondary' ); ?>
                 </form>
                 <?php if ( isset($_SESSION['flairltd_api_finder_results']) ) { echo '<h4>API Results:</h4><pre style="background:#f0f0f1; padding: 10px; border-radius: 4px; white-space: pre-wrap;">' . esc_html($_SESSION['flairltd_api_finder_results']) . '</pre>'; unset($_SESSION['flairltd_api_finder_results']); } ?>
            </div>
        <?php endif; ?>

        <?php if ( $account_id && $location_id ) : ?>
            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px; border: 1px solid #ccd0d4;">
                <h2>Step 4: Sync Status</h2>
                <table class="form-table">
                    <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'flairltd_reviews';
                        $total_reviews = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
                        $next_scheduled_timestamp = wp_next_scheduled('flairltd_daily_review_fetch_event');
                        $last_run_timestamp = get_option('flairltd_reviews_last_run');
                        $last_run_log = get_option('flairltd_review_cron_last_log');
                    ?>
                     <tr>
                        <th scope="row">Total Reviews in Database</th>
                        <td><strong><?php echo esc_html($total_reviews ?? '0'); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row">Next Scheduled Sync</th>
                        <td data-timestamp="<?php echo esc_attr($next_scheduled_timestamp); ?>"><?php echo $next_scheduled_timestamp ? date('Y-m-d H:i:s', $next_scheduled_timestamp) . ' (UTC)' : 'Not scheduled'; ?><span class="local-time-display" style="display:none; color:#555; font-style:italic; margin-left:10px;"></span></td>
                    </tr>
                     <tr>
                        <th scope="row">Last Successful Sync</th>
                        <td data-timestamp="<?php echo esc_attr($last_run_timestamp); ?>"><?php echo $last_run_timestamp ? date('Y-m-d H:i:s', $last_run_timestamp) . ' (UTC)' : 'Never'; ?><span class="local-time-display" style="display:none; color:#555; font-style:italic; margin-left:10px;"></span></td>
                    </tr>
                    <?php if ($last_run_log): ?>
                    <tr>
                        <th scope="row">Last Sync Log</th>
                        <td><pre style="background:#f0f0f1; padding: 10px; border-radius: 4px; white-space: pre-wrap;"><?php echo esc_html($last_run_log); ?></pre></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <form method="post" action="">
                    <input type="hidden" name="flairltd_run_review_cron_now" value="true">
                    <?php wp_nonce_field( 'flairltd_run_review_cron_nonce', 'flairltd_run_review_cron_nonce_field' ); ?>
                    <?php submit_button( 'Sync Reviews Now', 'secondary' ); ?>
                </form>
            </div>
         <?php endif; ?>
    </div>
    <?php
}

// =============================================================================
// SECTION 2: REVIEW SYSTEM CORE (DATABASE, CRON, API FETCHERS)
// =============================================================================

function flairltd_reviews_start_session() {
    if ( ! session_id() && ! headers_sent() ) {
        session_start();
    }
}
add_action('init', 'flairltd_reviews_start_session', 1);

function flairltd_reviews_create_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'flairltd_reviews';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        review_id VARCHAR(255) NOT NULL,
        reviewer_display_name VARCHAR(255) DEFAULT '' NOT NULL,
        reviewer_profile_photo_url TEXT,
        star_rating VARCHAR(20) NOT NULL,
        comment TEXT,
        create_time DATETIME NOT NULL,
        update_time DATETIME NOT NULL,
        reply_comment TEXT,
        reply_update_time DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY review_id (review_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_setup_theme', 'flairltd_reviews_create_reviews_table');

function flairltd_reviews_setup_review_cron() {
    if ( ! wp_next_scheduled( 'flairltd_daily_review_fetch_event' ) ) {
        $time = strtotime('02:00:00');
        if ($time < time()) {
            $time = strtotime('+1 day', $time);
        }
        wp_schedule_event( $time, 'daily', 'flairltd_daily_review_fetch_event' );
    }
}
add_action( 'init', 'flairltd_reviews_setup_review_cron' );

function flairltd_reviews_fetch_reviews_cron( $is_manual_run = false ) {
    global $wpdb;
    $start_time = microtime(true);
    $log = [];
    $log[] = 'Starting review sync at ' . date('Y-m-d H:i:s') . ' (UTC).';
    
    $max_retries = 3;
    $retry_delay = 30;
    $google_result = null;
    $success = false;
    $token_failure = false;
    
    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
        if ($attempt > 1) {
            $log[] = "Retry attempt {$attempt} of {$max_retries} after {$retry_delay} second delay...";
            sleep($retry_delay);
        }
        
        $google_result = flairltd_reviews_fetch_google_reviews();
        
        if ( is_array($google_result) ) {
            $success = true;
            break;
        } else {
            if (strpos($google_result, 'token_revoked') !== false ||
                strpos($google_result, 'token_invalid') !== false ||
                strpos($google_result, 'Refresh token is missing') !== false ||
                strpos($google_result, 'invalid_grant') !== false) {
                $token_failure = true;
                $log[] = "Attempt {$attempt}: Authentication failure detected. " . $google_result;
                $log[] = "Token issue detected. Sending email notification to site admin.";
                flairltd_reviews_send_token_failure_email($google_result);
                $log[] = "Email sent to " . get_option('admin_email') . " requesting re-authorization.";
                break;
            }
            
            $is_temporary_error = false;
            if (strpos($google_result, 'HTTP 500') !== false || 
                strpos($google_result, 'HTTP 502') !== false || 
                strpos($google_result, 'HTTP 503') !== false || 
                strpos($google_result, 'HTTP 504') !== false ||
                strpos($google_result, 'API request failed') !== false) {
                $is_temporary_error = true;
            }
            
            $log[] = "Attempt {$attempt}: Failed. " . $google_result;
            
            if (!$is_temporary_error) {
                $log[] = "Error is not temporary. Stopping retries.";
                break;
            }
            
            if ($attempt === $max_retries) {
                $log[] = "All {$max_retries} attempts failed. Will try again at next scheduled run.";
            }
        }
    }

    if ( $success ) {
        $newly_inserted = flairltd_reviews_insert_reviews($google_result);
        $inserted_count = count($newly_inserted);
        $log[] = "Google: Success. Fetched " . count($google_result) . " reviews from API, inserted {$inserted_count} new reviews into the database.";

        if ( ! empty($newly_inserted) ) {
            $associations_made = flairltd_reviews_associate_reviews_to_posts($newly_inserted);
            $log[] = "Association Engine: Processed {$inserted_count} new reviews and created {$associations_made} associations.";
        }
        
        update_option('flairltd_reviews_last_run', time());
    }

    if ( $is_manual_run ) {
        $log[] = "Manual run detected: Running association engine on all existing reviews.";
        $all_reviews = $wpdb->get_results("SELECT id, comment FROM {$wpdb->prefix}flairltd_reviews");
        if ( ! empty($all_reviews) ) {
            $total_associations = flairltd_reviews_associate_reviews_to_posts($all_reviews);
            $log[] = "Association Engine: Full scan complete. Found/updated {$total_associations} associations across " . count($all_reviews) . " reviews.";
        }
    }

    $end_time = microtime(true);
    $duration = round($end_time - $start_time, 4);
    $log[] = "Process finished in {$duration} seconds.";

    if ( $is_manual_run ) {
        wp_clear_scheduled_hook('flairltd_daily_review_fetch_event');
        $time = strtotime('02:00:00');
         if ($time < time()) {
            $time = strtotime('+1 day', $time);
        }
        wp_schedule_event($time, 'daily', 'flairltd_daily_review_fetch_event');
        $log[] = "Manual run detected: Next scheduled run has been reset to tomorrow at 02:00.";
    }
    
    update_option('flairltd_review_cron_last_log', implode("\n", $log));
}
add_action( 'flairltd_daily_review_fetch_event', 'flairltd_reviews_fetch_reviews_cron' );

function flairltd_reviews_handle_manual_review_cron_run() {
    if ( isset( $_POST['flairltd_run_review_cron_now'] ) && isset( $_POST['flairltd_run_review_cron_nonce_field'] ) ) {
        if ( ! wp_verify_nonce( $_POST['flairltd_run_review_cron_nonce_field'], 'flairltd_run_review_cron_nonce' ) ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;
        
        flairltd_reviews_fetch_reviews_cron( true );
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Review sync has been completed. Check the log on the Review Management page for details.</p></div>';
        });
    }
}
add_action( 'admin_init', 'flairltd_reviews_handle_manual_review_cron_run' );

function flairltd_reviews_handle_find_ids_action() {
    if ( isset( $_POST['flairltd_find_ids'] ) && isset( $_POST['flairltd_find_ids_nonce_field'] ) ) {
        if ( ! wp_verify_nonce( $_POST['flairltd_find_ids_nonce_field'], 'flairltd_find_ids_nonce' ) ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;

        $access_token = flairltd_reviews_get_google_access_token();
        if ( is_wp_error($access_token) ) {
            $_SESSION['flairltd_api_finder_results'] = "Error getting access token: " . $access_token->get_error_message();
            return;
        }

        $accounts_url = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';
        $response = wp_remote_get($accounts_url, [
            'headers' => ['Authorization' => 'Bearer ' . $access_token]
        ]);

        if ( is_wp_error($response) ) {
            $_SESSION['flairltd_api_finder_results'] = "Error fetching accounts: " . $response->get_error_message();
            return;
        }
        
        $accounts_body = wp_remote_retrieve_body($response);
        $accounts_data = json_decode($accounts_body, true);

        $output = "--- ACCOUNTS ---\n";
        if (isset($accounts_data['accounts']) && !empty($accounts_data['accounts'])) {
             $output .= print_r($accounts_data['accounts'], true);
             
             foreach ($accounts_data['accounts'] as $account) {
                 $account_name = $account['name'];
                 $locations_url = "https://mybusinessbusinessinformation.googleapis.com/v1/{$account_name}/locations?readMask=name,title";
                 
                 $loc_response = wp_remote_get($locations_url, [
                     'headers' => ['Authorization' => 'Bearer ' . $access_token]
                 ]);
                 
                 $loc_body = wp_remote_retrieve_body($loc_response);
                 $loc_data = json_decode($loc_body, true);
                 
                 $output .= "\n--- LOCATIONS for Account {$account_name} ---\n";
                 if (isset($loc_data['locations']) && !empty($loc_data['locations'])) {
                     $output .= print_r($loc_data['locations'], true);
                 } else {
                     $output .= "No locations found for this account.\n";
                     $output .= "Raw Response: " . print_r($loc_data, true) . "\n";
                 }
             }
        } else {
            $output .= "No accounts found.\n";
            $output .= "Raw Response: " . print_r($accounts_data, true) . "\n";
        }
        
        $_SESSION['flairltd_api_finder_results'] = $output;
    }
}
add_action('admin_init', 'flairltd_reviews_handle_find_ids_action');

// =============================================================================
// SECTION 3: REVIEW DISPLAY SHORTCODES
// =============================================================================

function flairltd_reviews_register_review_shortcodes() {
    add_shortcode( 'flair_reviews', 'flairltd_reviews_recent_reviews_shortcode_handler' );
    add_shortcode( 'flair_reviews_all', 'flairltd_reviews_all_reviews_shortcode_handler' );
    add_shortcode( 'flair_associated_reviews', 'flairltd_reviews_associated_reviews_shortcode_handler' );
}
add_action( 'init', 'flairltd_reviews_register_review_shortcodes' );

function flairltd_reviews_recent_reviews_shortcode_handler( $atts ) {
    global $wpdb;

    $atts = shortcode_atts( [
        'count' => 6,
    ], $atts, 'flair_reviews' );

    $count = intval( $atts['count'] );
    if ( $count < 1 ) {
        $count = 6;
    }

    $table_name = $wpdb->prefix . 'flairltd_reviews';
    $reviews = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$table_name} ORDER BY create_time DESC LIMIT %d",
        $count
    ) );

    if ( empty( $reviews ) ) {
        return '';
    }
    
    $output = '<div class="flairltd-reviews-widget">';
    $output .= flairltd_reviews_get_reviews_summary_html();
    $output .= '<div class="flairltd-reviews-container">';
    foreach ( $reviews as $review ) {
        $output .= flairltd_reviews_render_single_review_html( $review, false, true );
    }
    $output .= '</div>';
    
    $reviews_page_url = get_permalink( get_page_by_path( 'reviews' ) );
    if ($reviews_page_url) {
        $output .= '<a href="' . esc_url($reviews_page_url) . '" class="flairltd-reviews__view-all-link">View All Reviews</a>';
    }

    $output .= '</div>';

    return $output;
}

function flairltd_reviews_all_reviews_shortcode_handler( $atts ) {
    global $wpdb;

    $atts = shortcode_atts( [
        'replies' => 'false',
    ], $atts, 'flair_reviews_all' );

    $show_replies = filter_var( $atts['replies'], FILTER_VALIDATE_BOOLEAN );

    $table_name = $wpdb->prefix . 'flairltd_reviews';
    $reviews = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY create_time DESC" );

    if ( empty( $reviews ) ) {
        return '<p>No reviews have been imported yet.</p>';
    }

    $output = flairltd_reviews_get_reviews_summary_html();
    $output .= '<div class="flairltd-reviews-container flairltd-reviews-container--all">';
    foreach ( $reviews as $review ) {
        $output .= flairltd_reviews_render_single_review_html( $review, $show_replies, false );
    }
    $output .= '</div>';

    return $output;
}

function flairltd_reviews_associated_reviews_shortcode_handler() {
    global $wpdb;

    if ( ! is_singular() ) return '';

    $post_id = get_the_ID();
    $review_ids = get_post_meta( $post_id, '_flairltd_associated_review_ids', true );

    if ( empty( $review_ids ) || ! is_array( $review_ids ) ) return '';

    $table_name = $wpdb->prefix . 'flairltd_reviews';
    $placeholders = implode( ',', array_fill( 0, count( $review_ids ), '%d' ) );
    
    $reviews = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE id IN ({$placeholders}) ORDER BY create_time DESC",
        $review_ids
    ) );
    
    if ( empty( $reviews ) ) return '';

    $output = '<div class="flairltd-associated-reviews">';
    $output .= '<h2 class="flairltd-associated-reviews__title">What Our Clients Say</h2>';
    $output .= '<div class="flairltd-reviews-container">';
    foreach ( $reviews as $review ) {
        $output .= flairltd_reviews_render_single_review_html( $review, false, true );
    }
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

function flairltd_reviews_get_reviews_summary_html() {
    $average_rating = get_option( 'flairltd_reviews_average_rating', 0 );
    $total_count = get_option( 'flairltd_reviews_total_count', 0 );

    if ( ! $total_count ) {
        return '';
    }

    $rounded_rating = round( floatval( $average_rating ), 1 );

    $output = '<div class="flairltd-reviews-summary">';
    $output .= '<p class="flairltd-reviews-summary__text">';
    $output .= '<strong>Rated ' . esc_html( $rounded_rating ) . ' out of 5 stars</strong> based on ';
    $output .= esc_html( $total_count ) . ' customer <strong>Google</strong> reviews.';
    $output .= '</p>';
    $output .= '</div>';

    return $output;
}

function flairltd_reviews_render_single_review_html( $review, $show_reply = true, $truncate = false ) {
    $star_map = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
    $rating = $star_map[ $review->star_rating ] ?? 0;
    $star_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20px" height="20px"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';

    $comment_html = '';
    $word_limit = 25;
    if ( ! empty( $review->comment ) ) {
        $comment_text = nl2br( esc_html( $review->comment ) );
        $words = explode(' ', $review->comment);
        if ( $truncate && count($words) > $word_limit ) {
            $truncated_text = implode(' ', array_slice($words, 0, $word_limit)) . '...';
            $comment_html = '<div class="flairltd-review-card__comment-wrapper"><p class="flairltd-review-card__comment flairltd-review-card__comment--truncated">' . nl2br(esc_html($truncated_text)) . '</p><span class="flairltd-review-card__full-text" style="display:none;">' . $comment_text . '</span><span class="flairltd-review-card__read-more">Read More</span></div>';
        } else {
            $comment_html = '<div class="flairltd-review-card__comment-wrapper"><p class="flairltd-review-card__comment">' . $comment_text . '</p></div>';
        }
    }

    $output = '<div class="flairltd-review-card">';
    $output .= '<div class="flairltd-review-card__header">';
    if ( ! empty( $review->reviewer_profile_photo_url ) ) {
        $output .= '<img class="flairltd-review-card__avatar" src="' . esc_url( $review->reviewer_profile_photo_url ) . '" alt="' . esc_attr( $review->reviewer_display_name ) . '" width="48" height="48" loading="lazy">';
    }
    $output .= '<div class="flairltd-review-card__author-info"><span class="flairltd-review-card__author-name">' . esc_html( $review->reviewer_display_name ) . '</span><span class="flairltd-review-card__date">' . esc_html( date( 'F j, Y', strtotime( $review->create_time ) ) ) . '</span></div>';
    $output .= '</div>';
    $output .= '<div class="flairltd-review-card__rating">';
    for ( $i = 0; $i < $rating; $i++ ) { $output .= $star_svg; }
    $output .= '</div>';
    $output .= $comment_html;
    if ( $show_reply && ! empty( $review->reply_comment ) ) {
        $output .= '<div class="flairltd-review-card__reply"><strong class="flairltd-review-card__reply-author">Response from the owner:</strong><p class="flairltd-review-card__reply-text">' . nl2br( esc_html( $review->reply_comment ) ) . '</p></div>';
    }
    $output .= '</div>';

    return $output;
}

/**
 * Parses the word-to-URL mappings from the settings textarea.
 *
 * @return array Associative array: word => [urls]
 */
if ( ! function_exists( 'flairltd_reviews_get_word_mappings' ) ) {
    function flairltd_reviews_get_word_mappings() {
        $raw = get_option( 'flairltd_reviews_word_mappings', '' );
        $lines = explode( "\n", $raw );
        $mappings = [];

        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( empty( $line ) ) {
                continue;
            }
            $parts = explode( '|', $line, 2 );
            if ( count( $parts ) !== 2 ) {
                continue;
            }
            $word = strtolower( trim( $parts[0] ) );
            $urls = array_map( 'trim', explode( ',', $parts[1] ) );
            $urls = array_filter( $urls );
            if ( ! empty( $word ) && ! empty( $urls ) ) {
                $mappings[ $word ] = $urls;
            }
        }

        return $mappings;
    }
}

function flairltd_reviews_associate_reviews_to_posts( $reviews ) {
    if ( empty( $reviews ) ) {
        return 0;
    }

    $mappings = flairltd_reviews_get_word_mappings();
    if ( empty( $mappings ) ) {
        return 0;
    }

    $associations_made = 0;

    foreach ( $reviews as $review ) {
        $comment = $review->comment ?? '';
        if ( empty( $comment ) ) {
            continue;
        }

        $matched_post_ids = [];

        foreach ( $mappings as $word => $urls ) {
            if ( preg_match( '/\b' . preg_quote( $word, '/' ) . '\b/i', $comment ) ) {
                foreach ( $urls as $url ) {
                    $post_id = url_to_postid( $url );
                    if ( $post_id ) {
                        $matched_post_ids[] = $post_id;
                    }
                }
            }
        }

        if ( ! empty( $matched_post_ids ) ) {
            foreach ( array_unique( $matched_post_ids ) as $matched_post_id ) {
                $existing_ids = get_post_meta( $matched_post_id, '_flairltd_associated_review_ids', true );
                if ( ! is_array( $existing_ids ) ) {
                    $existing_ids = [];
                }

                $existing_ids[] = $review->id;
                $updated_ids = array_unique( $existing_ids );

                update_post_meta( $matched_post_id, '_flairltd_associated_review_ids', $updated_ids );
                update_post_meta( $matched_post_id, '_flairltd_associated_reviews_count', count( $updated_ids ) );

                $associations_made++;
            }
        }
    }

    return $associations_made;
}

// =============================================================================
// SECTION 4: ADMIN UI FOR ASSOCIATIONS
// =============================================================================

function flairltd_reviews_add_reviews_admin_column( $columns ) {
    $new_columns = [];
    foreach ($columns as $key => $title) {
        $new_columns[$key] = $title;
        if ($key === 'title') {
            $new_columns['associated_reviews'] = 'Associated Reviews';
        }
    }
    return $new_columns;
}

function flairltd_reviews_column_content( $column, $post_id ) {
    if ( 'associated_reviews' === $column ) {
        $count = get_post_meta( $post_id, '_flairltd_associated_reviews_count', true );
        $display_count = ! empty( $count ) ? intval( $count ) : 0;
        echo '<strong><a href="' . get_edit_post_link( $post_id ) . '">' . esc_html( $display_count ) . '</a></strong>';
    }
}

function flairltd_reviews_make_reviews_column_sortable( $columns ) {
    $columns['associated_reviews'] = '_flairltd_associated_reviews_count';
    return $columns;
}

function flairltd_reviews_sort_by_reviews_count( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( '_flairltd_associated_reviews_count' === $query->get( 'orderby' ) ) {
        $query->set( 'meta_key', '_flairltd_associated_reviews_count' );
        $query->set( 'orderby', 'meta_value_num' );
    }
}
add_action( 'pre_get_posts', 'flairltd_reviews_sort_by_reviews_count' );

$target_cpts_for_columns = get_post_types( ['public' => true], 'names' );
foreach ( $target_cpts_for_columns as $cpt ) {
    add_filter( "manage_{$cpt}_posts_columns", 'flairltd_reviews_add_reviews_admin_column' );
    add_action( "manage_{$cpt}_posts_custom_column", 'flairltd_reviews_column_content', 10, 2 );
    add_filter( "manage_edit-{$cpt}_sortable_columns", 'flairltd_reviews_make_reviews_column_sortable' );
}

// =============================================================================
// SECTION 5: ASSOCIATION MANAGEMENT META BOX
// =============================================================================

function flairltd_reviews_meta_box_setup() {
    $target_cpts = get_post_types( ['public' => true], 'names' );
    foreach ( $target_cpts as $cpt ) {
        add_meta_box(
            'flairltd_associated_reviews_meta_box',
            'Associated Reviews',
            'flairltd_reviews_meta_box_html',
            $cpt,
            'side',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'flairltd_reviews_meta_box_setup' );

function flairltd_reviews_meta_box_html( $post ) {
    global $wpdb;
    wp_nonce_field( 'flairltd_reviews_save_meta_box_data', 'flairltd_reviews_meta_box_nonce' );

    $review_ids = get_post_meta( $post->ID, '_flairltd_associated_review_ids', true );

    if ( empty( $review_ids ) || ! is_array( $review_ids ) ) {
        echo '<p>No reviews are currently associated with this page.</p>';
        return;
    }

    $table_name = $wpdb->prefix . 'flairltd_reviews';
    $placeholders = implode( ',', array_fill( 0, count( $review_ids ), '%d' ) );
    
    $reviews = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, reviewer_display_name, comment FROM {$table_name} WHERE id IN ({$placeholders})",
        $review_ids
    ) );
    
    if ( empty( $reviews ) ) {
        echo '<p>Associated review data could not be found.</p>';
        return;
    }

    echo '<div class="flairltd-reviews-meta-box-list">';
    foreach ( $reviews as $review ) {
        ?>
        <div class="review-item">
            <label>
                <input type="checkbox" name="associated_review_ids[]" value="<?php echo esc_attr( $review->id ); ?>" checked>
                <strong><?php echo esc_html( $review->reviewer_display_name ); ?>:</strong>
                <span style="color: #555;"><?php echo esc_html( wp_trim_words( $review->comment, 10, '...' ) ); ?></span>
            </label>
        </div>
        <?php
    }
    echo '</div>';
    echo '<p class="description">Uncheck a review and click "Update" to remove its association from this page.</p>';
}

function flairltd_reviews_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['flairltd_reviews_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['flairltd_reviews_meta_box_nonce'], 'flairltd_reviews_save_meta_box_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $submitted_ids = isset( $_POST['associated_review_ids'] ) ? $_POST['associated_review_ids'] : [];
    $sanitized_ids = array_map( 'intval', $submitted_ids );
    $unique_ids = array_unique( $sanitized_ids );

    update_post_meta( $post_id, '_flairltd_associated_review_ids', $unique_ids );
    update_post_meta( $post_id, '_flairltd_associated_reviews_count', count( $unique_ids ) );
}
add_action( 'save_post', 'flairltd_reviews_save_meta_box' );
