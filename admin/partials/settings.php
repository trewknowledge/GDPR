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

// check user capabilities
// if ( ! current_user_can( 'manage_options' ) ) {
// 	return;
// }
?>

<div class="wrap">
	<div class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab => $value ) : ?>
			<a href="<?php echo esc_url( admin_url( '/admin.php?page=gdpr-settings&tab=' . $tab ) ); ?>" class="nav-tab <?php echo ( $current_tab === $tab ) ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $value['name'] ); ?></a>
		<?php endforeach; ?>
	</div>

	<form action="options.php" method="post" class="gdpr-settings-form">
		<?php
		$current_tab = $tabs[ $current_tab ]['page'];

		?>


		<?php
		include_once plugin_dir_path( __FILE__ ) . 'templates/tmpl-cookies.php';
		settings_fields( 'gdpr' );
		do_settings_sections( $current_tab );
		submit_button();
		?>
	</form>

<!-- #poststuff -->
</div>
