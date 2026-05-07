<?php
/**
 * @package FlairFacilitiesLtd
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function flairltd_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'flairltd_brand', [ 'title' => __( 'Brand Settings', 'flairfacilitiesltd' ), 'priority' => 20 ] );

    $wp_customize->add_setting( 'flairltd_primary_color', [ 'default' => '#1e3a8a', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_primary_color', [ 'label' => __( 'Primary Blue', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    $wp_customize->add_setting( 'flairltd_bright_color', [ 'default' => '#2563eb', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_bright_color', [ 'label' => __( 'Bright Blue', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    $wp_customize->add_setting( 'flairltd_accent_color', [ 'default' => '#dc2626', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_accent_color', [ 'label' => __( 'Accent Red', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    $wp_customize->add_setting( 'flairltd_orange_color', [ 'default' => '#ea580c', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_orange_color', [ 'label' => __( 'Orange', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    $wp_customize->add_setting( 'flairltd_dark_color', [ 'default' => '#0a1628', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_dark_color', [ 'label' => __( 'Dark Navy', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    // Logo Settings
    $wp_customize->add_section( 'flairltd_logo', [ 'title' => __( 'Logo Settings', 'flairfacilitiesltd' ), 'priority' => 25 ] );
    $wp_customize->add_setting( 'flairltd_logo_width', [ 'default' => '180', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_logo_width', [ 'label' => __( 'Logo Width (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 50, 'max' => 400 ] ] );
    $wp_customize->add_setting( 'flairltd_logo_height', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_logo_height', [ 'label' => __( 'Logo Max Height (px) — 0 for auto', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 200 ] ] );
    $wp_customize->add_setting( 'flairltd_logo_padding_top', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_logo_padding_top', [ 'label' => __( 'Logo Padding Top (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ] );
    $wp_customize->add_setting( 'flairltd_logo_padding_bottom', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_logo_padding_bottom', [ 'label' => __( 'Logo Padding Bottom (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ] );
    $wp_customize->add_setting( 'flairltd_header_height', [ 'default' => '72', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_header_height', [ 'label' => __( 'Header Bar Height (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 40, 'max' => 120 ] ] );
    $wp_customize->add_setting( 'flairltd_header_shrunk_height', [ 'default' => '60', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_header_shrunk_height', [ 'label' => __( 'Header Shrunk Height (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 40, 'max' => 120 ] ] );

    // Footer Logo Settings
    $wp_customize->add_setting( 'flairltd_footer_logo_width', [ 'default' => '180', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_footer_logo_width', [ 'label' => __( 'Footer Logo Width (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 50, 'max' => 400 ] ] );
    $wp_customize->add_setting( 'flairltd_footer_logo_height', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_footer_logo_height', [ 'label' => __( 'Footer Logo Max Height (px) — 0 for auto', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 200 ] ] );
    $wp_customize->add_setting( 'flairltd_footer_logo_padding_top', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_footer_logo_padding_top', [ 'label' => __( 'Footer Logo Padding Top (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ] );
    $wp_customize->add_setting( 'flairltd_footer_logo_padding_bottom', [ 'default' => '16', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_footer_logo_padding_bottom', [ 'label' => __( 'Footer Logo Padding Bottom (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ] );

    // Typography
    $wp_customize->add_section( 'flairltd_typography', [ 'title' => __( 'Typography', 'flairfacilitiesltd' ), 'priority' => 28 ] );
    $headings = [
        'h1' => [ 'label' => 'H1 Size (px)', 'default' => 48, 'min' => 20, 'max' => 100 ],
        'h2' => [ 'label' => 'H2 Size (px)', 'default' => 40, 'min' => 18, 'max' => 80 ],
        'h3' => [ 'label' => 'H3 Size (px)', 'default' => 32, 'min' => 16, 'max' => 70 ],
        'h4' => [ 'label' => 'H4 Size (px)', 'default' => 24, 'min' => 14, 'max' => 60 ],
        'h5' => [ 'label' => 'H5 Size (px)', 'default' => 20, 'min' => 12, 'max' => 50 ],
        'h6' => [ 'label' => 'H6 Size (px)', 'default' => 18, 'min' => 10, 'max' => 40 ],
    ];
    foreach ( $headings as $tag => $cfg ) {
        $id = 'flairltd_' . $tag . '_size';
        $wp_customize->add_setting( $id, [ 'default' => $cfg['default'], 'sanitize_callback' => 'absint' ] );
        $wp_customize->add_control( $id, [ 'label' => __( $cfg['label'], 'flairfacilitiesltd' ), 'section' => 'flairltd_typography', 'type' => 'number', 'input_attrs' => [ 'min' => $cfg['min'], 'max' => $cfg['max'] ] ] );
    }

    // Footer Content
    $wp_customize->add_section( 'flairltd_footer', [ 'title' => __( 'Footer Content', 'flairfacilitiesltd' ), 'priority' => 29 ] );
    $wp_customize->add_setting( 'flairltd_footer_desc', [ 'default' => 'Leading commercial mechanical and heating contractor providing services for companies and communal dwellings in London and surrounding areas.', 'sanitize_callback' => 'wp_kses_post' ] );
    $wp_customize->add_control( 'flairltd_footer_desc', [ 'label' => __( 'Footer Description', 'flairfacilitiesltd' ), 'section' => 'flairltd_footer', 'type' => 'textarea' ] );

    $cert_count = 4;
    for ( $i = 1; $i <= $cert_count; $i++ ) {
        $wp_customize->add_setting( 'flairltd_cert_' . $i, [ 'default' => '', 'sanitize_callback' => 'absint' ] );
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'flairltd_cert_' . $i, [
            'label'     => __( 'Certification Image ' . $i, 'flairfacilitiesltd' ),
            'section'   => 'flairltd_footer',
            'mime_type' => 'image',
        ] ) );
        $wp_customize->add_setting( 'flairltd_cert_' . $i . '_width', [ 'default' => '80', 'sanitize_callback' => 'absint' ] );
        $wp_customize->add_control( 'flairltd_cert_' . $i . '_width', [ 'label' => __( 'Cert ' . $i . ' Width (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_footer', 'type' => 'number', 'input_attrs' => [ 'min' => 20, 'max' => 300 ] ] );
    }

    // Contact Info
    $wp_customize->add_section( 'flairltd_contact', [ 'title' => __( 'Contact Info', 'flairfacilitiesltd' ), 'priority' => 30 ] );
    $wp_customize->add_setting( 'flairltd_phone', [ 'default' => '020 7998 9005', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_phone', [ 'label' => __( 'Phone', 'flairfacilitiesltd' ) . ' — Shortcode: [flair_phone]', 'section' => 'flairltd_contact', 'type' => 'text' ] );
    $wp_customize->add_setting( 'flairltd_email', [ 'default' => 'info@flairfacilities.co.uk', 'sanitize_callback' => 'sanitize_email' ] );
    $wp_customize->add_control( 'flairltd_email', [ 'label' => __( 'Email', 'flairfacilitiesltd' ) . ' — Shortcode: [flair_email]', 'section' => 'flairltd_contact', 'type' => 'email' ] );
    // Address — broken into parts for schema markup
    $wp_customize->add_setting( 'flairltd_address_street', [ 'default' => '24 Kemp House, 152 City Road', 'sanitize_callback' => 'sanitize_textarea_field' ] );
    $wp_customize->add_control( 'flairltd_address_street', [ 'label' => __( 'Street Address', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'textarea' ] );

    $wp_customize->add_setting( 'flairltd_address_city', [ 'default' => 'London', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_address_city', [ 'label' => __( 'City / Locality', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'text' ] );

    $wp_customize->add_setting( 'flairltd_address_region', [ 'default' => 'Greater London', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_address_region', [ 'label' => __( 'Region / County', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'text' ] );

    $wp_customize->add_setting( 'flairltd_address_postcode', [ 'default' => 'EC1V 2NX', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_address_postcode', [ 'label' => __( 'Postcode', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'text' ] );

    $wp_customize->add_setting( 'flairltd_address_country', [ 'default' => 'GB', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_address_country', [ 'label' => __( 'Country Code (ISO 3166-1 alpha-2)', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'text' ] );

    $wp_customize->add_setting( 'flairltd_google_map_id', [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_google_map_id', [ 'label' => __( 'Google Map ID (for schema markup)', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'text' ] );

    // Social Media
    $wp_customize->add_section( 'flairltd_social', [ 'title' => __( 'Social Media', 'flairfacilitiesltd' ), 'priority' => 31 ] );
    $socials = [
        'facebook'  => __( 'Facebook URL', 'flairfacilitiesltd' ),
        'linkedin'  => __( 'LinkedIn URL', 'flairfacilitiesltd' ),
        'x'         => __( 'X (Twitter) URL', 'flairfacilitiesltd' ),
        'instagram' => __( 'Instagram URL', 'flairfacilitiesltd' ),
        'youtube'   => __( 'YouTube URL', 'flairfacilitiesltd' ),
    ];
    foreach ( $socials as $key => $label ) {
        $wp_customize->add_setting( 'flairltd_social_' . $key, [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
        $wp_customize->add_control( 'flairltd_social_' . $key, [ 'label' => $label, 'section' => 'flairltd_social', 'type' => 'url' ] );
    }
}
add_action( 'customize_register', 'flairltd_customize_register' );

function flairltd_customizer_css() {
    $primary = get_theme_mod( 'flairltd_primary_color', '#1e3a8a' );
    $bright  = get_theme_mod( 'flairltd_bright_color', '#2563eb' );
    $accent  = get_theme_mod( 'flairltd_accent_color', '#dc2626' );
    $orange  = get_theme_mod( 'flairltd_orange_color', '#ea580c' );
    $dark    = get_theme_mod( 'flairltd_dark_color', '#0a1628' );
    $logo_w  = get_theme_mod( 'flairltd_logo_width', '180' );
    $logo_h  = get_theme_mod( 'flairltd_logo_height', '0' );
    $logo_pt = get_theme_mod( 'flairltd_logo_padding_top', '0' );
    $logo_pb = get_theme_mod( 'flairltd_logo_padding_bottom', '0' );
    $footer_logo_w  = get_theme_mod( 'flairltd_footer_logo_width', '180' );
    $footer_logo_h  = get_theme_mod( 'flairltd_footer_logo_height', '0' );
    $footer_logo_pt = get_theme_mod( 'flairltd_footer_logo_padding_top', '0' );
    $footer_logo_pb = get_theme_mod( 'flairltd_footer_logo_padding_bottom', '16' );
    $header_h = get_theme_mod( 'flairltd_header_height', '72' );
    $header_sh = get_theme_mod( 'flairltd_header_shrunk_height', '60' );
    $h1 = get_theme_mod( 'flairltd_h1_size', 48 );
    $h2 = get_theme_mod( 'flairltd_h2_size', 40 );
    $h3 = get_theme_mod( 'flairltd_h3_size', 32 );
    $h4 = get_theme_mod( 'flairltd_h4_size', 24 );
    $h5 = get_theme_mod( 'flairltd_h5_size', 20 );
    $h6 = get_theme_mod( 'flairltd_h6_size', 18 );
    ?>
    <style type="text/css">
        :root {
            --ffl-primary: <?php echo esc_html( $primary ); ?>;
            --ffl-bright: <?php echo esc_html( $bright ); ?>;
            --ffl-accent: <?php echo esc_html( $accent ); ?>;
            --ffl-orange: <?php echo esc_html( $orange ); ?>;
            --ffl-dark: <?php echo esc_html( $dark ); ?>;
            --header-height: <?php echo absint( $header_h ); ?>px;
            --header-shrunk: <?php echo absint( $header_sh ); ?>px;
            --ffl-h1-size: <?php echo absint( $h1 ); ?>px;
            --ffl-h2-size: <?php echo absint( $h2 ); ?>px;
            --ffl-h3-size: <?php echo absint( $h3 ); ?>px;
            --ffl-h4-size: <?php echo absint( $h4 ); ?>px;
            --ffl-h5-size: <?php echo absint( $h5 ); ?>px;
            --ffl-h6-size: <?php echo absint( $h6 ); ?>px;
        }
        .ffl-site-logo img {
            <?php if ( $logo_w > 0 ) echo 'max-width: ' . absint( $logo_w ) . 'px;'; ?>
            width: auto;
            height: auto;
            <?php if ( $logo_h > 0 ) echo 'max-height: ' . absint( $logo_h ) . 'px;'; ?>
            padding-top: <?php echo absint( $logo_pt ); ?>px;
            padding-bottom: <?php echo absint( $logo_pb ); ?>px;
        }
        .ffl-footer-logo img {
            <?php if ( $footer_logo_w > 0 ) echo 'max-width: ' . absint( $footer_logo_w ) . 'px;'; ?>
            width: auto;
            height: auto;
            <?php if ( $footer_logo_h > 0 ) echo 'max-height: ' . absint( $footer_logo_h ) . 'px;'; ?>
            padding-top: <?php echo absint( $footer_logo_pt ); ?>px;
            padding-bottom: <?php echo absint( $footer_logo_pb ); ?>px;
        }
        h1 { font-size: var(--ffl-h1-size) !important; }
        h2 { font-size: var(--ffl-h2-size) !important; }
        h3 { font-size: var(--ffl-h3-size) !important; }
        h4, .ffl-footer-heading { font-size: var(--ffl-h4-size) !important; }
        h5 { font-size: var(--ffl-h5-size) !important; }
        h6 { font-size: var(--ffl-h6-size) !important; }
        @media (max-width: 600px) {
            h1 { font-size: calc(var(--ffl-h1-size) * 0.65) !important; }
            h2 { font-size: calc(var(--ffl-h2-size) * 0.70) !important; }
            h3 { font-size: calc(var(--ffl-h3-size) * 0.75) !important; }
            h4, .ffl-footer-heading { font-size: calc(var(--ffl-h4-size) * 0.80) !important; }
            h5 { font-size: calc(var(--ffl-h5-size) * 0.85) !important; }
            h6 { font-size: calc(var(--ffl-h6-size) * 0.85) !important; }
        }
        .ffl-footer.has-background { background-color: var(--ffl-dark) !important; }
        body.has-fullwidth-last-block main.wp-block-group.alignfull {
            padding-bottom: 0 !important;
        }
        @media (max-width: 781px) {
            .ffl-child-hero + main.wp-block-group.alignfull,
            .ffl-child-hero + .wp-block-group.alignfull {
                padding-top: 60px !important;
            }
        }

        /* Breadcrumbs */
        .ffl-breadcrumbs {
            background: rgba(10,22,40,0.03);
            border-bottom: 1px solid rgba(10,22,40,0.08);
            padding: 14px 24px;
        }
        .ffl-breadcrumbs-list {
            display: flex !important;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            list-style: none !important;
            margin: 0 auto;
            padding: 0;
            max-width: var(--wp--style--global--content-size);
            font-size: 13px;
            line-height: 1.4;
        }
        .ffl-breadcrumbs-list li { list-style: none !important; }
        .ffl-breadcrumbs-item a {
            color: var(--ffl-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .ffl-breadcrumbs-item a:hover { color: var(--ffl-accent); }
        .ffl-breadcrumbs-item--current span {
            color: var(--ffl-dark);
            font-weight: 400;
        }
        .ffl-breadcrumbs-separator {
            display: flex;
            align-items: center;
            color: var(--ffl-accent);
            flex-shrink: 0;
        }
        .ffl-breadcrumbs-separator svg {
            width: 14px;
            height: 14px;
        }
        @media (max-width: 781px) {
            .ffl-breadcrumbs { padding: 12px 20px; }
            .ffl-breadcrumbs-list { font-size: 12px; }
        }
    </style>
    <?php
}
add_action( 'wp_head', 'flairltd_customizer_css', 100 );

// Shortcodes
function flairltd_phone_shortcode() {
    $phone = get_theme_mod( 'flairltd_phone', '020 7998 9005' );
    $clean = preg_replace( '/[^0-9+]/', '', $phone );
    return '<a href="tel:' . esc_attr( $clean ) . '">' . esc_html( $phone ) . '</a>';
}
add_shortcode( 'flair_phone', 'flairltd_phone_shortcode' );

function flairltd_email_shortcode() {
    $email = get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' );
    return '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
}
add_shortcode( 'flair_email', 'flairltd_email_shortcode' );

function flairltd_header_phone_shortcode() {
    $phone = get_theme_mod( 'flairltd_phone', '020 7998 9005' );
    $clean = preg_replace( '/[^0-9+]/', '', $phone );
    return '<p class="has-text-align-center" style="font-size:12px">Call Us: <a href="tel:' . esc_attr( $clean ) . '">' . esc_html( $phone ) . '</a></p>';
}
add_shortcode( 'flair_header_phone', 'flairltd_header_phone_shortcode' );

function flairltd_footer_contact_shortcode() {
    $phone = get_theme_mod( 'flairltd_phone', '020 7998 9005' );
    $clean = preg_replace( '/[^0-9+]/', '', $phone );
    $email = get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' );
    return '<p class="has-text-color" style="color:rgba(255,255,255,0.6);font-size:14px;line-height:1.8"><a href="tel:' . esc_attr( $clean ) . '" style="color:#ff5555">' . esc_html( $phone ) . '</a><br><a href="mailto:' . esc_attr( $email ) . '" style="color:#ff5555">' . esc_html( $email ) . '</a></p>';
}
add_shortcode( 'flair_footer_contact', 'flairltd_footer_contact_shortcode' );

function flairltd_address_shortcode() {
    $street  = get_theme_mod( 'flairltd_address_street', '24 Kemp House, 152 City Road' );
    $city    = get_theme_mod( 'flairltd_address_city', 'London' );
    $region  = get_theme_mod( 'flairltd_address_region', 'Greater London' );
    $postcode = get_theme_mod( 'flairltd_address_postcode', 'EC1V 2NX' );
    $country = get_theme_mod( 'flairltd_address_country', 'GB' );

    $lines = array_filter( [ trim( $street ), trim( $city . ', ' . $region . ' ' . $postcode ), trim( $country ) ] );
    $address = implode( "\n", $lines );

    return '<div class="ffl-address-shortcode"><address>' . wp_kses_post( nl2br( esc_html( $address ) ) ) . '</address></div>';
}
add_shortcode( 'flair_address', 'flairltd_address_shortcode' );

/**
 * Migrate old single-line address to new split fields (runs once).
 */
function flairltd_migrate_address_fields() {
    if ( get_option( 'flairltd_address_migrated' ) ) {
        return;
    }

    $old = get_theme_mod( 'flairltd_address' );
    if ( ! empty( $old ) ) {
        $parts = array_map( 'trim', explode( "\n", $old ) );
        if ( ! empty( $parts[0] ) ) {
            set_theme_mod( 'flairltd_address_street', $parts[0] );
        }
        if ( ! empty( $parts[1] ) ) {
            // Try to extract city and postcode from line like "London, EC1V 2NX"
            if ( preg_match( '/^(.+?),\s*(.+)$/', $parts[1], $m ) ) {
                set_theme_mod( 'flairltd_address_city', trim( $m[1] ) );
                set_theme_mod( 'flairltd_address_postcode', trim( $m[2] ) );
            } else {
                set_theme_mod( 'flairltd_address_city', $parts[1] );
            }
        }
    }

    update_option( 'flairltd_address_migrated', '1' );
}
add_action( 'after_setup_theme', 'flairltd_migrate_address_fields' );

function flairltd_footer_desc_shortcode() {
    $desc = get_theme_mod( 'flairltd_footer_desc', 'Leading commercial mechanical and heating contractor providing services for companies and communal dwellings in London and surrounding areas.' );
    return wp_kses_post( wpautop( $desc ) );
}
add_shortcode( 'flair_footer_desc', 'flairltd_footer_desc_shortcode' );

function flairltd_footer_certs_shortcode() {
    $out = '<div class="flairltd-footer-certs">';
    for ( $i = 1; $i <= 4; $i++ ) {
        $img_id = get_theme_mod( 'flairltd_cert_' . $i );
        $width  = get_theme_mod( 'flairltd_cert_' . $i . '_width', 80 );
        if ( $img_id ) {
            $url = wp_get_attachment_image_url( $img_id, 'medium' );
            if ( $url ) {
                $out .= '<img src="' . esc_url( $url ) . '" alt="" width="' . absint( $width ) . '" style="height:auto;">';
            }
        }
    }
    $out .= '</div>';
    return $out;
}
add_shortcode( 'flair_footer_certs', 'flairltd_footer_certs_shortcode' );

function flairltd_social_icons_shortcode() {
    $socials = [
        'facebook'  => [
            'label' => 'Facebook',
            'svg'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        ],
        'linkedin'  => [
            'label' => 'LinkedIn',
            'svg'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        ],
        'x'         => [
            'label' => 'X',
            'svg'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'svg'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        ],
        'youtube'   => [
            'label' => 'YouTube',
            'svg'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        ],
    ];

    $out = '<div class="ffl-social-icons">';
    $has_any = false;
    foreach ( $socials as $key => $data ) {
        $url = get_theme_mod( 'flairltd_social_' . $key );
        if ( ! empty( $url ) ) {
            $has_any = true;
            $out .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $data['label'] ) . '">' . $data['svg'] . '</a>';
        }
    }
    $out .= '</div>';

    return $has_any ? $out : '';
}
add_shortcode( 'flair_social_icons', 'flairltd_social_icons_shortcode' );
