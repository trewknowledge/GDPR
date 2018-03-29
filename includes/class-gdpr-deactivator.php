<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Deactivator {

	/**
	 * Runs when the user deactivates the plugin.
	 * Clears the CRON job that deletes telemetry posts every 12 hours.
	 *
	 * @since  1.0.0
	 * @static
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook('telemetry_cleanup');
	}

}
