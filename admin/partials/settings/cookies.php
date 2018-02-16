<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
		<!-- main content -->
		<?php $tab = ( isset( $current_tab ) && ! empty( $current_tab ) ) ? '&tab=' . $current_tab : '' ; ?>
		<form method="post" class="gdpr-options" action="<?php echo admin_url( 'admin.php?page=gdpr-settings&settings-updated=1' . $tab ) ?>">
			<?php wp_nonce_field( 'gdpr_options_save' ); ?>
			<div id="post-body-content">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Settings', 'gdpr' ); ?></h2>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="banner_content"><?php esc_html_e( 'Banner content', 'gdpr' ); ?></label></th>
								<td>
									<textarea name="gdpr_options[cookies][banner_content]" id="banner_content" cols="53" rows="5"><?php echo wp_kses_post( $settings['cookies']['banner_content'] ) ?></textarea>
								</td>
							</tr>
							<tr>
								<th><label for="cookie-tabs"><?php esc_html_e( 'Add a tab', 'gdpr' ); ?></label></th>
								<td>
									<input type="text" id="cookie-tabs" class="regular-text">
									<button class="button button-primary add-tab"><?php esc_html_e( 'Add', 'gdpr' ); ?></button>
								</td>
							</tr>
						</table>
					</div><!-- .inside -->
				</div><!-- .postbox -->
				<div id="tabs">
					<?php if ( isset( $settings['cookies']['tabs'] ) ) : ?>
						<?php foreach ( $settings['cookies']['tabs'] as $tab_key => $tab ) : ?>
							<div class="postbox" id="cookie-tab-content-<?php echo esc_attr( $tab_key ); ?>">
								<h2 class="hndle"><?php echo esc_html( $settings['cookies']['tabs'][ $tab_key ]['name'] ) ?><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this tab.', 'gdpr' ); ?></span></button></h2>
								<input type="hidden" name="gdpr_options[cookies][tabs][<?php echo esc_attr( $tab_key ) ?>][name]" value="<?php echo esc_attr( $settings['cookies']['tabs'][ $tab_key ]['name'] ); ?>" />
								<div class="inside">
									<table class="form-table">
										<tr>
											<th><label for="always-active-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'Always active', 'gdpr' ); ?></label></th>
											<td>
												<label class="switch">
													<input type="checkbox" name="gdpr_options[cookies][tabs][<?php echo esc_attr( $tab_key ) ?>][always_active]" <?php checked( esc_attr( $tab['always_active'] ), 'on' ); ?> id="always-active-<?php echo esc_attr( $tab_key ); ?>">
													<span class="slider round"></span>
												</label>
											</td>
										</tr>
										<tr>
											<th><label for="tab-how-we-use-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'How we use', 'gdpr' ); ?></label></th>
											<td><textarea name="gdpr_options[cookies][tabs][<?php echo esc_attr( $tab_key ); ?>][how_we_use]" id="tab-how-we-use-<?php echo esc_attr( $tab_key ); ?>" cols="53" rows="5"><?php echo esc_html( $tab['how_we_use'] ); ?></textarea></td>
										</tr>
										<tr>
											<th><label for="cookies-used-<?php echo esc_attr( $tab_key ); ?>"><?php esc_html_e( 'Cookies used by the site', 'gdpr' ); ?></label></th>
											<td>
												<input type="text" name="gdpr_options[cookies][tabs][<?php echo esc_attr( $tab_key ); ?>][cookies_used]" value="<?php echo esc_attr( $tab['cookies_used'] ); ?>" id="cookies-used-<?php echo esc_attr( $tab_key ); ?>" class="regular-text" />
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
													<input type="hidden" name="gdpr_options[cookies][tabs][<?php echo esc_attr( $tab_key ); ?>][hosts][<?php echo esc_attr( $host_key ); ?>][name]" value="<?php echo esc_attr( $host_key ); ?>" />
													<div class="inside">
														<table class="form-table">
															<tr>
																<th><label for="hosts-cookies-used-<?php echo esc_attr( $host_key ); ?>">Cookies used</label></th>
																<td>
																	<input type="text" name="gdpr_options[cookies][tabs][<?php echo esc_attr( $tab_key ); ?>][hosts][<?php echo esc_attr( $host_key ); ?>][cookies_used]" value="<?php echo esc_attr( $host['cookies_used'] ); ?>" id="hosts-cookies-used-<?php echo esc_attr( $host_key ); ?>" class="regular-text" />
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
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<?php submit_button( 'Save Settings' ); ?>
			</div><!-- post-body-content -->
		</form>

		<!-- sidebar -->
		<div id="postbox-container-1" class="postbox-container">

			<div class="meta-box-sortables">

				<div class="postbox">

					<div class="handlediv" title="Click to toggle"><br></div>
					<!-- Toggle -->

					<h2 class="hndle"><span><?php esc_attr_e(
								'Sidebar Content Header', 'WpAdminStyle'
							); ?></span></h2>

					<div class="inside">
						<p><?php esc_attr_e( 'Everything you see here, from the documentation to the code itself, was created by and for the community. WordPress is an Open Source project, which means there are hundreds of people all over the world working on it. (More than most commercial platforms.) It also means you are free to use it for anything from your cat’s home page to a Fortune 500 web site without paying anyone a license fee and a number of other important freedoms.',
																'WpAdminStyle' ); ?></p>
					</div>
					<!-- .inside -->

				</div>
				<!-- .postbox -->

			</div>
			<!-- .meta-box-sortables -->

		</div>
		<!-- #postbox-container-1 .postbox-container -->

	</div>
	<!-- #post-body .metabox-holder .columns-2 -->

	<br class="clear">
</div>
