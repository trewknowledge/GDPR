<?php

require_once( __DIR__ . '/../credentials.php' );

class Tests_Public_AddRecaptcha extends WP_UnitTestCase {
	function test_should_not_print_if_recaptcha_disabled() {
		update_option( 'gdpr_use_recaptcha', false );
		$this->expectOutputString( '' );
		GDPR_Public::add_recaptcha();
	}
	function test_should_not_print_if_site_key_missing() {
		update_option( 'gdpr_use_recaptcha', true );
		update_option( 'gdpr_recaptcha_site_key', '' );
		$this->expectOutputString( '' );
		GDPR_Public::add_recaptcha();
	}
	function test_should_not_print_if_secret_key_missing() {
		update_option( 'gdpr_use_recaptcha', true );
		update_option( 'gdpr_recaptcha_secret_key', '' );
		$this->expectOutputString( '' );
		GDPR_Public::add_recaptcha();
	}
	function test_should_print_if_all_options_provided() {
		$site_key = RECAPTCHA_SITE_KEY;
		update_option( 'gdpr_use_recaptcha', true );
		update_option( 'gdpr_recaptcha_site_key', $site_key );
		update_option( 'gdpr_recaptcha_secret_key', RECAPTCHA_SECRET_KEY );
		$this->expectOutputString( '<div class="g-recaptcha" data-sitekey="' . esc_attr( $site_key ) . '"></div>' );
		GDPR_Public::add_recaptcha();
	}
}
