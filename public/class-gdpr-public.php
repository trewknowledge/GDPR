<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init();

	}

	private function init() {
		add_shortcode( 'gdpr-forget-me', array($this, 'right_to_be_forgotten_button') );
		add_shortcode( 'gdpr-right-to-access', array($this, 'right_to_access_button') );
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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GDPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GDPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GDPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GDPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'gdpr', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'right_to_be_forgotten_confirmation_message' => esc_html__( 'Are you sure you want to remove all your personal information from our site?', 'gdpr' ),
			'right_to_access_confirmation_message' => esc_html__( 'You are about to generate and download a file with all data we have about you. Are you sure you want to continue?', 'gdpr' ),
		) );

	}

}
