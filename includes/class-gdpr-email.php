<?php
class GDPR_Email {
	/**
	 * Locate template.
	 *
	 * Locate the called template.
	 * Search Order:
	 * 1. /themes/theme/gdpr/templates/$template_name
	 * 2. /plugins/gdpr/templates/$template_name.
	 *
	 * @since 0.1.0
	 *
	 * @param   string  $template_name    Template to load.
	 * @return  string                    Path to the template file.
	 */
	private static function locate_template( $template_name ) {
		// Set variable to search in gdpr folder of theme.
		$theme_path = 'gdpr/email/';

		// Set default plugin templates path.
		$plugin_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/email/'; // Path to the template folder

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
	 * @since 0.1.0
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

	public static function send( $user, $type, $args = array(), $attachments = array() ) {
		if ( ! $user instanceof WP_User ) {
			if ( ! is_email( $user ) && is_int( $user ) ) {
				$user = get_user_by( 'ID', $user );
			}
		}
		$email = $user instanceof WP_User ? $user->user_email : $user;

		$possible_types = apply_filters( 'gdpr_email_types', array(
			'request-to-delete' => apply_filters( 'gdpr_request_to_delete_email_subject', esc_html__( 'Someone requested to close your account.', 'gdpr' ) ),
			'deleted' => apply_filters( 'gdpr_deleted_email_subject', esc_html__( 'Your account has been closed.', 'gdpr' ) ),
		) );


		if ( ! in_array( $type, array_keys( $possible_types ), true ) ) {
			return;
		}
		$args['email_title'] = $possible_types[ $type ];

		$args = apply_filters( 'gdpr_email_args', $args );

		$content = self::get_template_html( $type . '.php', $args );
		return wp_mail( $email,
			$possible_types[ $type ],
			$content,
			array( 'Content-Type: text/html; charset=UTF-8' ),
			( ! empty( $attachments ) ) ? $attachments : array()
		);
	}
}
