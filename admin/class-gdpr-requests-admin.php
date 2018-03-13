<?php

/**
 * The admin facing requests functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The admin facing requests functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Requests_Admin extends GDPR_Requests {

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

	function add_to_deletion_requests() {
		if ( ! isset( $_REQUEST['gdpr_deletion_requests_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['gdpr_deletion_requests_nonce'] ), 'add-to-deletion-requests' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_REQUEST['user_email'] );
		$user = get_user_by( 'email', $email );

		if ( ! $user instanceof WP_User ) {
			add_settings_error( 'gdpr-requests', 'invalid-user', esc_html__( 'User not found.', 'gdpr' ), 'error' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'settings-updated' => true
						),
						wp_get_referer() . '#delete'
					)
				)
			);
			exit;
		} else {
			if ( in_array( 'administrator', $user->roles ) ) {
				$admins_query = new WP_User_Query( array(
						'role' => 'Administrator'
				)	);
				if ( 1 === $admins_query->get_total() ) {
					add_settings_error( 'gdpr-requests', 'invalid-request', sprintf( esc_html__( 'User %s is the only admin of the site. It cannot be deleted.', 'gdpr' ), $email ), 'error' );
					set_transient( 'settings_errors', get_settings_errors(), 30 );
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'settings-updated' => true
								),
								wp_get_referer() . '#delete'
							)
						)
					);
					exit;
				}
			}
		}

		$requests = ( array ) get_option( 'gdpr_requests', array() );

		if ( empty( $requests ) ) {
			parent::add_to_requests( $email, 'delete' );
			add_settings_error( 'gdpr-requests', 'new-request', sprintf( esc_html__( 'User %s was added to the deletion table.', 'gdpr' ), $email ), 'updated' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'settings-updated' => true
						),
						wp_get_referer() . '#delete'
					)
				)
			);
			exit;
		}

		$deletion_requests = array_filter( $requests, function( $arr ) {
			return 'delete' === $arr['type'];
		});
		$user_has_already_requested = array_search( $email, array_column( $deletion_requests, 'email' ) );

		if ( false !== $user_has_already_requested ) {
			add_settings_error( 'gdpr-requests', 'invalid-user', esc_html__( 'User already placed a deletion request.', 'gdpr' ), 'error' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'settings-updated' => true
						),
						wp_get_referer() . '#delete'
					)
				)
			);
			exit;
		}

		parent::add_to_requests( $email, 'delete' );
		add_settings_error( 'gdpr-requests', 'new-request', sprintf( esc_html__( 'User %s was added to the deletion table.', 'gdpr' ), $email ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true
					),
					wp_get_referer() . '#delete'
				)
			)
		);
		exit;
	}

	function cancel_request() {
		if ( ! isset( $_REQUEST['gdpr_nonce'], $_REQUEST['user_email'], $_REQUEST['type'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['gdpr_nonce'] ), 'gdpr-request-nonce' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_REQUEST['user_email'] );
		$type = sanitize_text_field( $_REQUEST['type'] );

		$allowed_types = parent::get_allowed_types();

		if ( ! in_array( $type, $allowed_types ) ) {
			add_settings_error( 'gdpr-requests', 'invalid-request', sprintf( esc_html__( 'Type of request \'%s\' is not an allowed type.', 'gdpr' ), $email ), 'updated' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'settings-updated' => true
						),
						wp_get_referer()
					)
				)
			);
			exit;
		}

		parent::remove_from_requests( $email, $type );

		add_settings_error( 'gdpr-requests', 'remove-request', sprintf( esc_html__( 'User %s was removed from this request table.', 'gdpr' ), $email ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true
					),
					wp_get_referer() . '#' . $type
				)
			)
		);
		exit;
	}

	function delete_user() {
		if ( ! isset( $_REQUEST['gdpr_delete_user'], $_REQUEST['user_email'] ) || ! wp_verify_nonce( $_REQUEST['gdpr_delete_user'], 'gdpr-request-delete-user' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_REQUEST['user_email'] );
		$user = get_user_by( 'email', $email );
		wp_delete_user( $user->ID );

		parent::remove_from_requests( $email, 'delete' );

		add_settings_error( 'gdpr-requests', 'new-request', sprintf( esc_html__( 'User %s was deleted from the site.', 'gdpr' ), $email ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true
					),
					wp_get_referer() . '#delete'
				)
			)
		);
		exit;
	}

	function anonymize_comments() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-anonymize-comments-action' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_POST['user_email'] );
		$comment_count = ( int ) $_POST['comment_count'];

		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof WP_User ) {
			wp_send_json_error( esc_html__( 'User not found.', 'gdpr' ) );
		}

		$comments = get_comments( array(
			'author_email' => $user->user_email,
			'include_unapproved' => true,
			'number' => $comment_count,
		) );

		foreach ( $comments as $comment ) {
			$new_comment = array();
			$new_comment['comment_ID'] = $comment->comment_ID;
			$new_comment['comment_author_IP'] = '0.0.0.0';
			$new_comment['comment_author_email'] = '';
			$new_comment['comment_author_url'] = '';
			$new_comment['comment_agent'] = '';
			$new_comment['comment_author'] = esc_html__( 'Guest', 'gdpr' );
			$new_comment['user_id'] = 0;
			wp_update_comment( $new_comment );
		}
		wp_send_json_success();
	}

	function reassign_content() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-reassign-content-action' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['user_email'], $_POST['reassign_to'], $_POST['post_type'], $_POST['post_count'] ) ) {
			wp_send_json_error( esc_html__( 'Essential data missing. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_POST['user_email'] );
		$reassign_to = ( int ) $_POST['reassign_to'];
		$post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
		$post_count = ( int ) $_POST['post_count'];

		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof WP_User ) {
			wp_send_json_error( esc_html__( 'User not found.', 'gdpr' ) );
		}

		$args = array(
			'author' => $user->ID,
			'post_type' => $post_type,
			'posts_per_page' => $post_count,
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				wp_update_post( array(
					'ID' => $post->ID,
					'post_author' => $reassign_to,
				) );
			}
			wp_send_json_success();
		}

		wp_send_json_error( esc_html__( 'Something went wrong. Please try again.', 'gdpr' ) );
	}

}
