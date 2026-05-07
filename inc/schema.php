<?php
/**
 * Structured Data / Schema.org markup
 *
 * @package FlairFacilitiesLtd
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Customizer: Google Map ID (already added in customizer.php) ──

// ── Page Meta Boxes ──────────────────────────────────────────────
function flairltd_schema_meta_boxes() {
    add_meta_box(
        'flairltd_schema_meta',
        __( 'Schema.org Markup', 'flairfacilitiesltd' ),
        'flairltd_schema_meta_box_html',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'flairltd_schema_meta_boxes' );

function flairltd_schema_meta_box_html( $post ) {
    wp_nonce_field( 'flairltd_schema_meta_save', 'flairltd_schema_meta_nonce' );

    $schema_type = get_post_meta( $post->ID, '_flairltd_schema_type', true );
    $service_name = get_post_meta( $post->ID, '_flairltd_schema_service_name', true );
    $service_desc = get_post_meta( $post->ID, '_flairltd_schema_service_desc', true );

    $types = [
        ''               => __( 'Default — WebPage', 'flairfacilitiesltd' ),
        'parent_service' => __( 'Parent Service Hub', 'flairfacilitiesltd' ),
        'child_service'  => __( 'Child Service', 'flairfacilitiesltd' ),
        'contact'        => __( 'Contact Page (HVACBusiness)', 'flairfacilitiesltd' ),
    ];
    ?>
    <p>
        <label for="flairltd_schema_type"><strong><?php _e( 'Schema Type', 'flairfacilitiesltd' ); ?></strong></label><br>
        <select name="flairltd_schema_type" id="flairltd_schema_type" style="width:100%;">
            <?php foreach ( $types as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $schema_type, $value ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="flairltd_schema_service_name"><strong><?php _e( 'Service Name (optional)', 'flairfacilitiesltd' ); ?></strong></label><br>
        <input type="text" name="flairltd_schema_service_name" id="flairltd_schema_service_name" value="<?php echo esc_attr( $service_name ); ?>" style="width:100%;" placeholder="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">
        <span class="description"><?php _e( 'Falls back to page title if empty.', 'flairfacilitiesltd' ); ?></span>
    </p>
    <p>
        <label for="flairltd_schema_service_desc"><strong><?php _e( 'Service Description (optional)', 'flairfacilitiesltd' ); ?></strong></label><br>
        <textarea name="flairltd_schema_service_desc" id="flairltd_schema_service_desc" rows="4" style="width:100%;" placeholder="<?php echo esc_attr( __( 'Falls back to excerpt or first paragraph.', 'flairfacilitiesltd' ) ); ?>"><?php echo esc_textarea( $service_desc ); ?></textarea>
    </p>
    <?php
}

function flairltd_schema_meta_save( $post_id ) {
    if ( ! isset( $_POST['flairltd_schema_meta_nonce'] ) || ! wp_verify_nonce( $_POST['flairltd_schema_meta_nonce'], 'flairltd_schema_meta_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['flairltd_schema_type'] ) ) {
        update_post_meta( $post_id, '_flairltd_schema_type', sanitize_text_field( $_POST['flairltd_schema_type'] ) );
    }
    if ( isset( $_POST['flairltd_schema_service_name'] ) ) {
        update_post_meta( $post_id, '_flairltd_schema_service_name', sanitize_text_field( $_POST['flairltd_schema_service_name'] ) );
    }
    if ( isset( $_POST['flairltd_schema_service_desc'] ) ) {
        update_post_meta( $post_id, '_flairltd_schema_service_desc', sanitize_textarea_field( $_POST['flairltd_schema_service_desc'] ) );
    }
}
add_action( 'save_post', 'flairltd_schema_meta_save' );

// ── Helpers ──────────────────────────────────────────────────────
function flairltd_get_intl_phone() {
    $phone = get_theme_mod( 'flairltd_phone', '020 7998 9005' );
    $digits = preg_replace( '/[^0-9]/', '', $phone );
    // UK number: if starts with 0, replace with +44
    if ( strpos( $digits, '0' ) === 0 ) {
        $digits = '+44' . substr( $digits, 1 );
    }
    return $digits;
}

function flairltd_get_logo_url() {
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'full' );
        if ( $url ) {
            return $url;
        }
    }
    return '';
}

function flairltd_get_schema_description( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $custom = get_post_meta( $post_id, '_flairltd_schema_service_desc', true );
    if ( ! empty( $custom ) ) {
        return $custom;
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        return '';
    }

    if ( ! empty( $post->post_excerpt ) ) {
        return wp_strip_all_tags( $post->post_excerpt, true );
    }

    $text = strip_shortcodes( $post->post_content );
    $text = wp_strip_all_tags( $text, true );
    $text = trim( $text );

    if ( strlen( $text ) > 300 ) {
        $text = substr( $text, 0, 300 );
        $last_space = strrpos( $text, ' ' );
        if ( $last_space > 200 ) {
            $text = substr( $text, 0, $last_space ) . '…';
        }
    }
    return $text;
}

function flairltd_get_schema_service_name( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $custom = get_post_meta( $post_id, '_flairltd_schema_service_name', true );
    return ! empty( $custom ) ? $custom : get_the_title( $post_id );
}

// ── HVACBusiness base data ───────────────────────────────────────
function flairltd_get_hvacbusiness_base() {
    $site_name   = get_bloginfo( 'name' );
    $site_url    = home_url( '/' );
    $logo        = flairltd_get_logo_url();
    $phone       = flairltd_get_intl_phone();
    $map_id      = get_theme_mod( 'flairltd_google_map_id', '' );
    $has_map     = ! empty( $map_id ) ? 'https://www.google.com/maps/d/u/0/embed?mid=' . $map_id : '';

    $street   = get_theme_mod( 'flairltd_address_street', '24 Kemp House, 152 City Road' );
    $city     = get_theme_mod( 'flairltd_address_city', 'London' );
    $region   = get_theme_mod( 'flairltd_address_region', 'Greater London' );
    $postcode = get_theme_mod( 'flairltd_address_postcode', 'EC1V 2NX' );
    $country  = get_theme_mod( 'flairltd_address_country', 'GB' );

    $base = [
        '@context'         => 'https://schema.org',
        '@type'            => 'HVACBusiness',
        'name'             => $site_name,
        'url'              => $site_url,
        'logo'             => $logo,
        'telephone'        => $phone,
        'companyNumber'    => '14816196',
        'iso6523'          => '0088:14816196',
        'vatID'            => 'GB467203885',
        'isicV4'           => '4322',
        'address'          => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $street,
            'addressLocality' => $city,
            'addressRegion'   => $region,
            'postalCode'      => $postcode,
            'addressCountry'  => $country,
        ],
        'areaServed'       => [
            [
                '@type' => 'AdministrativeArea',
                'name'  => 'Greater London',
            ],
            [
                '@type'      => 'GeoCircle',
                'geoMidpoint' => [
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => 51.5074,
                    'longitude' => -0.1278,
                ],
                'geoRadius'   => '30000',
            ],
        ],
        'hasCredential'    => [
            [
                '@type'              => 'EducationalOccupationalCredential',
                'name'               => 'Gas Safe Register',
                'credentialCategory' => 'Certification',
                'value'              => '970944',
                'url'                => 'https://www.gassaferegister.co.uk/',
            ],
        ],
    ];

    if ( $has_map ) {
        $base['hasMap'] = $has_map;
    }

    return $base;
}

function flairltd_get_knows_about() {
    $services = [
        [ 'name' => 'Commercial Gas Boiler Systems',        'sameAs' => 'https://en.wikipedia.org/wiki/Boiler',                    'slug' => 'boiler-heating-systems/commercial-gas-boiler-systems/',         'label' => 'Boiler & Heating Systems - Commercial Gas Boiler Systems' ],
        [ 'name' => 'Steam Boilers & Steam Pipes',          'sameAs' => 'https://www.wikidata.org/wiki/Q149535',                     'slug' => 'boiler-heating-systems/steam-boilers-steam-pipes/',             'label' => 'Boiler & Heating Systems - Steam Boilers & Steam Pipes' ],
        [ 'name' => 'Communal Heating Systems',             'sameAs' => 'https://en.wikipedia.org/wiki/District_heating',            'slug' => 'boiler-heating-systems/block-managed-communal-heating/',        'label' => 'Boiler & Heating Systems - Block Managed Heating' ],
        [ 'name' => 'HVAC & Mechanical Plant',              'sameAs' => 'https://en.wikipedia.org/wiki/Heating,_ventilation,_and_air_conditioning', 'slug' => 'hvac-cooling/hvac-ventilation-mechanical-plant/',              'label' => 'HVAC & Cooling - HVAC, Ventilation & Mechanical Plant' ],
        [ 'name' => 'Chillers & Cooling Plant',             'sameAs' => 'https://en.wikipedia.org/wiki/Chiller',                     'slug' => 'hvac-cooling/chillers-cooling-plant/',                          'label' => 'HVAC & Cooling - Chillers & Cooling Plant' ],
        [ 'name' => 'Air Conditioning Systems',             'sameAs' => 'https://en.wikipedia.org/wiki/Air_conditioning',            'slug' => 'hvac-cooling/air-conditioning-systems/',                        'label' => 'HVAC & Cooling - Air Conditioning Systems' ],
        [ 'name' => 'Heat Pumps',                           'sameAs' => 'https://en.wikipedia.org/wiki/Heat_pump',                   'slug' => 'hvac-cooling/heat-pumps/',                                      'label' => 'HVAC & Cooling - Heat Pumps' ],
        [ 'name' => 'Cooling Towers & Evaporative Condensers', 'sameAs' => 'https://en.wikipedia.org/wiki/Cooling_tower',            'slug' => 'hvac-cooling/cooling-towers-evaporative-condensers/',           'label' => 'HVAC & Cooling - Cooling Tower Services' ],
        [ 'name' => 'Air Handling Units (AHU)',             'sameAs' => 'https://en.wikipedia.org/wiki/Air_handler',                 'slug' => 'hvac-cooling/air-handling-units-ahu/',                          'label' => 'HVAC & Cooling - Air Handling Unit (AHU) Services' ],
        [ 'name' => 'Commercial Booster Pumps',             'sameAs' => 'https://en.wikipedia.org/wiki/Booster_pump',                'slug' => 'water-pressure-systems/booster-pumps/',                         'label' => 'Water & Pressure Systems - Booster Pumps' ],
        [ 'name' => 'Pressurisation Units',                 'sameAs' => 'https://www.wikidata.org/wiki/Q7241355',                    'slug' => 'water-pressure-systems/pressurisation-units/',                  'label' => 'Water & Pressure Systems - Pressurisation Units' ],
        [ 'name' => 'Building Management Systems (BMS)',    'sameAs' => 'https://en.wikipedia.org/wiki/Building_automation',         'slug' => 'gas-control-compliance/bms-repair-design-build/',               'label' => 'Gas, Control & Compliance - BMS Repair, Design & Build' ],
        [ 'name' => 'Gas Safety & Compliance',              'sameAs' => 'https://en.wikipedia.org/wiki/Gas_safety',                  'slug' => 'gas-control-compliance/gas-safety-certificates-cp42/',          'label' => 'Gas, Control & Compliance - Gas Safety Certificates & CP42' ],
        [ 'name' => 'Commercial Pipework & Lagging',        'sameAs' => 'https://en.wikipedia.org/wiki/Pipe_insulation',             'slug' => 'pipework-lagging-insulation/',                                  'label' => 'Pipework, Lagging & Insulation' ],
        [ 'name' => 'Phenolic Insulation Systems',          'sameAs' => 'https://en.wikipedia.org/wiki/Phenolic_foam',               'slug' => 'pipework-lagging-insulation/phenolic-insulation-systems/',      'label' => 'Pipework, Lagging & Insulation - Phenolic Insulation Systems' ],
        [ 'name' => 'Asbestos Insulation Removal',          'sameAs' => 'https://en.wikipedia.org/wiki/Asbestos_abatement',          'slug' => 'pipework-lagging-insulation/asbestos-insulation-removal/',      'label' => 'Pipework, Lagging & Insulation - Asbestos Insulation Removal' ],
        [ 'name' => 'Heat Interface Units (HIU)',           'sameAs' => 'https://www.wikidata.org/wiki/Q25303664',                   'slug' => 'pipework-lagging-insulation/heat-interface-units-hiu/',         'label' => 'Pipework, Lagging & Insulation - Heat Interface Unit Maintenance & Engineering' ],
    ];

    $out = [];
    foreach ( $services as $s ) {
        $out[] = [
            '@type'     => 'Thing',
            'name'      => $s['name'],
            'sameAs'    => $s['sameAs'],
            'subjectOf' => [
                '@type' => 'WebPage',
                '@id'   => home_url( '/' . $s['slug'] ),
                'name'  => $s['label'],
            ],
        ];
    }
    return $out;
}

// ── Main schema output ───────────────────────────────────────────
function flairltd_output_schema() {
    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );

    // ── HOMEPAGE ──
    if ( is_front_page() ) {
        $schema = flairltd_get_hvacbusiness_base();
        $schema['description'] = 'Flair Facilities provides expert commercial mechanical, HVAC, and gas engineering services across all 32 London boroughs and the M25 corridor. We specialize in large-scale boiler plant maintenance, commercial cooling systems, water booster solutions, and industrial pipework lagging. Our Gas Safe and REFCOM certified engineers deliver comprehensive facilities management, including BMS integration, compliance testing, and 24/7 emergency repair for the commercial and industrial sectors.';
        $schema['knowsAbout'] = flairltd_get_knows_about();
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        return;
    }

    // ── CONTACT PAGE ──
    if ( is_page() ) {
        $schema_type = get_post_meta( get_the_ID(), '_flairltd_schema_type', true );

        if ( $schema_type === 'contact' || in_array( get_post_field( 'post_name', get_the_ID() ), [ 'contact-us', 'contact', 'get-in-touch' ], true ) ) {
            $schema = flairltd_get_hvacbusiness_base();
            $schema['description'] = flairltd_get_schema_description();
            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
            return;
        }

        // ── PARENT SERVICE HUB ──
        if ( $schema_type === 'parent_service' ) {
            $page_id   = get_the_ID();
            $page_url  = get_permalink( $page_id );
            $page_name = flairltd_get_schema_service_name( $page_id );
            $page_desc = flairltd_get_schema_description( $page_id );

            $children = get_children( [
                'post_parent' => $page_id,
                'post_type'   => 'page',
                'post_status' => 'publish',
                'orderby'     => 'title',
                'order'       => 'ASC',
            ] );

            $offers = [];
            foreach ( $children as $child ) {
                $offers[] = [
                    '@type'      => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name'  => get_the_title( $child->ID ),
                        'url'   => get_permalink( $child->ID ),
                    ],
                ];
            }

            $schema = [
                '@context'         => 'https://schema.org',
                '@type'            => 'Service',
                'name'             => $page_name,
                'description'      => $page_desc,
                'url'              => $page_url,
                'provider'         => [
                    '@type' => 'HVACBusiness',
                    'name'  => $site_name,
                    'url'   => $site_url,
                ],
                'areaServed'       => [
                    '@type' => 'AdministrativeArea',
                    'name'  => 'Greater London',
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id'   => $page_url,
                ],
            ];

            if ( ! empty( $offers ) ) {
                $schema['hasOfferCatalog'] = [
                    '@type'          => 'OfferCatalog',
                    'name'           => $page_name . ' Services',
                    'itemListElement' => $offers,
                ];
            }

            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
            return;
        }

        // ── CHILD SERVICE ──
        if ( $schema_type === 'child_service' ) {
            $page_id   = get_the_ID();
            $page_url  = get_permalink( $page_id );
            $page_name = flairltd_get_schema_service_name( $page_id );
            $page_desc = flairltd_get_schema_description( $page_id );
            $image     = get_the_post_thumbnail_url( $page_id, 'full' );

            $schema = [
                '@context'         => 'https://schema.org',
                '@type'            => 'Service',
                'name'             => $page_name,
                'description'      => $page_desc,
                'url'              => $page_url,
                'provider'         => [
                    '@type'           => 'HVACBusiness',
                    'name'            => $site_name,
                    'url'             => $site_url,
                    'companyNumber'   => '14816196',
                    'hasCredential'   => [
                        [
                            '@type'              => 'EducationalOccupationalCredential',
                            'name'               => 'Gas Safe Register',
                            'value'              => '970944',
                        ],
                    ],
                ],
                'areaServed'       => [
                    '@type' => 'AdministrativeArea',
                    'name'  => 'Greater London',
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id'   => $page_url,
                ],
            ];

            if ( $image ) {
                $schema['image'] = $image;
            }

            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
            return;
        }

        // ── DEFAULT PAGE ──
        $page_id   = get_the_ID();
        $page_url  = get_permalink( $page_id );
        $page_name = get_the_title( $page_id );
        $page_desc = flairltd_get_schema_description( $page_id );

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => $page_name,
            'description' => $page_desc,
            'url'         => $page_url,
            'publisher'   => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'url'   => $site_url,
            ],
        ];

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        return;
    }

    // ── BLOG POSTS ──
    if ( is_singular( 'post' ) ) {
        $post_id   = get_the_ID();
        $post_url  = get_permalink( $post_id );
        $headline  = get_the_title( $post_id );
        $desc      = flairltd_get_schema_description( $post_id );
        $published = get_the_date( 'Y-m-d', $post_id );
        $modified  = get_the_modified_date( 'Y-m-d', $post_id );
        $image     = get_the_post_thumbnail_url( $post_id, 'full' );
        $logo      = flairltd_get_logo_url();

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'BlogPosting',
            'headline'      => $headline,
            'description'   => $desc,
            'url'           => $post_url,
            'datePublished' => $published,
            'dateModified'  => $modified ?: $published,
            'author'        => [
                '@type' => 'Organization',
                'name'  => $site_name,
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => $site_name,
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $logo,
                ],
            ],
        ];

        if ( $image ) {
            $schema['image'] = $image;
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        return;
    }
}
add_action( 'wp_head', 'flairltd_output_schema', 3 );
