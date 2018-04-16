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

<div class="gdpr gdpr-privacy-bar">
	<div class="gdpr-wrapper">
		<div class="gdpr-content">
			<p>
				<?php echo nl2br( esc_html( $content ) ); ?>
				<?php if ( $privacy_policy_page ): ?>
					<?php echo sprintf( '%s %s', esc_html( $link_label ), '<a href="' . esc_url( get_permalink( $privacy_policy_page ) ) . '" title="' . esc_attr( get_the_title( $privacy_policy_page ) ) . '">' . esc_html( get_the_title( $privacy_policy_page ) ) . '</a>' ) ?>
				<?php endif ?>
			</p>
		</div>
		<div class="gdpr-right">
			<button class="gdpr-preferences" type="button"><?php esc_html_e( 'Privacy Preferences', 'gdpr' ); ?></button>
			<button class="gdpr-agreement" type="button"><?php esc_html_e( 'I Agree', 'gdpr' ); ?></button>
		</div>
	</div>
</div>
