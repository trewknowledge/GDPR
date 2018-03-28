<script type="text/html" id="tmpl-consents">
	<div class="postbox" id="consent-type-content-{{data.key}}">
		<h2 class="hndle">{{data.name}} <span>(id: {{data.key}})</span><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Unregister this consent.', 'gdpr' ); ?></span></button></h2>
		<input type="hidden" name="{{data.option_name}}[{{data.key}}][name]" value="{{data.name}}" />
		<input type="hidden" name="{{data.option_name}}[{{data.key}}][id]" value="{{data.id}}" />
		<div class="inside">
			<table class="form-table">
				<tr>
					<th><label for="required-{{data.key}}"><?php esc_html_e( 'Required', 'gdpr' ); ?></label></th>
					<td>
						<label class="gdpr-switch">
							<input type="checkbox" name="{{data.option_name}}[{{data.key}}][required]" id="required-{{data.key}}">
							<span class="gdpr-slider round"></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><label for="consent-description-{{data.key}}"><?php esc_html_e( 'Consent description', 'gdpr' ); ?></label></th>
					<td><textarea name="{{data.option_name}}[{{data.key}}][description]" id="consent-description-{{data.key}}" cols="53" rows="3" required></textarea></td>
				</tr>
				<tr>
					<th><label for="consent-registration-{{data.key}}"><?php esc_html_e( 'Registration message', 'gdpr' ); ?></label></th>
					<td><textarea name="{{data.option_name}}[{{data.key}}][registration]" id="consent-registration-{{data.key}}" cols="53" rows="3" required></textarea></td>
				</tr>
			</table>
		</div><!-- .inside -->
	</div><!-- .postbox -->
</script>
