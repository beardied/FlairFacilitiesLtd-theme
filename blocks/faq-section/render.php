<?php
$items = ! empty( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];

// Filter out empty items.
$items = array_filter( $items, function( $item ) {
    return ! empty( $item['question'] ) && ! empty( $item['answer'] );
} );

if ( empty( $items ) ) {
    return '';
}

// Re-index after filtering.
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

// Ensure the schema hook is registered only once.
if ( ! has_action( 'wp_head', 'flairltd_faq_output_schema' ) ) {
    add_action( 'wp_head', 'flairltd_faq_output_schema', 5 );
}

$block_id = 'flairltd-faq-' . wp_rand( 1000, 9999 );
?>
<div class="flairltd-faq-section" id="<?php echo esc_attr( $block_id ); ?>">
    <div class="flairltd-faq-list">
        <?php foreach ( $items as $index => $item ) : ?>
        <details class="flairltd-faq-item" <?php echo $index === 0 ? 'open' : ''; ?>>
            <summary class="flairltd-faq-question">
                <span class="flairltd-faq-question-text"><?php echo esc_html( $item['question'] ); ?></span>
                <span class="flairltd-faq-icon" aria-hidden="true"></span>
            </summary>
            <div class="flairltd-faq-answer">
                <?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?>
            </div>
        </details>
        <?php endforeach; ?>
    </div>
</div>
