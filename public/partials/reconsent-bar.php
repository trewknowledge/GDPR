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

<div class="gdpr gdpr-reconsent-bar" style="/*display:none;*/">
	<div class="gdpr-wrapper">
		<div class="gdpr-content">
			<p><?php esc_html_e( 'Some of our policies have been updated', 'gdpr' ); ?></p>
			<div class="gdpr-policy-pages">
				<?php foreach ( $updated_consents as $consent_id => $consent ): ?>
					<span class="gdpr-policy-pages-item"><a href="<?php echo esc_url( get_permalink( $consent['policy-page'] ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'gdpr' ) ?></a> <?php echo esc_html( $consent['name'] ); ?> changes</span>
				<?php endforeach ?>
			</div>
		</div>
		<div class="gdpr-right">
			<ul class="gdpr-policy-list">
				<?php foreach ( $updated_consents as $consent_id => $consent ) : ?>
					<li class="gdpr-policy-list-item">
						<input type="checkbox" id="gdpr-policy-item-<?php echo esc_attr( $consent_id ); ?>" class="gdpr-policy-item" data-category="<?php echo esc_attr( $consent_id ); ?>" name="allowed_cookie_categories" value="<?php echo esc_attr( $consent['name'] ); ?>" checked>
						<label for="gdpr-policy-item-<?php echo esc_attr( $consent_id ); ?>"><?php echo esc_html( $consent['name'] ); ?></label>
					</li>
				<?php endforeach ?>
			</ul>
			<div class="gdpr-buttons">
				<button class="gdpr-agreement" type="button"><?php esc_html_e( 'I Agree', 'gdpr' ); ?></button>
			</div>
	</div>
</div>
