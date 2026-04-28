<?php
/**
 * FlairFacilitiesLtd Theme Functions
 *
 * @package FlairFacilitiesLtd
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLAIR_LTD_VERSION', '3.8.7' );
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
    wp_enqueue_style( 'flairltd-fonts', FLAIR_LTD_URI . '/assets/fonts/inter/inter-font.css', [], FLAIR_LTD_VERSION );
    wp_enqueue_style( 'flairltd-style', FLAIR_LTD_URI . '/assets/css/style.css', [ 'flairltd-fonts' ], FLAIR_LTD_VERSION );
    wp_enqueue_script( 'flairltd-animations', FLAIR_LTD_URI . '/assets/js/animations.js', [], FLAIR_LTD_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'flairltd_enqueue' );

/**
 * Register widget areas so the classic Widgets screen is available.
 */
function flairltd_register_sidebars() {
    register_sidebar( [
        'name'          => __( 'Sidebar', 'flairfacilitiesltd' ),
        'id'            => 'flairltd-sidebar',
        'description'   => __( 'Widgets displayed in the sidebar of the Child Hero template.', 'flairfacilitiesltd' ),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ] );
}
add_action( 'widgets_init', 'flairltd_register_sidebars' );

/**
 * Shortcode to render the sidebar widget area.
 * Usage: [flair_sidebar_widgets]
 */
function flairltd_sidebar_widgets_shortcode() {
    ob_start();
    if ( is_active_sidebar( 'flairltd-sidebar' ) ) {
        dynamic_sidebar( 'flairltd-sidebar' );
    } else {
        echo '<p class="flairltd-sidebar-placeholder">' . esc_html__( 'No widgets added yet. Go to Appearance → Widgets to add content to this sidebar.', 'flairfacilitiesltd' ) . '</p>';
    }
    return ob_get_clean();
}
add_shortcode( 'flair_sidebar_widgets', 'flairltd_sidebar_widgets_shortcode' );

function flairltd_block_assets() {
    wp_enqueue_style( 'flairltd-blocks', FLAIR_LTD_URI . '/assets/css/blocks.css', [], FLAIR_LTD_VERSION );
}
add_action( 'enqueue_block_assets', 'flairltd_block_assets' );

function flairltd_block_categories( $cats ) {
    return array_merge( $cats, [ [ 'slug' => 'flairltd', 'title' => __( 'Flair Facilities', 'flairfacilitiesltd' ), 'icon' => 'building' ] ] );
}
add_filter( 'block_categories_all', 'flairltd_block_categories', 10, 1 );

function flairltd_register_blocks() {
    $blocks = [ 'expertise-card', 'service-block', 'testimonial-block', 'stats-counter', 'hero', 'about-image', 'check-list', 'faq-section', 'page-menu' ];
    foreach ( $blocks as $b ) {
        register_block_type( FLAIR_LTD_DIR . 'blocks/' . $b );
    }
}
add_action( 'init', 'flairltd_register_blocks' );

/**
 * Force full-width alignment on section blocks so their backgrounds always span the viewport.
 */
function flairltd_force_fullwidth_blocks( $block_content, $block ) {
    $fullwidth_blocks = [ 'flairltd/faq-section', 'flairltd/page-menu' ];
    if ( in_array( $block['blockName'], $fullwidth_blocks, true ) ) {
        if ( strpos( $block_content, 'alignfull' ) === false ) {
            $block_content = preg_replace(
                '/<div class="wp-block-flairltd-([^"]*)"/',
                '<div class="wp-block-flairltd-$1 alignfull"',
                $block_content,
                1
            );
        }
    }
    return $block_content;
}
add_filter( 'render_block', 'flairltd_force_fullwidth_blocks', 10, 2 );

/**
 * Force eager loading + sync decoding on cover block featured images (child-hero LCP).
 */
function flairltd_cover_eager_loading( $block_content, $block ) {
    if ( $block['blockName'] === 'core/cover' && ! empty( $block['attrs']['useFeaturedImage'] ) ) {
        $block_content = str_replace( 'loading="lazy"', 'loading="eager"', $block_content );
        $block_content = str_replace( 'decoding="async"', 'decoding="sync"', $block_content );
        $block_content = str_replace( 'class="wp-block-cover__image-background', 'class="skip-lazy wp-block-cover__image-background', $block_content );
        $block_content = str_replace( 'class="wp-post-image', 'class="skip-lazy wp-post-image', $block_content );
        // Also add data-skip-lazy for EWWW ExactDN
        $block_content = str_replace( 'class="skip-lazy wp-block-cover__image-background', 'data-skip-lazy="1" class="skip-lazy wp-block-cover__image-background', $block_content );
    }
    return $block_content;
}
add_filter( 'render_block', 'flairltd_cover_eager_loading', 10, 2 );

/**
 * Restore real image src after EWWW replaces it with a placeholder on LCP images.
 * Hooks into EWWW's output buffer filter at priority 100 (after JS WebP at 20).
 */
function flairltd_restore_hero_image_src( $buffer ) {
    if ( strpos( $buffer, 'skip-lazy' ) === false ) {
        return $buffer;
    }
    $buffer = preg_replace_callback(
        '/<img\s+([^>]*skip-lazy[^>]*)>/i',
        function( $matches ) {
            $tag = $matches[0];
            // Only fix if src is a data-URI placeholder
            if ( ! preg_match( '/src="data:[^"]*"/i', $tag ) ) {
                return $tag;
            }
            // Restore real src from data-src-img
            if ( preg_match( '/data-src-img="([^"]*)"/i', $tag, $m ) ) {
                $tag = preg_replace( '/src="data:[^"]*"/i', 'src="' . $m[1] . '"', $tag, 1 );
            }
            // Restore real srcset from data-srcset-img
            if ( preg_match( '/data-srcset-img="([^"]*)"/i', $tag, $m ) ) {
                $tag = preg_replace( '/srcset="data:[^"]*"/i', 'srcset="' . $m[1] . '"', $tag, 1 );
            }
            return $tag;
        },
        $buffer
    );
    return $buffer;
}
add_filter( 'ewww_image_optimizer_filter_page_output', 'flairltd_restore_hero_image_src', 100 );

/**
 * Preload featured image in <head> for child-hero template pages.
 */
function flairltd_preload_child_hero_image() {
    if ( ! is_singular( 'page' ) ) {
        return;
    }
    $post_id    = get_the_ID();
    $template   = get_page_template_slug( $post_id );
    $thumb_id   = get_post_thumbnail_id( $post_id );

    // Only preload for child-hero template (or pages that explicitly use featured image in cover)
    if ( $template !== 'child-hero' || ! $thumb_id ) {
        return;
    }

    $image_url = wp_get_attachment_image_url( $thumb_id, 'full' );
    $srcset    = wp_get_attachment_image_srcset( $thumb_id, 'full' );
    $sizes     = wp_get_attachment_image_sizes( $thumb_id, 'full' );

    if ( $srcset && $sizes ) {
        echo '<link rel="preload" as="image" imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="' . esc_attr( $sizes ) . '" fetchpriority="high">' . "\n";
    } elseif ( $image_url ) {
        echo '<link rel="preload" as="image" href="' . esc_url( $image_url ) . '" fetchpriority="high">' . "\n";
    }
}
add_action( 'wp_head', 'flairltd_preload_child_hero_image', 1 );

function flairltd_block_editor_assets() {
    $blocks = [ 'expertise-card', 'service-block', 'testimonial-block', 'stats-counter', 'hero', 'about-image', 'check-list', 'faq-section', 'page-menu' ];
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

    // Pass Customizer brand colours to block editor scripts
    wp_localize_script( 'flairltd-block-service-block', 'flairltdCustomizerColors', [
        'primary' => get_theme_mod( 'flairltd_primary_color', '#1e3a8a' ),
        'bright'  => get_theme_mod( 'flairltd_bright_color', '#2563eb' ),
        'accent'  => get_theme_mod( 'flairltd_accent_color', '#dc2626' ),
        'orange'  => get_theme_mod( 'flairltd_orange_color', '#ea580c' ),
        'dark'    => get_theme_mod( 'flairltd_dark_color', '#0a1628' ),
    ] );
}
add_action( 'enqueue_block_editor_assets', 'flairltd_block_editor_assets' );

/**
 * Output JSON-LD FAQPage schema in the <head> when FAQ blocks are present.
 */
function flairltd_faq_output_schema() {
    if ( empty( $GLOBALS['flairltd_faq_schema_data'] ) || ! is_array( $GLOBALS['flairltd_faq_schema_data'] ) ) {
        return;
    }

    $faq_data = $GLOBALS['flairltd_faq_schema_data'];
    $main_entity = [];

    foreach ( $faq_data as $item ) {
        $question = wp_strip_all_tags( $item['question'] );
        // Preserve line breaks from <br> and </p> tags for readable schema text
        $answer_raw = str_replace( [ '<br>', '<br/>', '<br />', '</p>' ], "\n", $item['answer'] );
        $answer = trim( wp_strip_all_tags( $answer_raw ) );

        if ( empty( $question ) || empty( $answer ) ) {
            continue;
        }

        $main_entity[] = [
            '@type'          => 'Question',
            'name'           => $question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $answer,
            ],
        ];
    }

    if ( empty( $main_entity ) ) {
        return;
    }

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $main_entity,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * Generate breadcrumb trail array.
 */
function flairltd_get_breadcrumb_items() {
    $items = [];
    $home_url   = home_url( '/' );
    $home_label = __( 'Home', 'flairfacilitiesltd' );

    $items[] = [
        'url'   => $home_url,
        'label' => $home_label,
    ];

    if ( is_front_page() ) {
        return $items;
    }

    if ( is_page() ) {
        $page = get_queried_object();
        if ( $page && $page->post_parent ) {
            $ancestors = get_post_ancestors( $page->ID );
            $ancestors = array_reverse( $ancestors );
            foreach ( $ancestors as $ancestor_id ) {
                $ancestor = get_post( $ancestor_id );
                if ( $ancestor ) {
                    $items[] = [
                        'url'   => get_permalink( $ancestor_id ),
                        'label' => get_the_title( $ancestor_id ),
                    ];
                }
            }
        }
        $items[] = [
            'url'   => '',
            'label' => get_the_title(),
        ];
    } elseif ( is_single() ) {
        $post_type = get_post_type();
        if ( $post_type === 'post' ) {
            $blog_page = get_option( 'page_for_posts' );
            if ( $blog_page ) {
                $items[] = [
                    'url'   => get_permalink( $blog_page ),
                    'label' => get_the_title( $blog_page ),
                ];
            } else {
                $items[] = [
                    'url'   => get_post_type_archive_link( 'post' ),
                    'label' => __( 'Blog', 'flairfacilitiesltd' ),
                ];
            }
        } else {
            $post_type_obj = get_post_type_object( $post_type );
            if ( $post_type_obj && $post_type_obj->has_archive ) {
                $items[] = [
                    'url'   => get_post_type_archive_link( $post_type ),
                    'label' => $post_type_obj->label,
                ];
            }
        }
        $items[] = [
            'url'   => '',
            'label' => get_the_title(),
        ];
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( is_category() && $term->parent ) {
            $parent_term = get_term( $term->parent, $term->taxonomy );
            if ( ! is_wp_error( $parent_term ) ) {
                $items[] = [
                    'url'   => get_term_link( $parent_term ),
                    'label' => $parent_term->name,
                ];
            }
        }
        $items[] = [
            'url'   => '',
            'label' => single_term_title( '', false ),
        ];
    } elseif ( is_archive() ) {
        $items[] = [
            'url'   => '',
            'label' => get_the_archive_title(),
        ];
    } elseif ( is_search() ) {
        $items[] = [
            'url'   => '',
            'label' => __( 'Search Results', 'flairfacilitiesltd' ),
        ];
    } elseif ( is_404() ) {
        $items[] = [
            'url'   => '',
            'label' => __( 'Page Not Found', 'flairfacilitiesltd' ),
        ];
    }

    return $items;
}

/**
 * Build breadcrumb HTML.
 */
function flairltd_breadcrumbs_html() {
    if ( is_front_page() ) {
        return '';
    }

    $items = flairltd_get_breadcrumb_items();
    if ( count( $items ) < 2 ) {
        return '';
    }

    $output = '<nav class="ffl-breadcrumbs" aria-label="Breadcrumb">' . "\n";
    $output .= '<ol class="ffl-breadcrumbs-list">' . "\n";

    $last_index = count( $items ) - 1;
    foreach ( $items as $i => $item ) {
        $is_current = ( $i === $last_index );
        $class = $is_current ? 'ffl-breadcrumbs-item ffl-breadcrumbs-item--current' : 'ffl-breadcrumbs-item';
        $aria  = $is_current ? ' aria-current="page"' : '';

        $output .= '<li class="' . esc_attr( $class ) . '"' . $aria . '>';
        if ( ! empty( $item['url'] ) && ! $is_current ) {
            $output .= '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
        } else {
            $output .= '<span>' . esc_html( $item['label'] ) . '</span>';
        }
        $output .= '</li>' . "\n";

        if ( ! $is_current ) {
            $output .= '<li class="ffl-breadcrumbs-separator" aria-hidden="true">';
            $output .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
            $output .= '</li>' . "\n";
        }
    }

    $output .= '</ol>' . "\n";
    $output .= '</nav>' . "\n";

    return $output;
}

/**
 * Inject breadcrumbs after the header template part.
 */
function flairltd_breadcrumbs_after_header( $block_content, $block ) {
    if ( is_front_page() ) {
        return $block_content;
    }

    if ( isset( $block['attrs']['slug'] ) && $block['attrs']['slug'] === 'header' ) {
        $breadcrumbs = flairltd_breadcrumbs_html();
        if ( ! empty( $breadcrumbs ) ) {
            return $block_content . $breadcrumbs;
        }
    }

    return $block_content;
}
add_filter( 'render_block_core/template-part', 'flairltd_breadcrumbs_after_header', 10, 2 );

/**
 * Output BreadcrumbList JSON-LD schema in <head>.
 */
function flairltd_breadcrumb_schema() {
    if ( is_front_page() ) {
        return;
    }

    $items = flairltd_get_breadcrumb_items();
    if ( count( $items ) < 2 ) {
        return;
    }

    $item_list = [];
    foreach ( $items as $i => $item ) {
        $list_item = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => html_entity_decode( wp_strip_all_tags( $item['label'] ), ENT_QUOTES, 'UTF-8' ),
        ];
        if ( ! empty( $item['url'] ) ) {
            $list_item['item'] = $item['url'];
        }
        $item_list[] = $list_item;
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $item_list,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'flairltd_breadcrumb_schema', 5 );

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


/**
 * Add body class when last block is a full-width custom section.
 */
function flairltd_last_block_body_class( $classes ) {
    if ( ! is_singular() ) {
        return $classes;
    }
    $post_id = get_the_ID();
    $content = get_post_field( 'post_content', $post_id );
    if ( empty( $content ) ) {
        return $classes;
    }
    $blocks = parse_blocks( $content );
    $blocks = array_filter( $blocks, function( $b ) {
        return ! empty( $b['blockName'] );
    } );
    $blocks = array_values( $blocks );
    if ( empty( $blocks ) ) {
        return $classes;
    }
    $last = end( $blocks );
    $fullwidth_blocks = [ 'flairltd/faq-section', 'flairltd/page-menu' ];
    if ( in_array( $last['blockName'], $fullwidth_blocks, true ) ) {
        $classes[] = 'has-fullwidth-last-block';
    }
    return $classes;
}
add_filter( 'body_class', 'flairltd_last_block_body_class' );
