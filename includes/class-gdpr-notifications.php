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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
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

	public function notify( $user, $type = null ) {
		if ( ! is_a( $user, 'WP_User' ) ) {
			if ( ! is_int( $user ) ) {
				return;
			}
			$user = get_user_by( 'ID', $user );
		}

		$content = $this->template::get_template_html( 'email/request-to-be-forgotten.php', array(
			'user' => $user,
		) );
		error_log($content);

		wp_mail( $user->user_email,
			'Your preferences have been changed',
			$content,
			array('Content-Type: text/html; charset=UTF-8')
		);
	}


}
