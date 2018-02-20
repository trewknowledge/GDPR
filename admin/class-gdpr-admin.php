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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// self::save();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/gdpr-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/gdpr-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Adds a menu page for the plugin with all it's sub pages.
	 *
	 * @since   1.0.0
	 */
	public function add_menu() {
		$parent_menu_title = esc_html__( 'GDPR', 'gdpr' );
		$capability = 'manage_options';
		$parent_slug = 'gdpr-settings';
		$function = array( $this, 'settings_page_template' );
		$icon_url = 'dashicons-id';

		add_menu_page( $parent_menu_title, $parent_menu_title, $capability, $parent_slug, $function, $icon_url );

		$menu_title = esc_html__( 'Settings', 'gdpr' );
		$menu_slug = 'gdpr-settings';
		$function = array( $this, 'settings_page_template' );

		add_submenu_page( $parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $function );

	} // add_menu()

	public function register_settings() {
		register_setting( 'gdpr', 'gdpr_cookie_banner_content' );
		register_setting( 'gdpr', 'gdpr_cookie_popup_content' );

		$section_id = 'cookie_banner_section';
		$admin_page_title = 'Cookie Settings';
		$page = 'gdpr-settings';

		add_settings_section(
      $section_id,
      $admin_page_title,
      null,
      $page
    );

		$option = 'gdpr_cookie_banner_content';
		$field_title = esc_html__( 'Banner content', 'gdpr' );
    add_settings_field(
	    $option,
	    $field_title,
	    array( $this, 'field_textarea' ),
	    $page,
	    $section_id,
	    array(
	    	'label_for' => $option,
	    )
		);

		$option = 'gdpr_cookie_popup_content';
		$field_title = esc_html__( 'Cookie Categories', 'gdpr' );
		add_settings_field(
	    $option,
	    $field_title,
	    array( $this, 'cookie_tabs' ),
	    $page,
	    $section_id,
	    array(
	    	'label_for' => $option
	    )
		);

	}


	function field_textarea( $args ) {
		if ( ! isset( $args['label_for'] ) || empty( $args['label_for'] ) ) {
			_doing_it_wrong( 'field_textarea', 'All settings fields must have the label_for argument.', '1.0.0' );
		}
		ob_start();
		$value = get_option( $args['label_for'], '' );
		?>
		<textarea name="<?php echo esc_attr( $args['label_for'] ); ?>" id="<?php echo esc_attr( $args['label_for'] ); ?>" cols="53" rows="5"><?php echo wp_kses_post( $value ); ?></textarea>
	  <?php if ( isset( $args['description'] ) && ! empty( $args['description'] ) ): ?>
	  	<span class="description"><?php echo esc_html( $args['description'] ); ?></span>
	  <?php endif ?>

		<?php
	  echo ob_get_clean();
	} // end sandbox_toggle_header_callback

	function cookie_tabs( $args ) {
		if ( ! isset( $args['label_for'] ) || empty( $args['label_for'] ) ) {
			_doing_it_wrong( 'cookie_tabs', 'All settings fields must have the label_for argument.', '1.0.0' );
		}
		ob_start();
		$value = get_option( $args['label_for'], array() );
		?>
		<input type="text" id="cookie-tabs" class="regular-text" placeholder="<?php esc_attr_e( 'Category name', 'gdpr' ); ?>">
		<button class="button button-primary add-tab"><?php esc_html_e( 'Add', 'gdpr' ); ?></button>
		<div id="tabs">
			<?php if ( ! empty( $value ) ): ?>
				<?php foreach ( $value as $tab_key => $tab ): ?>
					<div class="postbox" id="cookie-tab-content-<?php echo esc_attr( $tab_key ); ?>">
						<h2 class="hndle"><?php echo esc_html( $tab['name'] ) ?><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this tab.', 'gdpr' ); ?></span></button></h2>
						<input type="hidden" name="gdpr_cookie_popup_content[<?php echo esc_attr( $tab_key ) ?>][name]" value="<?php echo esc_attr( $tab['name'] ); ?>" />
						<div class="inside">
							<table class="form-table">
								<tr>
									<th><label for="always-active-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'Always active', 'gdpr' ); ?></label></th>
									<td>
										<label class="gdpr-switch">
											<input type="checkbox" name="gdpr_cookie_popup_content[<?php echo esc_attr( $tab_key ) ?>][always_active]" <?php checked( esc_attr( $tab['always_active'] ), 'on' ); ?> id="always-active-<?php echo esc_attr( $tab_key ); ?>">
											<span class="slider round"></span>
										</label>
									</td>
								</tr>
								<tr>
									<th><label for="tab-how-we-use-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'How we use', 'gdpr' ); ?></label></th>
									<td><textarea name="gdpr_cookie_popup_content[<?php echo esc_attr( $tab_key ); ?>][how_we_use]" id="tab-how-we-use-<?php echo esc_attr( $tab_key ); ?>" cols="53" rows="5"><?php echo esc_html( $tab['how_we_use'] ); ?></textarea></td>
								</tr>
								<tr>
									<th><label for="cookies-used-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'Cookies used by the site', 'gdpr' ); ?></label></th>
									<td>
										<input type="text" name="gdpr_cookie_popup_content[<?php echo esc_attr( $tab_key ); ?>][cookies_used]" value="<?php echo esc_attr( $tab['cookies_used'] ); ?>" id="cookies-used-<?php echo esc_attr( $tab_key ); ?>" class="regular-text" />
										<br>
										<span class="description"><?php esc_html_e( 'Comma separated list.', 'gdpr' ); ?></span>
									</td>
								</tr>
								<tr>
									<th><label for="hosts-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'Hosts', 'gdpr' ); ?></label></th>
									<td>
										<input type="text" id="hosts-<?php echo esc_attr( $tab_key ); ?>" class="regular-text" />
										<button class="button button-primary add-host" data-tabid="<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'Add', 'gdpr' ); ?></button>
										<br>
										<span class="description"><?php esc_html_e( '3rd party cookie hosts.', 'gdpr' ); ?></span>
									</td>
								</tr>
							</table>
							<div class="tab-hosts" data-tabid="<?php echo esc_attr( $tab_key ); ?>">
								<?php if ( $tab['hosts'] ) : ?>
									<?php foreach( $tab['hosts'] as $host_key => $host ): ?>
										<div class="postbox">
											<h2 class="hndle"><?php echo esc_attr( $host_key ); ?><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this host.', 'gdpr' ); ?></span></button></h2>
											<input type="hidden" name="gdpr_cookie_popup_content[<?php echo esc_attr( $tab_key ); ?>][hosts][<?php echo esc_attr( $host_key ); ?>][name]" value="<?php echo esc_attr( $host_key ); ?>" />
											<div class="inside">
												<table class="form-table">
													<tr>
														<th><label for="hosts-cookies-used-<?php echo esc_attr( $host_key ); ?>">Cookies used</label></th>
														<td>
															<input type="text" name="gdpr_cookie_popup_content[<?php echo esc_attr( $tab_key ); ?>][hosts][<?php echo esc_attr( $host_key ); ?>][cookies_used]" value="<?php echo esc_attr( $host['cookies_used'] ); ?>" id="hosts-cookies-used-<?php echo esc_attr( $host_key ); ?>" class="regular-text" />
															<br>
															<span class="description">Comma separated list.</span>
														</td>
													</tr>
												</table>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div><!-- .inside -->
					</div><!-- .postbox -->
				<?php endforeach ?>
			<?php endif ?>
		</div>
		<?php
	  echo ob_get_clean();
	} // end sandbox_toggle_header_callback

	/**
	 * Settings Page Template
	 *
	 * @since 1.0.0
	 */
	public function settings_page_template() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash ( $_GET['tab'] ) ) : 'cookies';
		$settings = get_option( 'gdpr_options', array() );
		$tabs = array(
			'cookies' => array(
				'name' => 'Cookies',
				'page' => 'gdpr-settings'
			),
		);

		$tabs = apply_filters( 'gdpr_settings_pages', $tabs );

		include	plugin_dir_path( __FILE__ ) . 'partials/settings.php';
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

		update_option( 'gdpr_options', wp_unslash( $_POST['gdpr_options'] ) );
	}

}
