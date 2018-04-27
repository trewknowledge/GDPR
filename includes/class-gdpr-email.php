<?php
/**
 * This file handle emailing users.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * Handles emailing users.
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Email {
	/**
	 * Locate template.
	 *
	 * Locate the called template.
	 * Search Order:
	 * 1. /themes/theme/gdpr/templates/email/$template_name
	 * 2. /plugins/gdpr/templates/$template_name.
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
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @param  string  $template_name    Template to load.
	 * @param  array   $args             Arguments passed to the template file.
	 */
	private static function get_template( $template_name, $args = array() ) {
		$template_file = self::locate_template( $template_name );

		if ( ! file_exists( $template_file ) ) {
			return;
		}
		include $template_file;
	}

	/**
	 * Get the email content from the correct file.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  string $template_name Template to load.
	 * @param  array  $args          Arguments passed to the template file.
	 * @return string                Email contents.
	 */
	public static function get_email_content( $template_name, $args = array() ) {
		ob_start();
		self::get_template( $template_name, $args );
		return ob_get_clean();
	}

	/**
	 * Get a noreply email address.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @static
	 * @return string The noreply email address
	 */
	private static function get_do_not_reply_address() {
	  $sitename = strtolower( $_SERVER['SERVER_NAME'] );
    if ( substr( $sitename, 0, 4 ) === 'www.' ) {
			$sitename = substr( $sitename, 4 );
    }

	  return apply_filters( 'gdpr_do_not_reply_address', 'noreply@' . $sitename );
	}

	/**
	 * Create batches of users so we can throtle emails.
	 * Schedule CRON jobs every hour that sends the current batch of emails.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  string $key The confirmation key.
	 */
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

	/**
	 * The CRON job set by the prepare_data_breach_emails calls this function.
	 * This sends one of the data breach batch emails.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  array  $emails The batch recipients.
	 * @param  string $data   The contents of the email.
	 */
	public function send_data_breach_emails( $emails, $data ) {
		$content = isset( $data['content'] ) ? sanitize_textarea_field( $data['content'] ) : '';

		$nature = sanitize_textarea_field( wp_unslash( $data['nature'] ) );
		$office_contact = sanitize_textarea_field( wp_unslash( $data['office_contact'] ) );
		$consequences = sanitize_textarea_field( wp_unslash( $data['consequences'] ) );
		$measures = sanitize_textarea_field( wp_unslash( $data['measures'] ) );

		foreach ( (array) $emails as $email ) {
			$user = get_user_by( 'email', $email );
			if ( $user instanceof WP_User ) {
				GDPR_Audit_Log::log( $user->ID, esc_html__( 'Data breach notification sent to user.', 'gdpr' ) );
				/* translators: email content */
				GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'Email content: %s', 'gdpr'), $content ) );
				/* translators: nature of the data breach */
				GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'Nature of data breach: %s', 'gdpr'), $nature ) );
				/* translators: data protection officer contact information */
				GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'Data protection officer contact: %s', 'gdpr'), $office_contact ) );
				/* translators: likely consequences */
				GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'Likely consequences of breach: %s', 'gdpr'), $consequences ) );
				/* translators: measures taken */
				GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'Measures taken or proposed to be taken: %s', 'gdpr'), $measures ) );
			}
		}


		self::send( $emails, 'data-breach-notification', array(
			'content' => $content,
			'nature' => $nature,
			'office_contact' => $office_contact,
			'consequences' => $consequences,
			'measures' => $measures,
		) );
	}

	/**
	 * Actually send an email.
	 * This check if the type is one of the possible types of email.
	 * Set the headers. Get the email content from the correct file.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  array  $emails       The recipient email addresses.
	 * @param  string $type         The email type.
	 * @param  array  $args         The arguments to be used by the email template.
	 * @param  array  $attachments  Attachments
	 * @return bool                 Whether the email contents were sent successfully.
	 */
	public static function send( $emails, $type, $args = array(), $attachments = array() ) {
		$possible_types = apply_filters( 'gdpr_email_types', array(
			'new-request'                        => apply_filters( 'gdpr_new_request_email_subject', esc_html__( 'GDPR Notification: There is a new request waiting to be reviewed.', 'gdpr' ) ),
			'delete-request'                     => apply_filters( 'gdpr_delete_request_email_subject', esc_html__( 'Someone requested to close your account.', 'gdpr' ) ),
			'delete-resolved'                    => apply_filters( 'gdpr_delete_resolved_email_subject', esc_html__( 'Your account has been closed.', 'gdpr' ) ),
			'rectify-request'                    => apply_filters( 'gdpr_rectify_request_email_subject', esc_html__( 'Someone requested that we rectify data of your account.', 'gdpr' ) ),
			'rectify-resolved'                   => apply_filters( 'gdpr_rectify_resolved_email_subject', esc_html__( 'Your request has been completed.', 'gdpr' ) ),
			'complaint-request'                  => apply_filters( 'gdpr_complaint_request_email_subject', esc_html__( 'Someone made complaint on behalf of your account.', 'gdpr' ) ),
			'complaint-resolved'                 => apply_filters( 'gdpr_complaint_resolved_email_subject', esc_html__( 'Your request has been completed.', 'gdpr' ) ),
			'export-data-request'                => apply_filters( 'gdpr_export_data_request_email_subject', esc_html__( 'Someone requested to download your data.', 'gdpr' ) ),
			'export-data-resolved'               => apply_filters( 'gdpr_export_data_resolved_email_subject', esc_html__( 'Your request has been completed.', 'gdpr' ) ),
			'data-breach-request'                => apply_filters( 'gdpr_data_breach_request_email_subject', esc_html__( 'Someone requested to send a data breach notification.', 'gdpr' ) ),
			'data-breach-notification'           => apply_filters( 'gdpr_data_breach_resolved_email_subject', esc_html__( 'Data Breach Notification.', 'gdpr' ) ),
		) );

		if ( ! in_array( $type, array_keys( $possible_types ), true ) ) {
			return;
		}

		$no_reply = self::get_do_not_reply_address();
    $headers = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $no_reply . '>' );
		foreach ( (array) $emails as $email ) {
			$headers[] = 'Bcc: ' . sanitize_email( $email );
		}

		$content = self::get_email_content( $type . '.php', $args );

		return wp_mail( $no_reply,
			$possible_types[ $type ],
			html_entity_decode( $content, ENT_QUOTES, 'UTF-8' ),
			$headers,
			( ! empty( $attachments ) ) ? $attachments : array()
		);
	}
}
