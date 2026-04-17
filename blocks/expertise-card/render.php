<?php
$title = ! empty( $attributes['title'] ) ? $attributes['title'] : 'Boiler & Heating';
$desc  = ! empty( $attributes['description'] ) ? $attributes['description'] : 'Installation and repairs.';
$btn   = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Learn More →';
$url   = ! empty( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
?>
<div class="ffl-expertise-card">
    <h3><?php echo esc_html( $title ); ?></h3>
    <p><?php echo esc_html( $desc ); ?></p>
    <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $btn ); ?></a>
</div>
