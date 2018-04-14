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

<div class="gdpr cookie-preferences">
	<div class="wrapper">
		<form method="post" class="frm-gdpr-cookie-preferences">
			<header>
				<div class="box-title">
					<h3><?php esc_html_e( 'Privacy Preference Center', 'gdpr' ); ?></h3>
					<span class="close"></span>
				</div>
			</header>
			<div class="mobile-menu">
				<button type="button"><?php esc_html_e( 'Options', 'gdpr' ); ?></button>
			</div>
			<div class="content">
				<ul class="tabs">
					<li><button type="button" class="active" data-target="your-privacy"><?php esc_html_e( 'Your Privacy', 'gdpr' ); ?></button></li>
					<?php
					if ( ! empty( $tabs ) ) :
						foreach ( $tabs as $key => $tab ) :
					?>
						<li><button type="button" data-target="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $tab['name'] ); ?></button></li>
					<?php
						endforeach;
					endif;
					?>
					<?php if ( $privacy_policy_page ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $privacy_policy_page ) ); ?>" target="_blank"><?php esc_html_e( 'More information', 'gdpr' ); ?></a></li>
					<?php endif ?>
				</ul>
				<div class="tab-content">
					<div class="your-privacy active">
						<header>
							<h4><?php esc_html_e( 'Your Privacy', 'gdpr' ); ?></h4>
						</header>
						<div class="info">
							<p>
								<?php echo nl2br( esc_html( $cookie_privacy_excerpt ) ); ?>
							</p>
						</div>
					</div>
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<div class="<?php echo esc_attr( $key ); ?>">
							<header>
								<h4><?php echo esc_html( $tab['name'] ); ?></h4>
							</header><!-- /header -->
							<div class="info">
								<p><?php echo nl2br( esc_html( $tab['how_we_use'] ) ); ?></p>
								<?php if ( isset( $tab['cookies_used'] ) && $tab['cookies_used'] ) : ?>
									<div class="cookies-used">
										<div class="cookie-title">
											<p><?php esc_html_e( 'Cookies Used', 'gdpr' ); ?></p>
											<?php
											$site_cookies = array();
											$enabled      = true;
											$cookies_used = explode( ',', $tab['cookies_used'] );
											if ( ! empty( $cookies_used ) ) {
												foreach ( $cookies_used as $cookie ) {
													$site_cookies[] = trim( $cookie );
													if ( ! empty( $approved_cookies ) && ! in_array( trim( $cookie ), $approved_cookies, true ) ) {
														$enabled = false;
														break;
													}
												}
											}
											?>
											<?php if ( $tab['always_active'] ) : ?>
												<span class="always-active"><?php esc_html_e( 'Always Active', 'gdpr' ); ?></span>
												<input type="checkbox" class="gdpr-hidden" name="approved_cookies" value="<?php echo esc_attr( json_encode( $site_cookies ) ); ?>" checked>
											<?php else : ?>
												<label class="gdpr-switch">
													<input type="checkbox" name="approved_cookies" value="<?php echo esc_attr( json_encode( $site_cookies ) ); ?>" <?php echo ( $enabled ? 'checked' : '' ); ?>>
													<span class="gdpr-slider round"></span>
												</label>
											<?php endif; ?>
										</div>
										<div class="cookies">
											<span><?php echo esc_html( $tab['cookies_used'] ); ?></span>
										</div>
									</div>
								<?php endif ?>
								<?php if ( isset( $tab['hosts'] ) && ! empty( $tab['hosts'] ) ) : ?>
									<?php foreach ( $tab['hosts'] as $host_key => $host ) : ?>
										<div class="cookies-used">
											<div class="cookie-title">
												<p><?php echo esc_html( $host['name'] ); ?></p>
												<a href="<?php echo esc_url( $host['optout'] ); ?>" target="_blank" class="gdpr-button"><?php esc_html_e( 'Opt Out', 'gdpr' ); ?></a>
											</div>
											<div class="cookies">
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
				<input type="submit" value="<?php esc_html_e( 'Save Preferences', 'gdpr' ); ?>">
			</footer>
		</form>
	</div>
</div>
