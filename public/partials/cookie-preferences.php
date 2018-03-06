<?php

/**
 * This file is used to markup the cookie preferences window.
 *
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/public/partials
 */
?>

<div class="gdpr cookie-preferences">
	<div class="gdpr overlay"></div>
	<div class="wrapper">
		<form method="post" class="frm-gdpr-cookie-preferences">
			<header>
				<div class="logo">
					<?php the_custom_logo(); ?>
				</div>
				<div class="box-title">
					<h3><?php esc_html_e( 'Privacy Preference Center', 'gdpr' ); ?></h3>
				</div>
			</header>
			<div class="content">
				<ul class="tabs">
					<li><button type="button" class="active" data-target="your-privacy"><?php esc_html_e( 'Your Privacy', 'gdpr' ); ?></button></li>
					<?php
						foreach ( $tabs as $key => $tab ) {
							echo '<li><button type="button" data-target="' . esc_attr( $key ) . '" ' . '>' . esc_html( $tab['name'] ) . '</button></li>';
						}
					?>
					<?php if ( 'open' === get_option( 'default_comment_status' ) ): ?>
						<li><button type="button" data-target="comment-cookies"><?php esc_html_e( 'Comment Cookies', 'gdpr' ); ?></button></li>
					<?php endif; ?>
				</ul>
				<div class="tab-content">
					<div class="your-privacy active">
						<header>
							<h4><?php esc_html_e( 'Your Privacy', 'gdpr' ); ?></h4>
						</header>
						<div class="info">
							<p><?php echo nl2br( $cookie_privacy_excerpt ); ?></p>
						</div>
					</div>
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<div class="<?php echo esc_attr( $key ); ?>">
							<header>
								<h4><?php echo esc_html( $tab['name'] ); ?></h4>
								<?php
									$site_cookies = array();
									$enabled = true;
									$cookies_used = explode(',', $tab['cookies_used']);
									foreach ( $cookies_used as $cookie ) {
										$site_cookies[] = trim( $cookie );
										if ( ! empty( $approved_cookies ) ) {
											if ( ! in_array( trim( $cookie ), $approved_cookies ) ) {
												$enabled = false;
											}
										}
									}
								?>
								<?php if ( $tab['always_active'] ): ?>
									<span><?php esc_html_e( 'Always active', 'gdpr' ); ?></span>
									<input type="checkbox" class="gdpr-hidden" name="approved_cookies" value="<?php echo esc_attr( json_encode( $site_cookies ) ) ?>" checked>
								<?php else: ?>
									<label class="gdpr-switch">
										<input type="checkbox" name="approved_cookies" value="<?php echo esc_attr( json_encode( $site_cookies ) ) ?>" <?php echo ( $enabled ? 'checked' : '' ); ?>>
										<span class="gdpr-slider round"></span>
									</label>
								<?php endif; ?>
							</header><!-- /header -->
							<div class="info">
								<p><?php echo nl2br( $tab['how_we_use'] ); ?></p>
								<strong><?php esc_html_e( 'Cookies used', 'gdpr' ); ?></strong>
								<div class="cookies-used">
									<span><?php echo esc_html( $tab['cookies_used'] ); ?></span>
								</div>
								<?php if ( isset( $tab['hosts'] ) && ! empty( $tab['hosts'] ) ): ?>
									<?php foreach ( $tab['hosts'] as $host_key => $host ): ?>
										<div class="cookies-used">
											<div class="space-between">
												<p><?php echo esc_html( $host['name'] ); ?></p>
												<a href="<?php echo esc_url( $host['optout'] ); ?>" target="_blank" class="gdpr-button"><?php esc_html_e( 'Opt Out', 'gdpr' ); ?></a>
											</div>
											<span><?php echo esc_html( $host['cookies_used'] ); ?></span>
										</div>
									<?php endforeach ?>
								<?php endif ?>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if ( 'open' === get_option( 'default_comment_status' ) ): ?>
						<div class="comment-cookies">
							<header>
								<h4><?php esc_html_e( 'Comment Cookies', 'gdpr' ); ?></h4>
								<?php
									$site_cookies = array();
									$enabled = true;
									$cookies_used = array( 'comment_author' );
									foreach ( $cookies_used as $cookie ) {
										$site_cookies[] = trim( $cookie );
										if ( ! empty( $approved_cookies ) ) {
											if ( ! in_array( trim( $cookie ), $approved_cookies ) ) {
												$enabled = false;
											}
										}
									}
								?>
								<label class="gdpr-switch">
									<input type="checkbox" name="approved_cookies" value="<?php echo esc_attr( json_encode( $site_cookies ) ) ?>" <?php echo ( $enabled ? 'checked' : '' ); ?>>
									<span class="gdpr-slider round"></span>
								</label>
							</header>
							<div class="info">
								<p><?php esc_html_e( 'Comment cookies are convenience cookies that temporarily store data so you don\'t have to type it on our comment form all the time.', 'gdpr' ); ?></p>
								<strong><?php esc_html_e( 'Cookies used', 'gdpr' ); ?></strong>
								<div class="cookies-used">
									<span>comment_author_{$hash}, comment_author_email_{$hash}, comment_author_url_{$hash}</span>
								</div>
							</div>
						</div>
					<?php endif ?>
				</div>
			</div>
			<footer>
				<input type="submit" value="Save preferences">
			</footer>
		</form>
	</div>
</div>
