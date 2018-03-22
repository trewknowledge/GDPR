<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Admin {

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

	protected $sections;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		add_thickbox();
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-admin.js', array( 'jquery', 'wp-util' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'GDPR', array(
			'cookie_popup_content' => 'gdpr_cookie_popup_content'
		) );
	}

	/**
	 * Adds a menu page for the plugin with all it's sub pages.
	 *
	 * @since   1.0.0
	 */
	public function add_menu() {
		$page_title  = esc_html__( 'GDPR', 'gdpr' );
		$capability  = 'manage_options';
		$parent_slug = 'gdpr-requests';
		$function    = array( $this, 'requests_page_template' );
		$icon_url    = 'dashicons-id';

		$requests = get_option( 'gdpr_requests', array() );
		$confirmed_requests = array_filter( $requests, function( $item ) {
			return $item['confirmed'] == true;
		} );

		$menu_title  = esc_html__( 'GDPR', 'gdpr' );
		if ( count( $confirmed_requests ) ) {
			$menu_title  = sprintf( esc_html__( 'GDPR %s', 'gdpr' ), '<span class="awaiting-mod">' . count( $confirmed_requests ) . '</span>' );
		}

		add_menu_page( $page_title, $menu_title, $capability, $parent_slug, $function, $icon_url );

		$menu_title = esc_html__( 'Requests', 'gdpr' );
		$menu_slug  = 'gdpr-requests';
		$function   = array( $this, 'requests_page_template' );

		add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );

		$menu_title = esc_html__( 'Tools', 'gdpr' );
		$menu_slug  = 'gdpr-tools';
		$function   = array( $this, 'tools_page_template' );

		add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );

		$menu_title = esc_html__( 'Settings', 'gdpr' );
		$menu_slug  = 'gdpr-settings';
		$function   = array( $this, 'settings_page_template' );

		add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );


		$menu_slug  = 'edit.php?post_type=telemetry';

		$cpt = 'telemetry';
		$cpt_obj = get_post_type_object( $cpt );

		add_submenu_page( $parent_slug, $cpt_obj->labels->name, $cpt_obj->labels->menu_name, $capability, $menu_slug );

	}

	function sanitize_cookie_tabs( $tabs ) {

		$output = array();
		error_log( $tabs );
		if ( ! is_array( $tabs ) ) {
			return $tabs;
		}

		foreach ( $tabs as $key => $props ) {
			if ( '' === $props['name'] || '' === $props['how_we_use'] ) {
				unset( $tabs[ $key ] );
				continue;
			}
			$output[ $key ] = array(
				'name'          => sanitize_text_field( wp_unslash( $props['name'] ) ),
				'always_active' => isset( $props['always_active'] ) ? sanitize_text_field( wp_unslash( $props['always_active'] ) ) : 0,
				'how_we_use'    => wp_kses_post( $props['how_we_use'] ),
				'cookies_used'  => sanitize_text_field( wp_unslash( $props['cookies_used'] ) ),
			);

			if ( isset( $props['hosts'] ) ) {
				foreach ( $props['hosts'] as $host_key => $host ) {
					if ( empty( $host['name'] ) || empty( $host['cookies_used'] ) || empty( $host['cookies_used'] ) ) {
						unset( $props['hosts'][ $host_key ] );
						continue;
					}
					$output[ $key ]['hosts'][ $host_key ] = array(
						'name'         => sanitize_text_field( wp_unslash( $host['name'] ) ),
						'cookies_used' => sanitize_text_field( wp_unslash( $host['cookies_used'] ) ),
						'optout'       => esc_url_raw( $host['optout'] ),
					);
				}
			}
		}
		return $output;
	}

	public function register_settings() {
		$settings = array(
			'gdpr_privacy_policy_page'    => 'intval',
			'gdpr_cookie_banner_content'  => 'sanitize_textarea_field',
			'gdpr_cookie_privacy_excerpt' => 'sanitize_textarea_field',
			'gdpr_cookie_popup_content'   => array( $this, 'sanitize_cookie_tabs' ),
		);
		foreach ( $settings as $option_name => $sanitize_callback ) {
			register_setting( 'gdpr', $option_name, array( 'sanitize_callback' => $sanitize_callback ) );
		}
	}

	/**
	 * Settings Page Template
	 *
	 * @since 1.0.0
	 */
	public function settings_page_template() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general'; // Input var okay. CSRF ok.
		$settings    = get_option( 'gdpr_options', array() );
		$tabs        = array(
			'general' => 'General',
			'cookies' => 'Cookies',
		);

		$tabs = apply_filters( 'gdpr_settings_pages', $tabs );

		if ( 'cookies' === $current_tab ) {
			include_once plugin_dir_path( __FILE__ ) . 'partials/templates/tmpl-cookies.php';
		}

		include plugin_dir_path( __FILE__ ) . 'partials/settings.php';
	}

	/**
	 * Requests Page Template.
	 *
	 * @since 1.0.0
	 */
	public function requests_page_template() {
		$requests = ( array ) get_option( 'gdpr_requests', array() );

		if ( ! empty( $requests ) ) {
			foreach ( $requests as $index => $request ) {
				if ( ! $request['confirmed'] ) {
					continue;
				}
				${$request['type']}[ $index ] = $request;
			}
		}

		$tabs = array(
			'rectify' => array(
				'name' => __( 'Rectify Data', 'gdpr' ),
				'count' => isset( $rectify ) ? count( $rectify ) : 0,
			),
			'complaint' => array(
				'name' => __( 'Complaint', 'gdpr' ),
				'count' => isset( $complaint ) ? count( $complaint ) : 0,
			),
			'delete' => array(
				'name' => __( 'Erasure', 'gdpr' ),
				'count' => isset( $delete ) ? count( $delete ) : 0,
			),
		);

		include plugin_dir_path( __FILE__ ) . 'partials/requests.php';
	}

	/**
	 * Tools Page Template.
	 *
	 * @since 1.0.0
	 */
	public function tools_page_template() {

		$tabs = array(
			'access' => 'Access Data',
			'audit-log' => 'Audit Log',
		);

		include plugin_dir_path( __FILE__ ) . 'partials/tools.php';
	}

	function access_data() {
		if ( ! isset( $_POST['nonce'], $_POST['email'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'access-data' ) ) {
			wp_send_json_error();
		}

		$email = sanitize_email( $_POST['email'] );
		$user = get_user_by( 'email', $email );

		if ( ! $user instanceof WP_User ) {
			wp_send_json_error();
		}

		$usermeta = GDPR::get_user_meta( $user->ID );

		ob_start();
		echo '<h2>' . $user->display_name . '<span>( ' . $email . ' )</span></h2>';
		echo '<table class="widefat">
			<tr>
				<td class="row-title">Username</td>
				<td>' . esc_html( $user->user_login ) . '</td>
			</tr>
			<tr>
				<td class="row-title">First Name</td>
				<td>' . esc_html( $user->first_name ) . '</td>
			</tr>
			<tr>
				<td class="row-title">Last Name</td>
				<td>' . esc_html( $user->last_name ) . '</td>
			</tr>
			<tr>
				<td class="row-title">Email</td>
				<td>' . esc_html( $user->user_email ) . '</td>
			</tr>
			<tr>
				<td class="row-title">Nickname</td>
				<td>' . esc_html( $user->nickname ) . '</td>
			</tr>
			<tr>
				<td class="row-title">Bio</td>
				<td>' . esc_html( $user->description ) . '</td>
			</tr>
			<tr>
				<td class="row-title">URL</td>
				<td>' . esc_url( $user->user_url ) . '</td>
			</tr>
			<tr>
				<td class="row-title">Registered</td>
				<td>' . esc_html( $user->user_registered ) . '</td>
			</tr>
			<tr>
				<td class="row-title">Roles</td>
				<td>' . esc_html( implode( ', ', $user->roles ) ) . '</td>
			</tr>
		</table>';

		echo '<h2>Metadata</h2>';
		echo '<table class="widefat">
			<thead>
				<tr>
					<th>' . esc_html__( 'Name', 'gdpr' ) . '</th>
					<th>' . esc_html__( 'Value', 'gdpr' ) . '</th>
				</tr>
			</thead>';
		foreach ( $usermeta as $k => $v ) {
			echo '<tr>';
			echo '<td class="row-title">' . esc_html( $k ) . '</td>';
			echo '<td>';
				foreach ( $v as $value ) {
					if ( is_serialized( $value ) ) {

						echo '<pre>' . print_r( maybe_unserialize( $value ), true ) . '</pre><br />';
					} else {
						echo print_r( $value, true ) . '<br />';
					}
				}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';

		$result = ob_get_clean();
		wp_send_json_success( array( 'user_email' => $email, 'result' => $result ) );

	}

	function privacy_policy_page_missing() {
		?>
			<div class="notice notice-error is-dismissible">
				<p>
					<strong><?php echo sprintf( __( 'You must select a Privacy Policy Page.', 'gdpr' ), admin_url( 'admin.php?page=gdpr-settings' ) ); ?></strong>
				</p>
			</div>
		<?php
	}

}
