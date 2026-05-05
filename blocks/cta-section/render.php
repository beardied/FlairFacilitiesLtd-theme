<?php
$bg_color      = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#1e3a8a';
$bg_color2     = ! empty( $attributes['bgColor2'] ) ? $attributes['bgColor2'] : '#2563eb';
$bg_gradient   = ! empty( $attributes['bgGradient'] );
$animate       = ! isset( $attributes['animate'] ) || $attributes['animate'];
$show_button   = ! isset( $attributes['showButton'] ) || $attributes['showButton'];
$button_text   = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Call Now';
$btn_bg        = ! empty( $attributes['buttonBgColor'] ) ? $attributes['buttonBgColor'] : '#dc2626';
$btn_bg2       = ! empty( $attributes['buttonBgColor2'] ) ? $attributes['buttonBgColor2'] : '#ea580c';
$btn_gradient  = ! empty( $attributes['buttonGradient'] );
$btn_text_col  = ! empty( $attributes['buttonTextColor'] ) ? $attributes['buttonTextColor'] : '#ffffff';

$section_style = '';
if ( $bg_gradient ) {
    $section_style = 'background: linear-gradient(135deg, ' . esc_attr( $bg_color ) . ' 0%, ' . esc_attr( $bg_color2 ) . ' 100%);';
} else {
    $section_style = 'background-color: ' . esc_attr( $bg_color ) . ';';
}

$wrapper_class = 'ffl-cta-section alignfull';
if ( $animate && $bg_gradient ) {
    $wrapper_class .= ' is-animated-gradient';
}

$btn_style = '';
if ( $btn_gradient ) {
    $btn_style = 'background: linear-gradient(135deg, ' . esc_attr( $btn_bg ) . ' 0%, ' . esc_attr( $btn_bg2 ) . ' 100%); color: ' . esc_attr( $btn_text_col ) . ';';
} else {
    $btn_style = 'background-color: ' . esc_attr( $btn_bg ) . '; color: ' . esc_attr( $btn_text_col ) . ';';
}

$phone_raw = get_theme_mod( 'flairltd_phone', '020 7998 9005' );
$phone_clean = preg_replace( '/[^0-9+]/', '', $phone_raw );

// Inner blocks content is passed via $content.
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>" style="<?php echo esc_attr( $section_style ); ?>">
    <div class="ffl-cta-section-inner">
        <?php if ( ! empty( $content ) ) : ?>
            <div class="ffl-cta-section-content">
                <?php echo $content; ?>
            </div>
        <?php endif; ?>

        <?php if ( $show_button ) : ?>
            <div class="ffl-cta-section-button-wrap">
                <a href="tel:<?php echo esc_attr( $phone_clean ); ?>" class="ffl-cta-button" style="<?php echo esc_attr( $btn_style ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <?php echo esc_html( $button_text ); ?>
                </a>
                <span class="ffl-cta-button-number"><?php echo esc_html( $phone_raw ); ?></span>
            </div>
        <?php endif; ?>
    </div>
</section>
