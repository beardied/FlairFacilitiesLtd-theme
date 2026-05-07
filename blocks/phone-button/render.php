<?php
$text          = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Call Us Now';
$show_phone    = ! isset( $attributes['showPhoneNumber'] ) || $attributes['showPhoneNumber'];
$bg_color      = ! empty( $attributes['backgroundColor'] ) ? $attributes['backgroundColor'] : '#dc2626';
$text_color    = ! empty( $attributes['textColor'] ) ? $attributes['textColor'] : '#ffffff';
$radius        = isset( $attributes['borderRadius'] ) ? absint( $attributes['borderRadius'] ) : 8;
$font_size     = isset( $attributes['fontSize'] ) ? absint( $attributes['fontSize'] ) : 16;
$pad_x         = isset( $attributes['paddingX'] ) ? absint( $attributes['paddingX'] ) : 24;
$pad_y         = isset( $attributes['paddingY'] ) ? absint( $attributes['paddingY'] ) : 14;
$full_width    = ! empty( $attributes['fullWidth'] );

$phone_raw   = get_theme_mod( 'flairltd_phone', '020 7998 9005' );
$phone_clean = preg_replace( '/[^0-9+]/', '', $phone_raw );

$display_text = $show_phone ? trim( $text ) . ' ' . $phone_raw : trim( $text );

$style = sprintf(
    'display:inline-flex;align-items:center;justify-content:center;gap:8px;background-color:%s;color:%s;border-radius:%dpx;font-size:%dpx;font-weight:600;text-decoration:none;padding:%dpx %dpx;',
    esc_attr( $bg_color ),
    esc_attr( $text_color ),
    $radius,
    $font_size,
    $pad_y,
    $pad_x
);

if ( $full_width ) {
    $style .= 'width:100%;';
}

$wrapper_class = 'wp-block-flairltd-phone-button';
if ( ! empty( $attributes['align'] ) ) {
    $wrapper_class .= ' align' . esc_attr( $attributes['align'] );
}
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>">
    <a href="tel:<?php echo esc_attr( $phone_clean ); ?>" class="ffl-phone-button" style="<?php echo esc_attr( $style ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <?php echo esc_html( $display_text ); ?>
    </a>
</div>
