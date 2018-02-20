<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://trewknowledge.com
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// self::save();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Adds a menu page for the plugin with all it's sub pages.
	 *
	 * @since   1.0.0
	 */
	public function add_menu() {
		$parent_menu_title = esc_html__( 'GDPR', 'gdpr' );
		$capability = 'manage_options';
		$parent_slug = 'gdpr-settings';
		$function = array( $this, 'settings_page_template' );
		$icon_url = 'dashicons-id';

		add_menu_page( $parent_menu_title, $parent_menu_title, $capability, $parent_slug, $function, $icon_url );

		$menu_title = esc_html__( 'Settings', 'gdpr' );
		$menu_slug = 'gdpr-settings';
		$function = array( $this, 'settings_page_template' );

		add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );

	} // add_menu()

	/**
	 * Settings Page Template
	 *
	 * @since 1.0.0
	 */
	public function settings_page_template() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash ( $_GET['tab'] ) ) : 'cookies';
		$settings = get_option( 'gdpr_options', array() );
		$tabs = array(
			'cookies' => array(
				'name' => 'Cookies',
				'file' => plugin_dir_path( __FILE__ ) . 'partials/settings/cookies.php'
			),
		);

		$tabs = apply_filters( 'gdpr_settings_pages', $tabs );

		include	plugin_dir_path( __FILE__ ) . 'partials/settings.php';
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

		update_option( 'gdpr_options', wp_unslash( $_POST['gdpr_options'] ) );
	}

}
