<?php

/**
 * The public facing requests functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The public facing requests functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Requests_Public extends GDPR_Requests {

	/**
	 * Removes the user from the requests table, sends a notification email and
	 * delete the user from the site
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  WP_User $user  The user object.
	 * @param  string  $index The request key on the requests array.
	 * @return void
	 */
	public function delete_user( $user, $index ) {
		if ( ! $user instanceof WP_User ) {
			return false;
		}

		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}

		if ( parent::remove_from_requests( $index ) ) {
			$token = GDPR::generate_pin();
			GDPR_Email::send( $user->user_email, 'delete-resolved', array( 'token' => $token ) );
			GDPR_Audit_Log::log( $user->ID, esc_html__( 'User was removed from the site.', 'gdpr' ) );
			GDPR_Audit_Log::export_log( $user->ID, $token );
			wp_delete_user( $user->ID );
			wp_logout();
			return true;
		}
	}

	/**
	 * Prints a request form.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 * @param  string $type The type of request to display the correct form.
	 * @return mixed        Print the form html.
	 */
	public static function request_form( $type ) {
		if ( ! in_array( $type, parent::$allowed_types ) ) {
			return;
		}

		ob_start();
		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $type . '-form.php';
		return ob_get_clean();
	}

	/**
	 * Sends an email to the end user so it can confirm his request.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function send_request_email() {
		if ( ! isset( $_POST['type'] ) || ! in_array( $_POST['type'], parent::$allowed_types ) ) {
				wp_die( esc_html__( 'Invalid type of request. Please try again.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['gdpr_request_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gdpr_request_nonce'] ), 'gdpr-add-to-requests' ) ) {
			wp_die( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		$use_recaptcha = get_option( 'gdpr_use_recaptcha', false );
		if ( $use_recaptcha ) {
			$site_key = get_option( 'gdpr_recaptcha_site_key', '' );
			$secret_key = get_option( 'gdpr_recaptcha_secret_key', '' );

			if ( $site_key && $secret_key ) {
				if ( ! isset( $_POST['g-recaptcha-response'] ) || ! $_POST['g-recaptcha-response'] ) {
					wp_die( esc_html__( 'Please verify that you are not a robot.', 'gdpr' ) );
				}

				$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
					'body' => array(
						'secret' => $secret_key,
						'response' => $_POST['g-recaptcha-response'],
					),
				) );

				$recaptcha_result = wp_remote_retrieve_body( $response );
				$recaptcha_result = json_decode( $recaptcha_result );
				if ( ! $recaptcha_result || ! $recaptcha_result->success ) {
					wp_die( esc_html__( 'Please verify that you are not a robot.', 'gdpr' ) );
				}

			}
		}

		$type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		$data = isset( $_POST['data'] ) ? sanitize_textarea_field( $_POST['data'] ) : '';

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
		} else {
			$user = isset( $_POST['user_email'] ) ? get_user_by( 'email', sanitize_email( $_POST['user_email'] ) ) : null;
		}

		$email_args = array(
			'forgot_password_url' => add_query_arg(
			  array(
			    'action' => 'rp',
			    'key' => get_password_reset_key( $user ),
			    'login' => $user->user_login,
			  ),
			  wp_login_url()
			),
		);

		switch ( $type ) {
			case 'delete':
				if ( ! $user instanceof WP_User ) {
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'notify'         => 1,
									'user-not-found' => 1,
								),
								wp_get_referer()
							)
						)
					);
					exit;
				}

				if ( in_array( 'administrator', $user->roles ) ) {
					$admins_query = new WP_User_Query( array(
						'role' => 'Administrator',
					) );
					if ( 1 === $admins_query->get_total() ) {
						wp_safe_redirect(
							esc_url_raw(
								add_query_arg(
									array(
										'notify'        => 1,
										'cannot-delete' => 1,
									),
									wp_get_referer()
								)
							)
						);
						exit;
					}
				}
				break;

			case 'rectify':
			case 'complaint':
				if ( ! $data ) {
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'notify' => 1,
									'required-information-missing' => 1,
								),
								wp_get_referer()
							)
						)
					);
					exit;
				}
				$email_args['data'] = $data;
				break;
			case 'export-data':
				if ( ! $user instanceof WP_User ) {
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'notify'         => 1,
									'user-not-found' => 1,
								),
								wp_get_referer()
							)
						)
					);
					exit;
				}
				break;
		}

		$key = parent::add_to_requests( $user->user_email, $type, $data );

		if ( 'export-data' !== $type ) {
		  $email_args['confirm_url'] = add_query_arg(
		    array(
		      'type' => $type,
		      'key' => $key,
		      'email' => $user->user_email,
		    ),
		    home_url()
		  );
		} else {
			$email_args['confirm_url_xml'] = add_query_arg(
		    array(
		      'type'   => $type,
		      'key'    => $key,
		      'email'  => $user->user_email,
		      'format' => 'xml',
		    ),
		    home_url()
		  );
			$email_args['confirm_url_json'] = add_query_arg(
		    array(
		      'type'   => $type,
		      'key'    => $key,
		      'email'  => $user->user_email,
		      'format' => 'json',
		    ),
		    home_url()
		  );
		}


		if ( GDPR_Email::send(
			$user->user_email,
			"{$type}-request",
			$email_args
		) ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'notify'     => 1,
							'email-sent' => 1,
						),
						wp_get_referer()
					)
				)
			);
			exit;
		} else {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'notify' => 1,
							'error'  => 1,
						),
						wp_get_referer()
					)
				)
			);
			exit;
		}
	}

	/**
	 * Runs when a user confirms a request email.
	 * This process the request, set the request to confirmed on the database.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function request_confirmed() {
		if ( ! is_front_page() || ! isset( $_GET['type'], $_GET['key'], $_GET['email'] ) ) {
			return;
		}

		$type  = sanitize_text_field( wp_unslash( $_GET['type'] ) );
		$key   = sanitize_text_field( wp_unslash( $_GET['key'] ) );
		$email = sanitize_email( $_GET['email'] );

		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof WP_User ) {
			return;
		}

		$meta_key = get_user_meta( $user->ID, self::$plugin_name . "_{$type}_key", true );
		if ( empty( $meta_key ) ) {
			return;
		}

		if ( $key === $meta_key ) {
			$notification_email_args = array(
				'type' => $type,
				'review_url' => add_query_arg( array( 'page' => 'gdpr-requests#' . $type ), admin_url() ),
			);
			switch ( $type ) {
				case 'delete':
					$found_posts = parent::user_has_content( $user );
					$needs_review = get_option( 'gdpr_deletion_needs_review', true );
					if ( $found_posts || $needs_review ) {
						parent::confirm_request( $key );
						GDPR_Email::send( get_option( 'admin_email' ), 'new-request', $notification_email_args );
						GDPR_Audit_Log::log( $user->ID, esc_html__( 'User confirmed a request to be deleted.', 'gdpr' ) );
						if ( $found_posts ) {
							GDPR_Audit_Log::log( $user->ID, esc_html__( 'Content was found for that user.', 'gdpr' ) );
						}
						GDPR_Audit_Log::log( $user->ID, esc_html__( 'User added to the erasure review table.', 'gdpr' ) );
						wp_safe_redirect(
							esc_url_raw(
								add_query_arg(
									array(
										'user-deleted' => 0,
										'notify'       => 1,
									),
									home_url()
								)
							)
						);
						exit;
					} else {
						if ( $this->delete_user( $user, $key ) ) {
							wp_safe_redirect(
								esc_url_raw(
									add_query_arg(
										array(
											'user-deleted' => 1,
											'notify'       => 1,
										),
										home_url()
									)
								)
							);
							exit;
						}
					}
					break;
				case 'rectify':
				case 'complaint':
					parent::confirm_request( $key );
					GDPR_Email::send( get_option( 'admin_email' ), 'new-request', $notification_email_args );
					GDPR_Audit_Log::log( $user->ID, esc_html__( 'User placed a request for rectification or a complaint.', 'gdpr' ) );
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'request-confirmed' => 1,
									'notify'            => 1,
								),
								home_url()
							)
						)
					);
					exit;
					break;
				case 'export-data':
					$format = isset( $_GET['format'] ) ? sanitize_text_field( wp_unslash( $_GET['format'] ) ) : 'xml';
					/* translators: File format. Can be XML or JSON */
					GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'User downloaded all their data in %s format.', 'gdpr' ), $format ) );
					$this->file_export_data( $user->user_email, $format, $key );
					break;
			}
		}
	}

	/**
	 * Downloads the user data export in the chosen format.
	 * @since  1.2.0
	 * @param  string $email  The recipient.
	 * @param  string $format The export format. XML or JSON.
	 * @param  string $key    The request array key.
	 */
	private function file_export_data( $email, $format, $key ) {
		$email  = sanitize_email( $email );
		$format = sanitize_text_field( wp_unslash( $format ) );
		$key    = sanitize_text_field( wp_unslash( $key ) );

		$export = GDPR::generate_export( $email, $format );
		if ( $export ) {
			parent::remove_from_requests( $key );
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename=' .  $email . '.' . $format);
			echo $export;
		}
		die();
	}
}
