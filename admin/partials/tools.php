<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage admin/partials
 */

include_once plugin_dir_path( __FILE__ ) . 'templates/tmpl-tools.php';

if ( isset( $_GET['type'], $_GET['key'] ) ) {

	if ( 'data-breach-confirmed' === $_GET['type'] ) {
		$key = sanitize_text_field( wp_unslash( $_GET['key'] ) );

		$data_breach = get_option( 'gdpr_data_breach_initiated', array( 'key' => '' ) );
		if ( ! empty( $data_breach ) ) {
			if ( $key === $data_breach['key'] ) {
				GDPR_Email::prepare_data_breach_emails( $key );
				delete_option( 'gdpr_data_breach_initiated' );

				if ( $time = wp_next_scheduled( 'clean_gdpr_data_breach_request' ) ) {
					wp_unschedule_event( $time, 'clean_gdpr_data_breach_request' );
				}

				add_settings_error( 'gdpr', 'resolved', esc_html__( 'Data Breach confirmed. Preparing bulk emails.', 'gdpr' ), 'updated' );
		}

	}


	}
}

?>

<div class="wrap gdpr">
	<h1><?php esc_html_e( 'Tools', 'gdpr' ); ?></h1>
	<?php settings_errors(); ?>
	<div class="nav-tab-wrapper">
		<?php foreach ( $tabs as $key => $value ) : ?>
			<a href="<?php echo '#' . $key; ?>" class="nav-tab">
				<?php echo esc_html( $value ); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<div class="gdpr-tab hidden" data-id="access">
		<h2><?php esc_html_e( 'Access Data', 'gdpr' ) ?></h2>
		<div class="postbox not-full">
			<form class="gdpr-access-data-lookup" method="post">
				<div class="inside">
					<?php wp_nonce_field( 'gdpr-access-data', 'gdpr_access_data_nonce' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Search by email', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="user_email" class="regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( esc_html__( 'Search', 'gdpr' ), 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
	</div>

	<div class="gdpr-tab hidden" data-id="data-breach">
		<h2><?php esc_html_e( 'Data Breach', 'gdpr' ) ?></h2>
		<form class="gdpr-data-breach-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
			<?php wp_nonce_field( 'gdpr-data-breach', 'gdpr_data_breach_nonce' ); ?>
			<input type="hidden" name="action" value="gdpr_data_breach">
			<table class="form-table">
				<tr>
					<th><?php esc_html_e( 'Email content', 'gdpr' ) ?></th>
					<td>
						<textarea name="gdpr-data-breach-email-content" class="large-text" rows="5"></textarea>
						<span class="description"><?php esc_html_e( 'The content that the end user will see before the below information.', 'gdpr' ) ?></span>
					</td>
				</tr>
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
			<?php submit_button( esc_html__( 'Send confirmation email', 'gdpr' ), 'primary', '', false ); ?>
		</form>
	</div>

	<div class="gdpr-tab hidden" data-id="audit-log">
		<h2><?php esc_html_e( 'Audit Log', 'gdpr' ) ?></h2>
		<div class="postbox not-full">
			<form class="gdpr-audit-log-lookup" method="post">
				<div class="inside">
					<?php wp_nonce_field( 'gdpr-audit-log', 'gdpr_audit_log_nonce' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Search by email', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="user_email" class="regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<input type="text" name="token" placeholder="<?php esc_attr_e( '6 digit token (optional)', 'gdpr' ); ?>">
					<?php submit_button( esc_html__( 'Search', 'gdpr' ), 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
	</div>

<!-- #poststuff -->
</div>
