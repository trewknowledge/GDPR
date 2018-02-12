<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The admin-specific functionality of the plugin.
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

	/**
	 * The plugin options.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $options    The plugin options.
	 */
	private $options;

	/**
	 * The Audit Log Class.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $audit_log    The plugin Audit Log Class.
	 */
	private $audit_log;

	/**
	 * The Notifications Class.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 	  	string 	 $notifications    The plugin Notifications Class.
	 */
	private $notifications;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->set_options();
		$this->load_dependencies();
		self::save();
		$this->_check_requests_email_lookup();
	}

	function render_gdpr_section( $user ) {
		?>
		<h3><?php esc_html_e( 'Privacy Settings', 'gdpr' ); ?></h3>
		<?php
		$classes = apply_filters( 'gdpr_admin_button_class', 'gdpr-button' );
		$text = apply_filters( 'gdpr_right_to_be_forgotten_text', __( 'Forget me', 'gdpr' ) );
		?>
			<button class="gdpr-right-to-be-forgotten button <?php echo esc_attr( $classes ); ?>" data-nonce="<?php echo wp_create_nonce( 'request_to_be_forgotten' ) ?>"><?php echo esc_html( $text ); ?></button>
		<?php
		$text = apply_filters( 'gdpr_right_to_access_text', __( 'Download data', 'gdpr' ) );
		?>
			<button class="gdpr-right-to-access button <?php echo esc_attr( $classes ); ?>" data-nonce="<?php echo wp_create_nonce( 'request_personal_data' ) ?>"><?php echo esc_html( $text ); ?></button>
		<?php
	}

	private function _check_requests_email_lookup() {
		if (
			! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_POST['gdpr_action'], $_POST['email'], $_POST['_gdpr_email_lookup'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_gdpr_email_lookup'] ) ), 'gdpr-request-email-lookup' )
		) {
			return;
		}


		$action = sanitize_text_field( wp_unslash( $_POST['gdpr_action'] ) );

		if ( 'requests_email_lookup' !== $action ) {
			return;
		}

		$email = sanitize_email( wp_unslash( $_POST['email'] ) );
		$user = get_user_by( 'email', $email );

		if ( ! is_a( $user, 'WP_User' ) ) {
			return;
		}

		$this->_add_to_requests( $user );

	}

	private static function save() {
		if (
			! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_POST['gdpr_options'], $_POST['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'gdpr_options_save' )
		) {
			return;
		}

		$settings = get_option( 'gdpr_options', array() );
		$consents = array();
		if ( isset( $_POST['gdpr_options']['consents'] ) ) {
			foreach ( wp_unslash( $_POST['gdpr_options']['consents'] ) as $consent ) {
				$consents[sanitize_text_field( $consent['id'] )] = array(
					'title' => sanitize_text_field( $consent['title'] ),
					'description' => sanitize_text_field( $consent['description'] ),
				);
			}
		}
		$settings = array(
			'pp-page' => ( isset( $_POST['gdpr_options']['pp-page'] ) ) ? sanitize_text_field( wp_unslash( absint( $_POST['gdpr_options']['pp-page'] ) ) ) : '',
			'tos-page' => ( isset( $_POST['gdpr_options']['tos-page'] ) ) ? sanitize_text_field( wp_unslash( absint( $_POST['gdpr_options']['tos-page'] ) ) ) : '',
			'processor-contact-info' => ( isset( $_POST['gdpr_options']['processor-contact-info'] ) ) ? sanitize_email( wp_unslash( $_POST['gdpr_options']['processor-contact-info'] ) ) : '',
			'consents' => $consents,
		);

		update_option( 'gdpr_options', $settings );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - GDPR_Loader. Orchestrates the hooks of the plugin.
	 * - GDPR_i18n. Defines internationalization functionality.
	 * - GDPR_Admin. Defines all hooks for the admin area.
	 * - GDPR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for controlling the Audit Log and hashing of information.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-audit-log.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gdpr-notifications.php';

		$this->audit_log = new GDPR_Audit_Log( $this->plugin_name, $this->version );
		$this->notifications = new GDPR_Notification();

	}

	public function delete_user( $user_id ) {
		$token = $this->generate_pin();
		$this->notifications->send( $user_id, 'forgot', array( 'processor' => $this->options['processor-contact-info'], 'token' => $token ) );
		$this->audit_log->log( $user_id, esc_html__( 'User was removed from the site', 'gdpr') );
		$this->audit_log->export_log( $user_id, $token );
	}

	public function gdpr_audit_log_email_lookup() {
		if ( ! isset( $_POST['email'], $_POST['token'], $_POST['nonce'] ) ) {
			wp_send_json_error( esc_html__( 'Missing email, token or nonce values.', 'gdpr' ) );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-request-email-lookup' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$email = sanitize_email( wp_unslash( $_POST['email'] ) );
		$token = sanitize_text_field( wp_unslash( $_POST['token'] ) );

		$log = $this->audit_log->get_log( $email, $token );

		if ( ! $log ) {
			wp_send_json_error( esc_html__( 'We could not find a user with that email. If this user was already removed from the database, check on the logs folder for this user email and use the field below to decrypt the contents.', 'gdpr' ) );
		}

		wp_send_json_success( $log );

	}

	/**
	 * Hook that run when the user is first added to the database.
	 *
	 * @since 1.0.0
	 */
	public function user_register( $user_id ) {
		$meta_value = array();
		foreach ( $this->options['consents'] as $k => $consent ) {
			$meta_value[$k] = $consent;
		}
		$user = get_user_by( 'ID', $user_id );
		add_user_meta( $user_id, $this->plugin_name . '_consents', $meta_value, true );

		$this->audit_log->log( $user_id, sprintf( esc_html__( 'First name: %s', 'gdpr' ), $user->first_name ) );
		$this->audit_log->log( $user_id, sprintf( esc_html__( 'Last name: %s', 'gdpr' ), $user->last_name ) );
		$this->audit_log->log( $user_id, sprintf( esc_html__( "Email: %s \n", 'gdpr' ), $user->user_email ) );
		$this->audit_log->log( $user_id, esc_html__( 'User registered to the site.', 'gdpr' ) );
		foreach ( $this->options['consents'] as $consent ) {
			$this->audit_log->log( $user_id, sprintf( esc_html__( 'User gave explicit consent to %s', 'gdpr' ), $consent['title'] ) );
		}

	}

	private function _should_add_to_requests( $user ) {
		if ( ! is_a( $user, 'WP_User' ) ) {
			if ( ! is_int( $user ) ) {
				return;
			}
			$user = get_user_by( 'ID', $user );
		}

		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $pt ) {
			$post_count = count_user_posts( $user->ID, $pt);
			if ( $post_count > 0 ) {
				return true;
			}
		}

		$comments = get_comments( array(
			'author_email' => $user->user_email,
			'include_unapproved' => true,
			'number' => 1,
			'count' => true,
		) );

		if ( $comments ) {
			return true;
		}

		return false;
	}

	function anonymize_content() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-anonymize-comments-action' ) ) {
			wp_send_json_error( esc_html__( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$uid = absint( sanitize_text_field( wp_unslash( $_POST['uid'] ) ) );
		$comment_count = absint( sanitize_text_field( wp_unslash( $_POST['comment_count'] ) ) );

		$user = get_user_by( 'ID', $uid );
		if ( ! $user ) {
			wp_send_json_error( esc_html__( 'User does not exist', 'gdpr' ) );
		}

		$comments = get_comments( array(
			'author_email' => $user->user_email,
			'include_unapproved' => true,
			'number' => $comment_count,
		) );
		error_log(print_r($comments, true));

		foreach ( $comments as $comment ) {
			$new_comment = array();
			$new_comment['comment_ID'] = $comment->comment_ID;
			$new_comment['comment_author_IP'] = '0.0.0.0';
			$new_comment['comment_author_email'] = '';
			$new_comment['comment_author'] = esc_html__( 'Guest', 'gdpr' );
			wp_update_comment( $new_comment );
		}
		wp_send_json_success();
	}

	/**
	 * Function that runs when user confirms deletion from the site.
	 *
	 * @since 1.0.0
	 */
	public function forget_user() {
		if ( ! is_home() || ! is_front_page() || ! isset( $_GET['action'] ) ) {
			return;
		}

		if ( 'delete' === $_GET['action'] ) {
			if ( ! isset( $_GET['key'], $_GET['login'] ) ) {
				return;
			}
			$key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
			$login = sanitize_text_field( wp_unslash( $_GET['login'] ) );

			$user = get_user_by( 'login', $login );
			if ( ! $user ) {
				return;
			}

			$meta_key = get_user_meta( $user->ID, $this->plugin_name . '_delete_key', true );
			if ( empty( $meta_key ) ) {
				return;
			}
			if ( $key === $meta_key ) {
				$found_posts = $this->_should_add_to_requests( $user );

				if ( $found_posts ) {
					$this->_add_to_requests( $user );
				} else {
					require_once( ABSPATH.'wp-admin/includes/user.php' );
					if ( wp_delete_user( $user->ID ) ) {
						wp_logout();
						wp_safe_redirect( home_url() );
						exit;
					}
				}
			}
		}
	}

	/**
	 * Generates a random 6 digit pin.
	 * This pin is necessary to use with the audit log files.
	 *
	 * @since                  1.0.0
	 *
	 * @param  integer $length Number of digits.
	 * @return string          Returns the generated pin
	 */
	public function generate_pin( $length = 6 ) {
		$bytes = openssl_random_pseudo_bytes( $length / 2 );
		return strtoupper( bin2hex( $bytes ) );
	}

	/**
	 * Add the user to the requests table.
	 *
	 * @since            1.0.0
	 *
	 * @param WP_User/Int $user The WP_User instance or the user id.
	 * @return void
	 */
	private function _add_to_requests( $user ) {
		if ( ! is_a( $user, 'WP_User' ) ) {
			if ( ! is_int( $user ) ) {
				return;
			}
			$user = get_user_by( 'ID', $user );
		}

		$requests = get_option( 'gdpr_requests' ) ? get_option( 'gdpr_requests' ) : array();
		if ( array_key_exists( $user->ID, $requests ) ) {
			return;
		}


		$found_posts = $this->_should_add_to_requests( $user );

		if ( ! $found_posts ) {
			return;
		}

		$requests[$user->ID]['full_name'] = $user->user_firstname . ' ' . $user->user_lastname;
		$requests[$user->ID]['email'] = $user->user_email;
		$requests[$user->ID]['requested_on'] = date('Y/m/d');

		update_option( 'gdpr_requests', $requests );
	}

	/**
	 * Removes the user from the requests table and deletes the user from the site.
	 * This is run from the request table delete button.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function admin_forget_user() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-process-request-delete-action' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$uid = absint( sanitize_text_field( wp_unslash( $_POST['uid'] ) ) );

		require_once( ABSPATH.'wp-admin/includes/user.php' );
		if ( get_user_by( 'ID', $uid ) ) {
			wp_delete_user( $uid );
		}
		$requests = get_option( 'gdpr_requests' );
		unset( $requests[$uid] );
		update_option( 'gdpr_requests', $requests );

		wp_send_json_success();
	}

	/**
	 * Removes the user from the requests table.
	 * This is run from the request table cancel button.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function remove_user_from_review_table() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-process-request-delete-action' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$uid = absint( sanitize_text_field( wp_unslash( $_POST['uid'] ) ) );

		$requests = get_option( 'gdpr_requests' );
		unset( $requests[$uid] );
		update_option( 'gdpr_requests', $requests );

		wp_send_json_success();
	}

	/**
	 * Sends an email confirming the request of downloading the user emails.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function send_confirmation_email_data_breach() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-data-breach-request' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		if ( ! isset( $_POST['nature'], $_POST['contact'], $_POST['consequences'], $_POST['measures'] ) ) {
			wp_send_json_error();
		}

		$nature = sanitize_text_field( wp_unslash( $_POST['nature'] ) );
		$contact = sanitize_text_field( wp_unslash( $_POST['contact'] ) );
		$consequences = sanitize_text_field( wp_unslash( $_POST['consequences'] ) );
		$measures = sanitize_text_field( wp_unslash( $_POST['measures'] ) );

		$user = wp_get_current_user();
		$key  = wp_generate_password( 20, false );

		$data[ $key ] = array(
			'nature'       => $nature,
			'contact'      => $contact,
			'consequences' => $consequences,
			'measures'     => $measures,
		);

		update_option( $this->plugin_name . '_data_breach_key', $data );

		if ( $this->notifications->send( $user, 'data-breach', array(
			'user'         => $user,
			'key'          => $key,
			'nature'       => $nature,
			'contact'      => $contact,
			'consequences' => $consequences,
			'measures'     => $measures,
		) ) ) {
			wp_send_json_success();
		}

		wp_send_json_error();

	}

	/**
	 * Check if there is an action and a key query vars and if they match what is stored on the database.
	 * If it checks out, sends another email to the requesting user with a .txt file with all users emails.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function check_data_breach_key() {
		if (
			! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['action'], $_GET['key'] )
		) {
			return;
		}


		$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
		$key = sanitize_text_field( wp_unslash( $_GET['key'] ) );

		if ( 'data-breach' !== $action ) {
			return;
		}

		$stored_key = get_option( $this->plugin_name . '_data_breach_key' );
		if ( ! $stored_key ) {
			return;
		}
		if ( ! in_array( $key, array_keys( $stored_key ) ) ) {
			return;
		}

		$nature = $stored_key[ $key ]['nature'];
		$contact = $stored_key[ $key ]['contact'];
		$consequences = $stored_key[ $key ]['consequences'];
		$measures = $stored_key[ $key ]['measures'];

		$emails = array();
		$users = get_users( array( 'fields' => array( 'ID', 'user_email' ) ) );
		foreach ( $users as $user ) {
			$emails[] = $user->user_email;
			$this->audit_log->log( $user->ID, '#################' );
			$this->audit_log->log( $user->ID, esc_html__( 'DATA BREACH EVENT', 'gdpr' ) );
			$this->audit_log->log( $user->ID, '#################' );
			$this->audit_log->log( $user->ID, esc_html__( '#### Nature of the personal data breach ####', 'gdpr' ) );
			$this->audit_log->log( $user->ID, esc_html( $nature ) );
			$this->audit_log->log( $user->ID, esc_html__( '#### Name and contact details of the data protection officer ####', 'gdpr' ) );
			$this->audit_log->log( $user->ID, esc_html( $contact ) );
			$this->audit_log->log( $user->ID, esc_html__( '#### Likely consequences of the personal data breach ####', 'gdpr' ) );
			$this->audit_log->log( $user->ID, esc_html( $consequences ) );
			$this->audit_log->log( $user->ID, esc_html__( '#### Measures taken or proposed to be taken ####', 'gdpr' ) );
			$this->audit_log->log( $user->ID, esc_html( $measures ) );
			$this->audit_log->log( $user->ID, esc_html__( 'Controller/Processor requested all users emails in order to notify everyone of the breach.', 'gdpr' ) );
		}

		// generate and download file with all users emails.
		ob_start();
		foreach ( $emails as $email ) {
			echo "$email \n";
		}
		$filename = plugin_dir_path( __FILE__ ) . 'data-breach-users-export.txt';
		if ( file_put_contents( $filename, ob_get_clean() ) ) {
			$user = wp_get_current_user();
			$this->notifications->send( $user, 'data-breach-export', array(), array( $filename ) );
			unlink( $filename );
		}
		delete_option( $this->plugin_name . '_data_breach_key' );
		return true;
	}

	/**
	 * Reassigns posts from any post type to a different user.
	 *
	 * @return void
	 */
	function reassign_content_ajax_callback() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gdpr-process-request-reassign-action' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$uid = absint( sanitize_text_field( wp_unslash( $_POST['uid'] ) ) );
		$reassign_to = absint( sanitize_text_field( wp_unslash( $_POST['reassign_to'] ) ) );
		$pt = sanitize_text_field( wp_unslash( $_POST['pt'] ) );
		$post_count = absint( sanitize_text_field( wp_unslash( $_POST['post_count'] ) ) );

		$args = array(
			'author' => $uid,
			'post_type' => $pt,
			'posts_per_page' => $post_count,
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				wp_update_post( array(
					'ID' => $post->ID,
					'post_author' => $reassign_to,
				) );
				$new_author = get_user_by( 'ID', $reassign_to );
				$this->audit_log->log( $uid, sprintf( esc_html__( '(%s) Post %s reassigned to %s.', 'gdpr' ), $pt, get_the_title( $post->ID ), $new_author->first_name . ' ' . $new_author->last_name  ) );
			}
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	public function ignore_updated_page() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ignore-page-updated' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}
		$page = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
		if ( ! $page || ! in_array( $page, array( 'tos', 'pp' ) ) ) {
			wp_send_json_error();
		}

		$option = "gdpr_{$page}_updated";

		update_option( $option, 0 );
		wp_send_json_success();
	}

	public function notify_updated_page() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'notify-page-updated' ) ) {
			wp_send_json_error( __( 'Invalid or expired nonce.', 'gdpr' ) );
		}

		$page = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
		if ( ! $page || ! in_array( $page, array( 'tos', 'pp' ) ) ) {
			wp_send_json_error();
		}

		$users = get_users( array( 'role__not_in' => 'administrator' ) );

		foreach ( $users as $user ) {
			update_user_meta( $user->ID, "gdpr_{$page}_consent_needed", 1 );
			$consents = get_user_meta( $user->ID, 'gdpr_consents', true );
			if ( 'tos' === $page ) {
				unset( $consents['terms_of_service'] );
				$this->audit_log->log( $user->ID, esc_html__( 'The Terms of Service have been updated. All consents given by user were revoked until they accept the changes.', 'gdpr' ) );
			} elseif ( 'pp' === $page ) {
				unset( $consents['privacy_policy'] );
				$this->audit_log->log( $user->ID, esc_html__( 'The Privacy Policy have been updated. All consents given by user were revoked until they accept the changes.', 'gdpr' ) );
			}
			update_user_meta( $user->ID, 'gdpr_consents', $consents );
		}

		$option = "gdpr_{$page}_updated";

		update_option( $option, 0 );
		wp_send_json_success();
	}

	/**
	 * Hooks to the Wordpress Core registration form and add the consent text.
	 *
	 * @since 1.0.0
	 */
	public function register_form() {
		if ( ! isset( $this->options['tos-page'], $this->options['pp-page'] ) ) {
			return;
		}
		$tos = (int) $this->options['tos-page'];
		$pp = (int) $this->options['pp-page'];
		$text = sprintf(
			__( '<p>By registering to this site you agree to our <a href="%s">%s</a> and to our <a href="%s">%s</a>.</p><br>', 'gdpr' ),
			get_permalink( $tos ),
			get_the_title( $tos ),
			get_permalink( $pp ),
			get_the_title( $pp )
		);

		/**
		 * Filters the consent html
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The registration form consent html
		 */
		$text = apply_filters( 'gdpr_register_form_consent_text', $text );
	?>
			<?php echo wp_kses_post( $text ); ?>
	<?php
	} // register_form()

	/**
	 * Check if the Terms of Service or Privacy Policy pages have been updated.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $ID
	 * @param WP_Post $post
	 * @return void
	 */
	public function check_tos_pp_pages_updated( $ID, $post ) {
		if ( $ID == $this->options['tos-page'] ) {
			update_option( $this->plugin_name . '_tos_updated', 1 );
		}
		if ( $ID == $this->options['pp-page'] ) {
			update_option( $this->plugin_name . '_pp_updated', 1 );
		}
	}

	static function get_days_left( $date, $deadline ) {
		$interval = date_diff( date_create( $date ), date_create( date('Y/m/d') ) );

		if ( $interval->format('%a') > 30 ) {
			return '-' . $interval->format('%a') + $deadline;
		}

		return absint( $deadline - $interval->format('%a') );
	}

	/**
	 * Sets the class variable $options
	 */
	private function set_options() {

		$this->options = get_option( $this->plugin_name . '_options' );

	} // set_options()

	/**
	 * Adds a menu page for the plugin with all it's sub pages.
	 *
	 * @since   1.0.0
	 */
	public function add_menu() {

		$requests = get_option( 'gdpr_requests' );
		$requests = ( $requests ) ? count( $requests ) : 0;
		$requests_title = esc_attr( sprintf( esc_html__( '%d requests', 'gdpr' ), $requests ) );

		$badge = '<span class="update-plugins count-' . $requests . '" title="' . $requests_title . '">' . number_format_i18n( $requests ) . '</span>';

		$parent_slug = 'gdpr';
		$page_title = esc_html__( 'GDPR', 'gdpr' );
		$menu_title = sprintf( __( 'GDPR %s', 'gdpr' ), $badge );
		$capability = 'manage_options';
		$menu_slug = 'gdpr';
		$function = array( $this, 'gdpr_requests_page_template' );
		$icon_url = 'dashicons-id';

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );

		$page_title = esc_html__( 'Requests', 'gdpr' );
		$menu_title = sprintf( esc_html__( 'Requests %s', 'gdpr' ), $badge );
		$function = array( $this, 'gdpr_requests_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Settings', 'gdpr' );
		$menu_title = esc_html__( 'Settings', 'gdpr' );
		$menu_slug = 'gdpr-settings';
		$function = array( $this, 'gdpr_settings_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Audit Log', 'gdpr' );
		$menu_title = esc_html__( 'Audit Log', 'gdpr' );
		$menu_slug = 'gdpr-audit-log';
		$function = array( $this, 'gdpr_audit_log_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Right to Access', 'gdpr' );
		$menu_title = esc_html__( 'Right to Access', 'gdpr' );
		$menu_slug = 'gdpr-right-to-access';
		$function = array( $this, 'gdpr_right_to_access_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

		$page_title = esc_html__( 'Data Breach', 'gdpr' );
		$menu_title = esc_html__( 'Data Breach', 'gdpr' );
		$menu_slug = 'gdpr-data-breach';
		$function = array( $this, 'gdpr_data_breach_page_template' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

	} // add_menu()

	/**
	 * Requests Page Template
	 *
	 * @since 1.0.0
	 */
	public function gdpr_requests_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/requests.php';
	}

	/**
	 * Settings Page Template
	 *
	 * @since 1.0.0
	 */
	public function gdpr_settings_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/settings.php';
	}

	/**
	 * Audit Log Page Template
	 *
	 * @since 1.0.0
	 */
	public function gdpr_audit_log_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/audit-log.php';
	}

	/**
	 * Right to access page template.
	 *
	 * @since 1.0.0
	 */
	public function gdpr_right_to_access_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/right-to-access.php';
	}

	/**
	 * Right to access page template.
	 *
	 * @since 1.0.0
	 */
	public function gdpr_data_breach_page_template() {
		include	plugin_dir_path( __FILE__ ) . 'partials/data-breach.php';
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/gdpr-admin.js', array( 'jquery' ), $this->version, true );

		wp_localize_script( $this->plugin_name . '-admin', 'gdpr', array(
			'delete_text' => esc_html__( 'Delete', 'gdpr' ),
		) );

	}

}
