<?php

/**
 * This file is used to markup the cookie bar.
 *
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage public/partials
 */
?>

<div class="gdpr gdpr-privacy-bar" style="display:none;">
	<div class="gdpr-wrapper">
		<div class="gdpr-content">
			<p>
				<?php echo nl2br( wp_kses_post( $content ) ); ?>
			</p>
		</div>
		<div class="gdpr-right">
			<button class="gdpr-preferences" type="button"><?php esc_html_e( 'Privacy Preferences', 'gdpr' ); ?></button>
			<button class="gdpr-agreement" type="button"><?php esc_html_e( 'I Agree', 'gdpr' ); ?></button>
		</div>
	</div>
</div>
