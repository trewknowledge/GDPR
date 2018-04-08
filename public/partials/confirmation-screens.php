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

<div class="confirm-delete-request-dialog" title="<?php esc_attr_e( 'Close your account?', 'gdpr' ); ?>">
	<p>
		<?php esc_html_e( 'Your account will be closed and all data will be permanently deleted and cannot be recovered. Are you sure?', 'gdpr' ); ?>
	</p>
</div>


<?php
if ( isset( $_GET['notify'] ) && (bool) $_GET['notify'] ) :
	$title = __( 'Error!', 'gdpr' );
	if ( isset( $_GET['user-deleted'] ) ) {
		$title = __( 'Your account', 'gdpr' );
		if ( (bool) $_GET['user-deleted'] ) {
			$text = __( 'Your account has been closed. We are sorry to see you go.', 'gdpr' );
		} else {
			$text = __( 'Seems like you have published content on the site. We can\'t close your account right away before doing some review. You will receive an email when we are done reviewing your request.', 'gdpr' );
		}
	}
	if ( isset( $_GET['email-sent'] ) && (bool) $_GET['email-sent'] ) {
		$title = __( 'Email confirmation', 'gdpr' );
		$text  = __( 'We\'ve sent you a confirmation email.', 'gdpr' );
	}
	if ( isset( $_GET['user-not-found'] ) && (bool) $_GET['user-not-found'] ) {
		$text = __( 'User not found.', 'gdpr' );
	}
	if ( isset( $_GET['cannot-delete'] ) && (bool) $_GET['cannot-delete'] ) {
		$text = __( 'We can\'t delete this user.', 'gdpr' );
	}
	if ( isset( $_GET['required-information-missing'] ) && (bool) $_GET['required-information-missing'] ) {
		$text = __( 'Required information is missing from the form.', 'gdpr' );
	}
	if ( isset( $_GET['request-confirmed'] ) && (bool) $_GET['request-confirmed'] ) {
		$title = __( 'Request Received', 'gdpr' );
		$text  = __( 'Your request has been received. We will be in touch soon.', 'gdpr' );
	}
	if ( isset( $_GET['export-started'] ) && (bool) $_GET['export-started'] ) {
		$title = __( 'Data Export', 'gdpr' );
		$text  = __( 'Your export file is being generated. You will receive an email with your file soon.', 'gdpr' );
	}
	if ( isset( $_GET['error'] ) && (bool) $_GET['error'] ) {
		$text = __( 'There was a problem with your request. Please try again later.', 'gdpr' );
	}
	?>
	<div class="gdpr-general-dialog" title="<?php echo esc_attr( $title ); ?>">
		<p>
			<?php echo esc_html( $text ); ?>
		</p>
	</div>
<?php endif; ?>
