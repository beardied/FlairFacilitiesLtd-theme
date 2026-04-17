<?php
$quote  = ! empty( $attributes['quote'] ) ? $attributes['quote'] : 'Great service!';
$author = ! empty( $attributes['author'] ) ? $attributes['author'] : 'Client Name';
$rating = ! empty( $attributes['rating'] ) ? intval( $attributes['rating'] ) : 5;
?>
<div class="ffl-testi-card">
    <div class="ffl-testi-stars"><?php echo str_repeat( '★', $rating ); ?></div>
    <p class="ffl-testi-quote">"<?php echo esc_html( $quote ); ?>"</p>
    <cite class="ffl-testi-author">— <?php echo esc_html( $author ); ?></cite>
</div>
