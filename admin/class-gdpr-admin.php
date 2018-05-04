<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name and version.
 * Enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Allowed HTML for wp_kses.
	 * @since  1.0.5
	 * @access private
	 * @var    array   $allowed_html   The allowed HTML for wp_kses.
	 */
	private $allowed_html;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @param  string    $plugin_name       The name of this plugin.
	 * @param  string    $version    The version of this plugin.
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->allowed_html = array(
			'a' => array(
				'href' => true,
				'title' => true,
				'target' => true,
			),
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function enqueue_styles() {
		add_thickbox();
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-admin.js', array( 'jquery', 'wp-util', 'jquery-ui-sortable' ), $this->version, false );
	}

	/**
	 * Adds a menu page for the plugin with all it's sub pages.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
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
			$menu_title  = sprintf( esc_html( 'GDPR %s' ), '<span class="awaiting-mod">' . count( $confirmed_requests ) . '</span>' );
		}

		add_menu_page( $page_title, $menu_title, $capability, $parent_slug, $function, $icon_url );

		$menu_title = esc_html__( 'Requests', 'gdpr' );
		$menu_slug  = 'gdpr-requests';
		$function   = array( $this, 'requests_page_template' );

		$requests_hook = add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );

		$menu_title = esc_html__( 'Tools', 'gdpr' );
		$menu_slug  = 'gdpr-tools';
		$function   = array( $this, 'tools_page_template' );

		$tools_hook = add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );

		$menu_title = esc_html__( 'Settings', 'gdpr' );
		$menu_slug  = 'gdpr-settings';
		$function   = array( $this, 'settings_page_template' );

		$settings_hook = add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );


		$menu_slug  = 'edit.php?post_type=telemetry';

		$cpt = 'telemetry';
		$cpt_obj = get_post_type_object( $cpt );

		if ( $cpt_obj ) {
			add_submenu_page( $parent_slug, $cpt_obj->labels->name, $cpt_obj->labels->menu_name, $capability, $menu_slug );
		}


		add_action( "load-{$requests_hook}", array( 'GDPR_Help', 'add_requests_help' ) );
		add_action( "load-{$tools_hook}", array( 'GDPR_Help', 'add_tools_help' ) );
		add_action( "load-{$settings_hook}", array( 'GDPR_Help', 'add_settings_help' ) );
		add_action( "load-edit.php", array( 'GDPR_Help', 'add_telemetry_help' ) );
	}

	/**
	 * Sanitizing user input on the cookie tabs.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  array $tabs The cookie tabs.
	 * @return array       The sanitized options.
	 */
	public function sanitize_cookie_tabs( $tabs ) {

		$output = array();
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
				'always_active' => isset( $props['always_active'] ) ? boolval( $props['always_active'] ) : 0,
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

	/**
	 * Register settings.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function register_settings() {
		$settings = array(
			'gdpr_privacy_policy_page'                      => 'intval',
			'gdpr_cookie_banner_content'                    => array( $this, 'sanitize_with_links' ),
			'gdpr_cookie_privacy_excerpt'                   => 'sanitize_textarea_field',
			'gdpr_cookie_popup_content'                     => array( $this, 'sanitize_cookie_tabs' ),
			'gdpr_email_limit'                              => 'intval',
			'gdpr_consent_types'                            => array( $this, 'sanitize_consents' ),
			'gdpr_deletion_needs_review'                    => 'boolval',
			'gdpr_disable_css'                              => 'boolval',
			'gdpr_enable_telemetry_tracker'                 => 'boolval',
			'gdpr_use_recaptcha'                            => 'boolval',
			'gdpr_recaptcha_site_key'                       => 'sanitize_text_field',
			'gdpr_recaptcha_secret_key'                     => 'sanitize_text_field',
		);
		foreach ( $settings as $option_name => $sanitize_callback ) {
			register_setting( 'gdpr', $option_name, array( 'sanitize_callback' => $sanitize_callback ) );
		}
	}

	/**
	 * Sanitize content but allow links.
	 * @param  string $string The string that will be sanitized.
	 * @return string         Sanitized string.
	 * @since  1.4.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function sanitize_with_links( $string ) {
		return wp_kses( $string, $this->allowed_html );
	}

	/**
	 * Sanitize the consents option when saving.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  array $consents The consents that were registered.
	 * @return array           The sanitized consents array.
	 */
	public function sanitize_consents( $consents ) {
		$output = array();
		if ( ! is_array( $consents ) ) {
			return $consents;
		}

		foreach ( $consents as $key => $props ) {
			if ( '' === $props['name'] || '' === $props['description'] ) {
				unset( $consents[ $key ] );
				continue;
			}
			$output[ $key ] = array(
				'name'         => sanitize_text_field( wp_unslash( $props['name'] ) ),
				'required'     => isset( $props['required'] ) ? boolval( $props['required'] ) : 0,
				'description'  => wp_kses( wp_unslash( $props['description'] ), $this->allowed_html ),
				'registration' => wp_kses( wp_unslash( $props['registration'] ), $this->allowed_html ),
			);
		}
		return $output;
	}

	/**
	 * Settings Page Template
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function settings_page_template() {
		$privacy_policy_page = get_option( 'gdpr_privacy_policy_page', 0 );
		$tabs        = array(
			'general'  => esc_html__( 'General', 'gdpr' ),
			'cookies'  => esc_html__( 'Cookies', 'gdpr' ),
			'consents' => esc_html__( 'Consents', 'gdpr' ),
		);

		$tabs = apply_filters( 'gdpr_settings_pages', $tabs );

		include_once plugin_dir_path( __FILE__ ) . 'partials/templates/tmpl-cookies.php';
		include_once plugin_dir_path( __FILE__ ) . 'partials/templates/tmpl-consents.php';

		include plugin_dir_path( __FILE__ ) . 'partials/settings.php';
	}

	/**
	 * Requests Page Template.
	 *
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
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
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function tools_page_template() {

		$tabs = array(
			'access' => esc_html__( 'Access Data', 'gdpr' ),
			'data-breach' => esc_html__( 'Data Breach', 'gdpr' ),
			'audit-log' => esc_html__( 'Audit Log', 'gdpr' ),
		);

		include plugin_dir_path( __FILE__ ) . 'partials/tools.php';
	}

	/**
	 * The data markup on the access data page.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function access_data() {
		if ( ! isset( $_POST['nonce'], $_POST['email'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-access-data' ) ) {
			wp_send_json_error();
		}

		$email = sanitize_email( $_POST['email'] );
		$user = get_user_by( 'email', $email );

		if ( ! $user instanceof WP_User ) {
			wp_send_json_error();
		}

		$usermeta = GDPR::get_user_meta( $user->ID );
		$comments      = get_comments( array(
			'author_email'       => $user->user_email,
			'include_unapproved' => true,
		) );
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );

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

		if ( ! empty( $user_consents ) ) {
			echo '<h2>Consent Given</h2>';
			echo '<table class="widefat">
				<thead>
					<tr>
						<th>' . esc_html__( 'Consent ID', 'gdpr' ) . '</th>
					</tr>
				</thead>';
			foreach ( $user_consents as $v ) {
				echo '<tr>';
					echo '<td class="row-title">' . esc_html( $v ) . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		}

		if ( ! empty( $comments ) ) {
			echo '<h2>Comments</h2>';
			foreach ( $comments as $v ) {
				echo '<table class="widefat">
					<thead>
						<tr>
							<th class="row-title">' . esc_html__( 'Comment Field', 'gdpr' ) . '</th>
							<th class="row-title">' . esc_html__( 'Comment Data', 'gdpr' ) . '</th>
						</tr>
					</thead>
					<tr>
						<td class="row-title">comment_author</td>
						<td>' . esc_html( $v->comment_author ) . '</td>
					</tr>
					<tr>
						<td class="row-title">comment_author_email</td>
						<td>' . esc_html( $v->comment_author_email ) . '</td>
					</tr>
					<tr>
						<td class="row-title">comment_author_url</td>
						<td>' . esc_html( $v->comment_author_url ) . '</td>
					</tr>
					<tr>
						<td class="row-title">comment_author_IP</td>
						<td>' . esc_html( $v->comment_author_IP ) . '</td>
					</tr>
					<tr>
						<td class="row-title">comment_date</td>
						<td>' . esc_html( $v->comment_date ) . '</td>
					</tr>
					<tr>
						<td class="row-title">comment_agent</td>
						<td>' . esc_html( $v->comment_agent ) . '</td>
					</tr>
					<tr>
						<td class="row-title">comment_content</td>
						<td>' . esc_html( $v->comment_content ) . '</td>
					</tr>
				</table><br>';
			}
		}

		if ( ! empty( $usermeta ) ) {
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

		}

		do_action( 'admin_access_data_extra_tables', $email );

		$result = ob_get_clean();
		wp_send_json_success( array( 'user_email' => $email, 'result' => $result ) );

	}

	/**
	 * The audit-log for the audit log email lookup.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function audit_log() {
		if ( ! isset( $_POST['nonce'], $_POST['email'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'gdpr-audit-log' ) ) {
			wp_send_json_error();
		}

		$email = sanitize_email( $_POST['email'] );
		$token = null;

		if ( isset( $_POST['token'] ) ) {
			$token = sanitize_text_field( wp_unslash( $_POST['token'] ) );
		}

		$log = GDPR_Audit_log::get_log( $email, $token );

		if ( ! $log ) {
			wp_send_json_error( esc_html__( 'No logs found for this email.', 'gdpr' ) );
		}

		wp_send_json_success( $log );
	}

	/**
	 * Admin notice when the user haven't picked a privacy policy page.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function privacy_policy_page_missing() {
		$privacy_page = get_option( 'gdpr_privacy_policy_page', '' );
		if ( ! empty( $privacy_page ) ) {
			return;
		}
		?>
			<div class="notice notice-error is-dismissible">
				<p>
					<strong><?php echo esc_html__( '[GDPR] You must select a Privacy Policy Page.', 'gdpr' ); ?></strong>
				</p>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=gdpr-settings' ) ) ?>" class="button button-primary"><?php esc_html_e( 'Select your Privacy Policy page', 'gdpr' ); ?></a>
				</p>
			</div>
		<?php
	}

	/**
	 * Admin notice when the privacy policy has been updated.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function privacy_policy_updated_notice() {
		$updated = get_option( 'gdpr_privacy_policy_updated' );
		if ( ! $updated ) {
			return;
		}
		?>
			<div class="notice notice-error privacy-page-updated-notice is-dismissible">
				<p>
					<strong><?php echo esc_html__( 'Your Privacy Policy have been updated. In case this was not a small typo fix, you must ask users for explicit consent again.', 'gdpr' ); ?></strong>
				</p>
				<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
					<?php wp_nonce_field( 'gdpr-seek_consent', 'privacy-policy-updated-nonce' ); ?>
					<input type="hidden" name="action" value="seek_consent">
					<p>
						<?php submit_button( esc_html__( 'Ask for consent', 'gdpr' ), 'primary', 'submit', false ); ?>
					</p>
				</form>
				<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" class="frm-ignore-privacy-update">
					<?php wp_nonce_field( 'gdpr-ignore_update', 'privacy-policy-ignore-update-nonce' ); ?>
					<input type="hidden" name="action" value="ignore_privacy_policy_update">
					<p>
						<?php submit_button( esc_html__( 'Ignore', 'gdpr' ), 'secondary', 'submit', false ); ?>
					</p>
				</form>
			</div>
		<?php
	}

	/**
	 * Sends a confirmation email to the admin email address before continuing with the data breach notification.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function send_data_breach_confirmation_email() {
		if ( ! isset( $_POST['gdpr_data_breach_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST[ 'gdpr_data_breach_nonce' ] ), 'gdpr-data-breach' ) ) {
			wp_die( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		if (
			! isset(
				$_POST['gdpr-data-breach-email-content'],
				$_POST['gdpr-data-breach-nature'],
				$_POST['gdpr-name-contact-details-protection-officer'],
				$_POST['gdpr-likely-consequences'],
				$_POST['gdpr-measures-taken']
			)
		) {
			wp_die( esc_html__( 'One or more required fields are missing. Please try again.', 'gdpr' ) );
		}

		$email = get_bloginfo( 'admin_email' );
		$user = wp_get_current_user();
		$content = sanitize_textarea_field( wp_unslash( $_POST['gdpr-data-breach-email-content'] ) );
		$nature = sanitize_textarea_field( wp_unslash( $_POST['gdpr-data-breach-nature'] ) );
		$office_contact = sanitize_textarea_field( wp_unslash( $_POST['gdpr-name-contact-details-protection-officer'] ) );
		$consequences = sanitize_textarea_field( wp_unslash( $_POST['gdpr-likely-consequences'] ) );
		$measures = sanitize_textarea_field( wp_unslash( $_POST['gdpr-measures-taken'] ) );

		$key = wp_generate_password( 20, false );
		update_option( 'gdpr_data_breach_initiated', array(
			'key' => $key,
			'content' => $content,
			'nature' => $nature,
			'office_contact' => $office_contact,
			'consequences' => $consequences,
			'measures' => $measures
		)	);

		$confirm_url = add_query_arg(
		  array(
		    'type' => 'data-breach-confirmed',
		    'key' => $key
		  ),
		  get_home_url() . wp_get_referer() . '#data-breach'
		);

		GDPR_Email::send(
			$email,
			'data-breach-request',
			array(
				'requester' => $user->user_email,
				'nature'=> $nature,
				'office_contact' => $office_contact,
				'consequences' => $consequences,
				'measures' => $measures,
				'confirm_url' => $confirm_url,
			)
		);

		if ( $time = wp_next_scheduled( 'clean_gdpr_data_breach_request' ) ) {
			wp_unschedule_event( $time, 'clean_gdpr_data_breach_request' );
		}
		wp_schedule_single_event( time() + 2 * DAY_IN_SECONDS, 'clean_gdpr_data_breach_request' );

		add_settings_error( 'gdpr', 'resolved', esc_html__( 'Data breach notification has been initialized. An email confirmation has been sent to the website controller.', 'gdpr' ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true
					),
					wp_get_referer() . '#data-breach'
				)
			)
		);
		exit;
	}

	/**
	 * CRON Job runs this after a couple days to cancel the data breach request.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function clean_data_breach_request() {
		delete_option( 'gdpr_data_breach_initiated' );
	}

	/**
	 * CRON job runs this to clean up the telemetry post type every 12 hours.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function telemetry_cleanup() {
		$args = array(
			'post_type' => 'telemetry',
			'posts_per_page' => -1,
			'fields' => 'ids',
		);

		$telemetry_posts = get_posts( $args );

		foreach ( $telemetry_posts as $post ) {
			wp_delete_post( $post, true );
		}
	}

	/**
	 * Sanitizes the consents during wordpress registration.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  WP_Error $errors             The error object.
	 * @param  string $sanitized_user_login The user login.
	 * @param  string $user_email           The user email.
	 * @return WP_Error                     WP_Error object with added errors or not.
	 */
	public function registration_errors( $errors, $sanitized_user_login, $user_email ) {
    $consent_types = get_option( 'gdpr_consent_types', array() );
    if ( empty( $consent_types ) ) {
    	return $errors;
    }

    foreach ( $consent_types as $key => $consent ) {
    	if ( $consent['required'] ) {
    		if ( ! isset( $_POST['user_consents'][ $key ] ) ) {
			    $errors->add( 'missing_required_consents', sprintf(
			    	'<strong>%s</strong>: %s %s.',
			    	__( 'ERROR', 'gdpr' ),
			    	$consent['name'],
			    	__( 'is a required consent', 'gdpr' )
			    ) );
    		}
    	}
    }
    return $errors;
	}

	/**
	 * Remove the Privacy Policy consent from all users. On next login they will need to consent again.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function seek_consent() {
		if ( ! isset( $_POST['privacy-policy-updated-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['privacy-policy-updated-nonce'] ), 'gdpr-seek_consent' ) ) {
			wp_die( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		delete_option( 'gdpr_privacy_policy_updated' );

		$users = get_users( array(
			'fields' => 'all_with_meta'
		) );

		foreach ( $users as $user ) {
			$usermeta = get_user_meta( $user->ID, 'gdpr_consents' );
			if ( in_array( 'privacy-policy', $usermeta ) ) {
				GDPR_Audit_Log::log( $user->ID, esc_html__( 'Privacy Policy has been updated. Removing the Privacy Policy consent and requesting new consent.', 'gdpr' ) );
				delete_user_meta( $user->ID, 'gdpr_consents', 'privacy-policy' );
			}
		}

		add_settings_error( 'gdpr', 'resolved', esc_html__( 'Users will have to consent to the updated privacy policy on login.', 'gdpr' ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'settings-updated' => true
					),
					wp_get_referer()
				)
			)
		);
		exit;
	}

	/**
	 * Check if the privacy policy page content has been updated or not.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  int     $ID   The page ID.
	 * @param  WP_Post $post The post object.
	 */
	public function privacy_policy_updated( $ID, $post ) {
		$privacy_page = (int) get_option( 'gdpr_privacy_policy_page', 0 );
		$ID = (int) $ID;
		if ( $ID === $privacy_page ) {
			$revisions = wp_get_post_revisions( $ID );
			$revisions = array_filter( $revisions, function( $rev ) {
				return strpos( $rev->post_name, 'autosave' ) === false;
			});

			reset( $revisions );
			if ( current( $revisions )->post_content !== $post->post_content ) {
				update_option( 'gdpr_privacy_policy_updated', 1 );
			}
		}
	}

	/**
	 * Ignore the privacy policy update. The update was probably just a typo fix.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function ignore_privacy_policy_update() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'gdpr-ignore_update' ) ) {
			wp_send_json_error( esc_html__( 'We could not verify the the security token. Please try again.', 'gdpr' ) );
		}

		delete_option( 'gdpr_privacy_policy_updated' );
		wp_send_json_success();
	}

	/**
	 * Add consent checkboxes to the user profile on wp dashboard.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  WP_User $user The user object.
	 */
	public function edit_user_profile( $user ) {
		$consent_types = get_option( 'gdpr_consent_types', array() );
		$user_consents = get_user_meta( $user->ID, 'gdpr_consents' );
		if ( empty( $consent_types ) ) {
			return;
		}
		?>
    <h3><?php _e( 'Consent Management', 'gdpr' ); ?></h3>

    <table class="form-table">
    	<?php foreach ( $consent_types as $consent_key => $consent ): ?>
	      <tr>
	        <th>
	        	<label><?php echo esc_html( $consent['name'] ); ?></label>
	        </th>
	        <td>
	        	<?php if ( $consent['required'] ): ?>
		        	<input type="checkbox" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>" disabled checked>
		        	<input type="hidden" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>">
	        	<?php else: ?>
		        	<input type="checkbox" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>" <?php echo ! empty( $user_consents ) ? checked( in_array( $consent_key, $user_consents, true ), 1, false ) : ''; ?>>
	        	<?php endif ?>
	          <span class="description"><?php echo esc_html( $consent['description'] ); ?></span>
	        </td>
	      </tr>
    	<?php endforeach ?>
    </table>

		<?php
	}

	/**
	 * Save the user consent preferences when he update his profile on wp dashboard.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  int $user_id The user ID.
	 */
	public function user_profile_update( $user_id ) {
		if ( ! isset( $_POST['user_consents'] ) ) {
			return;
		}

		$consents = array_map( 'sanitize_text_field', (array) $_POST['user_consents'] );

		GDPR_Audit_Log::log( $user_id, esc_html__( 'Profile Updated. These are the user consents after the save:', 'gdpr' ) );

		delete_user_meta( $user_id, 'gdpr_consents' );

		foreach ( (array) $consents as $consent ) {
			$consent = sanitize_text_field( wp_unslash( $consent ) );
			add_user_meta( $user_id, 'gdpr_consents', $consent );
			GDPR_Audit_Log::log( $user_id, $consent );
		}

		setcookie( "gdpr[consent_types]", json_encode( $consents ), time() + YEAR_IN_SECONDS, "/" );
	}

	/**
	 * Add the consent checkboxes to the checkout page.
	 * @since  1.3.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  int $fields The checkout fields.
	 */
	public function woocommerce_consent_checkboxes( $fields ) {
		$consent_types = get_option( 'gdpr_consent_types', array() );

		foreach ( $consent_types as $key => $consent ) {
			$required = ( isset( $consent['required'] ) && $consent['required'] ) ? 'required' : '';

			$fields['account']['user_consents_' . esc_attr( $key ) ] = array(
				'type'         => 'checkbox',
				'label'        => wp_kses( $consent['registration'], $this->allowed_html ),
				'required'     => $required,
			);
		}
		return $fields;
	}

	/**
	 * Save the user consent when registering from the checkout page.
	 * @since  1.3.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @param  int $customer_id The user ID.
	 * @param  array $data All data submitted during checkout.
	 */
	public function woocommerce_checkout_save_consent( $customer_id, $data ) {
		$data = array_filter( $data );
		$consent_arr = array_filter( array_keys( $data ), function( $item ) {
			return false !== strpos( $item, 'user_consents_' );
		} );

		foreach ( $consent_arr as $key => $value ) {
			$consent = str_replace( 'user_consents_', '', $value );
			add_user_meta( $customer_id, 'gdpr_consents', $consent );
		}
	}

}
