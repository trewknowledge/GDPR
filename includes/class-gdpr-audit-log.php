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
 * @subpackage GDPR/includes
 */

/**
 * The Audit Log plugin class.
 *
 * This is used to help us save all interactions from the user regarding consents.
 *
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage GDPR/includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Audit_Log {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	private function crypt( $key, $data ) {
		$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
		$encrypted = openssl_encrypt( $data, 'aes-256-cbc', $key, 0, $iv );
		return base64_encode( $encrypted . '::' . $iv );
	}

	private function decrypt( $key, $data ) {
		list( $encrypted_data, $iv ) = explode( '::', base64_decode( $data ), 2 );
		return openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );
	}

	public function log( $user_id, $input ) {
		$user = get_user_by( 'ID', $user_id );
		$date = '[' . date('Y/m/d H:i:s') . '] ';
		$encrypted = $this->crypt( $user->user_email, $date . $input);
		add_user_meta( $user_id, $this->plugin_name . '_audit_log', $encrypted );
	}

	public function get_log( $email, $token = null ) {
		// Try getting an existing user
		$user = get_user_by( 'email', $email );
		if ( is_a( $user , 'WP_User') ) {
			$user_log = get_user_meta( $user->ID, $this->plugin_name . '_audit_log', false );
			ob_start();
			foreach ( $user_log as $log ) {
				echo $this->decrypt( $email, $log ) . "\n";
			}
			$log = ob_get_clean();
		} else {
			$path = plugin_dir_path( dirname( __FILE__ ) ) . 'logs/';
			$email_masked = $this->email_mask( $email . $token );
			$filename = base64_encode( $email_masked );
			$file_found = file_exists( $path . $filename );
			if ( ! $file_found ) {
				return false;
			} else {
				$log = file_get_contents( $path . $filename );
				return $this->decrypt( $email, $log );
			}
		}

		return $log;
	}

	private function email_mask( $email, $character = '-' ){
		$email_arr = explode( '@', $email, 2 );

		$length = strlen( $email_arr[0] );
		$length = ceil( $length / 2 );
		$username = substr( $email_arr[0], 0, $length ) . str_repeat( $character, $length );

		$length = strlen( $email_arr[1] );
		$length = ceil( $length / 2 );
		$domain = str_repeat( $character, $length ) . substr( $email_arr[1], $length - 1 , $length );

		return $username . '@' . $domain;
	}

	public function export_log( $user_id, $token ) {
		$user = get_user_by( 'ID', $user_id );
		if ( ! is_a( $user , 'WP_User') ) {
			return;
		}

		$path = plugin_dir_path( dirname( __FILE__ ) ) . 'logs/';

		$log = $this->get_log( $user->user_email );
		$filename = $this->email_mask( $user->user_email . $token );
		$filename = base64_encode( $filename );

		file_put_contents( $path . $filename, $this->crypt( $user->user_email, $log ) );
	}

}
