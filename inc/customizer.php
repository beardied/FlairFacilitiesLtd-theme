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
    $wp_customize->add_setting( 'flairltd_address', [ 'default' => "24 Kemp House, 152 City Road\nLondon, EC1V 2NX", 'sanitize_callback' => 'sanitize_textarea_field' ] );
    $wp_customize->add_control( 'flairltd_address', [ 'label' => __( 'Address', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'textarea' ] );
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

function flairltd_address_shortcode() {
    $address = get_theme_mod( 'flairltd_address', "24 Kemp House, 152 City Road\nLondon, EC1V 2NX" );
    return '<div class="ffl-address-shortcode"><address>' . wp_kses_post( nl2br( $address ) ) . '</address></div>';
}
add_shortcode( 'flair_address', 'flairltd_address_shortcode' );

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
