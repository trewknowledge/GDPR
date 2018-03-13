<form class="gdpr-add-to-deletion-requests" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
	<?php wp_nonce_field( 'add-to-deletion-requests', 'gdpr_deletion_requests_nonce' ); ?>
	<input type="hidden" name="action" value="public_add_to_deletion_requests">
	<input type="submit" style="display: none;">
</form>
<button type="button" class="gdpr-add-to-deletion-requests-button"><?php esc_html_e( 'Close my account', 'gdpr' ); ?></button>
