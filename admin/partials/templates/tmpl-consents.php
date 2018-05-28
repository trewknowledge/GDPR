<script type="text/html" id="tmpl-consents">
	<div class="postbox" id="consent-type-content-{{data.key}}">
		<h2 class="hndle">{{data.name}} <span>(id: {{data.key}})</span><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Unregister this consent.', 'gdpr' ); ?></span></button></h2>
		<input type="hidden" name="{{data.option_name}}[{{data.key}}][name]" value="{{data.name}}" />
		<div class="inside">
			<table class="form-table">
				<tr>
					<th>
						<label for="consent-policy-page-{{data.key}}">
							<?php esc_html_e( 'Policy Page', 'gdpr' ); ?>:
							<span class="screen-reader-text"><?php esc_attr_e( 'This page will be tracked for changes and you will be prompted to ask users to re-consent to the new policy. Selecting a page will make this consent required.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'This page will be tracked for changes and you will be prompted to ask users to re-consent to the new policy. Selecting a page will make this consent required.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td>
						<select name="gdpr_consent_types[{{data.key}}][policy-page]" id="consent-policy-page-{{data.key}}">
							<option value=""></option>
							<?php foreach ( $pages as $page ) : ?>
								<option value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="consent-description-{{data.key}}">
							<?php esc_html_e( 'Long description', 'gdpr' ); ?>:<span class="gdpr-required">*</span>
							<span class="screen-reader-text"><?php esc_attr_e( 'This will show up at the privacy preferences center, under the name of the consent.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'This will show up at the privacy preferences center, under the name of the consent.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td><textarea name="gdpr_consent_types[{{data.key}}][description]" id="consent-description-{{data.key}}" cols="53" rows="3" required></textarea></td>
				</tr>
				<tr>
					<th>
						<label for="consent-registration-{{data.key}}">
							<?php esc_html_e( 'Short description', 'gdpr' ); ?>:<span class="gdpr-required">*</span>
							<span class="screen-reader-text"><?php esc_attr_e( 'This will show up at registration forms next to checkboxes.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'This will show up at registration forms next to checkboxes.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td><textarea name="gdpr_consent_types[{{data.key}}][registration]" id="consent-registration-{{data.key}}" cols="53" rows="3" required></textarea></td>
				</tr>
			</table>
		</div><!-- .inside -->
	</div><!-- .postbox -->
</script>
