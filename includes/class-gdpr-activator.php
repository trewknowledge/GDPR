<?php

/**
 * Fired during plugin activation
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Activator {

	/**
	 * Runs when the user first activates the plugin.
	 * Sets a CRON jo to clean up the telemetry post type every 12 hours.
	 *
	 * @since  1.0.0
	 * @static
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public static function activate() {
		add_option( 'gdpr_disable_css', false );
		add_option( 'gdpr_enable_telemetry_tracker', false );
		add_option( 'gdpr_use_recaptcha', false );
		add_option( 'gdpr_recaptcha_site_key', '' );
		add_option( 'gdpr_recaptcha_secret_key', '' );

	}

}
