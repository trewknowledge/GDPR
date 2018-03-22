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

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Settings', 'gdpr' ); ?></h1>
	<div class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab => $value ) : ?>
			<a href="<?php echo esc_url( admin_url( '/admin.php?page=gdpr-settings&tab=' . $tab ) ); ?>" class="nav-tab <?php echo ( $current_tab === $tab ) ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $value ); ?></a>
		<?php endforeach; ?>
	</div>

	<?php settings_errors(); ?>

	<form action="options.php" method="post" class="gdpr-settings-form">

		<?php
		settings_fields( 'gdpr' );
		do_settings_sections( 'gdpr-settings' );
		submit_button();
		?>
	</form>

<!-- #poststuff -->
</div>
