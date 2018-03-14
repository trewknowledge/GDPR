<form class="gdpr-add-to-rectify-requests" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
	<?php wp_nonce_field( 'add-to-rectify-requests', 'gdpr_rectify_requests_nonce' ); ?>
	<input type="hidden" name="action" value="send_rectify_request_email_confirmation">
	<?php if ( ! is_user_logged_in() ): ?>
		<input type="email" name="user_email" placeholder="user@domain.com" required>
	<?php endif ?>
	<input type="submit" value="<?php esc_attr_e( 'Close my account', 'gdpr' ); ?>">
</form>
