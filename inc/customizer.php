<?php
/**
 * @package FlairFacilitiesLtd
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function flairltd_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'flairltd_brand', [ 'title' => __( 'Brand Settings', 'flairfacilitiesltd' ), 'priority' => 20 ] );

    $wp_customize->add_setting( 'flairltd_primary_color', [ 'default' => '#1e3a8a', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_primary_color', [ 'label' => __( 'Primary Blue', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    $wp_customize->add_setting( 'flairltd_accent_color', [ 'default' => '#dc2626', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_accent_color', [ 'label' => __( 'Accent Red', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    $wp_customize->add_setting( 'flairltd_dark_color', [ 'default' => '#0a1628', 'sanitize_callback' => 'sanitize_hex_color' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flairltd_dark_color', [ 'label' => __( 'Dark Navy', 'flairfacilitiesltd' ), 'section' => 'flairltd_brand' ] ) );

    // Logo padding
    $wp_customize->add_section( 'flairltd_logo', [ 'title' => __( 'Logo Settings', 'flairfacilitiesltd' ), 'priority' => 25 ] );
    $wp_customize->add_setting( 'flairltd_logo_padding_top', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_logo_padding_top', [ 'label' => __( 'Logo Padding Top (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ] );
    $wp_customize->add_setting( 'flairltd_logo_padding_bottom', [ 'default' => '0', 'sanitize_callback' => 'absint' ] );
    $wp_customize->add_control( 'flairltd_logo_padding_bottom', [ 'label' => __( 'Logo Padding Bottom (px)', 'flairfacilitiesltd' ), 'section' => 'flairltd_logo', 'type' => 'number', 'input_attrs' => [ 'min' => 0, 'max' => 100 ] ] );

    $wp_customize->add_section( 'flairltd_contact', [ 'title' => __( 'Contact Info', 'flairfacilitiesltd' ), 'priority' => 30 ] );
    $wp_customize->add_setting( 'flairltd_phone', [ 'default' => '020 7998 9005', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'flairltd_phone', [ 'label' => __( 'Phone', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'text' ] );
    $wp_customize->add_setting( 'flairltd_email', [ 'default' => 'info@flairfacilities.co.uk', 'sanitize_callback' => 'sanitize_email' ] );
    $wp_customize->add_control( 'flairltd_email', [ 'label' => __( 'Email', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'email' ] );
    $wp_customize->add_setting( 'flairltd_address', [ 'default' => "24 Kemp House, 152 City Road\nLondon, EC1V 2NX", 'sanitize_callback' => 'sanitize_textarea_field' ] );
    $wp_customize->add_control( 'flairltd_address', [ 'label' => __( 'Address', 'flairfacilitiesltd' ), 'section' => 'flairltd_contact', 'type' => 'textarea' ] );
}
add_action( 'customize_register', 'flairltd_customize_register' );

function flairltd_customizer_css() {
    $primary = get_theme_mod( 'flairltd_primary_color', '#1e3a8a' );
    $accent  = get_theme_mod( 'flairltd_accent_color', '#dc2626' );
    $dark    = get_theme_mod( 'flairltd_dark_color', '#0a1628' );
    $logo_pt = get_theme_mod( 'flairltd_logo_padding_top', '0' );
    $logo_pb = get_theme_mod( 'flairltd_logo_padding_bottom', '0' );
    ?>
    <style type="text/css">
        :root {
            --ffl-primary: <?php echo esc_html( $primary ); ?>;
            --ffl-accent: <?php echo esc_html( $accent ); ?>;
            --ffl-dark: <?php echo esc_html( $dark ); ?>;
        }
        .ffl-site-logo a,
        .ffl-site-logo img {
            padding-top: <?php echo absint( $logo_pt ); ?>px;
            padding-bottom: <?php echo absint( $logo_pb ); ?>px;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'flairltd_customizer_css', 100 );
