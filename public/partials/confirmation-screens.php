<div class="confirm-delete-request-dialog" title="<?php esc_attr_e( 'Close your account?', 'gdpr' ); ?>">
  <p>
  	<?php esc_html_e( 'Your account will be closed and all data will be permanently deleted and cannot be recovered. Are you sure?', 'gdpr' ); ?>
  </p>
</div>


<?php if ( isset( $_GET['notify'] ) && $_GET['notify'] ): ?>
	<?php
		$title = __( 'Error!', 'gdpr' );
		if ( isset( $_GET['user_deleted'] ) ) {
			$title = __( 'Your account', 'gdpr' );
			if ( $_GET['user_deleted'] ) {
				$text = __( 'Your account has been closed. We are sorry to see you go.', 'gdpr' );
			} else {
				$text = __( 'Seems like you have published content on the site. We can\'t close your account right away before doing some review. You will receive an email when we are done reviewing your request.', 'gdpr' );
			}
		}
		if ( isset( $_GET['email_sent'] ) ) {
			$title = __( 'Email confirmation', 'gdpr' );
			if ( $_GET['email_sent'] ) {
				$text = __( 'We\'ve sent you a confirmation email.', 'gdpr' );
			}
		}
		if ( isset( $_GET['user_not_found'] ) ) {
			if ( $_GET['user_not_found'] ) {
				$text = __( 'User not found.', 'gdpr' );
			}
		}
		if ( isset( $_GET['cannot_delete'] ) ) {
			if ( $_GET['cannot_delete'] ) {
				$text = __( 'We can\'t delete this user.', 'gdpr' );
			}
		}
	?>
	<div class="gdpr-general-dialog" title="<?php echo esc_attr( $title ); ?>">
		<p>
		<?php echo esc_html( $text ); ?>
		</p>
	</div>
<?php endif ?>
