<?php
$recipient_email = ! empty( $attributes['recipientEmail'] ) ? sanitize_email( $attributes['recipientEmail'] ) : '';
$default_email   = get_theme_mod( 'flairltd_email', 'info@flairfacilities.co.uk' );
$send_to         = $recipient_email ?: $default_email;
$form_id         = wp_rand( 100000, 999999 );

$wrapper_classes = 'ffl-contact-form-wrapper';
if ( ! empty( $attributes['align'] ) ) {
    $wrapper_classes .= ' align' . esc_attr( $attributes['align'] );
}
if ( ! isset( $attributes['hasBorder'] ) || $attributes['hasBorder'] ) {
    $wrapper_classes .= ' has-border';
}
?>

<div class="<?php echo esc_attr( $wrapper_classes ); ?>" id="<?php echo esc_attr( $attributes['anchor'] ?? '' ); ?>">
    <form class="ffl-contact-form" method="post" enctype="multipart/form-data" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
        <?php wp_nonce_field( 'flairltd_contact_submit', 'flairltd_contact_nonce' ); ?>
        <input type="hidden" name="recipient_email" value="<?php echo esc_attr( $send_to ); ?>">

        <!-- Honeypot -->
        <div class="ffl-contact-honeypot" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off" value="">
        </div>

        <div class="ffl-contact-grid">
            <div class="ffl-contact-field">
                <label for="ffl-contact-name-<?php echo esc_attr( $form_id ); ?>">Full Name</label>
                <input type="text" id="ffl-contact-name-<?php echo esc_attr( $form_id ); ?>" name="name" required placeholder="Your name">
            </div>

            <div class="ffl-contact-field">
                <label for="ffl-contact-email-<?php echo esc_attr( $form_id ); ?>">Email Address <span class="ffl-contact-required">*</span></label>
                <input type="email" id="ffl-contact-email-<?php echo esc_attr( $form_id ); ?>" name="email" required placeholder="you@company.com">
            </div>

            <div class="ffl-contact-field">
                <label for="ffl-contact-phone-<?php echo esc_attr( $form_id ); ?>">Phone Number</label>
                <input type="tel" id="ffl-contact-phone-<?php echo esc_attr( $form_id ); ?>" name="phone" placeholder="Your phone number">
            </div>

            <div class="ffl-contact-field">
                <label for="ffl-contact-subject-<?php echo esc_attr( $form_id ); ?>">Subject</label>
                <input type="text" id="ffl-contact-subject-<?php echo esc_attr( $form_id ); ?>" name="subject" placeholder="How can we help?">
            </div>
        </div>

        <div class="ffl-contact-field">
            <label for="ffl-contact-message-<?php echo esc_attr( $form_id ); ?>">Message <span class="ffl-contact-required">*</span></label>
            <textarea id="ffl-contact-message-<?php echo esc_attr( $form_id ); ?>" name="message" rows="5" required placeholder="Tell us about your project..."></textarea>
        </div>

        <div class="ffl-contact-field">
            <label for="ffl-contact-images-<?php echo esc_attr( $form_id ); ?>">Upload Images or CV (optional)</label>
            <input type="file" id="ffl-contact-images-<?php echo esc_attr( $form_id ); ?>" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,.doc,.docx" multiple>
            <small class="ffl-contact-hint">Accepted formats: JPG, PNG, GIF, WebP, PDF, DOC, DOCX</small>
        </div>

        <button type="submit" class="ffl-contact-submit wp-block-button__link">
            Send Message
        </button>

        <div class="ffl-contact-message" role="status" aria-live="polite"></div>
    </form>
</div>
