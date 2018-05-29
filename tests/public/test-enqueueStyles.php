<?php

class Tests_Public_EnqueueStyles extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
    $this->class_instance = new GDPR_Public( 'gdpr', '2.0.0' );
	}

	function test_should_not_load_css_if_user_disable_from_plugin_settings() {
		global $wp_styles;
		update_option( 'gdpr_disable_css', true );
		$this->class_instance->enqueue_styles();
		$this->assertFalse( isset( $wp_styles->registered['gdpr'] ) );

		update_option( 'gdpr_disable_css', false );
		$this->class_instance->enqueue_styles();
		$this->assertTrue( isset( $wp_styles->registered['gdpr'] ) );
	}
}
