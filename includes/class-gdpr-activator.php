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
		if( -1 === version_compare( phpversion(), GDPR_REQUIRED_PHP_VERSION ) ) {
			die(sprintf( esc_html__( 'Your current PHP version (%1$s) is below the plugin required version of %2$s.', 'gdpr' ), phpversion(), GDPR_REQUIRED_PHP_VERSION ));
		}

		add_option( 'gdpr_disable_css', false );
		add_option( 'gdpr_enable_telemetry_tracker', false );
		add_option( 'gdpr_use_recaptcha', false );
		add_option( 'gdpr_recaptcha_site_key', '' );
		add_option( 'gdpr_recaptcha_secret_key', '' );
		add_option( 'gdpr_add_consent_checkboxes_registration', true );
		add_option( 'gdpr_add_consent_checkboxes_checkout', true );
		add_option(
			'gdpr_cookie_popup_content', array(
				'necessary'   => array(
					'name'         => 'Necessary',
					'status'       => 'required',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
				'advertising' => array(
					'name'         => 'Advertising',
					'status'       => 'on',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
				'analytics'   => array(
					'name'         => 'Analytics',
					'status'       => 'on',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
				'other'       => array(
					'name'         => 'Other',
					'status'       => 'on',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
			)
		);
		add_option( 'gdpr_refresh_after_preferences_update', true );
		add_option( 'gdpr_enable_privacy_bar', true );
		add_option( 'gdpr_display_cookie_categories_in_bar', false );
		add_option( 'gdpr_hide_from_bots', true );
		add_option( 'gdpr_reconsent_template', 'modal' );
	}

}
