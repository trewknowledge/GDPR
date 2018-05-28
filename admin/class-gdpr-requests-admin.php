<?php

/**
 * The admin facing requests functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The admin facing requests functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Requests_Admin extends GDPR_Requests {

	/**
	 * Add the user to the deletion requests list.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function add_to_deletion_requests() {
		if ( ! isset( $_POST['gdpr_deletion_requests_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gdpr_deletion_requests_nonce'] ), 'gdpr-add-to-deletion-requests' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_POST['user_email'] );
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
					/* translators: User email */
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
			parent::add_to_requests( $email, 'delete', null, true );
			GDPR_Audit_Log::log( $user->ID, esc_html__( 'User added to the deletion requests list by admin.', 'gdpr' ) );
			/* translators: User email */
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

		parent::add_to_requests( $email, 'delete', null, true );
		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User added to the deletion requests list by admin.', 'gdpr' ) );
		/* translators: User email */
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

	/**
	 * Cancels a request.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function cancel_request() {
		if ( ! isset( $_POST['type'] ) ) {
			wp_die( esc_html__( 'We could not verify the type of request you want to cancel.', 'gdpr' ) );
		}

		$type = sanitize_text_field( trim( strtolower( $_POST['type'] ) ) );
		$allowed_types = parent::get_allowed_types();

		if ( ! in_array( $type, $allowed_types ) ) {
			/* translators: The type of request */
			wp_die( sprintf( esc_html__( 'Type of request \'%s\' is not an allowed type.', 'gdpr' ), $type ) );
		}

		$nonce_field = 'gdpr_cancel_' . $type . '_nonce';

		if ( ! isset( $_POST[ $nonce_field ], $_POST['user_email'], $_POST['index'] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $nonce_field ] ), 'gdpr-request-nonce' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_POST['user_email'] );
		$index = sanitize_text_field( wp_unslash( $_POST['index'] ) );

		parent::remove_from_requests( $index );
		$user = get_user_by( 'email', $email );
		/* translators: The type of request i.e 'delete' */
		GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'User was removed from the %s request list by admin.', 'gdpr' ), $type ) );

		/* translators: User email */
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

	/**
	 * Marks a request as resolved and notifies the user.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function mark_resolved() {
		if ( ! isset( $_POST['type'] ) ) {
			wp_die( esc_html__( 'We could not verify the type of request you want to cancel.', 'gdpr' ) );
		}

		$type = sanitize_text_field( trim( strtolower( $_POST['type'] ) ) );
		$allowed_types = parent::get_allowed_types();

		if ( ! in_array( $type, $allowed_types ) ) {
			/* translators: The type of request i.e. 'delete' */
			wp_die( sprintf( esc_html__( 'Type of request \'%s\' is not an allowed type.', 'gdpr' ), $type ) );
		}

		$nonce_field = 'gdpr_' . $type . '_mark_resolved_nonce';

		if ( ! isset( $_POST[ $nonce_field ], $_POST['user_email'], $_POST['index'] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $nonce_field ] ), 'gdpr-mark-as-resolved' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_POST['user_email'] );
		$index = sanitize_text_field( $_POST['index'] );


		parent::remove_from_requests( $index );

		GDPR_Email::send( $email, $type . '-resolved' );

		$user = get_user_by( 'email', $email );
		/* translators: User email */
		GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'User %s request was marked as resolved by admin.', 'gdpr' ), $user->user_email ) );

		add_settings_error( 'gdpr-requests', 'resolved', sprintf( esc_html__( 'Request was resolved. User %s has been notified.', 'gdpr' ), $email ), 'updated' );
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

	/**
	 * Deletes a user from the admin interface.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function delete_user() {
		if ( ! isset( $_POST['gdpr_delete_user'], $_POST['user_email'], $_POST['index'] ) || ! wp_verify_nonce( $_POST['gdpr_delete_user'], 'gdpr-request-delete-user' ) ) {
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( $_POST['user_email'] );
		$user = get_user_by( 'email', $email );
		$index = sanitize_text_field( $_POST['index'] );
		parent::remove_from_requests( $index );

		$token = GDPR::generate_pin();
		GDPR_Email::send( $user->user_email, 'delete-resolved', array( 'token' => $token ) );

		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User was removed from the site.', 'gdpr') );
		GDPR_Audit_Log::export_log( $user->ID, $token );
		wp_delete_user( $user->ID );

		/* translators: User email */
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

	/**
	 * Anonymize comments from a user.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function anonymize_comments() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-anonymize-comments-action' ) ) {
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
		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User comments were anonymized.', 'gdpr' ) );
		wp_send_json_success();
	}

	/**
	 * Reassign content to a different user.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function reassign_content() {
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

			$reassign_to_user = get_user_by( 'ID', $reassign_to );
			/* translators: 1: The post type, 2: The user the posts were reassigned to */
			GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'User %s were reassigned to %s.', 'gdpr' ), $post_type, $reassign_to_user->display_name ) );
			wp_send_json_success();
		}

		wp_send_json_error( esc_html__( 'Something went wrong. Please try again.', 'gdpr' ) );
	}

}
