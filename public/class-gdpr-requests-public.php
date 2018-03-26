<?php

/**
 * The public facing requests functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The public facing requests functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Requests_Public extends GDPR_Requests {

	/**
	 * Removes the user from the requests table, sends a notification email and
	 * delete the user from the site
	 *
	 * @param  WP_User $user  The user object.
	 * @param  string  $index The request key on the requests array.
	 * @return void
	 */
	function delete_user( $user, $index ) {
		if ( ! $user instanceof WP_User ) {
			return false;
		}

		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}

		if ( parent::remove_from_requests( $index ) ) {
			GDPR_Email::send( $user->user_email, 'delete-resolved', array( 'token' => 123456 ) );
			wp_delete_user( $user->ID );
			wp_logout();
		}
	}

	/**
	 * Prints a request form.
	 *
	 * @param  string $type The type of request to display the correct form.
	 * @return mixed        Print the form html.
	 */
	static function request_form( $type ) {
		if ( ! in_array( $type, parent::$allowed_types ) ) {
			return;
		}

		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $type . '-form.php';
	}

	/**
	 * Sends an email to the end user so it can confirm his request.
	 *
	 * @return void
	 */
	function send_request_email() {
		if ( ! isset( $_POST['type'] ) || ! in_array( $_POST['type'], parent::$allowed_types ) ) {
				wp_die( esc_html__( 'Invalid type of request. Please try again.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['gdpr_request_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gdpr_request_nonce'] ), 'add-to-requests' ) ) {
			wp_die( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		$type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		$data = isset( $_POST['data'] ) ? sanitize_textarea_field( $_POST['data'] ) : '';

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
		} else {
			$user = isset( $_POST['user_email'] ) ? get_user_by( 'email', sanitize_email( $_POST['user_email'] ) ) : null;
		}

		switch ( $type ) {
			case 'delete':
				if ( ! $user instanceof WP_User ) {
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'notify' => 1,
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
							'role' => 'Administrator'
					)	);
					if ( 1 === $admins_query->get_total() ) {
						wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'notify' => 1,
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
				}
				break;
			case 'export-data':
				if ( ! $user instanceof WP_User ) {
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'notify' => 1,
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
		if ( GDPR_Email::send( $user->user_email, "{$type}-request", array( 'user' => $user, 'key' => $key, 'data' => $data ) ) ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'notify' => 1,
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
							'error' => 1,
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
	 *
	 * @return void
	 */
	function request_confirmed() {
		if ( ! is_front_page() || ! isset( $_GET['type'], $_GET['key'], $_GET['email'] ) ) {
			return;
		}

		$type = sanitize_text_field( wp_unslash( $_GET['type'] ) );
		$key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
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
			switch ( $type ) {
				case 'delete':
					$found_posts = parent::user_has_content( $user );
					if ( $found_posts ) {
						parent::confirm_request( $key );
						wp_safe_redirect(
							esc_url_raw(
								add_query_arg(
									array(
										'user-deleted' => 0,
										'notify' => 1
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
											'notify' => 1
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
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'request-confirmed' => 1,
									'notify' => 1
								),
								home_url()
							)
						)
					);
					exit;
					break;
				case 'export-data':
					$format = isset( $_GET['format'] ) ? sanitize_text_field( wp_unslash( $_GET['format'] ) ) : 'xml';
					wp_schedule_single_event( time(), 'mail_export_data', array( 'email' => $user->user_email, 'format' => $format, 'key' => $key ) );
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'export-started' => 1,
									'notify' => 1
								),
								home_url()
							)
						)
					);
					exit;
					break;
			}
		}
	}

	/**
	 * Sends the user data export email with the chosen format.
	 *
	 * @param  string $email  The recipient.
	 * @param  string $format The export format. XML or JSON.
	 * @param  string $key    The request array key.
	 * @return void
	 */
	function mail_export_data( $email, $format, $key ) {
		$email = sanitize_email( $email );
		$format = sanitize_text_field( wp_unslash( $format ) );
		$key = sanitize_text_field( wp_unslash( $key ) );

		$export = GDPR::generate_export( $email, $format );
		$filename = get_temp_dir() . $email . '.' . $format;
		if ( $export ) {
			file_put_contents( $filename, $export );
			if ( GDPR_Email::send( $email, 'export-data-resolved', array(), array( $filename ) ) ) {
				unlink( $filename );
				parent::remove_from_requests( $key );
			}
		}
	}
}
