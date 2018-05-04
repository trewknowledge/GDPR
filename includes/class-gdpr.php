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
		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) OR ( defined( 'DOING_CRON' ) && DOING_CRON ) OR ( defined( 'DOING_AJAX' ) && DOING_AJAX ) OR ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
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
	 */
	public function set_locale() {

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

		add_filter( 'nonce_user_logged_out', array( $this, 'woo_nonce_fix' ), 100, 2 );
		add_action( 'plugins_loaded', array( $this, 'set_locale' ) );
		add_action( 'bp_account_details_fields', array( __CLASS__, 'consent_checkboxes' ) );
		add_action( 'woocommerce_register_form', array( __CLASS__, 'consent_checkboxes' ) );
		add_action( 'woocommerce_checkout_update_user_meta', array( $plugin_admin, 'woocommerce_checkout_save_consent' ), 10, 2 );
		add_filter( 'woocommerce_checkout_fields', array( $plugin_admin, 'woocommerce_consent_checkboxes' ) );
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
		add_action( 'register_form', array( __CLASS__, 'consent_checkboxes' ) );
		add_action( 'registration_errors', array( $plugin_admin, 'registration_errors' ), 10, 3 );
		add_action( 'user_register', array( __CLASS__, 'save_user_consent_on_registration' ) );
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
	 * Fixes nonce manipulation made by Woocommerce.
	 * @param  int $user_id The user id.
	 * @param  string $action  The nonce Action.
	 * @return int          The user id.
	 */
	function woo_nonce_fix( $user_id, $action ) {
		if ( ( 0 !== $user_id ) && $action && ( false !== strpos( $action, 'gdpr-' ) ) ) {
			$user_id = 0;
		}

		return $user_id;
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

		add_action( 'wp_enqueue_scripts',                                array( $plugin_public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts',                                array( $plugin_public, 'enqueue_scripts' ) );
		add_action( 'init',                                              array( $plugin_public, 'set_plugin_cookies' ) );
		add_action( 'wp_footer',                                         array( $plugin_public, 'overlay' ) );
		add_action( 'wp_footer',                                         array( $plugin_public, 'privacy_bar' ) );
		add_action( 'wp_footer',                                         array( $plugin_public, 'privacy_preferences_modal' ) );
		add_action( 'wp_footer',                                         array( $plugin_public, 'confirmation_screens' ) );
		add_action( 'wp_footer',                                         array( $plugin_public, 'is_consent_needed' ) );
		add_action( 'wp_ajax_disagree_with_terms',                       array( $plugin_public, 'logout' ) );
		add_action( 'wp_ajax_agree_with_terms',                          array( $plugin_public, 'agree_with_terms' ) );
		add_action( 'admin_post_gdpr_update_privacy_preferences',        array( $plugin_public, 'update_privacy_preferences' ) );
		add_action( 'admin_post_nopriv_gdpr_update_privacy_preferences', array( $plugin_public, 'update_privacy_preferences' ) );

		add_action( 'wp', array( $requests_public, 'request_confirmed' ) );
		add_action( 'admin_post_gdpr_send_request_email', array( $requests_public, 'send_request_email' ) );
		add_action( 'admin_post_nopriv_gdpr_send_request_email', array( $requests_public, 'send_request_email' ) );
	}

	/**
	 * Save the extra fields on a successful registration.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  int $user_id The user ID.
	 */
	public static function save_user_consent_on_registration( $user_id ) {
		GDPR_Audit_Log::log( $user_id, esc_html__( 'User registered to the site.', 'gdpr' ) );

		if ( isset( $_POST['user_consents'] ) ) {

			$consents = array_map( 'sanitize_text_field', array_keys( $_POST['user_consents'] ) );
			foreach ( $consents as $consent ) {
				/* translators: Name of consent */
				GDPR_Audit_Log::log( $user_id, sprintf( esc_html__( 'User gave explicit consent to %s', 'gdpr' ), $consent ) );
				add_user_meta( $user_id, 'gdpr_consents', $consent );
			}
			setcookie( "gdpr[consent_types]", json_encode( $consents ), time() + YEAR_IN_SECONDS, "/" );
		}
	}

	/**
	 * Returns the consent checkboxes to be used across the site.
	 * @since  1.2.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public static function get_consent_checkboxes() {
		$consent_types = get_option( 'gdpr_consent_types', array() );
		$sent_extras = ( isset( $_POST['user_consents'] ) ) ? $_POST['user_consents'] : array();
		$allowed_html = array(
			'a' => array(
				'href' => true,
				'title' => true,
				'target' => true,
			),
		);

		ob_start();
		foreach ( $consent_types as $key => $consent ) {
			$required = ( isset( $consent['required'] ) && $consent['required'] ) ? 'required' : '';
			$checked = ( isset( $sent_extras[ $key ] ) ) ? checked( $sent_extras[ $key ], 1, false ) : '';
			echo '<p>' .
				'<input type="checkbox" name="user_consents[' . esc_attr( $key ) . ']" id="' . esc_attr( $key ) . '-consent" value="1" ' . $required . ' ' . $checked . '>' .
				'<label for="' . esc_attr( $key ) . '-consent">' . wp_kses( $consent['registration'], $allowed_html ) . '</label>' .
			'</p>';
		}

		return ob_get_clean();
	}

	/**
	 * Renders consent checkboxes to be used across the site.
	 * @since  1.1.4
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public static function consent_checkboxes() {
		echo self::get_consent_checkboxes();
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
		$usermeta        = get_user_meta( $user_id );
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

		if ( ! $user instanceof WP_User ) {
			return false;
		}

		$usermeta      = self::get_user_meta( $user->ID );
		$comments      = get_comments( array(
			'author_email'       => $user->user_email,
			'include_unapproved' => true,
		) );
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );
		$extra_content = apply_filters( 'gdpr_export_data_extra_tables', '', $email );

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

				$comments_array = array();
				if ( ! empty( $comments ) ) {
					foreach ( $comments as $k => $v ) {
						$comments_array[ $k ] = array(
							'comment_author' => $v->comment_author,
							'comment_author_email' => $v->comment_author_email,
							'comment_author_url' => $v->comment_author_url,
							'comment_author_IP' => $v->comment_author_IP,
							'comment_date' => $v->comment_date,
							'comment_agent' => $v->comment_agent,
							'comment_content' => $v->comment_content,
						);
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
					'Comments'             => $comments_array,
				);

				if ( $extra_content ) {
					$json[ $extra_content['name'] ] = $extra_content['content'];
				}
				return json_encode( $json );
				break;
			case 'md':
			case 'markdown':
				# code...
				break;

			default: // XML
				$dom           = new DomDocument( '1.0', 'ISO-8859-1' );
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

				if ( ! empty( $comments ) ) {
					$comments_node = $dom->createElement( 'Comments' );
					$dom->appendChild( $comments_node );
					foreach ( $comments as $k => $v ) {
						$single_comment = $dom->createElement( 'Comment' );
						$comments_node->appendChild( $single_comment );
						$single_comment->appendChild( $dom->createElement( 'comment_author', htmlspecialchars( $v->comment_author ) ) );
						$single_comment->appendChild( $dom->createElement( 'comment_author_email', htmlspecialchars( $v->comment_author_email ) ) );
						$single_comment->appendChild( $dom->createElement( 'comment_author_url', htmlspecialchars( $v->comment_author_url ) ) );
						$single_comment->appendChild( $dom->createElement( 'comment_author_IP', htmlspecialchars( $v->comment_author_IP ) ) );
						$single_comment->appendChild( $dom->createElement( 'comment_date', htmlspecialchars( $v->comment_date ) ) );
						$single_comment->appendChild( $dom->createElement( 'comment_agent', htmlspecialchars( $v->comment_agent ) ) );
						$single_comment->appendChild( $dom->createElement( 'comment_content', htmlspecialchars( $v->comment_content ) ) );
					}
				}

				$meta_data = $dom->createElement( 'Metadata' );
				$dom->appendChild( $meta_data );

				foreach ( $usermeta as $k => $v ) {
					$k = is_numeric( substr( $k, 0, 1 ) ) ? '_' . $k : $k;
					$key = $dom->createElement( htmlspecialchars( $k ) );
					$meta_data->appendChild( $key );
					foreach ( $v as $value ) {
						$key->appendChild( $dom->createElement( 'item', htmlspecialchars( $value ) ) );
					}
				}

				if ( $extra_content ) {
					$extra = $dom->createElement( $extra_content['name'] );
					$dom->appendChild( $extra );
					foreach ( $extra_content['content'] as $key => $obj ) {
						$item = $extra->appendChild( $dom->createElement( 'item' ) );
						foreach ( $obj as $k => $value ) {
							$item->appendChild( $dom->createElement( $k, ( is_object( $value ) || is_array( $value ) ) ? serialize( (array) $value ) : $value ) );
						}
					}
				}

				$dom->preserveWhiteSpace = false;
				$dom->formatOutput       = true;
				return $dom->saveXML();
				break;
		}

		return false;

	}

	/**
	 * Export the generated export file.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	function export_data() {
		if ( ! isset( $_POST['nonce'], $_POST['email'], $_POST['type'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-export-data' ) ) {
			wp_send_json_error();
		}

		$type  = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		$email = sanitize_email( $_POST['email'] );
		$user  = get_user_by( 'email', $email );

		if ( ! $user instanceof WP_User ) {
			wp_send_json_error();
		}

		$export = self::generate_export( $email, $type );
		if ( $export ) {
			wp_send_json_success( $export );
		}

		wp_send_json_error();
	}

	/**
	 * Save a consent to the user meta.
	 * @since  1.1.4
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  integer $user_id The user ID.
	 * @param  string $consent The consent ID.
	 * @return void
	 */
	public static function save_consent( $user_id, $consent ) {
		$registered_consent = get_option( 'gdpr_consent_types', array( 'privacy-policy' ) );
		$consent_ids = array_keys( $registered_consent );
		$user = get_user_by( 'ID', $user_id );
		$consent = sanitize_text_field( wp_unslash( $consent ) );

		if ( $user ) {
			$user_consent = get_user_meta( $user_id, 'gdpr_consents' );
			if ( in_array( $consent, $consent_ids ) && ! in_array( $consent, $user_consent ) ) {
				add_user_meta( $user_id, 'gdpr_consents', $consent );
				$user_consent[] = $consent;
				setcookie( "gdpr[consent_types]", json_encode( $user_consent ), time() + YEAR_IN_SECONDS, "/" );
				return true;
			}
		}

		return false;
	}

	/**
	 * Remove a user consent.
	 * @since  1.1.4
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  integer $user_id The user ID.
	 * @param  string $consent The consent ID.
	 * @return void
	 */
	public static function remove_consent( $user_id, $consent ) {
		$user = get_user_by( 'ID', $user_id );

		if ( $user ) {
			$user_consent = get_user_meta( $user_id, 'gdpr_consents' );

			$consent = sanitize_text_field( wp_unslash( $consent ) );
			$key = array_search( $consent, $user_consent );
			if ( false !== $key ) {
				delete_user_meta( $user_id, 'gdpr_consents', $consent );
				unset( $user_consent[ $key ] );
				setcookie( "gdpr[consent_types]", json_encode( $user_consent ), time() + YEAR_IN_SECONDS, "/" );
				return true;
			}
		}

		return false;
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
