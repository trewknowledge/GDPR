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

	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			$this->plugin_name . '-notices',
			esc_attr( 'settings-updated' ),
			esc_html__( 'Settings Updated', 'gdpr' ),
			'updated'
		);
	}

	$pages = get_pages();
	$options = get_option( 'gdpr_options', array() );
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<?php	settings_errors( $this->plugin_name . '-notices' ); ?>
				<form method="post">
					<?php wp_nonce_field( 'gdpr_options_save' ); ?>
					<div class="postbox">
						<h2 class="hndle"><?php esc_html_e( 'General', 'gdpr' ); ?></h2>
						<div class="inside">
							<table class="form-table">
								<tr>
									<th><?php esc_html_e( 'Privacy Policy Page', 'gdpr' ) ?></th>
									<td>
										<?php $pp_page = ( isset( $options['pp-page'] ) ) ? $options['pp-page'] : ''; ?>
										<select name="gdpr_options[pp-page]">
											<option value=""><?php esc_html_e( 'Select your Privacy Policy Page', 'gdpr' ) ?></option>
											<?php foreach ( $pages as $page ): ?>
												<option value="<?php echo esc_attr( $page->ID ) ?>" <?php selected( $pp_page, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
											<?php endforeach; ?>
										</select>
										<span class="description"><?php esc_html_e( 'If this page is updated, you must notify users and ask for their consent again.', 'gdpr' ) ?></span>
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Terms of Service Page', 'gdpr' ) ?></th>
									<td>
										<?php $tos_page = ( isset( $options['tos-page'] ) ) ? $options['tos-page'] : ''; ?>
										<select name="gdpr_options[tos-page]">
											<option value=""><?php esc_html_e( 'Select your Terms of Service Page', 'gdpr' ) ?></option>
											<?php foreach ( $pages as $page ): ?>
												<option value="<?php echo esc_attr( $page->ID ) ?>" <?php selected( $tos_page, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
											<?php endforeach; ?>
										</select>
										<span class="description"><?php esc_html_e( 'If this page is updated, you must notify users and ask for their consent again.', 'gdpr' ) ?></span>
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Processor Contact Information', 'gdpr' ) ?></th>
									<td>
										<?php $processor_contact_info = ( isset( $options['processor-contact-info'] ) ) ? $options['processor-contact-info'] : ''; ?>
										<input type="email" class="all-options" name="gdpr_options[processor-contact-info]" value="<?php echo esc_attr( $processor_contact_info ); ?>">
										<span class="description"><a href="https://gdpr-info.eu/art-28-gdpr/" target="_blank" rel="nofollow"><?php esc_html_e( 'What is this?', 'gdpr' ); ?></a></span>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<h3><?php esc_html_e( 'Consents', 'gdpr' ); ?></h3>
					<p>You should have at the very least one consent filled for Terms of Service and one for Privacy Policy.</p>
					<p>For every type of data you get from the user, you should have a box filled up.</p>
					<p>This information shows up at the report the user can generate and download through the Right to Access component.</p>
					<p><strong>Example:</strong></p>
					<ul>
						<li>You want to collect their emails for mailing purposes.</li>
						<li>You implement a mailing solution like mailchimp.</li>
						<li>You need to add another consent box below.</li>
						<li>The title could be Email Collection</li>
						<li>The description must be a detailed explanation of why you are getting that data, what do you want to do with it, how long will you keep it and if you are going to share that data with third parties.</li>
					</ul>
					<?php $consents = ( isset( $options['consents'] ) ) ? $options['consents'] : array(); ?>
					<?php if ( ! empty( $consents ) ) : ?>
						<?php $i = 0; ?>
						<?php foreach ( $consents as $consent ) : ?>
							<div class="postbox repeater">
								<div class="inside">
									<table class="form-table">
										<tr>
											<th><?php esc_html_e( 'Consent Title', 'gdpr' ) ?></th>
											<td>
												<input type="text" readonly class="regular-text" value="<?php echo esc_attr( $consent['title'] ) ?>" name="gdpr_options[consents][<?php echo $i; ?>][title]" id="">
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Consent Description', 'gdpr' ) ?></th>
											<td>
												<textarea class="large-text" rows="4" name="gdpr_options[consents][<?php echo $i; ?>][description]"><?php echo esc_html( $consent['description'] ) ?></textarea>
											</td>
										</tr>
									</table>
									<a href="#" class="button delete-consent"><?php esc_html_e( 'Delete', 'gdpr' ); ?></a>
								</div>
							</div>
						<?php $i++; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<a href="#" class="button-primary add-another-consent"><?php esc_html_e( 'Add another consent', 'gdpr' ); ?></a>
					<?php submit_button( 'Save Settings' ); ?>
				</form>
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
