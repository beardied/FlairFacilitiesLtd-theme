<?php
$h          = ! empty( $attributes['headline'] ) ? $attributes['headline'] : '24/7 COMMERCIAL HEATING SYSTEM IN LONDON';
$desc       = ! empty( $attributes['description'] ) ? $attributes['description'] : '';
$badge1     = ! empty( $attributes['badge1'] ) ? $attributes['badge1'] : '';
$badge2     = ! empty( $attributes['badge2'] ) ? $attributes['badge2'] : '';
$badge3     = ! empty( $attributes['badge3'] ) ? $attributes['badge3'] : '';
$pText      = ! empty( $attributes['primaryBtnText'] ) ? $attributes['primaryBtnText'] : '';
$pUrl       = ! empty( $attributes['primaryBtnUrl'] ) ? $attributes['primaryBtnUrl'] : '#';
$sText      = ! empty( $attributes['secondaryBtnText'] ) ? $attributes['secondaryBtnText'] : '';
$sUrl       = ! empty( $attributes['secondaryBtnUrl'] ) ? $attributes['secondaryBtnUrl'] : '#';
$cTitle     = ! empty( $attributes['cardTitle'] ) ? $attributes['cardTitle'] : '';
$cDesc      = ! empty( $attributes['cardDescription'] ) ? $attributes['cardDescription'] : '';
$cBtn1Text  = ! empty( $attributes['cardBtn1Text'] ) ? $attributes['cardBtn1Text'] : '';
$cBtn1Url   = ! empty( $attributes['cardBtn1Url'] ) ? $attributes['cardBtn1Url'] : '#';
$cBtn2Text  = ! empty( $attributes['cardBtn2Text'] ) ? $attributes['cardBtn2Text'] : '';
$cBtn2Url   = ! empty( $attributes['cardBtn2Url'] ) ? $attributes['cardBtn2Url'] : '#';

$badges = array_filter( [ $badge1, $badge2, $badge3 ] );
?>
<section class="ffl-hero">
    <div class="ffl-hero-inner">
        <div class="ffl-hero-content">
            <h1 class="ffl-fade-up"><?php echo esc_html( $h ); ?></h1>
            <?php if ( $desc ) : ?>
                <p class="ffl-hero-desc ffl-fade-up ffl-fade-up-delay-1"><?php echo esc_html( $desc ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $badges ) ) : ?>
                <div class="ffl-hero-badges ffl-fade-up ffl-fade-up-delay-2">
                    <?php foreach ( $badges as $b ) : ?>
                        <span class="ffl-hero-badge"><?php echo esc_html( $b ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="ffl-hero-btns ffl-fade-up ffl-fade-up-delay-3">
                <?php if ( $pText ) : ?>
                    <a href="<?php echo esc_url( $pUrl ); ?>" class="ffl-btn ffl-btn-red"><?php echo esc_html( $pText ); ?></a>
                <?php endif; ?>
                <?php if ( $sText ) : ?>
                    <a href="<?php echo esc_url( $sUrl ); ?>" class="ffl-btn ffl-btn-blue"><?php echo esc_html( $sText ); ?></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="ffl-hero-card ffl-fade-up ffl-fade-up-delay-2">
            <?php if ( $cTitle ) : ?>
                <h3><?php echo esc_html( $cTitle ); ?></h3>
            <?php endif; ?>
            <?php if ( $cDesc ) : ?>
                <p><?php echo esc_html( $cDesc ); ?></p>
            <?php endif; ?>
            <?php if ( $cBtn1Text ) : ?>
                <a href="<?php echo esc_url( $cBtn1Url ); ?>" class="ffl-btn ffl-btn-red" style="width:100%;justify-content:center;margin-bottom:10px"><?php echo esc_html( $cBtn1Text ); ?></a>
            <?php endif; ?>
            <?php if ( $cBtn2Text ) : ?>
                <a href="<?php echo esc_url( $cBtn2Url ); ?>" class="ffl-btn ffl-btn-blue" style="width:100%;justify-content:center"><?php echo esc_html( $cBtn2Text ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
