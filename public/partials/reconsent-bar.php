<?php

/**
 * This file is used to markup the reconsent bar.
 *
 *
 * @link       https://trewknowledge.com
 * @since      2.0.0
 *
 * @package    GDPR
 * @subpackage public/partials
 */
?>

<div class="gdpr gdpr-reconsent-bar" style="display:none;">
	<div class="gdpr-wrapper">
		<div class="gdpr-content">
			<p><?php esc_html_e( 'Some of our policies have been updated. Please make sure to select the "View" link next to each item in order to view changes before agreeing.', 'gdpr' ); ?></p>
		</div>
		<div class="gdpr-right">
			<ul class="gdpr-policy-list">
				<?php foreach ( $updated_consents as $consent_id => $consent ) : ?>
					<li class="gdpr-policy-list-item">
						<input type="hidden" value="<?php echo esc_attr( $consent_id ); ?>">
						<label><?php echo esc_html( $consent['name'] ); ?></label>
						<a class="gdpr-policy-link" href="<?php echo esc_url( get_permalink( $consent['policy-page'] ) ); ?>" target="_blank">[<?php esc_html_e( 'View', 'gdpr' ); ?>]</a>
					</li>
				<?php endforeach ?>
			</ul>
			<div class="gdpr-buttons">
				<button class="gdpr-agreement" type="button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'gdpr-agree-with-new-policies' ) ); ?>"><?php esc_html_e( 'I Agree', 'gdpr' ); ?></button>
			</div>
			<span class="gdpr-close"></span>
		</div>
	</div>
</div>
