<?php
$title = ! empty( $attributes['title'] ) ? $attributes['title'] : 'Frequently Asked Questions';
$items = ! empty( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];
$bg_color = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#ffffff';
$bg_color2 = ! empty( $attributes['bgColor2'] ) ? $attributes['bgColor2'] : '#f8fafc';
$bg_gradient = ! empty( $attributes['bgGradient'] );
$animate = ! isset( $attributes['animate'] ) || $attributes['animate'];

$items = array_filter( $items, function( $item ) {
    return ! empty( $item['question'] ) && ! empty( $item['answer'] );
} );

if ( empty( $items ) ) {
    return '';
}

$items = array_values( $items );

// Collect FAQ data for schema injection.
if ( ! isset( $GLOBALS['flairltd_faq_schema_data'] ) ) {
    $GLOBALS['flairltd_faq_schema_data'] = [];
}
foreach ( $items as $item ) {
    $GLOBALS['flairltd_faq_schema_data'][] = [
        'question' => $item['question'],
        'answer'   => $item['answer'],
    ];
}

if ( ! has_action( 'wp_head', 'flairltd_faq_output_schema' ) ) {
    add_action( 'wp_head', 'flairltd_faq_output_schema', 5 );
}

$section_style = '';
if ( $bg_gradient ) {
    $section_style = 'background: linear-gradient(135deg, ' . esc_attr( $bg_color ) . ' 0%, ' . esc_attr( $bg_color2 ) . ' 100%);';
} else {
    $section_style = 'background-color: ' . esc_attr( $bg_color ) . ';';
}

$wrapper_class = 'flairltd-faq-section' . ( $animate ? ' ffl-fade-up' : '' );
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>" style="<?php echo esc_attr( $section_style ); ?>">
    <div class="flairltd-faq-inner">
        <?php if ( $title ) : ?>
            <h2 class="flairltd-faq-title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <div class="flairltd-faq-list">
            <?php foreach ( $items as $index => $item ) : ?>
            <div class="flairltd-faq-item">
                <div class="flairltd-faq-question">
                    <span class="flairltd-faq-badge flairltd-faq-badge--q">Q</span>
                    <span class="flairltd-faq-question-text"><?php echo esc_html( $item['question'] ); ?></span>
                </div>
                <div class="flairltd-faq-divider"></div>
                <div class="flairltd-faq-answer">
                    <span class="flairltd-faq-badge flairltd-faq-badge--a">A</span>
                    <div class="flairltd-faq-answer-text"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
