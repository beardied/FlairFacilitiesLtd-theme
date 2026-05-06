<?php
$title  = ! empty( $attributes['title'] ) ? $attributes['title'] : 'Boiler & Heating';
$desc   = ! empty( $attributes['description'] ) ? $attributes['description'] : 'Installation and repairs.';
$btn    = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Learn More';
$url    = ! empty( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
$bgImgId = ! empty( $attributes['backgroundImageId'] ) ? intval( $attributes['backgroundImageId'] ) : 0;
$bgImg  = ! empty( $attributes['backgroundImage'] ) ? $attributes['backgroundImage'] : '';
if ( $bgImgId ) {
    $bgImg = wp_get_attachment_image_url( $bgImgId, 'large' );
}
$ovColor = ! empty( $attributes['overlayColor'] ) ? $attributes['overlayColor'] : get_theme_mod( 'flairltd_primary_color', '#1e3a8a' );
$ovOp   = isset( $attributes['overlayOpacity'] ) ? intval( $attributes['overlayOpacity'] ) : 90;
$titleColor = ! empty( $attributes['titleColor'] ) ? $attributes['titleColor'] : '#ffffff';

$card_style = '';
$overlay_style = '';
$has_image = false;

if ( $bgImg ) {
    $has_image = true;
    $card_style = 'background-image: url(' . esc_url( $bgImg ) . '); background-size: cover; background-position: center;';
    $opacity = max( 0, min( 100, $ovOp ) ) / 100;
    $overlay_style = 'background: ' . esc_attr( $ovColor ) . '; opacity: ' . $opacity . ';';
}
?>
<div class="ffl-expertise-card<?php echo $has_image ? ' ffl-expertise-card-has-image' : ''; ?>" <?php if ( $card_style ) echo 'style="' . esc_attr( $card_style ) . '"'; ?>>
    <?php if ( $has_image ) : ?>
        <div class="ffl-expertise-card-overlay" style="<?php echo esc_attr( $overlay_style ); ?>"></div>
    <?php endif; ?>
    <div class="ffl-expertise-card-content">
        <h3 style="color: <?php echo esc_attr( $titleColor ); ?>"><?php echo esc_html( $title ); ?></h3>
        <p><?php echo esc_html( $desc ); ?></p>
        <a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $btn . ' — ' . $title . ': ' . $desc ); ?>"><?php echo esc_html( $btn ); ?></a>
    </div>
</div>
