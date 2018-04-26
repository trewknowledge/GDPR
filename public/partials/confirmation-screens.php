<?php
/**
 * The confirmation dialogs.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
?>

<div class="gdpr gdpr-general-confirmation gdpr-delete-confirmation">
	<div class="gdpr-wrapper">
		<header>
			<div class="gdpr-box-title">
				<h3><?php esc_attr_e( 'Close your account?', 'gdpr' ); ?></h3>
				<span class="gdpr-close"></span>
			</div>
		</header>
		<div class="gdpr-content">
			<p><?php esc_html_e( 'Your account will be closed and all data will be permanently deleted and cannot be recovered. Are you sure?', 'gdpr' ); ?></p>
		</div>
		<footer>
			<button class="gdpr-delete-account">Continue</button>
			<button class="gdpr-cancel"><?php esc_html_e( 'Cancel', 'gdpr' ); ?></button>
		</footer>
	</div>
</div>

<?php if ( isset( $_GET['notify'] ) && $_GET['notify'] ) : ?>
	<?php
	$title = __( 'Error!', 'gdpr' );
	if ( isset( $_GET['user-deleted'] ) ) {
		$title = __( 'Your account', 'gdpr' );
		if ( $_GET['user-deleted'] ) {
			$text = __( 'Your account has been closed. We are sorry to see you go.', 'gdpr' );
		} else {
			$text = __( 'Your request has been received and is being reviewed. You will receive an email when we are done.', 'gdpr' );
		}
	}
	if ( isset( $_GET['email-sent'] ) && $_GET['email-sent'] ) {
		$title = __( 'Email confirmation', 'gdpr' );
		$text  = __( 'We\'ve sent you a confirmation email.', 'gdpr' );
	}
	if ( isset( $_GET['user-not-found'] ) && $_GET['user-not-found'] ) {
		$text = __( 'User not found.', 'gdpr' );
	}
	if ( isset( $_GET['cannot-delete'] ) && $_GET['cannot-delete'] ) {
		$text = __( 'We can\'t delete this user.', 'gdpr' );
	}
	if ( isset( $_GET['required-information-missing'] ) && $_GET['required-information-missing'] ) {
		$text = __( 'Required information is missing from the form.', 'gdpr' );
	}
	if ( isset( $_GET['request-confirmed'] ) && $_GET['request-confirmed'] ) {
		$title = __( 'Request Received', 'gdpr' );
		$text  = __( 'Your request has been received. We will be in touch soon.', 'gdpr' );
	}
	if ( isset( $_GET['error'] ) && $_GET['error'] ) {
		$text = __( 'There was a problem with your request. Please try again later.', 'gdpr' );
	}
	?>
	<div class="gdpr gdpr-general-confirmation gdpr-accept-confirmation">
		<div class="gdpr-wrapper">
			<header>
				<div class="gdpr-box-title">
					<h3><?php echo esc_attr( $title ); ?></h3>
					<span class="gdpr-close"></span>
				</div>
			</header>
			<div class="gdpr-content">
				<p><?php echo esc_html( $text ); ?></p>
			</div>
			<footer>
				<button class="gdpr-ok">OK</button>
			</footer>
		</div>
	</div>
<?php endif ?>
