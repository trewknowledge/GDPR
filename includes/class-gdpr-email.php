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

	public static function get_email_content( $template_name, $args = array() ) {
		ob_start();
		self::get_template( $template_name, $args );
		return ob_get_clean();
	}

	private static function get_do_not_reply_address() {
	  $sitename = strtolower( $_SERVER['SERVER_NAME'] );
    if ( substr( $sitename, 0, 4 ) === 'www.' ) {
			$sitename = substr( $sitename, 4 );
    }

	  return apply_filters( 'gdpr_do_not_reply_address', 'noreply@' . $sitename );
	}

	public static function prepare_data_breach_emails( $key ) {
		$data_breach = get_option( 'gdpr_data_breach_initiated', array( 'key' => '' ) );
		if ( $key !== $data_breach['key'] ) {
			return;
		}

		$limit = get_option( 'gdpr_email_limit', 100 );

		$users = get_users( array(
			'fields' => 'all_with_meta'
		) );

		$steps = ceil( count( $users ) / $limit );

		foreach ( range( 0, $steps - 1 ) as $loop ) {
			$offset = $limit * $loop;
			$loop_emails = wp_list_pluck( $users, 'user_email' );
			$loop_emails = array_slice( $loop_emails, $offset, $limit );
			wp_schedule_single_event( time() + $loop * HOUR_IN_SECONDS, 'send_data_breach_emails', array( $loop_emails, $data_breach ) );
		}
	}

	public function send_data_breach_emails( $emails, $data ) {
		$subject = apply_filters( 'gdpr_data_breach_notification_email_subject', esc_html__( 'Data Breach Notification', 'gdpr' ) );

		$content = isset( $data['content'] ) ? sanitize_textarea_field( $data['content'] ) : '';

		self::send( $emails, 'data-breach-notification', array(
			'content' => $content,
			'nature' => $data['nature'],
			'office_contact' => $data['office_contact'],
			'consequences' => $data['consequences'],
			'measures' => $data['measures'],
		) );
	}

	public static function send( $emails, $type, $args = array(), $attachments = array() ) {
		$possible_types = apply_filters( 'gdpr_email_types', array(
			'delete-request'           => apply_filters( 'gdpr_delete_request_email_subject', esc_html__( 'Someone requested to close your account.', 'gdpr' ) ),
			'delete-resolved'          => apply_filters( 'gdpr_delete_resolved_email_subject', esc_html__( 'Your account has been closed.', 'gdpr' ) ),
			'rectify-request'          => apply_filters( 'gdpr_rectify_request_email_subject', esc_html__( 'Someone requested that we rectify data of your account.', 'gdpr' ) ),
			'rectify-resolved'         => apply_filters( 'gdpr_rectify_resolved_email_subject', esc_html__( 'Your request has been completed.', 'gdpr' ) ),
			'complaint-request'        => apply_filters( 'gdpr_complaint_request_email_subject', esc_html__( 'Someone made complaint on behalf of your account.', 'gdpr' ) ),
			'complaint-resolved'       => apply_filters( 'gdpr_complaint_resolved_email_subject', esc_html__( 'Your request has been completed.', 'gdpr' ) ),
			'export-data-request'      => apply_filters( 'gdpr_export_data_request_email_subject', esc_html__( 'Someone requested to download your data.', 'gdpr' ) ),
			'export-data-resolved'     => apply_filters( 'gdpr_export_data_resolved_email_subject', esc_html__( 'Your request has been completed.', 'gdpr' ) ),
			'data-breach-request'      => apply_filters( 'gdpr_data_breach_request_email_subject', esc_html__( 'Someone requested to send a data breach notification.', 'gdpr' ) ),
			'data-breach-notification' => apply_filters( 'gdpr_data_breach_resolved_email_subject', esc_html__( 'Data Breach Notification.', 'gdpr' ) ),
		) );

		if ( ! in_array( $type, array_keys( $possible_types ), true ) ) {
			return;
		}

		$from_email = self::get_do_not_reply_address();
    $headers = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );
		foreach ( (array) $emails as $email ) {
			$headers[] = 'Bcc: ' . $email;
		}

		$content = self::get_email_content( $type . '.php', $args );

		return wp_mail( $no_reply,
			$possible_types[ $type ],
			$content,
			$headers,
			( ! empty( $attachments ) ) ? $attachments : array()
		);
	}
}
