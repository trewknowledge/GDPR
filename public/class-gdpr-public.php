<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    GDPR
 * @subpackage GDPR/public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of the plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		setcookie('__utma', 'fernando', 0, '/');
		add_action('send_headers', array($this, 'block_cookies'));
		$this->block_cookies();
	}

	function block_cookies() {

		if ( ! isset( $_COOKIE['gdpr_approved_cookies'] ) ) {
			return;
		}

		$approved_cookies = json_decode( stripslashes( $_COOKIE['gdpr_approved_cookies'] ), true );
		foreach( headers_list() as $header ) {
	    if ( preg_match( '/Set-Cookie/', $header ) ) {
	    	$cookie_name = explode('=', $header);
	    	$cookie_name = str_replace( 'Set-Cookie: ', '', $cookie_name[0]);
	    	if ( ! in_array( $cookie_name, $approved_cookies['site_cookies'] ) ) {
		      header_remove( 'Set-Cookie' );
	    	}
	    }
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-public.js', array( 'jquery' ), $this->version, false );
	}

	public function cookie_bar() {
		if ( isset( $_COOKIE['gpdr_cookie_bar_closed'] ) ) { // Input var okay.
			return;
		}

		$content = get_option( 'gdpr_cookie_banner_content', '' );

		if ( empty( $content ) ) {
			return;
		}

		include plugin_dir_path( __FILE__ ) . 'partials/cookie-bar.php';
	}

	public function cookie_preferences() {
		$tabs = get_option( 'gdpr_cookie_popup_content', array() );
		if ( empty( $tabs ) ) {
			return;
		}

		include plugin_dir_path( __FILE__ ) . 'partials/cookie-preferences.php';
	}

}
