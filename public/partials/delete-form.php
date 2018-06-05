<?php
/**
 * The add to deletion requests form markup.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
?>

<form class="gdpr-request-form gdpr-add-to-deletion-requests" method="post">
	<?php wp_nonce_field( 'gdpr-add-to-requests', 'gdpr_request_nonce' ); ?>
	<input type="hidden" name="action" value="gdpr_send_request_email">
	<input type="hidden" name="type" value="delete">
	<?php if ( ! is_user_logged_in() ) : ?>
		<input type="email" name="user_email" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
	<?php endif ?>
	<?php GDPR_Public::add_recaptcha(); ?>
	<?php $submit_button_text = ( $submit_button_text ?: esc_attr__( 'Close my account', 'gdpr' ) ); ?>
	<input type="submit" value="<?php echo esc_attr( $submit_button_text ); ?>">
</form>
