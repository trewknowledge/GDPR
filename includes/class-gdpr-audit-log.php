<?php

/**
 * The file that defines the Audit Log component
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The Audit Log plugin class.
 *
 * This is used to help us save all interactions from the user regarding consents.
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Audit_Log {

	/**
	 * Encrypts a string.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @param  string $key  The encryption key.
	 * @param  string $data The data to be encrypted.
	 * @return string       The encrypted string.
	 */
	private static function crypt( $key, $data ) {
		$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
		$encrypted = openssl_encrypt( $data, 'aes-256-cbc', $key, 0, $iv );
		return base64_encode( $encrypted . '::' . $iv );
	}

	/**
	 * Decrypts a string.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @param  string $key  The encryption key.
	 * @param  string $data The data to be decrypted.
	 * @return string       The decrypted string.
	 */
	private static function decrypt( $key, $data ) {
		list( $encrypted_data, $iv ) = explode( '::', base64_decode( $data ), 2 );
		return openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );
	}

	/**
	 * Logs something to our audit log.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  int    $user_id The user ID.
	 * @param  string $input   The string to be logged.
	 */
	public static function log( $user_id, $input ) {
		$user = get_user_by( 'ID', $user_id );
		$date = '[' . date('Y/m/d H:i:s') . '] ';
		$encrypted = self::crypt( $user->user_email, $date . $input);
		add_user_meta( $user_id, 'gdpr_audit_log', $encrypted );
	}

	/**
	 * Returns the existing logs for an email.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  string $email The data subject email.
	 * @param  string $token A 6 digit token that is provided on user deletion.
	 * @return string        The decrypted log.
	 */
	public static function get_log( $email, $token = null ) {
		// Try getting an existing user
		$user = get_user_by( 'email', $email );
		if ( $user instanceof WP_User ) {
			$user_log = get_user_meta( $user->ID, 'gdpr_audit_log', false );
			ob_start();
			foreach ( $user_log as $log ) {
				echo self::decrypt( $email, $log ) . "\n";
			}
			$log = ob_get_clean();
		} else {
			$uploads_dir = wp_upload_dir();
			$basedir = $uploads_dir['basedir'];
			$path = $basedir . '/gdpr_logs/';
			$email_masked = self::email_mask( $email . $token );
			$filename = base64_encode( $email_masked );
			$file_found = file_exists( $path . $filename );
			if ( ! $file_found ) {
				return false;
			} else {
				$log = file_get_contents( $path . $filename );
				return self::decrypt( $email, $log );
			}
		}

		return $log;
	}

	/**
	 * Mask the email so it's not identifiable.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @param  string $email     The email to mask.
	 * @param  string $character The character that will replace letters.
	 * @return string            The masked email.
	 */
	private static function email_mask( $email, $character = '-' ){
		$email_arr = explode( '@', $email, 2 );

		$length = strlen( $email_arr[0] );
		$suplement = ( 0 !== $length % 2) ? 1 : 0;
		$length = floor( $length / 2 );
		$username = substr( $email_arr[0], 0, $length ) . str_repeat( $character, $length + $suplement );

		$length = strlen( $email_arr[1] );
		$suplement = ( 0 !== $length % 2) ? 1 : 0;
		$length = floor( $length / 2 );
		$domain = str_repeat( $character, $length + $suplement ) . substr( $email_arr[1], -$length, $length );

		return $username . '@' . $domain;
	}

	/**
	 * Exports the user audit log to a file.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  int    $user_id The user ID
	 * @param  string $token   The 6 digit token the user gets on deletion.
	 */
	public static function export_log( $user_id, $token ) {
		$user = get_user_by( 'ID', $user_id );
		if ( ! $user instanceof WP_User ) {
			return;
		}

		$uploads_dir = wp_upload_dir();
		$basedir = $uploads_dir['basedir'];
		$path = $basedir . '/gdpr_logs/';

		if ( wp_mkdir_p( $path ) ) {
			if ( ! file_exists( $path . 'index.php' ) ) {
				file_put_contents( $path . 'index.php', '' );
			}
			$log = self::get_log( $user->user_email );
			$filename = self::email_mask( $user->user_email . $token );
			$filename = base64_encode( $filename );

			file_put_contents( $path . $filename, self::crypt( $user->user_email, $log ) );
		}

	}

}
