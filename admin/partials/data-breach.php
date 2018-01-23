<?php
	/**
	 * Provide a admin area view for the plugin
	 *
	 * This file is used to markup the admin-facing aspects of the plugin.
	 *
	 * @link       http://trewknowledge.com
	 * @since      1.0.0
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
					<div class="inside">
						<p>By submitting this form we will send a confirmation email. If confirmed, we will log this event and generate a list with the email of all users.</br>
						These emails should be added to a mailing service and used only to send the data breach notification as required by law.</br>
						On this email, you should add the same type of information you are adding to these fields.</p>
						<form class="gdpr-data-breach-email" method="post" action="">
							<?php wp_nonce_field( 'gdpr-data-breach-request', '_gdpr_data_breach' ); ?>
							<table class="form-table">
								<tr>
									<th><?php esc_html_e( 'Nature of the personal data breach', 'gdpr' ) ?></th>
									<td>
										<textarea name="gdpr-data-breach-nature" class="large-text" rows="5" required></textarea>
										<span class="description"><?php esc_html_e( 'Describe the nature of the personal data breach including where possible, the categories and approximate number of data subjects concerned and the categories and approximate number of personal data records concerned.', 'gdpr' ) ?></span>
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Name and contact details of the data protection officer', 'gdpr' ) ?></th>
									<td>
										<textarea name="gdpr-name-contact-details-protection-officer" class="large-text" rows="5" required></textarea>
										<span class="description"><?php esc_html_e( 'Communicate the name and contact details of the data protection officer or other contact point where more information can be obtained.', 'gdpr' ) ?></span>
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Likely consequences of the personal data breach', 'gdpr' ) ?></th>
									<td>
										<textarea name="gdpr-likely-consequences" class="large-text" rows="5" required></textarea>
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Measures taken or proposed to be taken', 'gdpr' ) ?></th>
									<td>
										<textarea name="gdpr-measures-taken" class="large-text" rows="5" required></textarea>
										<span class="description"><?php esc_html_e( 'Describe the measures taken or proposed to be taken by the controller to address the personal data breach, including, where appropriate, measures to mitigate its possible adverse effects.', 'gdpr' ) ?></span>
									</td>
								</tr>
							</table>
							<?php submit_button( 'Send', 'primary', '', false ); ?>
						</form>
					</div>
				</div>
				<div class="postbox gdpr-audit-log-result">
					<h2><?php echo _e( 'Result', 'gdpr' ); ?></h2>
					<div class="inside">
						<textarea readonly class="gdpr-audit-log-result large-text" rows="20"></textarea>
						<button class="button-primary"><?php esc_html_e( 'Print', 'gdpr' ); ?></button>
					</div>
				</div>
				<div class="postbox gdpr-audit-log-error">
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
