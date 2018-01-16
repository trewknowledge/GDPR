<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Admin {

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
	 * The plugin options.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $options    The plugin options.
	 */
	private $options;

	/**
	 * The Audit Log Class.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $audit_log    The plugin Audit Log Class.
	 */
	private $audit_log;

	/**
	 * The Notifications Class.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $audit_log    The plugin Notifications Class.
	 */
	private $notifications;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->set_options();
		$this->load_dependencies();
		self::save();

	}

	private static function save() {
		if (
			! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_POST['gdpr_options'], $_POST['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'gdpr_options_save' )
		) {
			return;
		}

		$settings = get_option( 'gdpr_options', array() );
		$consents = array();
		if ( isset( $_POST['gdpr_options']['consents'] ) ) {
			foreach ( wp_unslash( $_POST['gdpr_options']['consents'] ) as $consent ) {
				$consents[] = array(
					'title' => sanitize_text_field( $consent['title'] ),
					'description' => sanitize_text_field( $consent['description'] ),
				);
			}
		}
		$settings = array(
			'pp-page' => ( isset( $_POST['gdpr_options']['pp-page'] ) ) ? sanitize_text_field( wp_unslash( absint( $_POST['gdpr_options']['pp-page'] ) ) ) : '',
			'tos-page' => ( isset( $_POST['gdpr_options']['tos-page'] ) ) ? sanitize_text_field( wp_unslash( absint( $_POST['gdpr_options']['tos-page'] ) ) ) : '',
			'processor-contact-info' => ( isset( $_POST['gdpr_options']['processor-contact-info'] ) ) ? sanitize_email( wp_unslash( $_POST['gdpr_options']['processor-contact-info'] ) ) : '',
			'consents' => $consents,
		);

		update_option( 'gdpr_options', $settings );
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
		 * The class responsible for controlling the Audit Log and hashing of information.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-audit-log.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-notifications.php';

		$this->audit_log = new GDPR_Audit_Log( $this->plugin_name, $this->version );
		$this->notifications = new GDPR_Notification();

	}

	public function export_audit_log( $user_id ) {
		$this->audit_log->log( $user_id, esc_html__( 'User was removed from the site', 'gdpr') );
		$this->audit_log->export_log( $user_id );
	}

	public function gdpr_audit_log_email_lookup() {
		if ( ! isset( $_POST['email'], $_POST['nonce'] ) ) {
			wp_send_json_error( esc_html__( 'Missing email or nonce values.', 'gdpr' ) );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-request-email-lookup' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$email = sanitize_email( wp_unslash( $_POST['email'] ) );

		$log = $this->audit_log->get_log( $email );

		if ( ! $log ) {
			wp_send_json_error( esc_html__( 'We could not find a user with that email. If this user was already removed from the database, check on the logs folder for this user email and use the field below to decrypt the contents.', 'gdpr' ) );
		}

		wp_send_json_success( $log );

	}

	/**
	 * Hook that run when the user is first added to the database.
	 *
	 * @since 1.0.0
	 */
	public function user_register( $user_id ) {
		$meta_value = array();
		foreach ( $this->options['consents'] as $consent ) {
			$meta_value[$consent['title']] = $consent['description'];
		}
		$user = get_user_by( 'ID', $user_id );
		add_user_meta( $user_id, $this->plugin_name . '_consents', $meta_value, true );

		$this->audit_log->log( $user_id, sprintf( esc_html__( 'First name: %s', 'gdpr' ), $user->first_name ) );
		$this->audit_log->log( $user_id, sprintf( esc_html__( 'Last name: %s', 'gdpr' ), $user->last_name ) );
		$this->audit_log->log( $user_id, sprintf( esc_html__( "Email: %s \n", 'gdpr' ), $user->user_email ) );
		$this->audit_log->log( $user_id, esc_html__( 'User registered to the site.', 'gdpr' ) );
		foreach ( $this->options['consents'] as $consent ) {
			$this->audit_log->log( $user_id, sprintf( esc_html__( 'User gave explicit consent to %s', 'gdpr' ), $consent['title'] ) );
		}

	}

	/**
	 * Function that runs when user confirms deletion from the site.
	 *
	 * @since 1.0.0
	 */
	public function forget_user() {
		if ( ! is_home() || ! is_front_page() || ! isset( $_GET['action'] ) ) {
			return;
		}

		if ( 'delete' === $_GET['action'] ) {
			if ( ! isset( $_GET['key'], $_GET['login'] ) ) {
				return;
			}
			$key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
			$login = sanitize_text_field( wp_unslash( $_GET['login'] ) );

			$user = get_user_by( 'login', $login );
			if ( ! $user ) {
				return;
			}

			$meta_key = get_user_meta( $user->ID, $this->plugin_name . '_delete_key', true );
			if ( empty( $meta_key ) ) {
				return;
			}
			if ( $key === $meta_key ) {
				$post_types = get_post_types( array( 'public' => true ) );
				$found_posts = false;
				foreach ( $post_types as $pt ) {
					$post_count = count_user_posts( $user->ID, $pt);
					if ( $post_count > 0 ) {
						$found_posts = true;
						break;
					}
				}
				if ( $found_posts ) {
					self::add_to_requests( $user );
				} else {
					require_once( ABSPATH.'wp-admin/includes/user.php' );
					if ( wp_delete_user( $user->ID ) ) {
						$this->notifications->send( $user, 'forgot', array( 'processor' => $this->options['processor-contact-info'] ) );
						wp_logout();
					}
				}
			}
		}
	}

	private static function add_to_requests( $user ) {
		if ( ! is_a( $user, 'WP_User' ) ) {
			if ( ! is_int( $user ) ) {
				return;
			}
			$user = get_user_by( 'ID', $user );
		}

		$requests = get_option( 'gdpr_requests' ) ? get_option( 'gdpr_requests' ) : array();
		if ( array_key_exists( $user->ID, $requests ) ) {
			return;
		}

		$requests[$user->ID]['full_name'] = $user->user_firstname . ' ' . $user->user_lastname;
		$requests[$user->ID]['email'] = $user->user_email;
		$requests[$user->ID]['requested_on'] = date('Y/m/d');

		update_option( 'gdpr_requests', $requests );
	}

	/**
	 * Hooks to the Wordpress Core registration form and add the consent text.
	 *
	 * @since 1.0.0
	 */
	public function register_form() {
		if ( ! isset( $this->options['tos-page'], $this->options['pp-page'] ) ) {
			return;
		}
		$tos = (int) $this->options['tos-page'];
		$pp = (int) $this->options['pp-page'];
		$text = sprintf(
			__( '<p>By registering to this site you agree to our <a href="%s">%s</a> and to our <a href="%s">%s</a>.</p><br>', 'gdpr' ),
			get_permalink( $tos ),
			get_the_title( $tos ),
			get_permalink( $pp ),
			get_the_title( $pp )
		);

		/**
		 * Filters the consent html
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The registration form consent html
		 */
		$text = apply_filters( 'gdpr_register_form_consent_text', $text );
	?>
			<?php echo wp_kses_post( $text ); ?>
	<?php
	} // register_form()

	/**
	 * Check if the Terms of Service or Privacy Policy pages have been updated.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $ID
	 * @param WP_Post $post
	 * @return void
	 */
	public function check_tos_pp_pages_updated( $ID, $post ) {
		if ( $ID == $this->options['tos-page'] ) {
			update_option( $this->plugin_name . '_tos_updated', 1 );
		}
		if ( $ID == $this->options['pp-page'] ) {
			update_option( $this->plugin_name . '_pp_updated', 1 );
		}
	}

	/**
	 * Sets the class variable $options
	 */
	private function set_options() {

		$this->options = get_option( $this->plugin_name . '_options' );

	} // set_options()

	/**
	 * Adds a menu page for the plugin with all it's sub pages.
	 *
	 * @since   1.0.0
	 */
	public function add_menu() {

		$requests = get_option( 'gdpr_requests' );
		$requests = ( $requests ) ? count( $requests ) : 0;
		$requests_title = esc_attr( sprintf( esc_html__( '%d requests', 'gdpr' ), $requests ) );

		$badge = '<span class="update-plugins count-' . $requests . '" title="' . $requests_title . '">' . number_format_i18n( $requests ) . '</span>';

		$parent_slug = 'gdpr';
		$page_title = esc_html__( 'GDPR', 'gdpr' );
		$menu_title = sprintf( __( 'GDPR %s', 'gdpr' ), $badge );
		$capability = 'manage_options';
		$menu_slug = 'gdpr';
		$function = array( $this, 'gdpr_requests_page_template' );
		$icon_url = 'dashicons-id';

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );

		$page_title = esc_html__( 'Requests', 'gdpr' );
		$menu_title = sprintf( esc_html__( 'Requests %s', 'gdpr' ), $badge );
		$function = array( $this, 'gdpr_requests_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Settings', 'gdpr' );
		$menu_title = esc_html__( 'Settings', 'gdpr' );
		$menu_slug = 'gdpr-settings';
		$function = array( $this, 'gdpr_settings_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Audit Log', 'gdpr' );
		$menu_title = esc_html__( 'Audit Log', 'gdpr' );
		$menu_slug = 'gdpr-audit-log';
		$function = array( $this, 'gdpr_audit_log_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Right to Access', 'gdpr' );
		$menu_title = esc_html__( 'Right to Access', 'gdpr' );
		$menu_slug = 'gdpr-right-to-access';
		$function = array( $this, 'gdpr_right_to_access_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

	} // add_menu()

	/**
	 * Requests Page Template
	 *
	 * @since 1.0.0
	 */
	public function gdpr_requests_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/requests.php';
	}

	/**
	 * Settings Page Template
	 *
	 * @since 1.0.0
	 */
	public function gdpr_settings_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/settings.php';
	}

	/**
	 * Audit Log Page Template
	 *
	 * @since 1.0.0
	 */
	public function gdpr_audit_log_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/audit-log.php';
	}

	/**
	 * Right to access page template.
	 *
	 * @since 1.0.0
	 */
	public function gdpr_right_to_access_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/right-to-access.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GDPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GDPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GDPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GDPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-admin.js', array( 'jquery' ), $this->version, false );

	}

}
