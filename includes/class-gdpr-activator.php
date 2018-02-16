<?php

/**
 * Fired during plugin activation
 *
 * @link       http://trewknowledge.com
 * @since      0.1.0
 *
 * @package    GDPR
 * @subpackage GDPR/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    GDPR
 * @subpackage GDPR/includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 */
	public static function activate() {
		$options = get_option( 'gdpr_options' );

		if ( ! isset( $options['consents'] ) || empty( $options['consents'] ) ) {
			update_option( 'gdpr_options', array(
				'consents' => array(
					'terms_of_service' => array(
						'title' => esc_html__( 'Terms of Service', 'gdpr' ),
						'description' => '',
					),
					'privacy_policy' => array(
						'title' => esc_html__( 'Privacy Policy', 'gdpr' ),
						'description' => '',
					),
				),
			) );
		}
	}

}
