<?php
$num = ! empty( $attributes['number'] ) ? $attributes['number'] : '15';
$suf = ! empty( $attributes['suffix'] ) ? $attributes['suffix'] : '+';
$lbl = ! empty( $attributes['label'] ) ? $attributes['label'] : 'Years Experience';
$bg  = ! empty( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#1e3a8a';
$bg2 = ! empty( $attributes['bgColor2'] ) ? $attributes['bgColor2'] : '#2563eb';
$grad = ! empty( $attributes['bgGradient'] );
$animate = ! isset( $attributes['animate'] ) || $attributes['animate'];

$style = '';
if ( $grad ) {
    $style = 'background: linear-gradient(135deg, ' . esc_attr( $bg ) . ' 0%, ' . esc_attr( $bg2 ) . ' 100%);';
} else {
    $style = 'background: ' . esc_attr( $bg ) . ';';
}

$wrapper_class = 'ffl-stat' . ( $animate ? ' ffl-fade-up' : '' );
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>" style="<?php echo esc_attr( $style ); ?>">
    <div class="ffl-stat-num" data-count="<?php echo esc_attr( $num ); ?>" data-suffix="<?php echo esc_attr( $suf ); ?>">0<?php echo esc_html( $suf ); ?></div>
    <div class="ffl-stat-label"><?php echo esc_html( $lbl ); ?></div>
</div>
