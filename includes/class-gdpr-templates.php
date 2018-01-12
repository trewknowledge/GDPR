<?php

/**
 * The file that finds and load templates.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/includes
 */

/**
 * The Templating plugin class.
 *
 * This is used to help us find the correct templates to load.
 *
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage GDPR/includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Templates {

	/**
	 * Locate template.
	 *
	 * Locate the called template.
	 * Search Order:
	 * 1. /themes/theme/gdpr/templates/$template_name
	 * 2. /plugins/gdpr/templates/$template_name.
	 *
	 * @since 1.0.0
	 *
	 * @param   string  $template_name    Template to load.
	 * @return  string                    Path to the template file.
	 */
	private static function locate_template( $template_name ) {
		// Set variable to search in gdpr folder of theme.
		$theme_path = 'gdpr/';

		// Set default plugin templates path.
		$plugin_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'; // Path to the template folder

		// Search template file in theme folder.
		$template = locate_template( array(
			$theme_path . $template_name
		) );

		// Get plugins template file.
		if ( ! $template ) {
			$template = $plugin_path . $template_name;
		}
		return $template;
	}

	/**
	 * Get template.
	 *
	 * Search for the template and include the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $template_name    Template to load.
	 * @param array   $args             Args passed for the template file.
	 * @param string  $template_path    Path to templates.
	 * @param string  $default_path     Default path to template files.
	 */
	private static function get_template( $template_name, $args = array() ) {
		$template_file = self::locate_template( $template_name );

		if ( ! file_exists( $template_file ) ) {
			return;
		}
		include $template_file;
	}

	public static function get_template_html( $template_name, $args = array() ) {
		ob_start();
		self::get_template( $template_name, $args );
		return ob_get_clean();
	}


}
