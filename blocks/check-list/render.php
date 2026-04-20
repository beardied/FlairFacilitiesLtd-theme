<?php
$items = [];
for ( $i = 1; $i <= 8; $i++ ) {
    $t = ! empty( $attributes[ "item{$i}Title" ] ) ? $attributes[ "item{$i}Title" ] : '';
    $d = ! empty( $attributes[ "item{$i}Description" ] ) ? $attributes[ "item{$i}Description" ] : '';
    if ( $t ) {
        $items[] = [ 'title' => $t, 'description' => $d ];
    }
}
if ( empty( $items ) ) {
    return;
}
?>
<div style="display:flex;flex-direction:column;gap:16px">
    <?php foreach ( $items as $item ) : ?>
        <div class="ffl-check">
            <div class="ffl-check-icon">&#10003;</div>
            <div>
                <strong style="color:#0a1628;font-size:15px"><?php echo esc_html( $item['title'] ); ?></strong>
                <?php if ( ! empty( $item['description'] ) ) : ?>
                    <p style="color:#64748b;font-size:14px;margin:2px 0 0;line-height:1.6"><?php echo esc_html( $item['description'] ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
