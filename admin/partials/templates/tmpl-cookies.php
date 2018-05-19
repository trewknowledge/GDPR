<script type="text/html" id="tmpl-cookie-tabs-hosts">
	<div class="postbox">
		<h2 class="hndle">{{data.host_key}}<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this domain.', 'gdpr' ); ?></span></button></h2>
		<input type="hidden" name="{{data.option_name}}[{{data.tab_key}}][domains][{{data.host_key}}][name]" value="{{data.host_key}}" />
		<div class="inside">
			<table class="form-table">
				<tr>
					<th><label for="hosts-cookies-used-{{data.host_key}}"><?php esc_html_e( 'Cookies used', 'gdpr' ); ?></label></th>
					<td>
						<textarea cols="53" rows="3" name="{{data.option_name}}[{{data.tab_key}}][domains][{{data.host_key}}][cookies_used]" id="hosts-cookies-used-{{data.host_key}}" required></textarea>
						<br>
						<span class="description"><?php esc_html_e( 'Comma separated list.', 'gdpr' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="hosts-cookies-optout-{{data.host_key}}"><?php esc_html_e( 'How to Opt Out', 'gdpr' ); ?></label></th>
					<td>
						<input type="text" name="{{data.option_name}}[{{data.tab_key}}][domains][{{data.host_key}}][optout]" id="hosts-cookies-optout-{{data.host_key}}" class="regular-text" required />
						<br>
						<span class="description"><?php esc_html_e( 'Url with instructions on how to opt out.', 'gdpr' ); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</script>
