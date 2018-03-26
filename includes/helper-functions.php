<?php

if ( ! function_exists( 'gdpr_cookie_preferences' ) ) {
	function gdpr_cookie_preferences( $text ) {
		echo '<button type="button" class="gdpr-cookie-preferences">' . esc_html( $text ) . '</button>';
	}
}

if ( ! function_exists( 'gdpr_request_form' ) ) {
	function gdpr_request_form( $type ) {
		GDPR_Requests_Public::request_form( $type );
	}
}

if ( ! function_exists( 'is_allowed_cookie') ) {
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
