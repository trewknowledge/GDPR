<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their store.
 *
 * @package     WooCommerce/Admin
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GDPR_Setup_Wizard class.
 */
class GDPR_Setup_Wizard {

	/**
	 * Current step
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	private $steps = array();


	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( apply_filters( 'gdpr_enable_setup_wizard', true ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'gdpr-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'gdpr-setup' !== $_GET['page'] ) { // WPCS: CSRF ok, input var ok.
			return;
		}

		wp_enqueue_style( 'gdpr-admin', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-admin.css', array(), null, 'all' );

		$default_steps = array(
			'introduction' => array(
				'name'    => esc_html__( 'Introduction', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_introduction' ),
			),
			'consent-management' => array(
				'name'    => esc_html__( 'Consent Management', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_consent_management' ),
			),
			'cookie-management' => array(
				'name'    => esc_html__( 'Cookie Management', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_cookie_management' ),
			),
			'policy-tracker' => array(
				'name'    => esc_html__( 'Policy tracker', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_policy_tracker' ),
			),
			'telemetry-tracker' => array(
				'name'    => esc_html__( 'Telemetry tracker', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_telemetry_tracker' ),
			),
			'data-subject-rights' => array(
				'name'    => esc_html__( 'Data subject rights', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_data_subject_rights' ),
			),
			'audit-log' => array(
				'name'    => esc_html__( 'Audit Log', 'gdpr' ),
				'view'    => array( $this, 'gdpr_setup_audit_log' ),
			),
		);

		$this->steps = apply_filters( 'gdpr_setup_wizard_steps', $default_steps );
		$this->step  = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) ); // WPCS: CSRF ok, input var ok.

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 3.0.0
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
	}

	public function get_next_set_button() {
		?>
		<div class="gdpr-setup-controls">
			<a class="gdpr-setup-button" href="<?php echo esc_url( $this->get_next_step_link() ) ?>" title="<?php esc_attr_e( 'Next &rsaquo;', 'gdpr' ); ?>"><?php esc_html_e( 'Next &rsaquo;', 'gdpr' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'GDPR &rsaquo; Introduction', 'gdpr' ); ?></title>
			<?php wp_print_styles( 'gdpr-admin' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="gdpr-setup-body">
			<div class="gdpr-setup">
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
		<a class="gdpr-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to your dashboard', 'gdpr' ); ?></a>
				</div>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps = $this->steps;
		?>
		<ol class="gdpr-setup-steps">
			<?php foreach ( $output_steps as $step_key => $step ) : ?>
				<li class="
					<?php
					if ( $step_key === $this->step ) {
						echo 'active';
					} elseif ( array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true ) ) {
						echo 'done';
					}
					?>
				"><?php echo esc_html( $step['name'] ); ?></li>
			<?php endforeach; ?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="gdpr-setup-content">';
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';

	}

	protected function gdpr_setup_introduction() {
		?>
		<p><?php esc_html_e( 'This plugin\'s intention is to help you comply with the new GDPR law.', 'gdpr' ); ?></p>
		<p><?php esc_html_e( 'Alone, this plugin cannot make your site compliant. This is not a plug-and-play type of plugin. It requires a little bit of coding.', 'gdpr' ); ?></p>
		<h3><?php esc_html_e( 'This plugin covers', 'gdpr' ); ?>:</h3>
		<ul>
			<li><?php esc_html_e( 'Consent Management', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Cookie Management', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Policy tracker', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Telemetry Tracker', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Right to Access', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Right to Erasure', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Right to rectify', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Complaint forms', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Data breach notifications', 'gdpr' ); ?></li>
			<li><?php esc_html_e( 'Audit log', 'gdpr' ); ?></li>
		</ul>

		<?php /* translators: link to documentation */ ?>
		<p><?php echo sprintf( esc_html__( 'You can learn more about the features and how to set it up from our %s', 'gdpr' ), '<a href="http://gdpr-wp.com/knowledge-base" target="_blank" title="' . esc_attr__( 'Documentation', 'gdpr' ) . '">' . esc_html__( 'Documentation', 'gdpr' ) . '</a>' ); ?></p>
		<?php
		$this->get_next_set_button();
	}

	protected function gdpr_setup_consent_management() {
		?>
		<p><?php esc_html_e( 'Consents can be optional or not depending if you attach a policy page to it. If you do, we will also track that page for updates and ask users to re-consent on log in.', 'gdpr' ); ?></p>
		<p><?php esc_html_e( 'We also made a few functions and shortcodes available. Such functions can be used in an IF statement to check if a particular consent was given or not and then display or hide some elements or features.', 'gdpr' ); ?></p>
		<p><?php esc_html_e( 'Consents are logged to the user record for auditing or for access purposes.', 'gdpr' ); ?></p>
		<?php
		$this->get_next_set_button();
	}

	protected function gdpr_setup_cookie_management() {}
	protected function gdpr_setup_policy_tracker() {}
	protected function gdpr_setup_telemetry_tracker() {}
	protected function gdpr_setup_data_subject_rights() {}
	protected function gdpr_setup_audit_log() {}
}

new GDPR_Setup_Wizard();
