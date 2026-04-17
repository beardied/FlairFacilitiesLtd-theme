<?php
$num = ! empty( $attributes['number'] ) ? $attributes['number'] : '15';
$suf = ! empty( $attributes['suffix'] ) ? $attributes['suffix'] : '+';
$lbl = ! empty( $attributes['label'] ) ? $attributes['label'] : 'Years Experience';
?>
<div class="ffl-stat">
    <div class="ffl-stat-num" data-count="<?php echo esc_attr( $num ); ?>" data-suffix="<?php echo esc_attr( $suf ); ?>">0<?php echo esc_html( $suf ); ?></div>
    <div class="ffl-stat-label"><?php echo esc_html( $lbl ); ?></div>
</div>
