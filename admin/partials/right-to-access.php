<?php
	/**
	 * Provide a admin area view for the plugin
	 *
	 * This file is used to markup the admin-facing aspects of the plugin.
	 *
	 * @link       http://trewknowledge.com
	 * @since      0.1.0
	 *
	 * @package    GDPR
	 * @subpackage GDPR/admin/partials
	 */

	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="postbox">
					<h2><?php echo _e( 'Email Lookup', 'gdpr' ); ?></h2>
					<div class="inside">
						<form class="gdpr-right-to-access-email-lookup" method="post" action="">
							<?php wp_nonce_field( 'request_personal_data', '_gdpr_email_lookup' ); ?>
							<input type="email" name="email" class="regular-text" id="gdpr-email-lookup-field" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
							<span class="spinner"></span>
							<?php submit_button( 'Submit', 'primary', '', false ); ?>
						</form>
					</div>
				</div>
				<div class="postbox gdpr-right-to-access-result">
					<h2><?php echo _e( 'Result', 'gdpr' ); ?></h2>
					<div class="inside">
						<textarea readonly class="gdpr-right-to-access-result large-text" rows="20"></textarea>
						<a href="#" class="button-primary download-data"><?php esc_html_e( 'Download', 'gdpr' ); ?></a>
					</div>
				</div>
				<div class="postbox gdpr-right-to-access-error">
					<h2><?php echo _e( 'Error', 'gdpr' ); ?></h2>
					<div class="inside">
						<p><?php esc_html_e( 'We could not find a user with that email.', 'gdpr' ); ?></p>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h2>About the plugin</h2>
					<div class="inside">
						Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aspernatur eius sit itaque vitae molestiae nostrum rerum tenetur ex veniam incidunt laudantium, in earum odio, maxime molestias. Id, libero. Saepe, ipsum.
					</div>
				</div>
				<div class="postbox">
					<h2>Resources & Reference</h2>
					<div class="inside">
						<ul>
							<li><a href="#">What is GDPR?</a></li>
							<li><a href="#">Why should I care?</a></li>
							<li><a href="#">How can I be compliant?</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
