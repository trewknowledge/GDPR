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
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
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
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @var    string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @var    string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
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
		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return;
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for adding help tabs.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-help.php';

		/**
		 * The class responsible logging user actions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-audit-log.php';

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
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 */
	private function set_locale() {

		load_plugin_textdomain(
			'gdpr',
			false,
			plugin_dir_url( dirname( __FILE__ ) ) . 'languages/'
		);

	}

	/**
	 * Register all of the common hooks.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 */
	private function define_common_hooks() {
		add_action( 'init', array( $this, 'block_cookies' ) );
		add_action( 'admin_init', array( $this, 'block_cookies' ) );
		add_action( 'wp_ajax_gdpr_generate_data_export', array( $this, 'export_data' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 */
	private function define_admin_hooks() {

		$plugin_admin   = new GDPR_Admin( $this->get_plugin_name(), $this->get_version() );
		$requests_admin = new GDPR_Requests_Admin( $this->get_plugin_name(), $this->get_version() );
		$telemetry      = new GDPR_Telemetry( $this->get_plugin_name(), $this->get_version() );
		$requests       = new GDPR_Requests( $this->get_plugin_name(), $this->get_version() );
		$plugin_emails  = new GDPR_Email();

		add_action( 'show_user_profile', array( $plugin_admin, 'edit_user_profile' ) );
		add_action( 'personal_options_update', array( $plugin_admin, 'user_profile_update' ) );
		add_action( 'admin_notices', array( $plugin_admin, 'privacy_policy_page_missing' ) );
		add_action( 'admin_notices', array( $plugin_admin, 'privacy_policy_updated_notice' ) );
		add_action( 'wp_ajax_ignore_privacy_policy_update', array( $plugin_admin, 'ignore_privacy_policy_update' ) );
		add_action( 'admin_post_seek_consent', array( $plugin_admin, 'seek_consent' ) );
		add_action( 'publish_page', array( $plugin_admin, 'privacy_policy_updated' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $plugin_admin, 'add_menu' ) );
		add_action( 'admin_init', array( $plugin_admin, 'register_settings' ) );
		add_action( 'register_form', array( $plugin_admin, 'register_form' ) );
		add_action( 'registration_errors', array( $plugin_admin, 'registration_errors' ), 10, 3 );
		add_action( 'user_register', array( $plugin_admin, 'user_register' ) );
		add_action( 'wp_ajax_gdpr_access_data', array( $plugin_admin, 'access_data' ) );
		add_action( 'wp_ajax_gdpr_audit_log', array( $plugin_admin, 'audit_log' ) );
		add_action( 'admin_post_gdpr_data_breach', array( $plugin_admin, 'send_data_breach_confirmation_email' ) );
		add_action( 'clean_gdpr_data_breach_request', array( $plugin_admin, 'clean_data_breach_request' ), 10, 2 ); // CRON JOB
		add_action( 'telemetry_cleanup', array( $plugin_admin, 'telemetry_cleanup' ) ); // CRON JOB

		add_action( 'admin_post_gdpr_delete_user', array( $requests_admin, 'delete_user' ) );
		add_action( 'admin_post_gdpr_cancel_request', array( $requests_admin, 'cancel_request' ) );
		add_action( 'admin_post_gdpr_add_to_deletion_requests', array( $requests_admin, 'add_to_deletion_requests' ) );
		add_action( 'admin_post_gdpr_mark_resolved', array( $requests_admin, 'mark_resolved' ) );
		add_action( 'wp_ajax_gdpr_anonymize_comments', array( $requests_admin, 'anonymize_comments' ) );
		add_action( 'wp_ajax_gdpr_reassign_content', array( $requests_admin, 'reassign_content' ) );

		add_action( 'init', array( $telemetry, 'register_post_type' ) );
		add_filter( 'http_api_debug', array( $telemetry, 'log_request' ), 10, 5 );
		add_filter( 'manage_telemetry_posts_columns', array( $telemetry, 'manage_columns' ) );
		add_filter( 'manage_telemetry_posts_custom_column', array( $telemetry, 'custom_column' ), 10, 2 );
		add_filter( 'restrict_manage_posts', array( $telemetry, 'actions_above_table' ) );
		add_filter( 'views_edit-telemetry', '__return_null' );

		// CRON JOBS
		add_action( 'clean_gdpr_requests', array( $requests, 'clean_requests' ) );
		add_action( 'clean_gdpr_user_request_key', array( $requests, 'clean_user_request_key' ), 10, 2 );

		add_action( 'send_data_breach_emails', array( $plugin_emails, 'send_data_breach_emails' ), 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 */
	private function define_public_hooks() {

		$plugin_public   = new GDPR_Public( $this->get_plugin_name(), $this->get_version() );
		$requests_public = new GDPR_Requests_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $plugin_public, 'overlay' ) );
		add_action( 'wp_footer', array( $plugin_public, 'cookie_bar' ) );
		add_action( 'wp_footer', array( $plugin_public, 'cookie_preferences' ) );
		add_action( 'wp_footer', array( $plugin_public, 'consents_preferences' ) );
		add_action( 'wp_footer', array( $plugin_public, 'confirmation_screens' ) );
		add_action( 'wp_footer', array( $plugin_public, 'is_consent_needed' ) );
		add_action( 'wp_ajax_update_consents', array( $plugin_public, 'update_consents' ) );
		add_action( 'wp_ajax_disagree_with_terms', array( $plugin_public, 'logout' ) );
		add_action( 'wp_ajax_agree_with_terms', array( $plugin_public, 'agree_with_terms' ) );

		add_action( 'wp', array( $requests_public, 'request_confirmed' ) );
		add_action( 'admin_post_gdpr_send_request_email', array( $requests_public, 'send_request_email' ) );
		add_action( 'admin_post_nopriv_gdpr_send_request_email', array( $requests_public, 'send_request_email' ) );
		add_action( 'mail_export_data', array( $requests_public, 'mail_export_data' ), 10, 3 ); // CRON JOB
	}

	/**
	 * Block cookies created by through that are not on the list of allowed cookies.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	function block_cookies() {
		$approved_cookies = array();
		if ( isset( $_COOKIE['gdpr_approved_cookies'] ) ) {
			$approved_cookies = json_decode( sanitize_key( wp_unslash( $_COOKIE['gdpr_approved_cookies'] ) ) );
		}

		foreach ( headers_list() as $header ) {
			if ( preg_match( '/Set-Cookie/', $header ) ) {
				$cookie_name = explode( '=', $header );
				$cookie_name = str_replace( 'Set-Cookie: ', '', $cookie_name[0] );
				if ( ! in_array( $cookie_name, $approved_cookies, true ) ) {
					header_remove( 'Set-Cookie' );
				}
			}
		}
	}

	/**
	 * Get user meta for exporting.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  int   $user_id  The user ID.
	 * @return array           The user meta minus not important metas.
	 */
	static function get_user_meta( $user_id ) {
		$usermeta = get_user_meta( $user_id );

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
			'gdpr_rectify_key',
			'gdpr_complaint_key',
			'gdpr_export-data_key',
		);

		return array_diff_key( $usermeta, array_flip( $remove_metadata ) );
	}

	/**
	 * Generates the export in JSON or XML formats.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  string $email  The user email.
	 * @param  string $format Either XML or JSON.
	 * @return string         Returns the file as string.
	 */
	static function generate_export( $email, $format ) {

		$email = sanitize_email( $email );
		$user  = get_user_by( 'email', $email );

		if ( ! $user ) {
			return false;
		}

		$usermeta      = self::get_user_meta( $user->ID );
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );
		$extra_content = apply_filters( 'export_data_extra_tables', '', $email );

		switch ( strtolower( $format ) ) {
			case 'json':
				$metadata = array();

				foreach ( $usermeta as $k => $v ) {
					$metadata[ $k ] = array();
					foreach ( $v as $value ) {
						if ( is_serialized( $value ) ) {
							$metadata[ $k ][] = maybe_unserialize( $value );
						} else {
							$metadata[ $k ] = $value;
						}
					}
				}

				$json = array(
					'Personal Information' => array(
						'Username'     => $user->user_login,
						'First name'   => $user->first_name,
						'Last name'    => $user->last_name,
						'Email'        => $user->user_email,
						'Nickname'     => $user->nickname,
						'Display name' => $user->display_name,
						'Description'  => $user->description,
						'Website'      => $user->user_url,
					),
					'Consents'             => $user_consents,
					'Metadata'             => $metadata,
				);

				if ( $extra_content ) {
					$json[ $extra_content['name'] ] = $extra_content['content'];
				}

				return json_encode( $json );

			case 'md':
			case 'markdown':
				break;

			default: // XML
				$dom = new DomDocument( '1.0', 'ISO-8859-1' );

				$personal_info = $dom->createElement( 'Personal_Information' );

				$dom->appendChild( $personal_info );
				$personal_info->appendChild( $dom->createElement( 'Username', $user->user_login ) );
				$personal_info->appendChild( $dom->createElement( 'First_Name', $user->first_name ) );
				$personal_info->appendChild( $dom->createElement( 'Last_Name', $user->last_name ) );
				$personal_info->appendChild( $dom->createElement( 'Email', $user->user_email ) );
				$personal_info->appendChild( $dom->createElement( 'Nickname', $user->nickname ) );
				$personal_info->appendChild( $dom->createElement( 'Display_Name', $user->display_name ) );
				$personal_info->appendChild( $dom->createElement( 'Description', $user->description ) );
				$personal_info->appendChild( $dom->createElement( 'Website', $user->user_url ) );

				if ( ! empty( $user_consents ) ) {
					$consents = $dom->createElement( 'Consents' );
					$dom->appendChild( $consents );
					foreach ( $user_consents as $consent_item ) {
						$consents->appendChild( $dom->createElement( 'consent', $consent_item ) );
					}
				}

				$meta_data = $dom->createElement( 'Metadata' );
				$dom->appendChild( $meta_data );

				foreach ( $usermeta as $k => $v ) {
					$key = $dom->createElement( $k );
					$meta_data->appendChild( $key );
					foreach ( $v as $value ) {
						$key->appendChild( $dom->createElement( 'item', $value ) );
					}
				}

				if ( $extra_content ) {
					$extra = $dom->createElement( $extra_content['name'] );
					$dom->appendChild( $extra );
					foreach ( $extra_content['content'] as $key => $obj ) {
						$item = $extra->appendChild( $dom->createElement( 'item' ) );
						foreach ( $obj as $k => $value ) {
							$item->appendChild( $dom->createElement( $k, maybe_serialize( $value ) ) );
						}
					}
				}

				$dom->preserveWhiteSpace = false;
				$dom->formatOutput       = true;

				return $dom->saveXML();
		}

		return false;

	}

	/**
	 * Export the generated export file.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	function export_data() {
		if ( ! isset( $_POST['nonce'], $_POST['email'], $_POST['type'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'export-data' ) ) {
			wp_send_json_error();
		}

		$type  = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		$email = sanitize_email( wp_unslash( $_POST['email'] ) );
		$user  = get_user_by( 'email', $email );

		if ( $user ) {
			$export = self::generate_export( $email, $type );
			if ( $export ) {
				wp_send_json_success( $export );
			}
		}

		wp_send_json_error();
	}

	/**
	 * Generates a random 6 digit pin.
	 * This pin is necessary to use with the audit log files.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  integer $length Number of digits.
	 * @return string          Returns the generated pin
	 */
	public static function generate_pin( $length = 6 ) {
		$bytes = openssl_random_pseudo_bytes( $length / 2 );
		return strtoupper( bin2hex( $bytes ) );
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @return string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
