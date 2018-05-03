<?php

/**
 * This file is used to markup the cookie preferences window.
 *
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage public/partials
 */
?>

<div class="gdpr gdpr-privacy-preferences">
	<div class="gdpr-wrapper">
		<form method="post" class="gdpr-privacy-preferences-frm" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
			<input type="hidden" name="action" value="gdpr_update_privacy_preferences">
			<?php wp_nonce_field( 'gdpr-update_privacy_preferences', 'update-privacy-preferences-nonce' ); ?>
			<header>
				<div class="gdpr-box-title">
					<h3><?php esc_html_e( 'Privacy Preference Center', 'gdpr' ); ?></h3>
					<span class="gdpr-close"></span>
				</div>
			</header>
			<div class="gdpr-mobile-menu">
				<button type="button"><?php esc_html_e( 'Options', 'gdpr' ); ?></button>
			</div>
			<div class="gdpr-content">
				<ul class="gdpr-tabs">
					<li><button type="button" class="gdpr-tab-button gdpr-active" data-target="gdpr-consent-management"><?php esc_html_e( 'Consent Management', 'gdpr' ); ?></button></li>
					<?php reset( $tabs ); ?>
					<li><button type="button" class="gdpr-tab-button gdpr-cookie-settings" data-target="<?php echo esc_attr( key( $tabs ) ); ?>"><?php esc_html_e( 'Cookie Settings', 'gdpr' ); ?></button>
						<ul class="gdpr-subtabs">
							<?php
							foreach ( $tabs as $key => $tab ) {
								echo '<li><button type="button" data-target="' . esc_attr( $key ) . '" ' . '>' . esc_html( $tab['name'] ) . '</button></li>';
							}
							?>
						</ul>
					</li>
				</ul>
				<div class="gdpr-tab-content">
					<div class="gdpr-consent-management gdpr-active">
						<header>
							<h4><?php esc_html_e( 'Consent Management', 'gdpr' ); ?></h4>
						</header>
						<div class="gdpr-info">
							<p><?php echo nl2br( esc_html( $cookie_privacy_excerpt ) ); ?></p>
							<?php foreach ( $consent_types as $consent_key => $type ) : ?>
								<div class="gdpr-cookies-used">
									<div class="gdpr-cookie-title">
										<p><?php echo esc_html( $type['name'] ); ?></p>
										<?php if ( $type['required'] ) : ?>
											<span class="gdpr-always-active"><?php esc_html_e( 'Required', 'gdpr' ); ?></span>
											<input type="checkbox" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>" checked style="display:none;">
										<?php else : ?>
											<label class="gdpr-switch">
												<input type="checkbox" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>" <?php echo ! empty( $user_consents ) ? checked( in_array( $consent_key, $user_consents, true ), 1, false ) : ''; ?>>
												<span class="gdpr-slider round"></span>
											</label>
										<?php endif; ?>
									</div>
									<div class="gdpr-cookies">
										<span><?php echo wp_kses( $type['description'], $this->allowed_html ); ?></span>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<div class="<?php echo esc_attr( $key ); ?>">
							<header>
								<h4><?php echo esc_html( $tab['name'] ); ?></h4>
							</header><!-- /header -->
							<div class="gdpr-info">
								<p><?php echo nl2br( $tab['how_we_use'] ); ?></p>
								<?php if ( isset( $tab['cookies_used'] ) && $tab['cookies_used'] ) : ?>
									<div class="gdpr-cookies-used">
										<div class="gdpr-cookie-title">
											<p><?php esc_html_e( 'Cookies Used', 'gdpr' ); ?></p>
											<?php
											$site_cookies = array();
											$enabled      = false;
											$cookies_used = explode( ',', $tab['cookies_used'] );
											$approved_cookies = isset( $_COOKIE['gdpr']['allowed_cookies'] ) ? json_decode( wp_unslash( $_COOKIE['gdpr']['allowed_cookies'] ) ) : array();
											foreach ( $cookies_used as $cookie ) {
												$site_cookies[] = trim( $cookie );
												if ( ! empty( $approved_cookies ) ) {
													if ( in_array( trim( $cookie ), $approved_cookies ) ) {
														$enabled = true;
													}
												}
											}
											?>
											<?php if ( $tab['always_active'] ) : ?>
												<span class="gdpr-always-active"><?php esc_html_e( 'Always Active', 'gdpr' ); ?></span>
												<input type="checkbox" class="gdpr-hidden" name="approved_cookies[]" value="<?php echo esc_attr( json_encode( $site_cookies ) ) ?>" checked>
											<?php else: ?>
												<label class="gdpr-switch">
													<input type="checkbox" name="approved_cookies[]" value="<?php echo esc_attr( json_encode( $site_cookies ) ) ?>" <?php checked( $enabled, true ); ?>>
													<span class="gdpr-slider round"></span>
												</label>
											<?php endif; ?>
										</div>
										<div class="gdpr-cookies">
											<span><?php echo esc_html( $tab['cookies_used'] ); ?></span>
										</div>
									</div>
								<?php endif ?>
								<?php if ( isset( $tab['hosts'] ) && ! empty( $tab['hosts'] ) ) : ?>
									<?php foreach ( $tab['hosts'] as $host_key => $host ) : ?>
										<div class="gdpr-cookies-used">
											<div class="gdpr-cookie-title">
												<p><?php echo esc_html( $host['name'] ); ?></p>
												<a href="<?php echo esc_url( $host['optout'] ); ?>" target="_blank" class="gdpr-button"><?php esc_html_e( 'Opt Out', 'gdpr' ); ?></a>
											</div>
											<div class="gdpr-cookies">
												<span><?php echo esc_html( $host['cookies_used'] ); ?></span>
											</div>
										</div>
									<?php endforeach ?>
								<?php endif ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<footer>
				<input type="submit" value="<?php esc_attr_e( 'Save Preferences', 'gdpr' ); ?>">
				<?php if ( $privacy_policy_page ) : ?>
					<span><a href="<?php echo esc_url( get_permalink( $privacy_policy_page ) ); ?>" target="_blank"><?php esc_html_e( 'More Information', 'gdpr' ); ?></a></span>
				<?php endif ?>
			</footer>
		</form>
	</div>
</div>
