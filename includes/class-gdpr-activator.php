<?php

/**
 * Fired during plugin activation
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
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
	 * @since    1.0.0
	 */
	public static function activate() {
		$forget_me = get_option( 'gdpr_forget_me_requests', array() );
		$data_portability = get_option( 'gdpr_data_portability', array() );
		$consents = get_option( 'gdpr_consents', array() );

		if ( empty( $forget_me ) ) {
			update_option( 'gdpr_forget_me_requests', array() );
		}
		if ( empty( $data_portability ) ) {
			update_option( 'gdpr_data_portability_requests', array() );
		}
		if ( empty( $consents ) ) {
			update_option( 'gdpr_consents', array() );
		}
	}

}
