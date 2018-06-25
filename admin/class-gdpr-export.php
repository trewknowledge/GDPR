<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Export/import plugin settings
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Woptima <woptima.com@gmail.com>
 */
class GDPR_import_export
{
	private static $options = [
		"gdpr_disable_css",
		"gdpr_use_recaptcha",
		"gdpr_recaptcha_site_key",
		"gdpr_recaptcha_secret_key",
		"gdpr_add_consent_checkboxes_registration",
		"gdpr_add_consent_checkboxes_checkout",
		"gdpr_cookie_popup_content",
		"gdpr_refresh_after_preferences_update",
		"gdpr_enable_privacy_bar",
		"gdpr_display_cookie_categories_in_bar",
		"gdpr_hide_from_bots",
		"gdpr_reconsent_template"
	];

	function __construct() {
		add_action( 'admin_init', [$this, 'GDPR_export_settings'] );
		add_action( 'admin_init', [$this, 'GDPR_import_settings'] );
		add_action( 'admin_footer', [$this, 'footer_scripts'] );
	}
	

	function GDPR_export_settings() {
		
		if(!isset( $_POST['gdpr_action'] )) {
			return;
		}
		if('export_settings' != $_POST['gdpr_action']) {
			return;
		}
		if( !wp_verify_nonce( $_POST['gdpr_export_nonce'], 'gdpr_export_nonce' ) ) {
			return;
		}
		if( !current_user_can( 'manage_options' ) ) {
			return;
		}
		foreach (self::$options as $option) {
			$options[$option] = maybe_unserialize(get_option($option));
		}
		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=GDPR-settings-export-' . date( 'd-m-Y' ) . '.json' );
		header( "Expires: 0" );
		 
		echo json_encode( $options, JSON_NUMERIC_CHECK );
		die();
	}


	function GDPR_import_settings() {
		if(!isset( $_POST['gdpr_action'] )) {
			return;
		}
		if('import_settings' != $_POST['gdpr_action']) {
			return;
		}
		if( !wp_verify_nonce( $_POST['gdpr_import_nonce'], 'gdpr_import_nonce' ) ) {
			return;
		}
		if( !current_user_can( 'manage_options' ) ) {
			return;
		}

		$extension = end( explode( '.', $_FILES['GDPR_settings_file']['name'] ) );
		if( $extension != 'json' ) {
			add_action( 'admin_notices', function() {
				self::place_notice('error','Please upload a valid .json file');
			} );
			return;
		}
		$import_file = $_FILES['GDPR_settings_file']['tmp_name'];
		if( empty( $import_file ) ) {
			add_action( 'admin_notices', function() {
				self::place_notice('error','Please upload a valid .json file');
			} );
			return;
		}
		// Retrieve the settings from the file and convert the json object to an array.
		$options = json_decode( file_get_contents( $import_file ), true );
		
		foreach ($options as $name => $value) {
			update_option($name, $value);
		}
		
		add_action( 'admin_notices', function() {
			self::place_notice('success','Imported settings succefully!');
		} );
	}

	private static function  place_notice($type,$message) {
	    ?>
	    <div class="notice notice-<?php echo $type ?> is-dismissible">
	        <p><?php echo $message ?></p>
	    </div>
	    <?php
	}

}

$GDPS_import_export = new GDPR_import_export();
