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

<div class="gdpr consents-preferences">
	<div class="wrapper">
		<form method="post" class="frm-gdpr-consents-preferences">
			<input type="hidden" name="action" value="update_consents">
			<?php wp_nonce_field( 'update_consents', 'update-consents-nonce' ); ?>
			<header>
				<div class="box-title">
					<h3><?php esc_html_e( 'Consent Management', 'gdpr' ); ?></h3>
					<span class="close"></span>
				</div>
			</header>
			<div class="content">
				<div class="tab-content">
					<div class="active">
						<div class="info">
							<?php foreach ( $consent_types as $consent_key => $type ): ?>
								<div class="cookies-used">
									<div class="cookie-title">
										<p><?php echo esc_html( $type['name'] ); ?></p>
										<?php if ( $type['required'] ): ?>
											<span class="required"><?php esc_html_e( 'Required', 'gdpr' ); ?></span>
											<input type="checkbox" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>" checked style="display:none;">
										<?php else: ?>
											<label class="gdpr-switch">
												<input type="checkbox" name="user_consents[]" value="<?php echo esc_attr( $consent_key ); ?>" <?php echo ! empty( $user_consents ) ? checked( in_array( $consent_key, $user_consents, true ), 1, false ) : ''; ?>>
												<span class="gdpr-slider round"></span>
											</label>
										<?php endif; ?>
									</div>
									<div class="cookies">
										<span><?php echo esc_html( $type['description'] ); ?></span>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<footer>
				<input type="submit" value="Save Preferences">
				<span class="error"></span>
			</footer>
		</form>
	</div>
</div>
