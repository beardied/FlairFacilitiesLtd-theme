<?php
/**
 * FlairFacilitiesLtd Theme Functions
 *
 * @package FlairFacilitiesLtd
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLAIR_LTD_VERSION', '3.2.1' );
define( 'FLAIR_LTD_DIR', get_template_directory() . '/' );
define( 'FLAIR_LTD_URI', get_template_directory_uri() );

function flairltd_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'custom-logo', [
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'block-templates' );

    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'flairfacilitiesltd' ),
        'footer'  => __( 'Footer Menu', 'flairfacilitiesltd' ),
    ] );

    add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'flairltd_setup' );

function flairltd_enqueue() {
    wp_enqueue_style( 'flairltd-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', [], null );
    wp_enqueue_style( 'flairltd-style', FLAIR_LTD_URI . '/assets/css/style.css', [], FLAIR_LTD_VERSION );
    wp_enqueue_script( 'flairltd-animations', FLAIR_LTD_URI . '/assets/js/animations.js', [], FLAIR_LTD_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'flairltd_enqueue' );

function flairltd_block_assets() {
    wp_enqueue_style( 'flairltd-blocks', FLAIR_LTD_URI . '/assets/css/blocks.css', [], FLAIR_LTD_VERSION );
}
add_action( 'enqueue_block_assets', 'flairltd_block_assets' );

function flairltd_block_categories( $cats ) {
    return array_merge( $cats, [ [ 'slug' => 'flairltd', 'title' => __( 'Flair Facilities', 'flairfacilitiesltd' ), 'icon' => 'building' ] ] );
}
add_filter( 'block_categories_all', 'flairltd_block_categories', 10, 1 );

function flairltd_register_blocks() {
    $blocks = [ 'expertise-card', 'service-block', 'testimonial-block', 'stats-counter', 'hero', 'about-image', 'check-list' ];
    foreach ( $blocks as $b ) {
        register_block_type( FLAIR_LTD_DIR . 'blocks/' . $b );
    }
}
add_action( 'init', 'flairltd_register_blocks' );

function flairltd_register_patterns() {
    register_block_pattern_category( 'flairltd', [ 'label' => __( 'Flair Facilities', 'flairfacilitiesltd' ) ] );
}
add_action( 'init', 'flairltd_register_patterns' );

require_once FLAIR_LTD_DIR . 'inc/customizer.php';

function flairltd_body_class( $classes ) {
    if ( is_front_page() ) $classes[] = 'is-front-page';
    return $classes;
}
add_filter( 'body_class', 'flairltd_body_class' );
