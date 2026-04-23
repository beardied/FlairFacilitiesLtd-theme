<?php
$title = ! empty( $attributes['title'] ) ? $attributes['title'] : 'In This Section';
$bg_color = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#f8fafc';
$bg_color2 = ! empty( $attributes['bgColor2'] ) ? $attributes['bgColor2'] : '#e2e8f0';
$bg_gradient = ! empty( $attributes['bgGradient'] );
$animate = ! isset( $attributes['animate'] ) || $attributes['animate'];

// Only works on singular pages.
if ( ! is_singular( 'page' ) ) {
    return '';
}

$current_id = get_the_ID();
$parent_id = wp_get_post_parent_id( $current_id );

// If no parent, show current page's children (if any).
if ( ! $parent_id ) {
    $parent_id = $current_id;
}

$siblings = get_pages( [
    'child_of'    => $parent_id,
    'parent'      => $parent_id,
    'sort_column' => 'menu_order',
    'sort_order'  => 'ASC',
    'exclude'     => $current_id,
] );

// Also exclude the parent itself if it's the current page.
if ( empty( $siblings ) && $parent_id === $current_id ) {
    return '';
}

$parent_page = get_post( $parent_id );
$parent_title = $parent_page ? $parent_page->post_title : '';
$parent_url = $parent_page ? get_permalink( $parent_page->ID ) : '';

$section_style = '';
if ( $bg_gradient ) {
    $section_style = 'background: linear-gradient(135deg, ' . esc_attr( $bg_color ) . ' 0%, ' . esc_attr( $bg_color2 ) . ' 100%);';
} else {
    $section_style = 'background-color: ' . esc_attr( $bg_color ) . ';';
}

$wrapper_class = 'flairltd-page-menu' . ( $animate ? ' ffl-fade-up' : '' );
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>" style="<?php echo esc_attr( $section_style ); ?>">
    <div class="flairltd-page-menu-inner">
        <?php if ( $title ) : ?>
            <h2 class="flairltd-page-menu-title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $parent_page && $parent_id !== $current_id ) : ?>
            <div class="flairltd-page-menu-parent">
                <a href="<?php echo esc_url( $parent_url ); ?>" class="flairltd-page-menu-parent-link">
                    <span class="flairltd-page-menu-parent-label">← Back to</span>
                    <span class="flairltd-page-menu-parent-name"><?php echo esc_html( $parent_title ); ?></span>
                </a>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $siblings ) ) : ?>
            <nav class="flairltd-page-menu-nav" aria-label="Section navigation">
                <ul class="flairltd-page-menu-list">
                    <?php foreach ( $siblings as $sibling ) : ?>
                        <li class="flairltd-page-menu-item">
                            <a href="<?php echo esc_url( get_permalink( $sibling->ID ) ); ?>" class="flairltd-page-menu-link">
                                <span class="flairltd-page-menu-link-text"><?php echo esc_html( $sibling->post_title ); ?></span>
                                <svg class="flairltd-page-menu-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php elseif ( $parent_id === $current_id ) : ?>
            <p class="flairltd-page-menu-empty">No sub-pages in this section.</p>
        <?php else : ?>
            <p class="flairltd-page-menu-empty">No other pages in this section.</p>
        <?php endif; ?>
    </div>
</section>
