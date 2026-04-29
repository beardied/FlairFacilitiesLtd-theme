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

$logo_count  = count( $logos );
$group_width = $logo_count * ( $max_width + $gap );
$target_width = 4000; // Cover up to 4K viewport + buffer.
$num_copies   = max( 2, (int) ceil( $target_width / max( 1, $group_width ) ) );
$num_copies   = min( $num_copies, 12 ); // Hard cap.
$shift_pct    = -100 / $num_copies;

$section_style = '--ffl-marquee-speed: ' . esc_attr( $speed ) . 's;'
    . ' --ffl-marquee-gap: ' . esc_attr( $gap ) . 'px;'
    . ' --ffl-marquee-logo-max-w: ' . esc_attr( $max_width ) . 'px;'
    . ' --ffl-marquee-logo-max-h: ' . esc_attr( $max_height ) . 'px;'
    . ' --ffl-marquee-shift: ' . esc_attr( $shift_pct ) . '%;';

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
        <?php for ( $i = 0; $i < $num_copies; $i++ ) : ?>
            <div class="ffl-logo-marquee-group" <?php echo $i > 0 ? 'aria-hidden="true"' : ''; ?>>
                <?php echo $logo_html; ?>
            </div>
        <?php endfor; ?>
    </div>
</section>
