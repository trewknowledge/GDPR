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
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->allowed_html = array(
			'a' => array(
				'href' => true,
				'title' => true,
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
			$site_key = get_option( 'gdpr_recaptcha_site_key', '' );
			$secret_key = get_option( 'gdpr_recaptcha_secret_key', '' );

			if ( $site_key && $secret_key ) {
				echo '<div class="g-recaptcha" data-sitekey="' . $site_key . '"></div>';
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
			$site_key = get_option( 'gdpr_recaptcha_site_key', '' );
			$secret_key = get_option( 'gdpr_recaptcha_secret_key', '' );

			if ( $site_key && $secret_key ) {
				wp_enqueue_script( $this->plugin_name . '-recaptcha', 'https://www.google.com/recaptcha/api.js' );
			}
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'GDPR', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'aborting' => esc_html__( 'Aborting', 'gdpr' ),
			'is_user_logged_in' => is_user_logged_in(),
		) );
	}

	/**
	 * Prints the privacy bar for the end user to save the consent and cookie settings.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function privacy_bar() {
		$content             = get_option( 'gdpr_cookie_banner_content', '' );
		$tabs                = get_option( 'gdpr_cookie_popup_content', array() );

		if ( empty( $content ) ) {
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
		$approved_cookies = isset( $_COOKIE['gdpr']['allowed_cookies'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr']['allowed_cookies'] ) ) : array();
		$user_consents = isset( $_COOKIE['gdpr']['consent_types'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr']['consent_types'] ) ) : array();
		$tabs = get_option( 'gdpr_cookie_popup_content', array() );

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
	 * Update the user allowed cookies and types of consent.
	 * If the user is logged in, we also save consent to user meta.
	 * @since  1.1.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function update_privacy_preferences() {
		if ( ! isset( $_POST['update-privacy-preferences-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['update-privacy-preferences-nonce'] ), 'gdpr-update_privacy_preferences' ) ) {
			wp_die( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['user_consents'] ) ) {
			wp_die( esc_html__( "You need to at least consent to our Privacy Policy.", 'gdpr' ) );
		}

		$consents = array_map( 'sanitize_text_field', (array) $_POST['user_consents'] );
		$cookies = isset( $_POST['approved_cookies'] ) ? array_map( 'sanitize_text_field', (array) $_POST['approved_cookies'] ) : array();

		$approved_cookies = array();
		if ( ! empty( $cookies ) ) {
			foreach ( $cookies as $cookieArr ) {
				$cookieArr = json_decode( wp_unslash( $cookieArr ) );
				foreach ( $cookieArr as $cookie ) {
					$approved_cookies[] = $cookie;
				}
			}
		}

		$cookies_as_json = json_encode( $approved_cookies );
		$consents_as_json = json_encode( $consents );

		setcookie( "gdpr[allowed_cookies]", $cookies_as_json, time() + YEAR_IN_SECONDS, "/" );
		setcookie( "gdpr[consent_types]", $consents_as_json, time() + YEAR_IN_SECONDS, "/" );

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

		wp_safe_redirect( esc_url_raw( wp_get_referer() ) );
		exit;
	}

	/**
	 * Check if the user did not consent to the privacy policy
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @return bool     Whether the user consented or not.
	 */
	public function is_consent_needed() {
		$privacy_policy_page = get_option( 'gdpr_privacy_policy_page' );
		if ( ! $privacy_policy_page ) {
			return;
		}

		$page_obj      = get_post( $privacy_policy_page );
		$user          = wp_get_current_user();
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );

		include plugin_dir_path( __FILE__ ) . 'partials/reconsent-modal.php';
	}

	/**
	 * Log the user out if they does not agree with the privacy policy terms when prompted.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function logout() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-user_disagree_with_terms' ) ) {
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
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-user_agree_with_terms' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		$user = wp_get_current_user();
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );
		$user_consents[] = 'privacy-policy';
		$user_consents = array_unique( $user_consents );
		add_user_meta( $user->ID, 'gdpr_consents', 'privacy-policy' );
		setcookie( "gdpr[consent_types]", json_encode( $user_consents ), time() + YEAR_IN_SECONDS, "/" );
		GDPR_Audit_Log::log( $user->ID, esc_html__( 'User consented to the Privacy Policies.', 'gdpr' ) );
		wp_send_json_success();
	}

	public function set_plugin_cookies() {
		$user_id = get_current_user_id();

		if ( ! isset( $_COOKIE['gdpr']['consent_types'] ) ) {
			if ( ! $user_id ) {
				setcookie( 'gdpr[consent_types]', '[]', time() + YEAR_IN_SECONDS, "/" );
			} else {
				$user_consents = get_user_meta( $user_id, 'gdpr_consents' );
				setcookie( "gdpr[consent_types]", json_encode( $user_consents ), time() + YEAR_IN_SECONDS, "/" );
			}
		} else {
			if ( $user_id ) {
				$user_consents = (array) get_user_meta( $user_id, 'gdpr_consents' );
				$cookie_consents = (array) json_decode( wp_unslash( $_COOKIE['gdpr']['consent_types'] ) );

				$intersect = array_intersect( $user_consents, $cookie_consents );
				$diff = array_merge( array_diff( $user_consents, $intersect ), array_diff( $cookie_consents, $intersect ) );

				if ( ! empty( $diff ) ) {
					setcookie( "gdpr[consent_types]", json_encode( $user_consents ), time() + YEAR_IN_SECONDS, "/" );
				}
			}
		}


		if ( ! isset( $_COOKIE['gdpr']['allowed_cookies'] ) ) {
			$registered_cookies = get_option( 'gdpr_cookie_popup_content', array() );
			$cookies = array();
			if ( ! empty( $registered_cookies ) ) {
				$required_cookies = array_filter( $registered_cookies, function( $item ) {
					return $item['always_active'] == 1;
				});
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
				setcookie( "gdpr[allowed_cookies]", json_encode( $cookies ), time() + YEAR_IN_SECONDS, "/" );
			} else {
				setcookie( "gdpr[allowed_cookies]", '[]', time() + YEAR_IN_SECONDS, "/" );
			}
		}
	}

}
