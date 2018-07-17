<?php


class GDPR_Templates {
	/**
	 * Locate template.
	 *
	 * Locate the called template.
	 * Search Order:
	 * 1. /themes/theme/gdpr/{folder}/$template_name.
	 * 2. /plugins/gdpr/templates/{folder}/$template_name.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @param  string  $template_name    Template to load.
	 * @return string                    Path to the template file.
	 */
	private static function locate_template( $template_name ) {
		// Set variable to search in gdpr folder of theme.
		$theme_path = 'gdpr/';

		// Set default plugin templates path.
		$plugin_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'; // Path to the template folder

		// Search template file in theme folder.
		$template = locate_template(
			array(
				$theme_path . $template_name,
			)
		);

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
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @param  string  $template_name    Template to load.
	 * @param  array   $args             Arguments passed to the template file.
	 */
	public static function get_template( $template_name, $args = array() ) {
		$template_file = self::locate_template( $template_name );

		if ( ! file_exists( $template_file ) ) {
			return;
		}
		include $template_file;
	}

}
