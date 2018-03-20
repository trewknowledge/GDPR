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
 * @subpackage GDPR/admin/partials
 */

include_once plugin_dir_path( __FILE__ ) . 'templates/tmpl-access-data.php';
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

	<div class="tab hidden" data-id="access">
		<h2><?php esc_html_e( 'Access Data', 'gdpr' ) ?></h2>
		<div class="postbox not-full">
			<form class="gdpr-access-data-lookup" method="post">
				<div class="inside">
					<?php wp_nonce_field( 'access-data', 'gdpr_access_data_nonce' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Search by email', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="user_email" class="regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( esc_html__( 'Submit', 'gdpr' ), 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
	</div>

	<div class="tab hidden" data-id="portability">
		<h2><?php esc_html_e( 'Data Portability', 'gdpr' ) ?></h2>

	</div>

<!-- #poststuff -->
</div>
