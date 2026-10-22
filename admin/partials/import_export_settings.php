<?php

$import_msg = '';
if ( ! empty ( $_GET['settings-imported'] ) && $_GET['settings-imported'] == 1 ) :
	$import_msg = 'All settings imported successfully!';
endif;

?>
<div class="wrap gdpr">
	<h1><?php esc_html_e( 'Export/Import', 'gdpr' ); ?></h1>
	<?php settings_errors(); ?>
	<div class="nav-tab-wrapper">
		<a href="<?php echo esc_url( '#export' ); ?>" class="nav-tab"><?php echo esc_html__( 'Export', 'gdpr' ); ?></a>
		<a href="<?php echo esc_url( '#import' ); ?>" class="nav-tab"><?php echo esc_html__( 'Import', 'gdpr' ); ?></a>
	</div>

	<div class="gdpr-tab hidden" data-id="export">
		<h2><?php esc_html_e( 'Export Settings', 'gdpr' ); ?></h2>
		<div class="inside">
			<?php wp_nonce_field( 'gdpr-export-settings', 'gdpr_export_settings_nonce' ); ?>
			<textarea id="gdpr_settings_data" name="export_settings" class="large-text" rows="30"><?php echo $gdpr_settings_data; ?></textarea>
			<button class="button button-primary copy-settings"><?php esc_html_e( 'Copy', 'gdpr' ); ?></button>
			<span class="spinner"></span>
		</div>
	</div>

	<div class="gdpr-tab hidden" data-id="import">
		<h2><?php esc_html_e( 'Import', 'gdpr' ); ?></h2>
		<div class="gdpr-import-msg"><?php echo esc_html( $import_msg ); ?></div>
		<form class="gdpr-import-settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'gdpr-import-settings', 'gdpr_settings_import_nonce' ); ?>
			<input type="hidden" name="action" value="gdpr_import_settings">
			<textarea id="import-settings" name="import_settings" class="large-text" rows="30"></textarea>
			<?php submit_button( esc_html__( 'Import', 'gdpr' ), 'primary gdpr-import', '', false ); ?>
		</form>
	</div>
<!-- #poststuff -->
</div>

