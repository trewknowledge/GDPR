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

		$this->audit_log = new GDPR_Audit_Log( $this->plugin_name, $this->version );

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
		$meta_value = array(
			'tos' => 1,
			'pp' => 1,
		);
		$user = get_user_by( 'ID', $user_id );
		add_user_meta( $user_id, $this->plugin_name . '_consents', $meta_value, true );

		$this->audit_log->log( $user_id, sprintf( esc_html__( 'First name: %s', 'gdpr' ), $user->first_name ) );
		$this->audit_log->log( $user_id, sprintf( esc_html__( 'Last name: %s', 'gdpr' ), $user->last_name ) );
		$this->audit_log->log( $user_id, sprintf( esc_html__( "Email: %s \n", 'gdpr' ), $user->user_email ) );
		$this->audit_log->log( $user_id, esc_html__( 'User registered to the site.', 'gdpr' ) );
		$this->audit_log->log( $user_id, esc_html__( 'User gave explicit consent to the site Privacy Policy and Terms of Service.', 'gdpr' ) );
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
			update_option( $this->plugin_name . '-tos-updated', 1 );
		}
		if ( $ID == $this->options['pp-page'] ) {
			update_option( $this->plugin_name . '-pp-updated', 1 );
		}
	}

	/**
	 * Sets the class variable $options
	 */
	private function set_options() {

		$this->options = get_option( $this->plugin_name . '-options' );

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
		$menu_label = sprintf( __( 'GDPR %s', 'gdpr' ), $badge );

		add_menu_page(
			esc_html__( 'GDPR', 'gdpr' ),
			$menu_label,
			'manage_options',
			'gdpr',
			array( $this, 'gdpr_requests_page_template' ),
			'dashicons-id'
		);

		$menu_label = sprintf( esc_html__( 'Requests %s', 'gdpr' ), $badge );
		add_submenu_page(
			'gdpr',
			esc_html__( 'Requests', 'gdpr' ),
			$menu_label,
			'manage_options',
			'gdpr',
			array( $this, 'gdpr_requests_page_template' )
		);

		add_submenu_page(
			'gdpr',
			esc_html__( 'Settings', 'gdpr' ),
			esc_html__( 'Settings', 'gdpr' ),
			'manage_options',
			'gdpr-settings',
			array( $this, 'gdpr_settings_page_template' )
		);

		add_submenu_page(
			'gdpr',
			esc_html__( 'Audit Log', 'gdpr' ),
			esc_html__( 'Audit Log', 'gdpr' ),
			'manage_options',
			'gdpr-audit-log',
			array( $this, 'gdpr_audit_log_page_template' )
		);

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
	 * Registers all the settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			$this->plugin_name,
			$this->plugin_name . '-options',
			array( $this, 'sanitize_options' )
		);
	}


	/**
	 * Sanitizes the saved options
	 *
	 * @since 1.0.0
	 */
	public function sanitize_options( $input ) {

		$input['tos-page'] = intval( $input['tos-page'] );
		$input['pp-page'] = intval( $input['pp-page'] );

		return $input;
	}

	/**
	 * Register settings API sections
	 *
	 * @since 1.0.0
	 */
	public function register_sections() {
		add_settings_section(
			$this->plugin_name . '-section-settings',
			esc_html__( 'General', 'gdpr' ),
			array( $this, 'section_settings' ),
			$this->plugin_name . '-settings'
		);

		add_settings_section(
			$this->plugin_name . '-section-audit-log',
			esc_html__( 'Audit Log', 'gdpr' ),
			array( $this, 'section_audit_log' ),
			$this->plugin_name . '-audit-log'
		);
	}

	/**
	 * Register settings API fields
	 *
	 * @since 1.0.0
	 */
	public function register_fields() {
		$pages = get_pages();
		$selections = array();
		foreach ( $pages as $page ) {
			$selections[] = array(
				'label' => $page->post_title,
				'value' => $page->ID,
			);
		}
		add_settings_field(
			'pp-page',
			esc_html__( 'Privacy Policy', 'gdpr' ),
			array( $this, 'select_field' ),
			$this->plugin_name . '-settings',
			$this->plugin_name . '-section-settings',
			array(
				'label_for' => 'pp-page',
				'blank' => esc_html__( 'Select your Privacy Policy Page', 'gdpr' ),
				'label' => esc_html__( 'Privacy Policy', 'gdpr' ),
				'description' => esc_html__( 'If this page is updated, you must notify users and ask for their consent again.', 'gdpr' ),
				'class' => '',
				'required' => true,
				'selections' => $selections,
			)
		);
		add_settings_field(
			'tos-page',
			esc_html__( 'Terms of Service', 'gdpr' ),
			array( $this, 'select_field' ),
			$this->plugin_name . '-settings',
			$this->plugin_name . '-section-settings',
			array(
				'label_for' => 'tos-page',
				'blank' => esc_html__( 'Select your Terms of Service Page', 'gdpr' ),
				'label' => esc_html__( 'Terms of Service', 'gdpr' ),
				'description' => esc_html__( 'If this page is updated, you must notify users and ask for their consent again.', 'gdpr' ),
				'class' => '',
				'required' => true,
				'selections' => $selections,
			)
		);
		add_settings_field(
			'processor-contact-info',
			esc_html__( 'Processor Contact Information', 'gdpr' ),
			array( $this, 'input_field' ),
			$this->plugin_name . '-settings',
			$this->plugin_name . '-section-settings',
			array(
				'label_for' => 'processor-contact-info',
				'label' => esc_html__( 'Processor Contact Information', 'gdpr' ),
				'description' => sprintf( __( '<a href="%s" target="_blank" rel="nofollow">What is this?</a>', 'gdpr' ), esc_url( 'https://gdpr-info.eu/art-28-gdpr/' ) ) ,
				'class' => 'regular-text',
				'required' => true,
				'type' => 'email',
			)
		);
	}

	public function section_settings() {
		include	plugin_dir_path( __FILE__ ) . 'partials/sections/settings.php';
	}

	public function section_audit_log() {
		include	plugin_dir_path( __FILE__ ) . 'partials/sections/audit-log.php';
	}

	/**
	 * Creates a text field
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 								The HTML field
	 */
	public function input_field( $args ) {

		$defaults['class'] 			= 'text widefat';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['label_for'] . ']';
		$defaults['placeholder'] 	= '';
		$defaults['type'] 			= 'text';
		$defaults['value'] 			= '';
		$defaults['required'] 	= false;

		apply_filters( $this->plugin_name . '-field-text-options-defaults', $defaults );

		$atts = wp_parse_args( $args, $defaults );

		if ( ! empty( $this->options[$atts['label_for']] ) ) {

			$atts['value'] = $this->options[$atts['label_for']];

		}

		include( plugin_dir_path( __FILE__ ) . 'partials/fields/text.php' );

	} // field_text()

	/**
	 * Creates a select field
	 *
	 * Note: label is blank since its created in the Settings API
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 								The HTML field
	 */
	public function select_field( $args ) {

		$defaults['aria'] 			= '';
		$defaults['blank'] 			= '';
		$defaults['class'] 			= 'widefat';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['label_for'] . ']';
		$defaults['selections'] 	= array();
		$defaults['value'] 			= '';
		$defaults['required']		= false;

		apply_filters( $this->plugin_name . '-field-select-options-defaults', $defaults );

		$atts = wp_parse_args( $args, $defaults );

		if ( ! empty( $this->options[$atts['label_for']] ) ) {

			$atts['value'] = $this->options[$atts['label_for']];

		}

		if ( empty( $atts['aria'] ) && ! empty( $atts['description'] ) ) {

			$atts['aria'] = $atts['description'];

		} elseif ( empty( $atts['aria'] ) && ! empty( $atts['label'] ) ) {

			$atts['aria'] = $atts['label'];

		}

		include	plugin_dir_path( __FILE__ ) . 'partials/fields/select.php';
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
