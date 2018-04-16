<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name and version.
 * Enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    GDPR
 * @subpackage public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string    $plugin_name    The name of the plugin.
	 * @param  string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-public.css', array( 'wp-jquery-ui-dialog' ), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-public.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'GDPR', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Prints the privacy bar for the end user to save the consent and cookie settings.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function privacy_bar() {
		if ( isset( $_COOKIE['gdpr_approved_cookies'] ) ) { // Input var okay.
			return;
		}

		$content = get_option( 'gdpr_cookie_banner_content', '' );
		$tabs = get_option( 'gdpr_cookie_popup_content', array() );
		$link_label = get_option( 'gdpr_cookie_banner_privacy_policy_link_label', '' );
		$privacy_policy_page = get_option( 'gdpr_privacy_policy_page', 0 );

		if ( empty( $content ) || empty( $tabs ) ) {
			return;
		}

		include plugin_dir_path( __FILE__ ) . 'partials/privacy-bar.php';
	}

	/**
	 * The privacy preferences modal.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function privacy_preferences_modal() {
		$cookie_privacy_excerpt = get_option( 'gdpr_cookie_privacy_excerpt', '' );
		$consent_types = get_option( 'gdpr_consent_types', array() );
		$privacy_policy_page = get_option( 'gdpr_privacy_policy_page', 0 );
		$approved_cookies = isset( $_COOKIE['gdpr_approved_cookies'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr_approved_cookies'] ) ) : array();
		$tabs = get_option( 'gdpr_cookie_popup_content', array() );
		if ( empty( $tabs ) ) {
			return;
		}

		include plugin_dir_path( __FILE__ ) . 'partials/privacy-preferences-modal.php';
	}

	/**
	 * The black overlay for the plugin modals.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function overlay() {
		echo '<div class="gdpr-overlay"></div>';
	}

	/**
	 * Prints the confirmation dialogs.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function confirmation_screens() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/confirmation-screens.php';
	}

	/**
	 * Update the user consents from the front end modal window.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function update_consents() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'update_consents' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['consents'] ) ) {
			wp_send_json_error( esc_html__( "You can't disable all consents.", 'gdpr' ) );
		}

		$user = wp_get_current_user();
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );

		delete_user_meta( $user->ID, 'gdpr_consents' );

		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User updated consents from modal. These are the user consents after the save:', 'gdpr' ) );
		foreach ( (array) $_POST['consents'] as $consent ) {
			$consent = sanitize_text_field( wp_unslash( $consent ) );
			add_user_meta( $user->ID, 'gdpr_consents', $consent );
			GDPR_Audit_Log::log( $user->ID, $consent['name'] );
		}

		wp_send_json_success( $user_consents );
	}

	/**
	 * Check if the user did not consent to the privacy policy
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @return bool     Whether the user consented or not.
	 */
	public function is_consent_needed() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$privacy_policy_page = get_option( 'gdpr_privacy_policy_page' );
		if ( ! $privacy_policy_page ) {
			return;
		}

		$page_obj = get_post( $privacy_policy_page );
		if ( empty( $page_obj->post_content ) ) {
			return;
		}

		$user = wp_get_current_user();
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );

		if ( ! in_array( 'privacy-policy', (array) $user_consents ) ) {
			include plugin_dir_path( __FILE__ ) . 'partials/reconsent-modal.php';
		}
	}

	/**
	 * Log the user out if they does not agree with the privacy policy terms when prompted.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function logout() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'user_disagree_with_terms' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		wp_logout();
		wp_send_json_success();
	}

	/**
	 * The user agreed with the privacy policy terms when prompted.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function agree_with_terms() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'user_agree_with_terms' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		$user = wp_get_current_user();
		add_user_meta( $user->ID, 'gdpr_consents', 'privacy-policy' );
		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User consented to the Privacy Policies.', 'gdpr' ) );
		wp_send_json_success();
	}

}
