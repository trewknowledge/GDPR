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
			<p><?php echo nl2br( wp_kses_post( $content ) ); ?></p>
		</div>
		<div class="gdpr-right">
			<?php if ( $show_cookie_cat_checkboxes ) : ?>
				<ul class="gdpr-cookie-categories">
					<?php foreach ( $registered_cookies as $cookie_cat_id => $cookie_cat ) : ?>
						<?php
						$enabled = ( 'off' === $cookie_cat['status'] ) ? false : true;
						if ( empty( $cookie_cat['cookies_used'] ) ) {
							continue;
						}
						?>
						<li class="gdpr-cookie-categories-item">
							<input type="checkbox" id="gdpr-cookie-category-<?php echo esc_attr( $cookie_cat_id ); ?>" class="gdpr-cookie-category" data-category="<?php echo esc_attr( $cookie_cat_id ); ?>" name="allowed_cookie_categories" value="<?php echo esc_attr( $cookie_cat['name'] ); ?>" <?php checked( $enabled, true ); ?> <?php echo ( 'required' === $cookie_cat['status'] ) ? 'disabled' : ''; ?>>
							<label for="gdpr-cookie-category-<?php echo esc_attr( $cookie_cat_id ); ?>"><?php echo esc_html( $cookie_cat['name'] ); ?></label>
						</li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
			<div class="gdpr-buttons">
				<button class="gdpr-preferences" type="button"><?php esc_html_e( 'Privacy Preferences', 'gdpr' ); ?></button>
				<button class="gdpr-agreement" type="button"><?php echo esc_html( $button_text ); ?></button>
			</div>
			<span class="gdpr-close"></span>
		</div>
	</div>
</div>
