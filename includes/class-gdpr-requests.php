<?php

/**
 * The requests functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The requests functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Requests {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected static $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected static $version;

	protected static $allowed_types = array( 'access', 'rectify', 'portability', 'complaint', 'delete' );


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		self::$plugin_name = $plugin_name;
		self::$version     = $version;

	}

	protected function get_allowed_types() {
    return self::$allowed_types;
  }

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

	protected function remove_from_requests( $email, $type, $index ) {
		$requests = ( array ) get_option( 'gdpr_requests', array() );
		$email = sanitize_email( $email );
		$type = sanitize_text_field( $type );

		error_log('REQUESTS');
		error_log( print_r( $requests, true ) );
		error_log('Index');
		error_log( print_r( $index, true ) );

		$filtered_requests = array_filter( $requests, function( $arr ) use ( $type ) {
			return $type === $arr['type'];
		});
		error_log('Filtered Requests');
		error_log( print_r( $filtered_requests, true ) );

		$found = in_array( $email, array_column( $filtered_requests, 'email' ) );
		if ( $found ) {

			unset( $requests[ $index ] );
			update_option( 'gdpr_requests', $requests );
		}
	}

	protected function add_to_requests( $email, $type, $data = '' ) {
		$requests = ( array ) get_option( 'gdpr_requests', array() );

		$email = sanitize_email( $email );
		$type = sanitize_text_field( wp_unslash( $type ) );
		$data = sanitize_textarea_field( $data );

		if ( ! in_array( $type, self::$allowed_types ) ) {
			return false;
		}

		$requests[] = array(
			'email' => $email,
			'date'  => date( "F j, Y" ),
			'type'  => $type,
			'data' => $data,
		);

		update_option( 'gdpr_requests', $requests );

		return $requests;
	}

}
