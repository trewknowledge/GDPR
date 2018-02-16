<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://trewknowledge.com
 * @since      0.1.0
 *
 * @package    GDPR
 * @subpackage GDPR/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    GDPR
 * @subpackage GDPR/public
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options.
	 *
	 * @since 		0.1.0
	 * @access 		private
	 * @var 	  	string 	 $options    The plugin options.
	 */
	private $options;

	/**
	 * The Audit Log Class.
	 *
	 * @since 		0.1.0
	 * @access 		private
	 * @var 	  	string 	 $audit_log    The plugin Audit Log Class.
	 */
	private $audit_log;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-audit-log.php';

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->audit_log = new GDPR_Audit_Log( $this->plugin_name, $this->version );
		$this->set_options();

		add_shortcode( 'gdpr-forget-me', array($this, 'right_to_be_forgotten_button') );
		add_shortcode( 'gdpr-right-to-access', array($this, 'right_to_access_button') );
	}

	public function consent_modal() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();
		$should_seek_pp_consent = get_user_meta( $user->ID, 'gdpr_pp_consent_needed', true );
		$should_seek_tos_consent = get_user_meta( $user->ID, 'gdpr_tos_consent_needed', true );

		if ( $should_seek_pp_consent ) {
			$this->_display_consent_modal( 'pp' );
		}

		if ( $should_seek_tos_consent ) {
			$this->_display_consent_modal( 'tos' );
		}
	}

	private function _display_consent_modal( $page ) {
		$user = wp_get_current_user();
		if ( 'pp' === $page ) {
			$name = esc_html__( 'Privacy Policy', 'gdpr' );
		} else if ( 'tos' === $page ) {
			$name = esc_html__( 'Terms of Service', 'gdpr' );
		}
		$page_id = $this->options[$page . '-page'];
		$page_obj = get_post( $page_id );

		ob_start();
		?>
			<div class="gdpr-consent-modal">
				<div class="gdpr-consent-modal-content">
					<h3><?php echo sprintf( esc_html__( 'Our %s has been updated.', 'gdpr' ), $name ); ?></h3>
					<h4><?php echo esc_html__( 'To continue using the site you need to read the revised version and agree to the terms.', 'gdpr' ); ?></h4>
					<textarea readonly><?php echo $page_obj->post_content; ?></textarea>
					<div class="gdpr-consent-buttons">
						<a href="#" class="gdpr-agree" data-nonce="<?php echo wp_create_nonce( 'user_agree_with_terms' ) ?>" data-page="<?php echo $page ?>"><?php esc_html_e( 'Agree', 'gdpr' ); ?></a>
						<a href="#" class="gdpr-disagree" data-nonce="<?php echo wp_create_nonce( 'user_disagree_with_terms' ) ?>"><?php esc_html_e( 'Disagree', 'gdpr' ); ?></a>
					</div>
				</div>
			</div>
		<?php
		$return = ob_get_clean();
		echo $return;
	}

	public function logout() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'user_disagree_with_terms' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		wp_logout();
		wp_send_json_success();
	}

	public function agree_with_terms() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'user_agree_with_terms' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['page'] ) ) {
			wp_send_json_error();
		}

		$page = sanitize_text_field( wp_unslash( $_POST['page'] ) );

		$user = wp_get_current_user();
		update_user_meta( $user->ID, "gdpr_{$page}_consent_needed", 0 );
		$consents = get_user_meta( $user->ID, 'gdpr_consents', true );
		$registered_consents = $this->options['consents'];
		if ( 'tos' === $page ) {
			$consents['terms_of_service'] = $registered_consents['terms_of_service'];
			$this->audit_log->log( $user->ID, esc_html__( 'User consented to the new Terms of Service.', 'gdpr' ) );
		} else if ( 'pp' === $page ) {
			$consents['privacy_policy'] = $registered_consents['privacy_policy'];
			$this->audit_log->log( $user->ID, esc_html__( 'User consented to the new Privacy Policy.', 'gdpr' ) );
		}
		update_user_meta( $user->ID, 'gdpr_consents', $consents );
		wp_send_json_success();
	}

	public function right_to_be_forgotten_button() {
		$user = get_current_user_id();
		if ( ! $user ) {
			return;
		}

		ob_start();
		$classes = apply_filters( 'gdpr_button_class', 'gdpr-button' );
		$text = apply_filters( 'gdpr_right_to_be_forgotten_text', __( 'Forget me', 'gdpr' ) );
		?>
			<button class="gdpr-right-to-be-forgotten <?php echo esc_attr( $classes ); ?>" data-nonce="<?php echo wp_create_nonce( 'request_to_be_forgotten' ) ?>"><?php echo esc_html( $text ); ?></button>
		<?php
		return ob_get_clean();
	}

	public function right_to_access_button() {
		$user = get_current_user_id();
		if ( ! $user ) {
			return;
		}

		ob_start();
		$classes = apply_filters( 'gdpr_button_class', 'gdpr-button' );
		$text = apply_filters( 'gdpr_right_to_access_text', __( 'Download data', 'gdpr' ) );
		?>
			<button class="gdpr-right-to-access <?php echo esc_attr( $classes ); ?>" data-nonce="<?php echo wp_create_nonce( 'request_personal_data' ) ?>"><?php echo esc_html( $text ); ?></button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Sets the class variable $options
	 */
	private function set_options() {

		$this->options = get_option( $this->plugin_name . '_options' );

	} // set_options()

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'js/gdpr-public.js', array( 'jquery' ), $this->version, true );

		wp_localize_script( $this->plugin_name . '-public', 'gdpr', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );

	}

}
