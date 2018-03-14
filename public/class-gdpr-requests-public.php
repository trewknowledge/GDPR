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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
	}


	function process_user_deletion( $user ) {
		if ( ! $user instanceof WP_User ) {
			return false;
		}

		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}
		GDPR_Email::send( $user->user_email, 'deleted', array( 'token' => 123456 ) );
		wp_delete_user( $user->ID );
		parent::remove_from_requests( $user->user_email, 'delete' );
		wp_logout();
		return true;
	}

	static function request_form( $type ) {
		if ( ! in_array( $type, parent::$allowed_types ) ) {
			return;
		}

		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $type . '-form.php';
	}

	function send_deletion_request_email_confirmation() {
		if ( ! isset( $_POST['gdpr_deletion_requests_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gdpr_deletion_requests_nonce'] ), 'add-to-deletion-requests' ) ) {
			wp_die( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
		} else {
			$user = get_user_by( 'email', sanitize_email( $_POST['user_email'] ) );
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
							'cannot_delete' => 1,
						),
						wp_get_referer()
					)
				)
			);
			exit;
			}
		}

		if ( ! $user instanceof WP_User ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'notify' => 1,
							'user_not_found' => 1,
						),
						wp_get_referer()
					)
				)
			);
			exit;
		}

		$key = wp_generate_password( 20, false );
		update_user_meta( $user->ID, parent::$plugin_name . '_delete_key', $key );

		GDPR_Email::send( $user, 'request-to-delete', array( 'user' => $user, 'key' => $key ) );

		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'notify' => 1,
						'email_sent' => 1,
					),
					wp_get_referer()
				)
			)
		);
		exit;
	}

	/**
	 * Function that runs when user confirms deletion from the site.
	 *
	 * @since 1.0.0
	 */
	public function request_to_delete_confirmed() {
		if ( ! is_front_page() || ! isset( $_GET['action'], $_GET['key'], $_GET['email'] ) || 'user_delete' !== $_GET['action'] ) {
			return;
		}

		$key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
		$email = sanitize_email( $_GET['email'] );

		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof WP_User ) {
			return;
		}

		$meta_key = get_user_meta( $user->ID, self::$plugin_name . '_delete_key', true );
		if ( empty( $meta_key ) ) {
			return;
		}

		if ( $key === $meta_key ) {
			$found_posts = parent::user_has_content( $user );

			if ( $found_posts ) {
				parent::add_to_requests( $email, 'delete' );
				wp_safe_redirect(
					esc_url_raw(
						add_query_arg(
							array(
								'user_deleted' => 0,
								'notify' => 1
							),
							home_url()
						)
					)
				);
				exit;
			} else {
				if ( $this->process_user_deletion( $user ) ) {
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'user_deleted' => 1,
									'notify' => 1
								),
								home_url()
							)
						)
					);
					exit;
				}
			}
		}
	}

}
