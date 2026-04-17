<?php
$title = ! empty( $attributes['title'] ) ? $attributes['title'] : 'Service Title';
$desc  = ! empty( $attributes['description'] ) ? $attributes['description'] : 'Description';
$icon  = ! empty( $attributes['icon'] ) ? $attributes['icon'] : '🔥';
$btn   = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Get a Quote';
$url   = ! empty( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
?>
<div class="ffl-service-card">
    <div class="ffl-service-icon"><?php echo esc_html( $icon ); ?></div>
    <h3><?php echo esc_html( $title ); ?></h3>
    <p><?php echo esc_html( $desc ); ?></p>
    <a href="<?php echo esc_url( $url ); ?>" class="ffl-btn ffl-btn-blue"><?php echo esc_html( $btn ); ?></a>
</div>
