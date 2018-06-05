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
	 * Allowed HTML for wp_kses.
	 * @since  1.1.0
	 * @access private
	 * @var    array   $allowed_html   The allowed HTML for wp_kses.
	 */
	private $allowed_html;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  string    $plugin_name    The name of the plugin.
	 * @param  string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->allowed_html = array(
			'a' => array(
				'href'   => true,
				'title'  => true,
				'target' => true,
			),
		);
	}

	/**
	 * Checks if recaptcha is being used and add the code.
	 * Should be called from the request forms.
	 * @since  1.4.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public static function add_recaptcha() {
		$use_recaptcha = get_option( 'gdpr_use_recaptcha', false );
		if ( $use_recaptcha ) {
			$site_key   = get_option( 'gdpr_recaptcha_site_key', '' );
			$secret_key = get_option( 'gdpr_recaptcha_secret_key', '' );

			if ( $site_key && $secret_key ) {
				echo '<div class="g-recaptcha" data-sitekey="' . esc_attr( $site_key ) . '"></div>';
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function enqueue_styles() {
		$disable_css = get_option( 'gdpr_disable_css', false );
		if ( ! $disable_css ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function enqueue_scripts() {
		$use_recaptcha = get_option( 'gdpr_use_recaptcha', false );
		if ( $use_recaptcha ) {
			$site_key   = get_option( 'gdpr_recaptcha_site_key', '' );
			$secret_key = get_option( 'gdpr_recaptcha_secret_key', '' );

			if ( $site_key && $secret_key ) {
				wp_enqueue_script( $this->plugin_name . '-recaptcha', 'https://www.google.com/recaptcha/api.js' );
			}
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name, 'GDPR', array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'logouturl'         => is_user_logged_in() ? esc_url( wp_logout_url( home_url() ) ) : '',
				'i18n'              => array(
					'aborting'              => esc_html__( 'Aborting', 'gdpr' ),
					'logging_out'           => esc_html__( 'You are being logged out.', 'gdpr' ),
					'continue'              => esc_html__( 'Continue', 'gdpr' ),
					'cancel'                => esc_html__( 'Cancel', 'gdpr' ),
					'ok'                    => esc_html__( 'OK', 'gdpr' ),
					'close_account'         => esc_html__( 'Close your account?', 'gdpr' ),
					'close_account_warning' => esc_html__( 'Your account will be closed and all data will be permanently deleted and cannot be recovered. Are you sure?', 'gdpr' ),
					'are_you_sure'          => esc_html__( 'Are you sure?', 'gdpr' ),
					'policy_disagree'       => esc_html__( 'By disagreeing you will no longer have access to our site and will be logged out.', 'gdpr' ),
				),
				'is_user_logged_in' => is_user_logged_in(),
				'refresh'           => get_option( 'gdpr_refresh_after_preferences_update', true ),
			)
		);
	}

	/**
	 * Prints the privacy bar for the end user to save the consent and cookie settings.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function privacy_bar() {
		$privacy_bar_enabled        = get_option( 'gdpr_enable_privacy_bar', true );
		$content                    = get_option( 'gdpr_cookie_banner_content', '' );
		$registered_cookies         = get_option( 'gdpr_cookie_popup_content', array() );
		$show_cookie_cat_checkboxes = get_option( 'gdpr_display_cookie_categories_in_bar', false );
		$button_text                = apply_filters( 'gdpr_privacy_bar_button_text', esc_html__( 'I Agree', 'gdpr' ) );
		$privacy_bar_enabled        = apply_filters( 'gdpr_privacy_bar_display', $privacy_bar_enabled );
		$hide_from_bots             = get_option( 'gdpr_hide_from_bots', true );

		if ( ! $privacy_bar_enabled || ( $hide_from_bots && $this->is_crawler() ) ) {
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
		$consent_types          = get_option( 'gdpr_consent_types', array() );
		$approved_cookies       = isset( $_COOKIE['gdpr']['allowed_cookies'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr']['allowed_cookies'] ) ) : array(); // WPCS: Input var ok, sanitization ok..
		$user_consents          = isset( $_COOKIE['gdpr']['consent_types'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr']['consent_types'] ) ) : array(); // WPCS: Input var ok, sanitization ok.
		$tabs                   = get_option( 'gdpr_cookie_popup_content', array() );
		$hide_from_bots         = get_option( 'gdpr_hide_from_bots', true );

		if ( $hide_from_bots && $this->is_crawler() ) {
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
		echo '<div class="gdpr gdpr-overlay"></div>';
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
	 * Update the user allowed cookies and types of consent.
	 * If the user is logged in, we also save consent to user meta.
	 * Ajax version of a previous function.
	 * @since  2.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function update_privacy_preferences() {
		if ( ! isset( $_POST['update-privacy-preferences-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['update-privacy-preferences-nonce'] ), 'gdpr-update-privacy-preferences' ) ) { // WPCS: Input var ok.
			wp_send_json_error(
				array(
					'title'   => esc_html__( 'Error!', 'gdpr' ),
					'content' => esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ),
				)
			);
		}
		$consents    = isset( $_POST['user_consents'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['user_consents'] ) ) : array(); // WPCS: Input var ok.
		$cookies     = isset( $_POST['approved_cookies'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['approved_cookies'] ) ) : array(); // WPCS: Input var ok.
		$all_cookies = isset( $_POST['all_cookies'] ) ? array_map( 'sanitize_text_field', (array) json_decode( wp_unslash( $_POST['all_cookies'] ) ) ) : array(); // WPCS: Input var ok, sanitization ok.

		$approved_cookies = array();
		if ( ! empty( $cookies ) ) {
			foreach ( $cookies as $cookie_array ) {
				$cookie_array = json_decode( wp_unslash( $cookie_array ) );
				foreach ( $cookie_array as $cookie ) {
					$approved_cookies[] = $cookie;
				}
			}
		}

		$cookies_to_remove = array_diff( $all_cookies, $approved_cookies );

		$cookies_as_json  = json_encode( $approved_cookies );
		$consents_as_json = json_encode( $consents );

		setcookie( 'gdpr[allowed_cookies]', $cookies_as_json, time() + YEAR_IN_SECONDS, '/' );
		setcookie( 'gdpr[consent_types]', $consents_as_json, time() + YEAR_IN_SECONDS, '/' );

		foreach ( $cookies_to_remove as $cookie ) {
			if ( GDPR::similar_in_array( $cookie, array_keys( $_COOKIE ) ) ) { // WPCS: Input var ok.
				$domain = get_site_url();
				$domain = wp_parse_url( $domain, PHP_URL_HOST );
				unset( $_COOKIE[ $cookie ] ); // WPCS: Input var ok.
				setcookie( $cookie, null, -1, '/', $domain );
				setcookie( $cookie, null, -1, '/', '.' . $domain );
			}
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			GDPR_Audit_Log::log( $user->ID, esc_html__( 'User updated their privacy preferences. These are the new approved cookies and consent preferences:', 'gdpr' ) );
			if ( ! empty( $consents ) ) {
				delete_user_meta( $user->ID, 'gdpr_consents' );
				foreach ( $consents as $consent ) {
					$consent = sanitize_text_field( wp_unslash( $consent ) );
					add_user_meta( $user->ID, 'gdpr_consents', $consent );
					GDPR_Audit_Log::log( $user->ID, 'Consent: ' . $consent );
				}
			}

			if ( ! empty( $approved_cookies ) ) {
				foreach ( $approved_cookies as $cookie ) {
					GDPR_Audit_Log::log( $user->ID, 'Cookie: ' . $cookie );
				}
			}
		}

		wp_send_json_success();
	}

	/**
	 * Check if the user did not consent to the privacy policy
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @return bool     Whether the user consented or not.
	 */
	public function is_consent_needed() {
		$consents = get_option( 'gdpr_consent_types', array() );
		if ( empty( $consents ) || ! is_array( $consents ) ) {
			return;
		}
		$required_consents = array_filter(
			$consents, function( $consent ) {
				return ! empty( $consent['policy-page'] );
			}
		);

		if ( ! $required_consents || ! is_user_logged_in() ) {
			return;
		}

		$user          = wp_get_current_user();
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );

		if ( ! is_array( $user_consents ) ) {
			return;
		}

		$updated_consents = array_filter(
			$required_consents, function( $consent, $consent_id ) use ( $user_consents ) {
				return ! in_array( $consent_id, $user_consents, true );
			}, ARRAY_FILTER_USE_BOTH
		);

		if ( empty( $updated_consents ) ) {
			return;
		}

		$reconsent_template = get_option( 'gdpr_reconsent_template', 'modal' );

		if ( 'bar' === $reconsent_template ) {
			include plugin_dir_path( __FILE__ ) . 'partials/reconsent-bar.php';
		} else {
			include plugin_dir_path( __FILE__ ) . 'partials/reconsent-modal.php';
		}

	}

	protected function is_crawler() {
	  return ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'] ) );
	}

	public function set_plugin_cookies() {
		$user_id = get_current_user_id();

		if ( ! isset( $_COOKIE['gdpr']['consent_types'] ) ) { // WPCS: Input var ok.
			if ( ! $user_id ) {
				setcookie( 'gdpr[consent_types]', '[]', time() + YEAR_IN_SECONDS, '/' );
			} else {
				$user_consents = get_user_meta( $user_id, 'gdpr_consents' );
				setcookie( 'gdpr[consent_types]', json_encode( $user_consents ), time() + YEAR_IN_SECONDS, '/' );
			}
		} else {
			if ( $user_id ) {
				$user_consents   = (array) get_user_meta( $user_id, 'gdpr_consents' );
				$cookie_consents = (array) json_decode( wp_unslash( $_COOKIE['gdpr']['consent_types'] ) ); // WPCS: Input var ok, sanitization ok.

				$intersect = array_intersect( $user_consents, $cookie_consents );
				$diff      = array_merge( array_diff( $user_consents, $intersect ), array_diff( $cookie_consents, $intersect ) );

				if ( ! empty( $diff ) ) {
					setcookie( 'gdpr[consent_types]', json_encode( $user_consents ), time() + YEAR_IN_SECONDS, '/' );
				}
			}
		}

		if ( ! isset( $_COOKIE['gdpr']['allowed_cookies'] ) ) { // WPCS: Input var ok.
			$registered_cookies = get_option( 'gdpr_cookie_popup_content', array() );
			$cookies            = array();
			if ( ! empty( $registered_cookies ) ) {
				$required_cookies = array_filter(
					$registered_cookies, function( $item ) {
						return 'required' === $item['status'] || 'soft' === $item['status'];
					}
				);
				if ( ! empty( $required_cookies ) ) {
					foreach ( $required_cookies as $category ) {
						$cookies_used = explode( ',', $category['cookies_used'] );
						foreach ( $cookies_used as $cookie ) {
							$cookies[] = trim( $cookie );
						}
					}
				}
			}

			if ( ! empty( $cookies ) ) {
				setcookie( 'gdpr[allowed_cookies]', json_encode( $cookies ), time() + YEAR_IN_SECONDS, '/' );
			} else {
				setcookie( 'gdpr[allowed_cookies]', '[]', time() + YEAR_IN_SECONDS, '/' );
			}
		}
	}

	public function agree_with_new_policies() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'gdpr-agree-with-new-policies' ) ) { // WPCS: Input var ok.
			wp_send_json_error(
				array(
					'title'   => esc_html__( 'Error!', 'gdpr' ),
					'content' => esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ),
				)
			);
		}
		$consents = isset( $_POST['consents'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['consents'] ) ) : array(); // WPCS: Input var ok.
		$user_id  = get_current_user_id();

		foreach ( $consents as $consent ) {
			add_user_meta( $user_id, 'gdpr_consents', $consent );
			GDPR_Audit_Log::log( $user_id, sprintf( esc_html__( 'User provided new consent for %1$s.', 'gdpr' ), esc_html( $consent ) ) );
		}

		wp_send_json_success();
	}

}
