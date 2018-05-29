<?php

class Tests_Public_EnqueueScripts extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
    $this->class_instance = new GDPR_Public( 'gdpr', '2.0.0' );
	}

	function test_should_not_load_recaptcha_js_if_recaptcha_disabled() {
		global $wp_scripts;
		update_option( 'gdpr_use_recaptcha', false );
		$this->class_instance->enqueue_scripts();
		$this->assertFalse( isset( $wp_scripts->registered['gdpr-recaptcha'] ) );
		wp_dequeue_script( 'gdpr' );
	}

	function test_should_not_load_recaptcha_js_if_recaptcha_site_key_missing() {
		global $wp_scripts;
		update_option( 'gdpr_use_recaptcha', true );
		update_option( 'gdpr_recaptcha_site_key', '' );
		$this->class_instance->enqueue_scripts();
		$this->assertFalse( isset( $wp_scripts->registered['gdpr-recaptcha'] ) );
		wp_dequeue_script( 'gdpr' );
	}

	function test_should_not_load_recaptcha_js_if_recaptcha_secret_key_missing() {
		global $wp_scripts;
		update_option( 'gdpr_use_recaptcha', true );
		update_option( 'gdpr_recaptcha_secret_key', '' );
		$this->class_instance->enqueue_scripts();
		$this->assertFalse( isset( $wp_scripts->registered['gdpr-recaptcha'] ) );
		wp_dequeue_script( 'gdpr' );
	}

	function test_should_load_recaptcha_js_if_recaptcha_credentials_were_filled() {
		global $wp_scripts;
		update_option( 'gdpr_use_recaptcha', true );
		update_option( 'gdpr_recaptcha_site_key', RECAPTCHA_SITE_KEY );
		update_option( 'gdpr_recaptcha_secret_key', RECAPTCHA_SECRET_KEY );
		$this->class_instance->enqueue_scripts();
		$this->assertTrue( isset( $wp_scripts->registered['gdpr-recaptcha'] ) );
		wp_dequeue_script( 'gdpr' );
	}

	function test_should_load_plugin_public_js_file() {
		global $wp_scripts;
		$this->class_instance->enqueue_scripts();
		$this->assertTrue( isset( $wp_scripts->registered['gdpr'] ) );
		wp_dequeue_script( 'gdpr' );
	}
}
