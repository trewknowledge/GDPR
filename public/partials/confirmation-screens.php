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

<?php
$confirmation_title = '';
$confirmation_text  = '';
if ( isset( $_GET['notify'] ) && absint( $_GET['notify'] ) ) : // WPCS: Input var ok, CSRF ok.
	?>
	<?php
	if ( isset( $_GET['user-deleted'] ) ) { // WPCS: Input var ok, CSRF ok.
		$confirmation_title = __( 'Your account', 'gdpr' );
		if ( absint( $_GET['user-deleted'] ) ) { // WPCS: Input var ok, CSRF ok.
			$confirmation_text = __( 'Your account has been closed. We are sorry to see you go.', 'gdpr' );
		} else {
			$confirmation_text = __( 'Your request has been received and is being reviewed. You will receive an email when we are done.', 'gdpr' );
		}
	}
	if ( isset( $_GET['request-confirmed'] ) && absint( $_GET['request-confirmed'] ) ) { // WPCS: Input var ok, CSRF ok.
		$confirmation_title = __( 'Request Received', 'gdpr' );
		$confirmation_text  = __( 'Your request has been received. We will be in touch soon.', 'gdpr' );
	}
	if ( isset( $_GET['user-not-found'] ) && absint( $_GET['user-not-found'] ) ) { // WPCS: Input var ok CSRF ok.
		$confirmation_title = __( 'Error!', 'gdpr' );
		$confirmation_text  = __( 'User not found.', 'gdpr' );
	}
	if ( isset( $_GET['request-key-not-found'] ) && absint( $_GET['request-key-not-found'] ) ) { // WPCS: Input var ok, CSRF ok.
		$confirmation_title = __( 'Error!', 'gdpr' );
		$confirmation_text  = __( 'We could not confirm the request key. It may be expired.', 'gdpr' );
	}
	if ( isset( $_GET['request-key-not-match'] ) && absint( $_GET['request-key-not-match'] ) ) { // WPCS: Input var ok, CSRF ok.
		$confirmation_title = __( 'Error!', 'gdpr' );
		$confirmation_text  = __( 'The key used does not match the request key we have stored.', 'gdpr' );
	}
	?>
<?php endif ?>
<div class="gdpr gdpr-general-confirmation">
	<div class="gdpr-wrapper">
		<header>
			<div class="gdpr-box-title">
				<h3><?php echo esc_attr( $confirmation_title ); ?></h3>
				<span class="gdpr-close"></span>
			</div>
		</header>
		<div class="gdpr-content">
			<p><?php echo esc_html( $confirmation_text ); ?></p>
		</div>
		<footer>
			<button class="gdpr-ok" data-callback="closeNotification"><?php esc_html_e( 'OK', 'gdpr' ); ?></button>
		</footer>
	</div>
</div>
