<?php

declare( strict_types=1 );

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

$notify = filter_input( INPUT_GET, 'notify', FILTER_SANITIZE_NUMBER_INT );
$user_deleted = filter_input( INPUT_GET, 'user-deleted', FILTER_SANITIZE_NUMBER_INT );
$request_confirmed = filter_input( INPUT_GET, 'request-confirmed', FILTER_SANITIZE_NUMBER_INT );
$user_not_found = filter_input( INPUT_GET, 'user-not-found', FILTER_SANITIZE_NUMBER_INT );
$request_key_not_found = filter_input( INPUT_GET, 'request-key-not-found', FILTER_SANITIZE_NUMBER_INT );
$request_key_not_match = filter_input( INPUT_GET, 'request-key-not-match', FILTER_SANITIZE_NUMBER_INT );

$modal_title = '';
$modal_text = '';
if ( isset( $notify ) && absint( $notify ) ) :
	?>
	<?php
	if ( isset( $user_deleted ) ) {
		$modal_title = __( 'Your account', 'gdpr' );
		if ( absint( $user_deleted ) ) {
			$modal_text = __( 'Your account has been closed. We are sorry to see you go.', 'gdpr' );
		} else {
			$modal_text = __( 'Your request has been received and is being reviewed. You will receive an email when we are done.', 'gdpr' );
		}
	}
	if ( isset( $request_confirmed ) && absint( $request_confirmed ) ) {
		$modal_title = __( 'Request Received', 'gdpr' );
		$modal_text = __( 'Your request has been received. We will be in touch soon.', 'gdpr' );
	}
	if ( isset( $user_not_found ) && absint( $user_not_found ) ) {
		$modal_title = __( 'Error!', 'gdpr' );
		$modal_text = __( 'User not found.', 'gdpr' );
	}
	if ( isset( $request_key_not_found ) && absint( $request_key_not_found ) ) {
		$modal_title = __( 'Error!', 'gdpr' );
		$modal_text = __( 'We could not confirm the request key. It may be expired.', 'gdpr' );
	}
	if ( isset( $request_key_not_match ) && absint( $request_key_not_match ) ) {
		$modal_title = __( 'Error!', 'gdpr' );
		$modal_text = __( 'The key used does not match the request key we have stored.', 'gdpr' );
	}
	?>
<?php endif ?>
<div class="gdpr gdpr-general-confirmation">
    <div class="gdpr-wrapper">
        <header>
            <div class="gdpr-box-title">
				<?php if ( $modal_title ) : ?>
					<h3><?php echo esc_attr( $modal_title ); ?></h3>
				<?php endif; ?>
                <span class="gdpr-close"></span>
            </div>
        </header>
        <div class="gdpr-content">
            <p><?php echo esc_html( $modal_text ); ?></p>
        </div>
        <footer>
            <button class="gdpr-ok" data-callback="closeNotification"><?php esc_html_e( 'OK', 'gdpr' ); ?></button>
        </footer>
    </div>
</div>
