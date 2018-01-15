<?php

/**
 * The file that defines the Notification component
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/includes
 */

/**
 * The Notification plugin class.
 *
 * This is used to help us notify users of changes they've made to their profile.
 *
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage GDPR/includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Notification {

	/**
	 * The template class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $template    The template class instance.
	 */
	protected $template;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
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
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-templates.php';

		$this->template = new GDPR_Templates();

	}

	public function send( $user, $type, $args = array() ) {
		if ( ! is_a( $user, 'WP_User' ) ) {
			if ( ! is_int( $user ) ) {
				return;
			}
			$user = get_user_by( 'ID', $user );
		}

		$possible_types = apply_filters( 'gdpr_notification_types', array(
			'forget' => esc_html__( 'Confirm account deletion', 'gdpr' ),
			'forgot' => esc_html__( 'You account has been deleted', 'gdpr' ),
			'tos_updated' => esc_html__( 'Our terms of service have been updated', 'gdpr' ),
			'pp_updated' => esc_html__( 'Our privacy policy have been updated', 'gdpr' ),
		) );

		if ( ! in_array( $type, array_keys( $possible_types ), true ) ) {
			return;
		}

		$args = apply_filters( 'gdpr_notification_args', $args );
		$content = $this->template::get_template_html( 'email/' . $type . '.php', $args );

		return wp_mail( $user->user_email,
			$possible_types[$type],
			$content,
			array( 'Content-Type: text/html; charset=UTF-8' )
		);
	}


}
