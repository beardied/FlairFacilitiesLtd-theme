<?php
/**
 * FlairFacilitiesLtd Theme Functions
 *
 * @package FlairFacilitiesLtd
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLAIR_LTD_VERSION', '3.5.3' );
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

function flairltd_block_editor_assets() {
    $blocks = [ 'expertise-card', 'service-block', 'testimonial-block', 'stats-counter', 'hero', 'about-image', 'check-list' ];
    $deps = [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ];

    foreach ( $blocks as $b ) {
        $handle = 'flairltd-block-' . $b;
        $script_path = FLAIR_LTD_DIR . 'blocks/' . $b . '/index.js';
        $script_url  = FLAIR_LTD_URI . '/blocks/' . $b . '/index.js';

        if ( file_exists( $script_path ) ) {
            wp_register_script( $handle, $script_url, $deps, FLAIR_LTD_VERSION, true );
            wp_enqueue_script( $handle );
        }
    }
}
add_action( 'enqueue_block_editor_assets', 'flairltd_block_editor_assets' );

function flairltd_register_patterns() {
    register_block_pattern_category( 'flairltd', [ 'label' => __( 'Flair Facilities', 'flairfacilitiesltd' ) ] );
}
add_action( 'init', 'flairltd_register_patterns' );

require_once FLAIR_LTD_DIR . 'inc/customizer.php';
require_once FLAIR_LTD_DIR . 'inc/google-reviews.php';

function flairltd_body_class( $classes ) {
    if ( is_front_page() ) $classes[] = 'is-front-page';
    return $classes;
}
add_filter( 'body_class', 'flairltd_body_class' );

// Disable comments and feeds
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );
add_action( 'admin_menu', function() {
    remove_menu_page( 'edit-comments.php' );
} );
add_action( 'admin_init', function() {
    global $pagenow;
    if ( $pagenow === 'comment.php' || $pagenow === 'edit-comments.php' ) {
        wp_redirect( admin_url() );
        exit;
    }
} );
add_action( 'init', function() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}, 100 );
add_action( 'wp', function() {
    wp_deregister_script( 'comment-reply' );
} );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );

function flairltd_sync_menu_to_navigation( $menu_id ) {
    $locations = get_nav_menu_locations();
    $map = [
        'primary' => 'Primary Navigation',
        'footer'  => 'Footer Navigation',
    ];
    foreach ( $map as $location => $title ) {
        if ( ! empty( $locations[ $location ] ) && $locations[ $location ] == $menu_id ) {
            $items = wp_get_nav_menu_items( $menu_id );
            if ( ! $items ) continue;
            $blocks = [];
            foreach ( $items as $item ) {
                $blocks[] = '<!-- wp:navigation-link {"label":"' . esc_js( $item->title ) . '","url":"' . esc_url( $item->url ) . '","kind":"custom","isTopLevelLink":true} /-->';
            }
            $content = implode( "\n", $blocks );
            $existing = get_posts( [
                'post_type'      => 'wp_navigation',
                'name'           => sanitize_title( $title ),
                'posts_per_page' => 1,
            ] );
            if ( ! empty( $existing ) ) {
                wp_update_post( [ 'ID' => $existing[0]->ID, 'post_content' => $content, 'post_title' => $title ] );
            } else {
                wp_insert_post( [
                    'post_type'    => 'wp_navigation',
                    'post_name'    => sanitize_title( $title ),
                    'post_title'   => $title,
                    'post_content' => $content,
                    'post_status'  => 'publish',
                ] );
            }
        }
    }
}
add_action( 'wp_update_nav_menu', 'flairltd_sync_menu_to_navigation' );
