<?php
$url    = ! empty( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$alt    = ! empty( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : 'Flair Facilities Engineer';
$s1n    = ! empty( $attributes['stat1Number'] ) ? $attributes['stat1Number'] : '15';
$s1s    = ! empty( $attributes['stat1Suffix'] ) ? $attributes['stat1Suffix'] : '+';
$s1l    = ! empty( $attributes['stat1Label'] ) ? $attributes['stat1Label'] : 'Years Experience';
$s2n    = ! empty( $attributes['stat2Number'] ) ? $attributes['stat2Number'] : '100';
$s2s    = ! empty( $attributes['stat2Suffix'] ) ? $attributes['stat2Suffix'] : '%';
$s2l    = ! empty( $attributes['stat2Label'] ) ? $attributes['stat2Label'] : 'Satisfaction';
$bg     = ! empty( $attributes['statBgColor'] ) ? $attributes['statBgColor'] : get_theme_mod( 'flairltd_dark_color', '#0a1628' );
$bg2    = ! empty( $attributes['statBgColor2'] ) ? $attributes['statBgColor2'] : get_theme_mod( 'flairltd_primary_color', '#1e3a8a' );
$grad   = ! empty( $attributes['statBgGradient'] );
$animate = ! isset( $attributes['animate'] ) || $attributes['animate'];

if ( ! $url ) {
    $url = get_template_directory_uri() . '/assets/images/engineer.jpg';
}

$stat_style = '';
if ( $grad ) {
    $stat_style = 'background: linear-gradient(135deg, ' . esc_attr( $bg ) . ' 0%, ' . esc_attr( $bg2 ) . ' 100%);';
} else {
    $stat_style = 'background: ' . esc_attr( $bg ) . ';';
}

$stat_class = '';
if ( $animate && $grad ) {
    $stat_class = 'is-animated-gradient';
}
?>
<div style="position:relative;border-radius:16px;overflow:hidden">
    <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" style="width:100%;height:500px;object-fit:cover" class="skip-lazy" loading="eager" decoding="async">
    <div class="ffl-about-stats <?php echo esc_attr( $stat_class ); ?>" style="<?php echo esc_attr( $stat_style ); ?>">
        <div class="ffl-about-stat">
            <div class="ffl-about-stat__num"><?php echo esc_html( $s1n . $s1s ); ?></div>
            <div class="ffl-about-stat__label"><?php echo esc_html( $s1l ); ?></div>
        </div>
        <div class="ffl-about-stat">
            <div class="ffl-about-stat__num"><?php echo esc_html( $s2n . $s2s ); ?></div>
            <div class="ffl-about-stat__label"><?php echo esc_html( $s2l ); ?></div>
        </div>
    </div>
</div>
