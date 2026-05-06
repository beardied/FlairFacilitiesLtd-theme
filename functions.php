<?php
/**
 * FlairFacilitiesLtd Theme Functions
 *
 * @package FlairFacilitiesLtd
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLAIR_LTD_VERSION', '3.14.7' );
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
    wp_enqueue_style( 'flairltd-style', FLAIR_LTD_URI . '/assets/css/style.css', [], FLAIR_LTD_VERSION );
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

function flairltd_block_editor_styles() {
    wp_enqueue_style( 'flairltd-blocks', FLAIR_LTD_URI . '/assets/css/blocks.css', [], FLAIR_LTD_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'flairltd_block_editor_styles' );

function flairltd_block_categories( $cats ) {
    return array_merge( $cats, [ [ 'slug' => 'flairltd', 'title' => __( 'Flair Facilities', 'flairfacilitiesltd' ), 'icon' => 'building' ] ] );
}
add_filter( 'block_categories_all', 'flairltd_block_categories', 10, 1 );

/**
 * Preload critical font files + preconnect to third-party origins.
 */
function flairltd_resource_hints() {
    $font_base = FLAIR_LTD_URI . '/assets/fonts/inter/';
    echo '<link rel="preload" as="font" href="' . esc_url( $font_base . 'inter-latin.woff2' ) . '" type="font/woff2" crossorigin="anonymous">' . "\n";
    echo '<link rel="preconnect" href="https://static.cloudflareinsights.com">' . "\n";
}
add_action( 'wp_head', 'flairltd_resource_hints', 1 );

/**
 * Add fetchpriority="high" to the main stylesheet.
 */
function flairltd_style_fetchpriority( $html, $handle ) {
    if ( 'flairltd-style' === $handle && false === strpos( $html, 'fetchpriority' ) ) {
        $html = str_replace( "media='all'", "media='all' fetchpriority='high'", $html );
    }
    return $html;
}
add_filter( 'style_loader_tag', 'flairltd_style_fetchpriority', 10, 2 );

/**
 * Disable WordPress emoji script — saves an inline script + external JS request.
 */
function flairltd_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'flairltd_disable_emojis' );

function flairltd_register_blocks() {
    $blocks = [ 'expertise-card', 'service-block', 'testimonial-block', 'stats-counter', 'hero', 'about-image', 'check-list', 'faq-section', 'page-menu', 'contact-form', 'logo-marquee', 'cta-section' ];
    foreach ( $blocks as $b ) {
        register_block_type( FLAIR_LTD_DIR . 'blocks/' . $b );
    }
}
add_action( 'init', 'flairltd_register_blocks' );

/**
 * Force full-width alignment on section blocks so their backgrounds always span the viewport.
 */
function flairltd_force_fullwidth_blocks( $block_content, $block ) {
    $fullwidth_blocks = [ 'flairltd/faq-section', 'flairltd/page-menu', 'flairltd/logo-marquee', 'flairltd/cta-section' ];
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
 * Wrap page H1 titles in a full-width gradient bar on standard pages.
 * Skipped for child-hero template and pages containing a hero block.
 */
function flairltd_page_title_gradient_bar( $block_content, $block ) {
    if ( $block['blockName'] !== 'core/post-title' ) {
        return $block_content;
    }

    if ( ! is_page() || is_front_page() ) {
        return $block_content;
    }

    $template = get_page_template_slug();
    if ( $template === 'child-hero' ) {
        return $block_content;
    }

    $page = get_post();
    if ( $page && ( has_block( 'flairltd/hero', $page ) || has_block( 'core/cover', $page ) ) ) {
        return $block_content;
    }

    // Only wrap H1 titles
    $level = $block['attrs']['level'] ?? 1;
    if ( $level !== 1 ) {
        return $block_content;
    }

    return '<div class="ffl-page-title-bar alignfull"><div class="ffl-page-title-inner">' . $block_content . '</div></div>';
}
add_filter( 'render_block', 'flairltd_page_title_gradient_bar', 10, 2 );

/**
 * Extract the first H1 from single post content and prepend a full-width title bar.
 */
function flairltd_post_first_h1_title_bar( $block_content, $block ) {
    if ( $block['blockName'] !== 'core/post-content' ) {
        return $block_content;
    }

    if ( ! is_singular( 'post' ) ) {
        return $block_content;
    }

    // Find the first <h1> tag, extract it for the title bar, and remove from content.
    if ( preg_match( '/<h1[^>]*>(.*?)<\/h1>/is', $block_content, $matches, PREG_OFFSET_CAPTURE ) ) {
        $h1_html = $matches[0][0];
        $h1_pos  = $matches[0][1];
        $h1_text = $matches[1][0];

        // Remove the H1 from the content.
        $block_content = substr_replace( $block_content, '', $h1_pos, strlen( $h1_html ) );

        // Prepend title bar before the content block.
        $title_bar = '<div class="ffl-page-title-bar alignfull"><div class="ffl-page-title-inner"><h1 class="wp-block-post-title">' . $h1_text . '</h1></div></div>';
        $block_content = $title_bar . $block_content;
    }

    return $block_content;
}
add_filter( 'render_block', 'flairltd_post_first_h1_title_bar', 10, 2 );

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
    $blocks = [ 'expertise-card', 'service-block', 'testimonial-block', 'stats-counter', 'hero', 'about-image', 'check-list', 'faq-section', 'page-menu', 'contact-form', 'logo-marquee', 'cta-section' ];
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
        'email'   => get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' ),
        'phone'   => get_theme_mod( 'flairltd_phone', '020 7998 9005' ),
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

    // Add gradient title bar class on standard pages (not child-hero, no hero/cover block)
    if ( is_page() && ! is_front_page() ) {
        $template = get_page_template_slug();
        if ( $template !== 'child-hero' ) {
            $page = get_post();
            if ( $page && ! has_block( 'flairltd/hero', $page ) && ! has_block( 'core/cover', $page ) ) {
                $classes[] = 'has-page-title-bar';
            }
        }
    }

    // Add title bar class on single posts that have an H1 in their content
    if ( is_singular( 'post' ) ) {
        $post = get_post();
        if ( $post && preg_match( '/<h1[\s>]/i', $post->post_content ) ) {
            $classes[] = 'has-page-title-bar';
        }
    }

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


/**
 * Contact Form — AJAX submission handler
 */
function flairltd_contact_form_submit() {
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['flairltd_contact_nonce'] ?? '', 'flairltd_contact_submit' ) ) {
        wp_send_json_error( [ 'message' => 'Security check failed. Please refresh the page and try again.' ] );
    }

    // Honeypot check
    if ( ! empty( $_POST['website'] ) ) {
        wp_send_json_error( [ 'message' => 'Spam detected.' ] );
    }

    // Rate limiting
    $ip    = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key   = 'flairltd_contact_rate_' . md5( $ip );
    $count = get_transient( $key );
    if ( $count && $count >= 5 ) {
        wp_send_json_error( [ 'message' => 'Too many submissions. Please try again in 5 minutes.' ] );
    }

    // Field validation
    $name      = sanitize_text_field( $_POST['name'] ?? '' );
    $email     = sanitize_email( $_POST['email'] ?? '' );
    $phone     = sanitize_text_field( $_POST['phone'] ?? '' );
    $subject   = sanitize_text_field( $_POST['subject'] ?? '' );
    $message   = sanitize_textarea_field( $_POST['message'] ?? '' );
    $recipient = sanitize_email( $_POST['recipient_email'] ?? get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' ) );

    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( [ 'message' => 'Please fill in all required fields.' ] );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'Please enter a valid email address.' ] );
    }

    // File upload handling
    $attachments   = [];
    $max_size      = 5 * 1024 * 1024; // 5MB
    $allowed_exts  = [ 'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx' ];

    if ( ! empty( $_FILES['images'] ) && is_array( $_FILES['images']['name'] ) ) {
        $file_count = count( $_FILES['images']['name'] );
        if ( $file_count > 10 ) {
            wp_send_json_error( [ 'message' => 'Maximum 10 images allowed.' ] );
        }

        for ( $i = 0; $i < $file_count; $i++ ) {
            if ( $_FILES['images']['error'][ $i ] !== UPLOAD_ERR_OK ) {
                continue;
            }

            $tmp_name = $_FILES['images']['tmp_name'][ $i ];
            $filename = $_FILES['images']['name'][ $i ];
            $filesize = $_FILES['images']['size'][ $i ];

            if ( $filesize > $max_size ) {
                wp_send_json_error( [ 'message' => 'One or more images exceed the 5MB limit.' ] );
            }

            // Validate extension (primary check — wp_check_filetype_and_ext can be too strict on temp files)
            $file_info = wp_check_filetype( $filename, [
                'jpg|jpeg' => 'image/jpeg',
                'png'      => 'image/png',
                'gif'      => 'image/gif',
                'webp'     => 'image/webp',
                'pdf'      => 'application/pdf',
                'doc'      => 'application/msword',
                'docx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ] );
            if ( empty( $file_info['ext'] ) || ! in_array( strtolower( $file_info['ext'] ), $allowed_exts, true ) ) {
                wp_send_json_error( [ 'message' => 'Invalid file type. Only images (JPG, PNG, GIF, WebP) and documents (PDF, DOC, DOCX) are allowed.' ] );
            }

            // Secondary MIME check (lenient — accept application/octet-stream which temp files sometimes report)
            $mime_type = '';
            if ( function_exists( 'mime_content_type' ) ) {
                $mime_type = mime_content_type( $tmp_name );
            } elseif ( function_exists( 'finfo_file' ) ) {
                $finfo = finfo_open( FILEINFO_MIME_TYPE );
                $mime_type = finfo_file( $finfo, $tmp_name );
                finfo_close( $finfo );
            }
            $valid_mimes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/octet-stream', 'application/zip', // DOCX is technically a zip
            ];
            if ( $mime_type && ! in_array( $mime_type, $valid_mimes, true ) ) {
                wp_send_json_error( [ 'message' => 'Invalid file type. Only images (JPG, PNG, GIF, WebP) and documents (PDF, DOC, DOCX) are allowed.' ] );
            }

            $temp_dir  = get_temp_dir();
            $temp_file = $temp_dir . wp_unique_filename( $temp_dir, sanitize_file_name( $filename ) );

            if ( move_uploaded_file( $tmp_name, $temp_file ) ) {
                $attachments[] = $temp_file;
            }
        }
    }

    // Email headers
    $from_email = get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' );
    $from_name  = get_bloginfo( 'name' );
    $headers    = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    // Staff email
    $staff_subject = 'New Contact Form Submission' . ( $subject ? ': ' . $subject : '' );
    $staff_body    = flairltd_contact_email_template_staff( $name, $email, $phone, $subject, $message );
    $staff_sent    = wp_mail( $recipient, $staff_subject, $staff_body, $headers, $attachments );

    // Customer confirmation email
    $customer_subject = 'Thank you for contacting ' . $from_name;
    $customer_body    = flairltd_contact_email_template_customer( $name, $subject, $message );
    $customer_headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
    ];
    wp_mail( $email, $customer_subject, $customer_body, $customer_headers );

    // Clean up temp files
    foreach ( $attachments as $file ) {
        if ( file_exists( $file ) ) {
            @unlink( $file );
        }
    }

    // Increment rate limit counter
    set_transient( $key, ( $count ?: 0 ) + 1, 300 ); // 5 minutes

    if ( $staff_sent ) {
        wp_send_json_success( [ 'message' => 'Thank you! Your message has been sent and we will be in touch shortly.' ] );
    } else {
        wp_send_json_error( [ 'message' => 'Failed to send email. Please try again or contact us directly.' ] );
    }
}
add_action( 'wp_ajax_flairltd_contact_submit', 'flairltd_contact_form_submit' );
add_action( 'wp_ajax_nopriv_flairltd_contact_submit', 'flairltd_contact_form_submit' );

/**
 * Get the Customizer logo as HTML for email templates.
 */
function flairltd_email_logo_html() {
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( ! $logo_id ) {
        return '';
    }

    $logo_url = wp_get_attachment_image_url( $logo_id, 'medium' );
    if ( ! $logo_url ) {
        return '';
    }

    $alt = esc_attr( get_bloginfo( 'name' ) );
    return '<div class="logo-bar"><img src="' . esc_url( $logo_url ) . '" alt="' . $alt . '" width="180"></div>';
}

/**
 * Staff email template.
 */
function flairltd_contact_email_template_staff( $name, $email, $phone, $subject, $message ) {
    $site_name = esc_html( get_bloginfo( 'name' ) );
    $date      = esc_html( wp_date( 'j F Y, g:i a' ) );
    $name_e    = esc_html( $name );
    $email_e   = esc_html( $email );
    $phone_e   = esc_html( $phone );
    $subject_e = esc_html( $subject );
    $message_e = nl2br( esc_html( $message ) );

    $logo_html = flairltd_email_logo_html();

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{font-family:Inter,system-ui,sans-serif;background:#f1f5f9;margin:0;padding:20px;color:#0f172a}
.container{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1)}
.logo-bar{padding:24px 24px 0;text-align:center;background:#fff}
.logo-bar img{max-width:180px;height:auto}
.header{background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);padding:24px;text-align:center}
.header h1{color:#fff;margin:0;font-size:22px;font-weight:700}
.content{padding:32px 24px}
.field{margin-bottom:20px}
.field-label{font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:6px;font-weight:600}
.field-value{font-size:15px;color:#0f172a;line-height:1.6}
.message-box{background:#f8fafc;border-left:4px solid #2563eb;padding:16px;border-radius:0 8px 8px 0;margin-top:8px}
.footer{padding:20px 24px;background:#f8fafc;text-align:center;font-size:13px;color:#64748b;border-top:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="container">
    {$logo_html}
    <div class="header">
        <h1>New Contact Form Submission</h1>
    </div>
    <div class="content">
        <div class="field">
            <div class="field-label">Submitted</div>
            <div class="field-value">{$date}</div>
        </div>
        <div class="field">
            <div class="field-label">Name</div>
            <div class="field-value">{$name_e}</div>
        </div>
        <div class="field">
            <div class="field-label">Email</div>
            <div class="field-value"><a href="mailto:{$email_e}">{$email_e}</a></div>
        </div>
        <div class="field">
            <div class="field-label">Phone</div>
            <div class="field-value">{$phone_e}</div>
        </div>
        <div class="field">
            <div class="field-label">Subject</div>
            <div class="field-value">{$subject_e}</div>
        </div>
        <div class="field">
            <div class="field-label">Message</div>
            <div class="message-box">{$message_e}</div>
        </div>
    </div>
    <div class="footer">
        Sent from {$site_name} contact form
    </div>
</div>
</body>
</html>
HTML;
}

/**
 * Customer confirmation email template.
 */
function flairltd_contact_email_template_customer( $name, $subject, $message ) {
    $site_name = esc_html( get_bloginfo( 'name' ) );
    $name_e    = esc_html( $name );
    $subject_e = esc_html( $subject );
    $message_e = nl2br( esc_html( $message ) );

    $logo_html = flairltd_email_logo_html();

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{font-family:Inter,system-ui,sans-serif;background:#f1f5f9;margin:0;padding:20px;color:#0f172a}
.container{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1)}
.logo-bar{padding:24px 24px 0;text-align:center;background:#fff}
.logo-bar img{max-width:180px;height:auto}
.header{background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);padding:24px;text-align:center}
.header h1{color:#fff;margin:0;font-size:22px;font-weight:700}
.content{padding:32px 24px}
.intro{font-size:16px;line-height:1.7;margin-bottom:24px;color:#334155}
.field{margin-bottom:20px}
.field-label{font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;margin-bottom:6px;font-weight:600}
.field-value{font-size:15px;color:#0f172a;line-height:1.6}
.message-box{background:#f8fafc;border-left:4px solid #2563eb;padding:16px;border-radius:0 8px 8px 0;margin-top:8px}
.cta{text-align:center;margin-top:28px}
.cta a{display:inline-block;padding:14px 28px;background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px}
.footer{padding:20px 24px;background:#f8fafc;text-align:center;font-size:13px;color:#64748b;border-top:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="container">
    {$logo_html}
    <div class="header">
        <h1>Thank You for Contacting Us</h1>
    </div>
    <div class="content">
        <p class="intro">Hi {$name_e},</p>
        <p class="intro">Thank you for reaching out to {$site_name}. We have received your message and a member of our team will get back to you as soon as possible.</p>
        <div class="field">
            <div class="field-label">Your Subject</div>
            <div class="field-value">{$subject_e}</div>
        </div>
        <div class="field">
            <div class="field-label">Your Message</div>
            <div class="message-box">{$message_e}</div>
        </div>
        <div class="cta">
            <a href="https://dev.flairfacilities.co.uk">Visit Our Website</a>
        </div>
    </div>
    <div class="footer">
        This is an automated confirmation. Please do not reply to this email.<br>
        &copy; {$site_name}
    </div>
</div>
</body>
</html>
HTML;
}


/**
 * Custom XML Sitemap — plain XML for search engines only
 * Outputs at /sitemap.xml
 */

// Disable the broken built-in WordPress sitemap
add_filter( 'wp_sitemaps_enabled', '__return_false' );

// Add rewrite rules
function flairltd_seo_rewrite_rules() {
    add_rewrite_rule( '^sitemap\.xml$', 'index.php?flairltd_sitemap=1', 'top' );
    add_rewrite_rule( '^robots\.txt$', 'index.php?flairltd_robots=1', 'top' );
    add_rewrite_rule( '^llms\.txt$', 'index.php?flairltd_llms=1', 'top' );
}
add_action( 'init', 'flairltd_seo_rewrite_rules', 5 );

// Register query vars
function flairltd_seo_query_vars( $vars ) {
    $vars[] = 'flairltd_sitemap';
    $vars[] = 'flairltd_robots';
    $vars[] = 'flairltd_llms';
    return $vars;
}
add_filter( 'query_vars', 'flairltd_seo_query_vars' );

// Flush rewrite rules on theme switch
function flairltd_seo_flush_rules() {
    flairltd_seo_rewrite_rules();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'flairltd_seo_flush_rules' );

// ── Plain XML Sitemap ────────────────────────────────────────────
function flairltd_sitemap_output() {
    if ( ! get_query_var( 'flairltd_sitemap' ) ) {
        return;
    }

    while ( ob_get_level() ) {
        ob_end_clean();
    }

    header( 'Content-Type: application/xml; charset=UTF-8' );
    header( 'X-Robots-Tag: noindex, follow', true );

    $urls = flairltd_get_sitemap_urls();

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ( $urls as $url ) {
        echo "  <url>\n";
        echo "    <loc>" . esc_url( $url['loc'] ) . "</loc>\n";
        if ( ! empty( $url['lastmod'] ) ) {
            echo "    <lastmod>" . esc_html( $url['lastmod'] ) . "</lastmod>\n";
        }
        if ( ! empty( $url['changefreq'] ) ) {
            echo "    <changefreq>" . esc_html( $url['changefreq'] ) . "</changefreq>\n";
        }
        if ( ! empty( $url['priority'] ) ) {
            echo "    <priority>" . esc_html( $url['priority'] ) . "</priority>\n";
        }
        echo "  </url>\n";
    }

    echo '</urlset>';
    exit;
}
add_action( 'template_redirect', 'flairltd_sitemap_output', 1 );

// ── robots.txt ───────────────────────────────────────────────────
function flairltd_robots_txt_output() {
    if ( ! get_query_var( 'flairltd_robots' ) ) {
        return;
    }

    while ( ob_get_level() ) {
        ob_end_clean();
    }

    header( 'Content-Type: text/plain; charset=UTF-8' );

    $sitemap_url = home_url( '/sitemap.xml' );
    $host        = wp_parse_url( home_url(), PHP_URL_HOST );

    echo "User-agent: *\n";
    echo "Disallow: /wp-admin/\n";
    echo "Disallow: /wp-includes/\n";
    echo "Disallow: /wp-content/plugins/\n";
    echo "Disallow: /wp-login.php\n";
    echo "Disallow: /wp-register.php\n";
    echo "Disallow: /feed/\n";
    echo "Disallow: /comments/feed/\n";
    echo "Disallow: /trackback/\n";
    echo "Disallow: /author/\n";
    echo "Disallow: /cdn-cgi/\n";
    echo "Disallow: /*?*\n";
    echo "Allow: /wp-admin/admin-ajax.php\n";
    echo "Allow: /wp-content/uploads/\n";
    echo "Allow: /wp-content/themes/\n";
    echo "\n";
    echo "Host: {$host}\n";
    echo "Sitemap: {$sitemap_url}\n";
    exit;
}
add_action( 'template_redirect', 'flairltd_robots_txt_output', 1 );

// ── llms.txt ─────────────────────────────────────────────────────
function flairltd_llms_txt_output() {
    if ( ! get_query_var( 'flairltd_llms' ) ) {
        return;
    }

    while ( ob_get_level() ) {
        ob_end_clean();
    }

    header( 'Content-Type: text/plain; charset=UTF-8' );

    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );
    $email     = get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' );

    // Fetch all published pages with full objects so we have parent info.
    $all_pages = get_posts( [
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ] );

    // Determine the most recent modification date across all pages.
    $last_modified = '';
    foreach ( $all_pages as $p ) {
        $mod = get_the_modified_date( 'Y-m-d H:i:s', $p->ID );
        if ( $mod && ( ! $last_modified || $mod > $last_modified ) ) {
            $last_modified = $mod;
        }
    }
    if ( ! $last_modified ) {
        $last_modified = gmdate( 'Y-m-d H:i:s' );
    }

    // Build hierarchy.
    $top_level = [];
    $children  = [];
    foreach ( $all_pages as $p ) {
        if ( $p->post_parent == 0 ) {
            $top_level[] = $p;
        } else {
            $children[ $p->post_parent ][] = $p;
        }
    }

    // Sort children alphabetically by title.
    foreach ( $children as $parent_id => &$child_list ) {
        usort( $child_list, function( $a, $b ) {
            return strcasecmp( $a->post_title, $b->post_title );
        } );
    }
    unset( $child_list );

    echo "# {$site_name}\n";
    echo "# llms.txt — AI training & crawling policy\n";
    echo "# Content current as of: {$last_modified} GMT\n";
    echo "\n";

    // Policy section.
    echo "# Policy\n";
    echo "Allow: {$site_url}\n";
    echo "Disallow: /wp-admin/\n";
    echo "Disallow: /wp-login.php\n";
    echo "Disallow: /feed/\n";
    echo "\n";

    // Key pages — homepage first, then parent pages alphabetically
    // with their child pages immediately after.
    echo "# Key pages\n";
    echo "- {$site_url}\n";

    foreach ( $top_level as $page ) {
        $page_url = get_permalink( $page->ID );
        if ( ! $page_url || trailingslashit( $page_url ) === trailingslashit( $site_url ) ) {
            continue; // Skip homepage duplicate.
        }
        echo "- {$page_url}\n";

        if ( ! empty( $children[ $page->ID ] ) ) {
            foreach ( $children[ $page->ID ] as $child ) {
                $child_url = get_permalink( $child->ID );
                if ( $child_url ) {
                    echo "- {$child_url}\n";
                }
            }
        }
    }

    echo "\n";
    echo "# Contact\n";
    echo "Email: {$email}\n";
    echo "Website: {$site_url}\n";
    exit;
}
add_action( 'template_redirect', 'flairltd_llms_txt_output', 1 );

/**
 * Collect all URLs for the sitemap.
 */
function flairltd_get_sitemap_urls() {
    $urls = [];

    // Homepage
    $front_page_id = (int) get_option( 'page_on_front' );
    if ( $front_page_id ) {
        $home_lastmod = get_the_modified_date( 'Y-m-d\TH:i:s+00:00', $front_page_id );
        if ( ! $home_lastmod ) {
            $home_lastmod = get_the_date( 'Y-m-d\TH:i:s+00:00', $front_page_id );
        }
    } else {
        // Blog homepage — use the most recently modified post/page.
        $latest = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ] );
        $home_lastmod = ! empty( $latest ) ? get_the_modified_date( 'Y-m-d\TH:i:s+00:00', $latest[0] ) : gmdate( 'Y-m-d\TH:i:s+00:00' );
    }

    $urls[] = [
        'loc'        => home_url( '/' ),
        'lastmod'    => $home_lastmod,
        'changefreq' => 'daily',
        'priority'   => '1.0',
    ];

    // Published pages
    $pages = get_posts( [
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ] );

    foreach ( $pages as $page_id ) {
        $page_url = get_permalink( $page_id );
        if ( ! $page_url || trailingslashit( $page_url ) === trailingslashit( home_url( '/' ) ) ) {
            continue; // Skip homepage duplicate
        }

        $lastmod = get_the_modified_date( 'Y-m-d\TH:i:s+00:00', $page_id );
        if ( ! $lastmod ) {
            $lastmod = get_the_date( 'Y-m-d\TH:i:s+00:00', $page_id );
        }

        $urls[] = [
            'loc'        => $page_url,
            'lastmod'    => $lastmod,
            'changefreq' => 'weekly',
            'priority'   => '0.8',
        ];
    }

    // Published posts (if any)
    $posts = get_posts( [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ] );

    foreach ( $posts as $post_id ) {
        $post_url = get_permalink( $post_id );
        if ( ! $post_url ) {
            continue;
        }

        $lastmod = get_the_modified_date( 'Y-m-d\TH:i:s+00:00', $post_id );
        if ( ! $lastmod ) {
            $lastmod = get_the_date( 'Y-m-d\TH:i:s+00:00', $post_id );
        }

        $urls[] = [
            'loc'        => $post_url,
            'lastmod'    => $lastmod,
            'changefreq' => 'monthly',
            'priority'   => '0.6',
        ];
    }

    // Allow other functions to add URLs
    return apply_filters( 'flairltd_sitemap_urls', $urls );
}

/**
 * Handle redirects via external map file
 */
add_action('template_redirect', function() {
    // 1. Get the requested path and strip query strings (?abc=123)
    $raw_uri = strtok($_SERVER['REQUEST_URI'], '?');
    
    // 2. Normalize by removing trailing slashes for consistent matching
    $current_path = untrailingslashit(strtolower($raw_uri));
    
    // Default to root if empty after strip
    if (empty($current_path)) { $current_path = '/'; }

    // 3. Load the dedicated map file
    $map_file = get_stylesheet_directory() . '/redirects-map.php';
    if (!file_exists($map_file)) return;

    $redirect_list = include $map_file;

    // 4. CHECK 1: Exact Matches
    foreach ($redirect_list as $old => $new) {
        if (strpos($old, '[BULK]') === 0) continue; // Skip bulk for second pass

        if (untrailingslashit(strtolower($old)) === $current_path) {
            wp_redirect(home_url($new), 301);
            exit;
        }
    }

    // 5. CHECK 2: Bulk Patterns (For all the London area URLs)
    foreach ($redirect_list as $old => $new) {
        if (strpos($old, '[BULK]') === 0) {
            $pattern = str_replace('[BULK]', '', $old);
            if (strpos($current_path, untrailingslashit(strtolower($pattern))) === 0) {
                wp_redirect(home_url($new), 301);
                exit;
            }
        }
    }
}, 1); // Priority 1 to catch redirects as early as possible