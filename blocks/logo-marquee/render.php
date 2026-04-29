<?php
$logos       = ! empty( $attributes['logos'] ) ? $attributes['logos'] : [];
$bg_color    = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#f8fafc';
$speed       = isset( $attributes['scrollSpeed'] ) ? absint( $attributes['scrollSpeed'] ) : 30;
$max_width   = isset( $attributes['logoMaxWidth'] ) ? absint( $attributes['logoMaxWidth'] ) : 150;
$max_height  = isset( $attributes['logoMaxHeight'] ) ? absint( $attributes['logoMaxHeight'] ) : 80;
$pad_top     = isset( $attributes['paddingTop'] ) ? absint( $attributes['paddingTop'] ) : 60;
$pad_bottom  = isset( $attributes['paddingBottom'] ) ? absint( $attributes['paddingBottom'] ) : 60;
$gap         = isset( $attributes['gap'] ) ? absint( $attributes['gap'] ) : 80;
$pause_hover = ! isset( $attributes['pauseOnHover'] ) || $attributes['pauseOnHover'];

if ( empty( $logos ) ) {
    return '';
}

$section_style = '--ffl-marquee-speed: ' . esc_attr( $speed ) . 's;'
    . ' --ffl-marquee-gap: ' . esc_attr( $gap ) . 'px;'
    . ' --ffl-marquee-logo-max-w: ' . esc_attr( $max_width ) . 'px;'
    . ' --ffl-marquee-logo-max-h: ' . esc_attr( $max_height ) . 'px;';

$wrapper_class = 'ffl-logo-marquee alignfull';
if ( $pause_hover ) {
    $wrapper_class .= ' is-pause-hover';
}

$logo_html = '';
foreach ( $logos as $logo ) {
    $url = ! empty( $logo['url'] ) ? $logo['url'] : '';
    $alt = ! empty( $logo['alt'] ) ? $logo['alt'] : '';
    if ( ! $url ) {
        continue;
    }
    $logo_html .= '<div class="ffl-logo-marquee-item">'
        . '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">'
        . '</div>';
}

if ( empty( $logo_html ) ) {
    return '';
}
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>" style="background-color: <?php echo esc_attr( $bg_color ); ?>; padding-top: <?php echo esc_attr( $pad_top ); ?>px; padding-bottom: <?php echo esc_attr( $pad_bottom ); ?>px;">
    <div class="ffl-logo-marquee-track" style="<?php echo esc_attr( $section_style ); ?>">
        <div class="ffl-logo-marquee-group">
            <?php echo $logo_html; ?>
        </div>
        <div class="ffl-logo-marquee-group" aria-hidden="true">
            <?php echo $logo_html; ?>
        </div>
        <div class="ffl-logo-marquee-group" aria-hidden="true">
            <?php echo $logo_html; ?>
        </div>
        <div class="ffl-logo-marquee-group" aria-hidden="true">
            <?php echo $logo_html; ?>
        </div>
    </div>
</section>
