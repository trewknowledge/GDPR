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

<div class="gdpr gdpr-reconsent-preferences">
	<div class="gdpr-wrapper" style="display: block;">
		<form method="post" class="gdpr-reconsent-preferences-frm">
			<input type="hidden" name="action" value="gdpr_update_reconsent_preferences">
			<?php wp_nonce_field( 'gdpr-update-reconsent-preferences', 'update-reconsent-preferences-nonce' ); ?>
			<header>
				<div class="gdpr-box-title">
					<h3><?php esc_html_e( 'Reconsent Center', 'gdpr' ); ?></h3>
					<span class="gdpr-close"></span>
				</div>
			</header>
			<div class="gdpr-mobile-menu">
				<button type="button"><?php esc_html_e( 'Options', 'gdpr' ); ?></button>
			</div>
			<div class="gdpr-content">
				<div class="gdpr-tabs">
					<ul class="">
						<?php reset( $updated_consents ); ?>
						<?php if ( ! empty( $updated_consents ) ) : ?>
							<li><button type="button" class="gdpr-tab-button gdpr-cookie-settings gdpr-active" data-target="<?php echo esc_attr( key( $updated_consents ) ); ?>"><?php esc_html_e( 'Consent Management', 'gdpr' ); ?></button>
								<ul class="gdpr-subtabs">
									<?php
									$policy_counter = 1;
									foreach ( $updated_consents as $consent_id => $consent ) :
										echo '<li><button' . ( 1 === $policy_counter ? ' class="gdpr-active"' : '' ) . ' type="button" data-target="' . esc_attr( $consent_id ) . '" ' . '>' . esc_html( $consent['name'] ) . '</button></li>';
										$policy_counter++;
									endforeach
									?>
								</ul>
							</li>
						<?php endif; ?>
					</ul>
					<ul class="gdpr-policies">
						<?php if ( ! empty( $consent_types ) ) : ?>
							<?php foreach ( $consent_types as $consent_key => $type ) : ?>
								<?php
								if ( ! $type['policy-page'] ) {
									continue;
								}
								?>
								<li><a href="<?php echo esc_url( get_permalink( $type['policy-page'] ) ); ?>" target="_blank"><?php echo esc_html( $type['name'] ); ?></a></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
				<div class="gdpr-tab-content">
					<?php foreach ( $updated_consents as $consent_id => $consent ) : ?>
						<div class="<?php echo esc_attr( $consent_id ); ?> gdpr-active">
							<header>
								<h4><?php echo esc_html( $consent['name'] ); ?></h4>
							</header><!-- /header -->
							<div class="gdpr-info">
								<p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec ullamcorper nulla non metus auctor fringilla. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<input type="hidden" name="all_cookies" value="<?php echo esc_attr( json_encode( $all_cookies ) ); ?>">
			</div>
			<footer>
				<input type="submit" value="<?php esc_attr_e( 'Accept', 'gdpr' ); ?>">
			</footer>
		</form>
	</div>
</div>
