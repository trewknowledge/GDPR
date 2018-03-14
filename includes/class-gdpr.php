<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage GDPR/includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR {

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
	public function __construct() {
		if ( defined( 'GDPR_VERSION' ) ) {
			$this->version = GDPR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'gdpr';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) OR ( defined( 'DOING_CRON' ) && DOING_CRON ) OR ( defined( 'DOING_AJAX' ) && DOING_AJAX ) OR ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return;
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - GDPR_Loader. Orchestrates the hooks of the plugin.
	 * - GDPR_i18n. Defines internationalization functionality.
	 * - GDPR_Admin. Defines all hooks for the admin area.
	 * - GDPR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining the telemetry post type.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gdpr-telemetry.php';

		/**
		 * The class responsible for defining the requests section of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-requests.php';

		/**
		 * The class responsible for defining the admin facing requests section of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gdpr-requests-admin.php';

		/**
		 * The class responsible for defining the admin facing requests section of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gdpr-requests-public.php';

		/**
		 * The class responsible for locating the email templates and sending emails.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-email.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gdpr-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gdpr-public.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the GDPR_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		load_plugin_textdomain(
			'gdpr',
			false,
			plugin_dir_url( dirname( __FILE__ ) ) . 'languages/'
		);

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new GDPR_Admin( $this->get_plugin_name(), $this->get_version() );
		$telemetry = new GDPR_Telemetry( $this->get_plugin_name(), $this->get_version() );
		$requests_admin = new GDPR_Requests_Admin( $this->get_plugin_name(), $this->get_version() );
		$requests_public = new GDPR_Requests_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $plugin_admin, 'add_menu' ) );
		add_action( 'admin_init', array( $plugin_admin, 'register_settings' ) );
		add_action( 'init', array( $this, 'block_cookies' ) );
		add_action( 'admin_init', array( $this, 'block_cookies' ) );

		add_action( 'admin_post_delete_user', array( $requests_admin, 'delete_user' ) );
		add_action( 'admin_post_cancel_request', array( $requests_admin, 'cancel_request' ) );
		add_action( 'admin_post_add_to_deletion_requests', array( $requests_admin, 'add_to_deletion_requests' ) );
		add_action( 'wp_ajax_gdpr_anonymize_comments', array( $requests_admin, 'anonymize_comments' ) );
		add_action( 'wp_ajax_gdpr_reassign_content', array( $requests_admin, 'reassign_content' ) );

		add_action( 'wp', array( $requests_public, 'request_to_delete_confirmed' ) );
		add_action( 'admin_post_send_deletion_request_email_confirmation', array( $requests_public, 'send_deletion_request_email_confirmation' ) );
		add_action( 'admin_post_nopriv_send_deletion_request_email_confirmation', array( $requests_public, 'send_deletion_request_email_confirmation' ) );


		add_action( 'init', array( $telemetry, 'register_post_type' ) );
		add_filter( 'http_api_debug', array( $telemetry, 'log_request' ), 10, 5 );
		add_filter( 'manage_telemetry_posts_columns', array( $telemetry, 'manage_columns' ) );
		add_filter( 'manage_telemetry_posts_custom_column', array( $telemetry, 'custom_column' ), 10, 2 );
		add_filter( 'restrict_manage_posts', array( $telemetry, 'actions_above_table' ) );
		add_filter( 'views_edit-telemetry', '__return_null' );

	}

	function block_cookies() {
		$approved_cookies = ( isset( $_COOKIE['gdpr_approved_cookies'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr_approved_cookies'] ), true ) : array() );
		if ( ! in_array( 'comment_author', $approved_cookies ) ) {
			remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' );
		}
		foreach( headers_list() as $header ) {
	    if ( preg_match( '/Set-Cookie/', $header ) ) {
	    	$cookie_name = explode('=', $header);
	    	$cookie_name = str_replace( 'Set-Cookie: ', '', $cookie_name[0] );
	    	if ( ! in_array( $cookie_name, $approved_cookies ) ) {
		      header_remove( 'Set-Cookie' );
	    	}
	    }
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new GDPR_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $plugin_public, 'cookie_bar' ) );
		add_action( 'wp_footer', array( $plugin_public, 'cookie_preferences' ) );
		add_action( 'wp_footer', array( $plugin_public, 'confirmation_screens' ) );

	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
