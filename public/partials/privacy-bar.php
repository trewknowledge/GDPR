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
			<p class="h5"><?php esc_html_e( 'This website uses Cookies', 'gdpr' ); ?></p>
			<p>
				<?php echo nl2br( wp_kses_post( $content ) ); ?>
			</p>
			<ul class="gdpr-cookie-categories">
				<li>
					<input type="checkbox" id="gdpr-cookie-category-necessary" name="allowed_cookie_categories" value="<?php esc_attr_e( 'Necessary', 'gdpr' ); ?>" checked disabled>
					<label for="gdpr-cookie-category-necessary"><?php esc_html_e( 'Necessary', 'gdpr' ); ?></label>
				</li>
				<li>
					<input type="checkbox" id="gdpr-cookie-category-advertising" name="allowed_cookie_categories" value="<?php esc_attr_e( 'Advertising', 'gdpr' ); ?>" checked>
					<label for="gdpr-cookie-category-advertising"><?php esc_html_e( 'Advertising', 'gdpr' ); ?></label>
				</li>
				<li>
					<input type="checkbox" id="gdpr-cookie-category-statistics" name="allowed_cookie_categories" value="<?php esc_attr_e( 'Analytics', 'gdpr' ); ?>" checked>
					<label for="gdpr-cookie-category-statistics"><?php esc_html_e( 'Analytics', 'gdpr' ); ?></label>
				</li>
				<li>
					<input type="checkbox" id="gdpr-cookie-category-other" name="allowed_cookie_categories" value="<?php esc_attr_e( 'Other', 'gdpr' ); ?>" checked>
					<label for="gdpr-cookie-category-other"><?php esc_html_e( 'Other', 'gdpr' ); ?></label>
				</li>
			</ul>
		</div>
		<div class="gdpr-right">
			<button class="gdpr-preferences" type="button"><?php esc_html_e( 'Privacy Preferences', 'gdpr' ); ?></button>
			<button class="gdpr-agreement" type="button"><?php echo esc_html( $button_text ); ?></button>
		</div>
	</div>
</div>
