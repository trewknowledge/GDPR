<?php
/**
 * The plugin helper functions
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

if ( ! function_exists( 'gdpr_cookie_preferences' ) ) {
	/**
	 * Adds a button to re-open the cookie preferences modal.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string $text The button text.
	 * @param  string $type The type of preferences. Possible options are `cookies` or `consents`
	 */
	function gdpr_preferences( $text, $type ) {
		echo '<button type="button" class="gdpr-preferences" data-type="' . esc_attr( $type ) . '">' . esc_html( $text ) . '</button>';
	}
}

if ( ! function_exists( 'gdpr_request_form' ) ) {
	/**
	 * Load the request forms.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string $type The type of request.
	 */
	function gdpr_request_form( $type ) {
		GDPR_Requests_Public::request_form( $type );
	}
}

if ( ! function_exists( 'gdpr_request_form_shortcode' ) ) {
	/**
	 * Create the request form shortcode.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string $atts Shortcode attributes.
	 */
	function gdpr_request_form_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'type' => '',
		), $atts, 'gdpr_request_form' );

		return GDPR_Requests_Public::request_form( $atts['type'] );
	}

	add_shortcode( 'gdpr_request_form', 'gdpr_request_form_shortcode' );
}

if ( ! function_exists( 'is_allowed_cookie') ) {
	/**
	 * Checks if a cookie is allowed
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string  $cookie_name The cookie name.
	 * @return bool                 Whether the cookie is allowed or not.
	 */
	function is_allowed_cookie( $cookie_name ) {
		if ( isset( $_COOKIE['gdpr_approved_cookies'] ) ) {
			$allowed_cookies = json_decode( wp_unslash( $_COOKIE['gdpr_approved_cookies'] ), true );
			if ( in_array( $cookie_name, $allowed_cookies ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'have_consent' ) ) {
	/**
	 * Checks if a user gave consent.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string $consent The consent id.
	 * @return bool            Whether the user gave consent or not.
	 */
	function have_consent( $consent ) {
		$user = wp_get_current_user();

		$consents = get_user_meta( $user->ID, 'gdpr_consents' );

		if ( in_array( $consent, $consents ) ) {
			return true;
		}

		return false;
	}
}
