<?php

if ( ! function_exists( 'gdpr_cookie_preferences_trigger' ) ) {
	function gdpr_cookie_preferences_trigger( $text ) {
		echo '<button type="button" class="gdpr-cookie-preferences">' . esc_html( $text ) . '</button>';
	}
}
