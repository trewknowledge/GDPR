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
	<div class="gdpr-contained-wrapper">
		<p class="h5"><?php esc_html_e( 'Some of our policies have been updated', 'gdpr' ); ?></p>
		<?php foreach ( $consent_needed as $consent_id => $consent ): ?>
			<div class="gdpr-policy-list">
				<p><?php echo esc_html( $consent['name'] ); ?></p>
				<div class="gdpr-buttons">
					<a href="<?php echo esc_url( get_permalink( $consent['policy-page'] ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'gdpr' ) ?></a>
					<button class="gdpr-agree" type="button"><?php esc_html_e( 'Agree', 'gdpr' ); ?></button>
				</div>
			</div>
		<?php endforeach ?>

	</div>
</div>
