<?php

/**
 * The admin notices functionality of the plugin.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The admin notices functionality of the plugin.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Admin_Notices {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $options    The plugin options.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Runs when the processor contact information is missing.
	 *
	 * @since 1.0.0
	 */
	public function processor_contact_missing() {
		?>
			<div class="notice error tos-missing-notice is-dismissible">
				<p>
					<strong><?php echo sprintf( __( 'You must set the processor contact information. Click <a href="%s">here</a> to fix that.', 'gdpr' ), admin_url( 'admin.php?page=gdpr-settings' ) ); ?></strong>
				</p>
			</div>
		<?php
	}

	/**
	 * Runs when the terms of service page information is missing.
	 *
	 * @since 1.0.0
	 */
	public function tos_missing() {
		?>
			<div class="notice error tos-missing-notice is-dismissible">
				<p>
					<strong><?php echo sprintf( __( 'You must select a Terms of Service Page. Click <a href="%s">here</a> to fix that.', 'gdpr' ), admin_url( 'admin.php?page=gdpr-settings' ) ); ?></strong>
				</p>
			</div>
		<?php
	}

	/**
	 * Runs when the terms of service page information has been updated.
	 *
	 * @since 1.0.0
	 */
	public function tos_updated() {
		?>
			<div class="notice error tos-updated-notice is-dismissible">
				<p>
					<strong><?php _e( 'Your Terms of Service have been updated. In case this was not a small typo fix, you must ask users for explicit consent again. <a href="#" class="button-primary tos-updated-notify">Ask for consent</a> <a href="#" class="button tos-updated-ignore">Ignore</a>', 'gdpr' ); ?></strong>
				</p>
			</div>
		<?php
	}

	/**
	 * Runs when the privacy policy page information is missing.
	 *
	 * @since 1.0.0
	 */
	public function pp_missing() {
		?>
			<div class="notice error pp-missing-notice is-dismissible">
				<p>
					<strong><?php echo sprintf( __( 'You must select a Terms of Service Page. Click <a href="%s">here</a> to fix that.', 'gdpr' ), admin_url( 'admin.php?page=gdpr-settings' ) ); ?></strong>
				</p>
			</div>
		<?php
	}

	/**
	 * Runs when the privacy policy page information has been updated.
	 *
	 * @since 1.0.0
	 */
	public function pp_updated() {
		?>
			<div class="notice error pp-updated-notice is-dismissible">
				<p>
					<strong><?php _e( 'Your Privacy Policy have been updated. In case this was not a small typo fix, you must ask users for explicit consent again. <a href="#" class="button-primary pp-updated-notify">Ask for consent</a> <a href="#" class="button pp-updated-ignore">Ignore</a>', 'gdpr' ); ?></strong>
				</p>
			</div>
		<?php
	}
}
