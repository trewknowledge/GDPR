<?php
/**
 * GDPR Logging
 *
 * @package Gdpr
 */

declare( strict_types=1 );

/**
 * Class Gdpr_Logging
 *
 * @package Gdpr
 */
class Gdpr_Logging {

	/**
	 * Track the IP address when the user accepts the Consent Policy.
	 */
	public function track_ip_at_time_of_accepting_consent() : void {

		$msg = sprintf(
			// translators: IP Address.
			__(
				'IP Address at the time of consent: %s',
				'peake-admin'
			),
			\WC_Geolocation::get_ip_address()
		);

		\GDPR_Audit_Log::log( get_current_user_id(), $msg );
	}
}
