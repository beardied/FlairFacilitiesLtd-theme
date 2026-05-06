<?php
/**
 * Sitemap Auto-Submission to Search Engines
 *
 * @package FlairFacilitiesLtd
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Admin Styles ─────────────────────────────────────────────────
function flairltd_sitemap_submit_admin_styles( $hook ) {
    if ( $hook !== 'settings_page_flairltd-sitemap-submit' ) {
        return;
    }
    ?>
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 26px;
            vertical-align: middle;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 26px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2271b1;
        }
        input:checked + .slider:before {
            transform: translateX(22px);
        }
        input:disabled + .slider {
            background-color: #e0e0e0;
            cursor: not-allowed;
            opacity: 0.6;
        }
        input:disabled + .slider:before {
            background-color: #f0f0f0;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #2271b1;
        }
    </style>
    <?php
}
add_action( 'admin_head', 'flairltd_sitemap_submit_admin_styles' );

// ── Admin Menu ───────────────────────────────────────────────────
function flairltd_sitemap_submit_menu() {
    add_submenu_page(
        'options-general.php',
        __( 'Sitemap Submit', 'flairfacilitiesltd' ),
        __( 'Sitemap Submit', 'flairfacilitiesltd' ),
        'manage_options',
        'flairltd-sitemap-submit',
        'flairltd_sitemap_submit_page'
    );
}
add_action( 'admin_menu', 'flairltd_sitemap_submit_menu' );

// ── Register Settings ────────────────────────────────────────────
function flairltd_sitemap_submit_settings() {
    register_setting( 'flairltd_sitemap_submit_group', 'flairltd_submit_bing',    [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    register_setting( 'flairltd_sitemap_submit_group', 'flairltd_submit_yandex',  [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    register_setting( 'flairltd_sitemap_submit_group', 'flairltd_submit_yahoo',   [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    register_setting( 'flairltd_sitemap_submit_group', 'flairltd_submit_duckduckgo', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );

    add_settings_section(
        'flairltd_sitemap_submit_section',
        __( 'Auto-Submit Sitemap', 'flairfacilitiesltd' ),
        'flairltd_sitemap_submit_section_cb',
        'flairltd-sitemap-submit'
    );

    $engines = [
        'bing'        => __( 'Bing', 'flairfacilitiesltd' ),
        'yandex'      => __( 'Yandex', 'flairfacilitiesltd' ),
        'yahoo'       => __( 'Yahoo', 'flairfacilitiesltd' ),
        'duckduckgo'  => __( 'DuckDuckGo', 'flairfacilitiesltd' ),
    ];

    foreach ( $engines as $key => $label ) {
        add_settings_field(
            'flairltd_submit_' . $key,
            $label,
            'flairltd_sitemap_submit_field_cb',
            'flairltd-sitemap-submit',
            'flairltd_sitemap_submit_section',
            [ 'key' => $key, 'label' => $label ]
        );
    }
}
add_action( 'admin_init', 'flairltd_sitemap_submit_settings' );

// ── Section Callback ─────────────────────────────────────────────
function flairltd_sitemap_submit_section_cb() {
    $discouraged = ! get_option( 'blog_public' );
    $sitemap_url = esc_url( home_url( '/sitemap.xml' ) );

    echo '<p>' . __( 'Automatically submit your sitemap to search engines when pages or posts are published or updated.', 'flairfacilitiesltd' ) . '</p>';
    echo '<p><strong>' . __( 'Sitemap URL:', 'flairfacilitiesltd' ) . '</strong> <a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a></p>';

    if ( $discouraged ) {
        echo '<div class="notice notice-warning inline"><p><strong>' . __( 'Search engine visibility is currently disabled.', 'flairfacilitiesltd' ) . '</strong> ' . __( 'Go to Settings → Reading and uncheck "Discourage search engines from indexing this site" to enable sitemap submission.', 'flairfacilitiesltd' ) . '</p></div>';
    } else {
        echo '<div class="notice notice-info inline"><p>' . __( 'Search engine visibility is enabled. You can toggle submission below.', 'flairfacilitiesltd' ) . '</p></div>';
    }
}

// ── Field Callback ───────────────────────────────────────────────
function flairltd_sitemap_submit_field_cb( $args ) {
    $key         = $args['key'];
    $option      = get_option( 'flairltd_submit_' . $key, '0' );
    $discouraged = ! get_option( 'blog_public' );
    $disabled    = $discouraged ? 'disabled' : '';
    $checked     = checked( '1', $option, false );

    // Force option to 0 if search engines discouraged
    if ( $discouraged && $option === '1' ) {
        update_option( 'flairltd_submit_' . $key, '0' );
        $checked = '';
    }

    echo '<label class="switch">';
    echo '<input type="checkbox" id="flairltd_submit_' . esc_attr( $key ) . '" name="flairltd_submit_' . esc_attr( $key ) . '" value="1" ' . $checked . ' ' . $disabled . '>';
    echo '<span class="slider"></span>';
    echo '</label>';

    $notes = [
        'bing'       => __( 'Pings Bing Webmaster Tools on every publish/update.', 'flairfacilitiesltd' ),
        'yandex'     => __( 'Pings Yandex Webmaster on every publish/update.', 'flairfacilitiesltd' ),
        'yahoo'      => __( 'Yahoo uses Bing\'s index — enable Bing to cover Yahoo.', 'flairfacilitiesltd' ),
        'duckduckgo' => __( 'DuckDuckGo sources from Bing — enable Bing for best coverage.', 'flairfacilitiesltd' ),
    ];

    if ( isset( $notes[ $key ] ) ) {
        echo '<p class="description">' . esc_html( $notes[ $key ] ) . '</p>';
    }
}

// ── Admin Page ───────────────────────────────────────────────────
function flairltd_sitemap_submit_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Handle manual submit
    $submitted = false;
    if ( isset( $_POST['flairltd_manual_submit'] ) && check_admin_referer( 'flairltd_sitemap_submit_action', 'flairltd_sitemap_submit_nonce' ) ) {
        $results = flairltd_submit_sitemap_to_engines( true );
        $submitted = true;
    }

    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <?php if ( $submitted ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php _e( 'Sitemap submitted.', 'flairfacilitiesltd' ); ?></strong></p>
                <?php foreach ( $results as $engine => $result ) : ?>
                    <p><?php echo esc_html( $engine ) . ': ' . ( $result['success'] ? __( 'Success', 'flairfacilitiesltd' ) : __( 'Failed', 'flairfacilitiesltd' ) . ' — ' . esc_html( $result['message'] ) ); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'flairltd_sitemap_submit_group' );
            do_settings_sections( 'flairltd-sitemap-submit' );
            submit_button( __( 'Save Settings', 'flairfacilitiesltd' ) );
            ?>
        </form>

        <?php if ( ! empty( $_POST ) ) : // Flush rewrite rules after settings save ?>
            <?php flush_rewrite_rules(); ?>
        <?php endif; ?>

        <hr>
        <h2><?php _e( 'Manual Submit', 'flairfacilitiesltd' ); ?></h2>
        <p><?php _e( 'Submit your sitemap right now to all enabled search engines.', 'flairfacilitiesltd' ); ?></p>
        <form method="post">
            <?php wp_nonce_field( 'flairltd_sitemap_submit_action', 'flairltd_sitemap_submit_nonce' ); ?>
            <?php submit_button( __( 'Submit Sitemap Now', 'flairfacilitiesltd' ), 'secondary', 'flairltd_manual_submit' ); ?>
        </form>
    </div>
    <?php
}

// ── Submit Sitemap ───────────────────────────────────────────────
function flairltd_submit_sitemap_to_engines( $force = false ) {
    $sitemap_url = esc_url_raw( home_url( '/sitemap.xml' ) );
    $results     = [];

    $engines = [
        'bing' => [
            'enabled' => get_option( 'flairltd_submit_bing', '0' ),
            'url'     => 'https://www.bing.com/webmaster/ping.aspx?sitemap=' . urlencode( $sitemap_url ),
        ],
        'yandex' => [
            'enabled' => get_option( 'flairltd_submit_yandex', '0' ),
            'url'     => 'https://webmaster.yandex.ru/ping?sitemap=' . urlencode( $sitemap_url ),
        ],
    ];

    foreach ( $engines as $name => $cfg ) {
        if ( ! $force && $cfg['enabled'] !== '1' ) {
            continue;
        }

        $response = wp_remote_get( $cfg['url'], [ 'timeout' => 15, 'sslverify' => false ] );

        if ( is_wp_error( $response ) ) {
            $results[ $name ] = [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        } else {
            $code = wp_remote_retrieve_response_code( $response );
            $results[ $name ] = [
                'success' => $code >= 200 && $code < 300,
                'message' => 'HTTP ' . $code,
            ];
        }
    }

    return $results;
}

// ── Auto-submit on publish / update ──────────────────────────────
function flairltd_auto_submit_sitemap( $new_status, $old_status, $post ) {
    // Only trigger on publish or update of published content
    if ( $new_status !== 'publish' ) {
        return;
    }

    // Only for pages and posts
    if ( ! in_array( $post->post_type, [ 'page', 'post' ], true ) ) {
        return;
    }

    // Don't run on autosave or revisions
    if ( wp_is_post_autosave( $post->ID ) || wp_is_post_revision( $post->ID ) ) {
        return;
    }

    // Don't submit if search engines discouraged
    if ( ! get_option( 'blog_public' ) ) {
        return;
    }

    // Check if any engine is enabled
    $engines = [ 'bing', 'yandex' ];
    $any_enabled = false;
    foreach ( $engines as $engine ) {
        if ( get_option( 'flairltd_submit_' . $engine, '0' ) === '1' ) {
            $any_enabled = true;
            break;
        }
    }

    if ( ! $any_enabled ) {
        return;
    }

    // Submit sitemap
    flairltd_submit_sitemap_to_engines();
}
add_action( 'transition_post_status', 'flairltd_auto_submit_sitemap', 20, 3 );
