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

<div class="gdpr cookie-bar">
	<div class="wrapper">
		<div class="content">
			<p><?php echo esc_html( $content ); ?></p>
		</div>
		<div class="right">
			<button class="gdpr-cookie-preferences" type="button"><?php esc_html_e( 'Cookie settings', 'gdpr' ); ?></button>
			<button class="accept-cookies" type="button"><?php esc_html_e( 'I understand', 'gdpr' ); ?></button>
		</div>
	</div>
</div>
