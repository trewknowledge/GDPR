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
function gdpr_preferences( $text, $tab = 'gdpr-consent-management' ) {
	echo '<button type="button" class="gdpr-preferences" data-tab="' . esc_attr( $tab ) . '">' . esc_html( $text ) . '</button>';
}

function gdpr_preferences_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'text' => esc_html__( 'Privacy Preferences', 'gdpr' ),
			'tab'  => 'gdpr-consent-management',
		), $atts, 'gdpr_preferences'
	);

	ob_start();
	gdpr_preferences( $atts['text'], $atts['tab'] );
	return ob_get_clean();
}

add_shortcode( 'gdpr_preferences', 'gdpr_preferences_shortcode' );

/**
 * Load the request forms.
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string $type        The type of request.
 * @param  string $button_text The submit button text.
 */
function gdpr_request_form( $type, $button_text = '' ) {
	echo GDPR_Requests_Public::request_form( $type, $button_text ); // phpcs:ignore
}

/**
 * Create the request form shortcode.
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string $atts Shortcode attributes.
 */
function gdpr_request_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type' => '',
			'text' => '',
		), $atts, 'gdpr_request_form'
	);

	return GDPR_Requests_Public::request_form( $atts['type'], $atts['text'] );
}

add_shortcode( 'gdpr_request_form', 'gdpr_request_form_shortcode' );

function gdpr_get_consent_checkboxes( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => false,
		), $atts, 'gdpr_consent_checkboxes'
	);

	return GDPR::get_consent_checkboxes( $atts['id'] );
}

/**
 * Checks if a cookie is allowed
 * @since  1.0.0
 * @author Fernando Claussen <fernandoclaussen@gmail.com>
 * @param  string  $cookie_name The cookie name.
 * @return bool                 Whether the cookie is allowed or not.
 */
function is_allowed_cookie( $cookie_name, $exactMatch = false ) {

	gdpr_deprecated_function( 'is_allowed_cookie', '2.1.2' );

	if ( isset( $_COOKIE['gdpr']['allowed_cookies'] ) ) { // phpcs:ignore
		$allowed_cookies = array_map(
			'sanitize_text_field',
			json_decode(
				wp_unslash( $_COOKIE['gdpr']['allowed_cookies'] ), // phpcs:ignore
				true
			)
		);
		if ( in_array( $cookie_name, $allowed_cookies, true ) ) {
			return true;
		}
		if ( ! $exactMatch ) {
			$name = preg_quote( $cookie_name, '~' );
			$result = preg_grep( '~' . $name . '~', $allowed_cookies );
			return ! empty( $result );
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
		_deprecated_function( esc_html( $function ), esc_html( $version ), esc_html( $replacement ) );
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
	gdpr_deprecated_function( 'has_consent', '2.1.2' );
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( defined( 'WPCOM_IS_VIP_ENV' ) && WPCOM_IS_VIP_ENV ) {
			$consents = (array) get_user_attribute( $user->ID, 'gdpr_consents' );
		} else {
			$consents = (array) get_user_meta( $user->ID, 'gdpr_consents' );
		}
	} elseif ( isset( $_COOKIE['gdpr']['consent_types'] ) && ! empty( $_COOKIE['gdpr']['consent_types'] ) ) { // phpcs:ignore
		$consents = array_map( 'sanitize_text_field', (array) json_decode( wp_unslash( $_COOKIE['gdpr']['consent_types'] ) ) ); // phpcs:ignore
	}

	if ( isset( $consents ) && ! empty( $consents ) ) {
		if ( in_array( $consent, $consents, true ) ) {
			return true;
		}
	}

	return false;
}

function is_dnt() {
	return ( isset( $_SERVER['HTTP_DNT'] ) && '1' === $_SERVER['HTTP_DNT'] ); // phpcs:ignore
}
