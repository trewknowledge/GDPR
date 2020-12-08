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
		if ( ! isset( $_POST['gdpr_deletion_requests_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gdpr_deletion_requests_nonce'] ), 'gdpr-add-to-deletion-requests' ) ) { // phpcs:ignore
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : ''; // phpcs:ignore
		$user  = get_user_by( 'email', $email );

		if ( ! $user instanceof WP_User ) {
			add_settings_error( 'gdpr-requests', 'invalid-user', esc_html__( 'User not found.', 'gdpr' ), 'error' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'settings-updated' => true,
						),
						wp_get_referer() . '#delete'
					)
				)
			);
			exit;
		} else {
			if ( in_array( 'administrator', $user->roles, true ) ) {
				$admins_query = new WP_User_Query(
					array(
						'role' => 'Administrator',
					)
				);
				if ( 1 === $admins_query->get_total() ) {
					/* translators: User email */
					add_settings_error( 'gdpr-requests', 'invalid-request', sprintf( esc_html__( 'User %s is the only admin of the site. It cannot be deleted.', 'gdpr' ), $email ), 'error' );
					set_transient( 'settings_errors', get_settings_errors(), 30 );
					wp_safe_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'settings-updated' => true,
								),
								wp_get_referer() . '#delete'
							)
						)
					);
					exit;
				}
			}
		}

		$requests = (array) get_option( 'gdpr_requests', array() );

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
							'settings-updated' => true,
						),
						wp_get_referer() . '#delete'
					)
				)
			);
			exit;
		}

		$deletion_requests = array_filter(
			$requests, function( $arr ) {
				return 'delete' === $arr['type'];
			}
		);
		$user_has_already_requested = array_search( $email, array_column( $deletion_requests, 'email' ), true );

		if ( false !== $user_has_already_requested ) {
			add_settings_error( 'gdpr-requests', 'invalid-user', esc_html__( 'User already placed a deletion request.', 'gdpr' ), 'error' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'settings-updated' => true,
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
						'settings-updated' => true,
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
		if ( ! isset( $_POST['type'] ) ) { // phpcs:ignore
			wp_die( esc_html__( 'We could not verify the type of request you want to cancel.', 'gdpr' ) );
		}

		$type          = trim( strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) ); // phpcs:ignore
		$allowed_types = parent::get_allowed_types();

		if ( ! in_array( $type, $allowed_types, true ) ) {
			/* translators: The type of request */
			wp_die( sprintf( esc_html__( 'Type of request \'%s\' is not an allowed type.', 'gdpr' ), esc_html( $type ) ) );
		}

		$nonce_field = 'gdpr_cancel_' . $type . '_nonce';

		if ( ! isset( $_POST[ $nonce_field ], $_POST['user_email'], $_POST['index'] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $nonce_field ] ), 'gdpr-request-nonce' ) ) { // phpcs:ignore
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( wp_unslash( $_POST['user_email'] ) ); // phpcs:ignore
		$index = sanitize_text_field( wp_unslash( $_POST['index'] ) ); // phpcs:ignore

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
						'settings-updated' => true,
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
		if ( ! isset( $_POST['type'] ) ) { // phpcs:ignore
			wp_die( esc_html__( 'We could not verify the type of request you want to cancel.', 'gdpr' ) );
		}

		$type          = isset( $_POST['type'] ) ? trim( strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) ) : ''; // phpcs:ignore
		$allowed_types = parent::get_allowed_types();

		if ( ! in_array( $type, $allowed_types, true ) ) {
			/* translators: The type of request i.e. 'delete' */
			wp_die( sprintf( esc_html__( 'Type of request \'%s\' is not an allowed type.', 'gdpr' ), esc_html( $type ) ) );
		}

		$nonce_field = 'gdpr_' . $type . '_mark_resolved_nonce';

		if ( ! isset( $_POST[ $nonce_field ], $_POST['user_email'], $_POST['index'] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $nonce_field ] ), 'gdpr-mark-as-resolved' ) ) { // phpcs:ignore
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( wp_unslash( $_POST['user_email'] ) ); // phpcs:ignore
		$index = sanitize_text_field( wp_unslash( $_POST['index'] ) ); // phpcs:ignore

		parent::remove_from_requests( $index );

		GDPR_Email::send( $email, $type . '-resolved' );

		$user = get_user_by( 'email', $email );
		/* translators: User email. */
		GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'User %s request was marked as resolved by admin.', 'gdpr' ), $user->user_email ) );

		/* translators: User email. */
		add_settings_error( 'gdpr-requests', 'resolved', sprintf( esc_html__( 'Request was resolved. User %s has been notified.', 'gdpr' ), $email ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true,
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
		if ( ! isset( $_POST['gdpr_delete_user'], $_POST['user_email'], $_POST['index'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gdpr_delete_user'] ), 'gdpr-request-delete-user' ) ) { // phpcs:ignore
			wp_die( esc_html__( 'We could not verify the user email or the security token. Please try again.', 'gdpr' ) );
		}

		$email = sanitize_email( wp_unslash( $_POST['user_email'] ) ); // phpcs:ignore
		$user  = get_user_by( 'email', $email );
		$index = sanitize_text_field( wp_unslash( $_POST['index'] ) ); // phpcs:ignore
		parent::remove_from_requests( $index );

		$token = GDPR::generate_pin();
		GDPR_Email::send( $user->user_email, 'delete-resolved', array( 'token' => $token ) );

		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User was removed from the site.', 'gdpr' ) );
		GDPR_Audit_Log::export_log( $user->ID, $token );
		wp_delete_user( $user->ID );

		/* translators: User email */
		add_settings_error( 'gdpr-requests', 'new-request', sprintf( esc_html__( 'User %s was deleted from the site.', 'gdpr' ), $email ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true,
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
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-anonymize-comments-action' ) ) { // phpcs:ignore
			wp_send_json_error( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		$email         = isset( $_POST['userEmail'] ) ? sanitize_email( wp_unslash( $_POST['userEmail'] ) ) : ''; // phpcs:ignore
		$comment_count = isset( $_POST['commentCount'] ) ? (int) $_POST['commentCount'] : 0; // phpcs:ignore

		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof WP_User ) {
			wp_send_json_error( esc_html__( 'User not found.', 'gdpr' ) );
		}

		$comments = get_comments(
			array(
				'author_email'       => $user->user_email,
				'include_unapproved' => true,
				'number'             => $comment_count,
			)
		);

		foreach ( $comments as $comment ) {
			$new_comment                         = array();
			$new_comment['comment_ID']           = $comment->comment_ID;
			$new_comment['comment_author_IP']    = '0.0.0.0';
			$new_comment['comment_author_email'] = '';
			$new_comment['comment_author_url']   = '';
			$new_comment['comment_agent']        = '';
			$new_comment['comment_author']       = esc_html__( 'Guest', 'gdpr' );
			$new_comment['user_id']              = 0;
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
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-reassign-content-action' ) ) { // phpcs:ignore
			wp_send_json_error( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['userEmail'], $_POST['reassignTo'], $_POST['postType'], $_POST['postCount'] ) ) { // phpcs:ignore
			wp_send_json_error( esc_html__( 'Essential data missing. Please try again.', 'gdpr' ) );
		}

		$email       = sanitize_email( wp_unslash( $_POST['userEmail'] ) ); // phpcs:ignore
		$reassign_to = (int) $_POST['reassignTo']; // phpcs:ignore
		$post_type   = sanitize_text_field( wp_unslash( $_POST['postType'] ) ); // phpcs:ignore
		$post_count  = (int) $_POST['postCount']; // phpcs:ignore

		$user = get_user_by( 'email', $email );
		if ( ! $user instanceof WP_User ) {
			wp_send_json_error( esc_html__( 'User not found.', 'gdpr' ) );
		}

		$args = array(
			'author'         => $user->ID,
			'post_type'      => $post_type,
			'posts_per_page' => $post_count,
		);

		$posts = new WP_Query( $args );

		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				wp_update_post(
					array(
						'ID'          => get_the_ID(),
						'post_author' => $reassign_to,
					)
				);
			}
			wp_reset_postdata();

			$reassign_to_user = get_user_by( 'ID', $reassign_to );
			/* translators: 1: The post type, 2: The user the posts were reassigned to */
			GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'User %1$s were reassigned to %2$s.', 'gdpr' ), $post_type, $reassign_to_user->display_name ) );
			wp_send_json_success();
		}

		wp_send_json_error( esc_html__( 'Something went wrong. Please try again.', 'gdpr' ) );
	}

	/**
	 * Reset all plugin data
	 * @since  1.0.0
	 * @author Moutushi Mandal <moutushi82@gmail.com>
	 */
	public function reset_plugin_data() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr_reset_data' ) ) { // phpcs:ignore
			wp_send_json_error( esc_html__( 'We could not verify the security token. Please try again.', 'gdpr' ) );
		}

		// Add new options
		update_option( 'gdpr_disable_css', false );
		update_option( 'gdpr_use_recaptcha', false );
		update_option( 'gdpr_recaptcha_site_key', '' );
		update_option( 'gdpr_recaptcha_secret_key', '' );
		update_option( 'gdpr_add_consent_checkboxes_registration', true );
		update_option( 'gdpr_add_consent_checkboxes_checkout', true );
		update_option(
			'gdpr_cookie_popup_content', array(
				'necessary'   => array(
					'name'         => 'Necessary',
					'status'       => 'required',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
				'advertising' => array(
					'name'         => 'Advertising',
					'status'       => 'on',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
				'analytics'   => array(
					'name'         => 'Analytics',
					'status'       => 'on',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
				'other'       => array(
					'name'         => 'Other',
					'status'       => 'on',
					'cookies_used' => '',
					'how_we_use'   => '',
				),
			)
		);
		update_option( 'gdpr_refresh_after_preferences_update', true );
		update_option( 'gdpr_enable_privacy_bar', true );
		update_option( 'gdpr_display_cookie_categories_in_bar', false );
		update_option( 'gdpr_hide_from_bots', true );
		update_option( 'gdpr_reconsent_template', 'modal' );
		update_option( 'gdpr_cookie_banner_content', '' );
		update_option( 'gdpr_cookie_privacy_excerpt', '' );
		update_option( 'gdpr_consent_types', '' );
		update_option( 'gdpr_privacy_bar_position', 'bottom' );
		
		GDPR_Audit_Log::log( $user->ID, sprintf( esc_html__( 'Plugin data reset on %1$s.', 'gdpr' ), date( 'm/d/Y' ) ) );
		wp_send_json_success();

	}

}
