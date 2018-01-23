<?php

/**
 * The file that defines the core plugin class
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
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      GDPR_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
		$this->define_common_hooks();

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
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-loader.php';

		/**
		 * The class responsible for sending notifications to users.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-notifications.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gdpr-admin.php';

		/**
		 * The class responsible for defining all notices on the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gdpr-admin-notices.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gdpr-public.php';

		$this->loader = new GDPR_Loader();
		$this->notifications = new GDPR_Notification();

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

		$plugin_i18n = new GDPR_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

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
		$plugin_notices = new GDPR_Admin_Notices( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
		$this->loader->add_action( 'publish_page', $plugin_admin, 'check_tos_pp_pages_updated', 10, 2 );
		$this->loader->add_action( 'register_form', $plugin_admin, 'register_form' );
		$this->loader->add_action( 'user_register', $plugin_admin, 'user_register' );
		$this->loader->add_action( 'template_redirect', $plugin_admin, 'forget_user' );
		$this->loader->add_action( 'delete_user', $plugin_admin, 'delete_user' );
		$this->loader->add_action( 'wp_ajax_gdpr_audit_log_email_lookup', $plugin_admin, 'gdpr_audit_log_email_lookup' );
		$this->loader->add_action( 'wp_ajax_gdpr_forget_user', $plugin_admin, 'admin_forget_user' );
		$this->loader->add_action( 'wp_ajax_gdpr_reassign_content', $plugin_admin, 'reassign_content_ajax_callback' );
		$this->loader->add_action( 'wp_ajax_gdpr_ignore_updated_page', $plugin_admin, 'ignore_updated_page' );
		$this->loader->add_action( 'wp_ajax_gdpr_notify_updated_page', $plugin_admin, 'notify_updated_page' );
		$this->loader->add_action( 'wp_ajax_gdpr_send_confirmation_email_data_breach', $plugin_admin, 'send_confirmation_email_data_breach' );
		$this->loader->add_action( 'wp_ajax_gdpr_anonymize_content', $plugin_admin, 'anonymize_content' );

		// Admin Notices

		$options = get_option( $this->plugin_name . '_options' );
		$tos = get_option( $this->plugin_name . '_tos_updated' );
		$pp = get_option( $this->plugin_name . '_pp_updated' );
		if ( empty( $options['tos-page'] ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_notices, 'tos_missing' );
		}
		if ( empty( $options['pp-page'] ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_notices, 'pp_missing' );
		}
		if ( empty( $options['processor-contact-info'] ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_notices, 'processor_contact_missing' );
		}
		if ( $tos && current_user_can( 'manage_options' ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_notices, 'tos_updated' );
		}
		if ( $pp && current_user_can( 'manage_options' ) ) {
			$this->loader->add_action( 'admin_notices', $plugin_notices, 'pp_updated' );
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

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'consent_modal' );
		$this->loader->add_action( 'wp_ajax_disagree_with_terms', $plugin_public, 'logout' );
		$this->loader->add_action( 'wp_ajax_agree_with_terms', $plugin_public, 'agree_with_terms' );

	}

	/**
	 * Register all of the hooks related to both admin and public facing functionality.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_common_hooks() {
		$this->loader->add_action( 'wp_ajax_process_right_to_be_forgotten', $this, 'process_right_to_be_forgotten' );
		$this->loader->add_action( 'wp_ajax_process_right_to_access', $this, 'process_right_to_access' );
		$this->loader->add_action( 'wp_ajax_gdpr_right_to_access_email_lookup', $this, 'process_right_to_access' );
	}

	function process_right_to_be_forgotten() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'request_to_be_forgotten' )) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$user = wp_get_current_user();
		$key = wp_generate_password( 20 );
		update_user_meta( $user->ID, $this->get_plugin_name() . '_delete_key', $key );
		if ( $this->notifications->send( $user, 'forget', array( 'key' => $key, 'user' => $user ) ) ) {
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	function process_right_to_access() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'request_personal_data' )) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$email = ( isset( $_POST['email'] ) ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		$result = $this->generate_xml( $email );
		if ( $result ) {
			wp_send_json_success( $result );
		}

		wp_send_json_error();
	}

	private function generate_xml( $email = '' ) {

		if ( empty( $email ) ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}
			$user = wp_get_current_user();
		} else {
			$user = get_user_by( 'email', $email );
		}

		if ( ! is_a( $user, 'WP_User' ) ) {
			return false;
		}

		$usermeta = get_user_meta( $user->ID );
		$remove_metadata = array(
			'nickname',
			'first_name',
			'last_name',
			'description',
			'rich_editing',
			'syntax_highlighting',
			'comment_shortcuts',
			'admin_color',
			'use_ssl',
			'show_admin_bar_front',
			'wp_capabilities',
			'wp_user_level',
			'gdpr_consents',
			'gdpr_audit_log',
			'dismissed_wp_pointers',
			'gdpr_delete_key',
		);
		$usermeta = array_diff_key( $usermeta, array_flip( $remove_metadata ) );

		$dom = new DomDocument( "1.0", "ISO-8859-1" );
		$personal_info = $dom->createElement('Personal_Information');
		$dom->appendChild( $personal_info );
		$personal_info->appendChild( $dom->createElement( 'Username', $user->user_login ) );
		$personal_info->appendChild( $dom->createElement( 'First_Name', $user->first_name ) );
		$personal_info->appendChild( $dom->createElement( 'Last_Name', $user->last_name ) );
		$personal_info->appendChild( $dom->createElement( 'Email', $user->user_email ) );
		$personal_info->appendChild( $dom->createElement( 'Nickname', $user->nickname ) );
		$personal_info->appendChild( $dom->createElement( 'Display_Name', $user->display_name ) );
		$personal_info->appendChild( $dom->createElement( 'Description', $user->description ) );
		$personal_info->appendChild( $dom->createElement( 'Website', $user->user_url ) );

		$meta_data = $dom->createElement('Meta_Data');
		$dom->appendChild( $meta_data );

		foreach ( $usermeta as $k => $v ) {
			if ( count($v) === 1 ) {
				$key = $dom->createElement( $k, $v[0] );
				$meta_data->appendChild( $key );
			} else {
				$key = $dom->createElement( $k );
				$meta_data->appendChild( $key );
				foreach ( $v as $value ) {
					$key->appendChild( $dom->createElement( 'item', $value ) );
				}
			}
		}

		$consents = $dom->createElement( 'Consents' );
		$dom->appendChild( $consents );
		$gdpr_consents = get_user_meta( $user->ID, 'gdpr_consents', true);
		foreach ( $gdpr_consents as $consent_item ) {
			$consent = $dom->createElement( 'Consent' );
			$consents->appendChild( $consent );
			$consent->appendChild( $dom->createElement( 'Title', $consent_item['title'] ) );
			$consent->appendChild( $dom->createElement( 'Description', $consent_item['description'] ) );
		}


		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		return $dom->saveXML();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    GDPR_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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
