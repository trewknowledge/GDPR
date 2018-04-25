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

/**
 * Adds a button to re-open the cookie preferences modal.
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string $text The button text.
 * @param  string $type The type of preferences. Possible options are `cookies` or `consents`
 */
function gdpr_preferences( $text ) {
	echo '<button type="button" class="gdpr-preferences">' . esc_html( $text ) . '</button>';
}

function gdpr_preferences_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'text' => esc_html__( 'Privacy Preferences', 'gdpr' ),
	), $atts, 'gdpr_preferences' );

	ob_start();
	gdpr_preferences( $atts['text'] );
	return ob_get_clean();
}

add_shortcode( 'gdpr_preferences', 'gdpr_preferences_shortcode' );

/**
 * Load the request forms.
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string $type The type of request.
 */
function gdpr_request_form( $type ) {
	echo GDPR_Requests_Public::request_form( $type );
}

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

/**
 * Checks if a cookie is allowed
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string  $cookie_name The cookie name.
 * @return bool                 Whether the cookie is allowed or not.
 */
function is_allowed_cookie( $cookie_name ) {
	if ( isset( $_COOKIE['gdpr']['allowed_cookies'] ) ) {
		$allowed_cookies = json_decode( wp_unslash( $_COOKIE['gdpr']['allowed_cookies'] ), true );
		$name = preg_quote( $cookie_name, '~' );
		$result = preg_grep( '~' . $name . '~', $allowed_cookies );
		if ( in_array( $cookie_name, $allowed_cookies ) || ! empty( $result ) ) {
			return true;
		}
	}

	return false;
}

function gdpr_deprecated_function( $function, $version, $replacement = null ) {
	if ( defined( 'DOING_AJAX' ) ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version );
		$log_string  = "The {$function} function is deprecated since version {$version}.";
		$log_string .= $replacement ? " Replace with {$replacement}." : '';
	} else {
		_deprecated_function( $function, $version, $replacement );
	}
}

/**
 * Checks if a user gave consent.
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string $consent The consent id.
 * @return bool            Whether the user gave consent or not.
 */
function have_consent( $consent ) {
	gdpr_deprecated_function( 'have_consent', '1.1.0', 'has_consent' );
	return has_consent( $consent );
}

function has_consent( $consent ) {

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$consents = (array) get_user_meta( $user->ID, 'gdpr_consents' );
	} else if ( isset( $_COOKIE['gdpr']['consent_types'] ) && ! empty( $_COOKIE['gdpr']['consent_types'] ) ) {
		$consents = array_map( 'sanitize_text_field', (array) json_decode( wp_unslash( $_COOKIE['gdpr']['consent_types'] ) ) );
	}

	if ( isset( $consents ) && ! empty( $consents ) ) {
		if ( in_array( $consent, $consents ) ) {
			return true;
		}
	}

	return false;
}
