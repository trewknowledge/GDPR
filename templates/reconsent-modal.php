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

<div class="gdpr gdpr-reconsent">
	<div class="gdpr-wrapper">
		<div class="reconsent-form">
			<header>
				<div class="gdpr-box-title">
					<h3><?php esc_html_e( 'Some of our policies have been updated.', 'gdpr' ); ?></h3>
				</div>
			</header>
			<div class="gdpr-mobile-menu">
				<button type="button"><?php esc_html_e( 'Options', 'gdpr' ); ?></button>
			</div>
			<div class="gdpr-content">
				<div class="gdpr-tabs">
					<ul class="">
						<?php reset( $args['updated_consents'] ); ?>
						<?php if ( ! empty( $args['updated_consents'] ) ) : ?>
							<li><button type="button" class="gdpr-tab-button gdpr-cookie-settings gdpr-active" data-target="<?php echo esc_attr( key( $args['updated_consents'] ) ); ?>"><?php esc_html_e( 'Consent Management', 'gdpr' ); ?></button>
								<ul class="gdpr-subtabs">
									<?php
									$policy_counter = 1;
									foreach ( $args['updated_consents'] as $consent_id => $consent ) :
										echo '<li><button' . ( 1 === $policy_counter ? ' class="gdpr-active"' : '' ) . ' type="button" data-target="' . esc_attr( $consent_id ) . '" ' . '>' . esc_html( $consent['name'] ) . '</button></li>';
										$policy_counter++;
									endforeach;
									?>
								</ul>
							</li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="gdpr-tab-content">
					<?php $counter = 0; ?>
					<?php foreach ( $args['updated_consents'] as $consent_id => $consent ) : ?>
						<div class="<?php echo esc_attr( $consent_id ); ?> <?php echo ( 0 === $counter ? 'gdpr-active' : '' ); ?>">
							<header>
								<h4><?php echo esc_html( $consent['name'] ); ?></h4>
							</header><!-- /header -->
							<div class="gdpr-info">
								<div class="gdpr-policy-content">
									<?php
									$page_obj = get_post( $consent['policy-page'] );
									if ( class_exists( 'SiteOrigin_Panels' ) && get_post_meta( $page_obj->ID, 'panels_data', true ) ) {
										echo wp_kses_post( SiteOrigin_Panels::renderer()->render( $page_obj->ID ) );
									} else {
										echo wp_kses_post( apply_filters( 'the_content', $page_obj->post_content ) );
									}
									?>
								</div>
							</div>
						</div>
						<?php $counter = 1; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<footer>
				<div class="gdpr-buttons">
					<form method="post" class="gdpr-reconsent-frm">
						<?php foreach ( $args['updated_consents'] as $consent_id => $consent ) : ?>
							<input type="hidden" name="gdpr-updated-policy" value="<?php echo esc_attr( $consent_id ); ?>">
						<?php endforeach; ?>
						<?php wp_nonce_field( 'gdpr-agree-with-new-policies', 'agree-with-new-policies-nonce' ); ?>
						<input type="submit" class="gdpr-agreement" value="<?php esc_attr_e( 'I Agree', 'gdpr' ); ?>">
						<span class="gdpr-disagree"><a href="#"><?php esc_attr_e( 'Disagree', 'gdpr' ); ?></a></span>
					</form>
				</div>
			</footer>
		</div>
	</div>
</div>
