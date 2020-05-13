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
$gdpr_title = '';
$text  = '';
if ( isset( $_GET['notify'] ) && absint( $_GET['notify'] ) ) : // phpcs:ignore
?>
	<?php
	if ( isset( $_GET['user-deleted'] ) ) { // phpcs:ignore
		$gdpr_title = __( 'Your account', 'gdpr' );
		if ( absint( $_GET['user-deleted'] ) ) { // phpcs:ignore
			$text = __( 'Your account has been closed. We are sorry to see you go.', 'gdpr' );
		} else {
			$text = __( 'Your request has been received and is being reviewed. You will receive an email when we are done.', 'gdpr' );
		}
	}
	if ( isset( $_GET['request-confirmed'] ) && absint( $_GET['request-confirmed'] ) ) { // phpcs:ignore
		$gdpr_title = __( 'Request Received', 'gdpr' );
		$text  = __( 'Your request has been received. We will be in touch soon.', 'gdpr' );
	}
	if ( isset( $_GET['user-not-found'] ) && absint( $_GET['user-not-found'] ) ) { // phpcs:ignore
		$gdpr_title = __( 'Error!', 'gdpr' );
		$text  = __( 'User not found.', 'gdpr' );
	}
	if ( isset( $_GET['request-key-not-found'] ) && absint( $_GET['request-key-not-found'] ) ) { // phpcs:ignore
		$gdpr_title = __( 'Error!', 'gdpr' );
		$text  = __( 'We could not confirm the request key. It may be expired.', 'gdpr' );
	}
	if ( isset( $_GET['request-key-not-match'] ) && absint( $_GET['request-key-not-match'] ) ) { // phpcs:ignore
		$gdpr_title = __( 'Error!', 'gdpr' );
		$text  = __( 'The key used does not match the request key we have stored.', 'gdpr' );
	}
	?>
<?php endif ?>
<div class="gdpr gdpr-general-confirmation">
	<div class="gdpr-wrapper">
		<header>
			<div class="gdpr-box-title">
				<h3><?php echo esc_attr( $gdpr_title ); ?></h3>
				<span class="gdpr-close"></span>
			</div>
		</header>
		<div class="gdpr-content">
			<p><?php echo esc_html( $text ); ?></p>
		</div>
		<footer>
			<button class="gdpr-ok" data-callback="closeNotification"><?php esc_html_e( 'OK', 'gdpr' ); ?></button>
		</footer>
	</div>
</div>
