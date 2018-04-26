<?php

/**
 * The requests functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The requests functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Requests {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @static
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	protected static $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @static
	 * @var    string    $version    The current version of this plugin.
	 */
	protected static $version;

	/**
	 * Allowed types of requests.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @static
	 * @var    array
	 */
	protected static $allowed_types = array( 'export-data', 'rectify', 'complaint', 'delete' );


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string    $plugin_name   The name of this plugin.
	 * @param  string    $version       The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		self::$plugin_name = $plugin_name;
		self::$version     = $version;
	}

	/**
	 * Allowed types getter.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @return array The allowed request types.
	 */
	protected function get_allowed_types() {
    return self::$allowed_types;
  }

  /**
   * Checks if the user has any content published on the site. Including comments.
   * @since  1.0.0
   * @author Fernando Claussen <fernandoclaussen@gmail.com>
   * @static
   * @param  WP_User/int 	$user The user object or the user ID.
   * @return bool               Whether the user has content or not.
   */
	static function user_has_content( $user ) {
		if ( ! $user instanceof WP_User ) {
			if ( ! is_int( $user ) ) {
				return;
			}
			$user = get_user_by( 'ID', $user );
		}

		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $pt ) {
			$post_count = count_user_posts( $user->ID, $pt);
			if ( $post_count > 0 ) {
				return true;
			}
		}

		$comments = get_comments( array(
			'author_email' => $user->user_email,
			'include_unapproved' => true,
			'number' => 1,
			'count' => true,
		) );

		if ( $comments ) {
			return true;
		}

		$extra_checks = apply_filters( 'gdpr_user_has_content', false );

		return $extra_checks;
	}

	/**
	 * Removes the user from the requests list.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @param  string $index The request key.
	 * @return bool          Whether the user was removed from the requests list.
	 */
	protected function remove_from_requests( $index ) {
		$requests = ( array ) get_option( 'gdpr_requests', array() );
		$index = sanitize_text_field( wp_unslash( $index ) );

		if ( array_key_exists( $index, $requests ) ) {
			unset( $requests[ $index ] );
			update_option( 'gdpr_requests', $requests );
			return true;
		}

		return false;
	}

	/**
	 * Set the user request as confirmed.
	 * Unschedules the cron jobs that clean up the requests that haven't been confirmed.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @param  string $key The request key.
	 * @return bool        Whether the request was confirmed or not.
	 */
	protected function confirm_request( $key ) {
		$key = sanitize_text_field( wp_unslash( $key ) );
		$requests = ( array ) get_option( 'gdpr_requests', array() );

		if ( empty( $requests ) || ! isset( $requests[ $key ] ) ) {
			return false;
		}

		$requests[ $key ]['confirmed'] = true;
		$type = $requests[ $key ]['type'];
		$email = $requests[ $key ]['email'];

		$user = get_user_by( 'email', $email );

		if ( $user instanceof WP_User ) {
			$meta_key = self::$plugin_name . "_{$type}_key";
			update_option( 'gdpr_requests', $requests );
			delete_user_meta( $user->ID, $meta_key );
			if ( $time = wp_next_scheduled( 'clean_gdpr_user_request_key', array( 'user_id' => $user->ID, 'meta_key' => $meta_key ) ) ) {
				wp_unschedule_event( $time, 'clean_gdpr_user_request_key', array( 'user_id' => $user->ID, 'meta_key' => $meta_key ) );
			}
		}

		return true;
	}

	/**
	 * The function the CRON job calls. It checks after a couple days if a request was confirmed or not.
	 * If it wasn't, the request gets removed.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string $key The request key.
	 */
	function clean_requests( $key ) {
		$key = sanitize_text_field( $key );
		$requests = ( array ) get_option( 'gdpr_requests', array() );

		if ( array_key_exists( $key, $requests ) ) {
			if ( ! $requests[ $key ]['confirmed'] ) {
				unset( $requests[ $key ] );
				update_option( 'gdpr_requests', $requests );
			}
		}
	}

	/**
	 * Whenever a user places a request, the request key is saved as a user meta for comparison.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  int    $user_id  The user ID.
	 * @param  string $meta_key The user meta key.
	 */
	function clean_user_request_key( $user_id, $meta_key ) {
		$user_id = ( int ) $user_id;
		$meta_key = sanitize_text_field( $meta_key );

		$meta = get_user_meta( $user_id, $meta_key, true );

		if ( $meta ) {
			delete_user_meta( $user_id, $meta_key );
		}

		/* translators: Name of the usermeta */
		GDPR_Audit_Log::log( $user_id, sprintf( esc_html__( 'User request expired. Removing %s user_meta.', 'gdpr' ), $meta_key ) );
	}

	/**
	 * Add a user to the request list. Set up the cleanup CRON job.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access protected
	 * @param  string  $email      The requestant email.
	 * @param  string  $type       The type of request.
	 * @param  string  $data       Some types of request have an extra field. E.g. Complaint and Rectify data.
	 * @param  string  $confirmed  If the request is confirmed or not.
	 */
	protected function add_to_requests( $email, $type, $data = null, $confirmed = false ) {
		$requests = ( array ) get_option( 'gdpr_requests', array() );

		$email = sanitize_email( $email );
		$type = sanitize_text_field( wp_unslash( $type ) );
		$data = sanitize_textarea_field( wp_unslash( $data ) );

		if ( ! in_array( $type, self::$allowed_types ) ) {
			return false;
		}

		$key = wp_generate_password( 20, false );
		$requests[ $key ] = array(
			'email'     => $email,
			'date'      => date( "F j, Y" ),
			'type'      => $type,
			'data'      => $data,
			'confirmed' => $confirmed
		);

		/**
		 * Remove user from the requests if it did not confirm in 2 days.
		 */
		$user = get_user_by( 'email', $email );
		if ( $user instanceof WP_User ) {
			$meta_key = self::$plugin_name . '_' . $type . '_key';
			update_user_meta( $user->ID, $meta_key, $key );
			if ( $time = wp_next_scheduled( 'clean_gdpr_user_request_key', array( 'user_id' => $user->ID, 'meta_key' => $meta_key ) ) ) {
				wp_unschedule_event( $time, 'clean_gdpr_user_request_key', array( 'user_id' => $user->ID, 'meta_key' => $meta_key ) );
			}
			wp_schedule_single_event( time() + 2 * DAY_IN_SECONDS, 'clean_gdpr_user_request_key', array( 'user_id' => $user->ID, 'meta_key' => $meta_key ) );
		}

		update_option( 'gdpr_requests', $requests );

		return $key;
	}

}
