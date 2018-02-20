<script type="text/html" id="tmpl-cookie-tabs">
	<div class="postbox" id="cookie-tab-content-{{data.key}}">
		<h2 class="hndle">{{data.name}}<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this tab.', 'gdpr' ); ?></span></button></h2>
		<input type="hidden" name="{{data.option_name}}[{{data.key}}][name]" value="{{data.name}}" />
		<div class="inside">
			<table class="form-table">
				<tr>
					<th><label for="always-active-{{data.key}}"><?php esc_html_e( 'Always active', 'gdpr' ); ?></label></th>
					<td>
						<label class="gdpr-switch">
							<input type="checkbox" name="{{data.option_name}}[{{data.key}}][always_active]" id="always-active-{{data.key}}">
							<span class="slider round"></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><label for="tab-how-we-use-{{data.key}}"><?php esc_html_e( 'How we use', 'gdpr' ); ?></label></th>
					<td><textarea name="{{data.option_name}}[{{data.key}}][how_we_use]" id="tab-how-we-use-{{data.key}}" cols="53" rows="5"></textarea></td>
				</tr>
				<tr>
					<th><label for="cookies-used-{{data.key}}"><?php esc_html_e( 'Cookies used by the site', 'gdpr' ); ?></label></th>
					<td>
						<input type="text" name="{{data.option_name}}[{{data.key}}][cookies_used]" id="cookies-used-{{data.key}}" class="regular-text" />
						<br>
						<span class="description"><?php esc_html_e( 'Comma separated list.', 'gdpr' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="hosts-{{data.key}}"><?php esc_html_e( 'Hosts', 'gdpr' ); ?></label></th>
					<td>
						<input type="text" id="hosts-{{data.key}}" class="regular-text" />
						<button class="button button-primary add-host" data-tabid="{{data.key}}"><?php esc_html_e( 'Add', 'gdpr' ); ?></button>
						<br>
						<span class="description"><?php esc_html_e( '3rd party cookie hosts.', 'gdpr' ); ?></span>
					</td>
				</tr>
			</table>
			<div class="tab-hosts" data-tabid="{{data.key}}">

			</div>
		</div><!-- .inside -->
	</div><!-- .postbox -->
</script>


<script type="text/html" id="tmpl-cookie-tabs-hosts">
	<div class="postbox">
		<h2 class="hndle">{{data.host_key}}<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this host.', 'gdpr' ); ?></span></button></h2>
		<input type="hidden" name="{{data.option_name}}[{{data.tab_key}}][hosts][{{data.host_key}}][name]" />
		<div class="inside">
			<table class="form-table">
				<tr>
					<th><label for="hosts-cookies-used-{{data.host_key}}"><?php esc_html_e( 'Cookies used', 'gdpr' ); ?></label></th>
					<td>
						<input type="text" name="{{data.option_name}}[{{data.tab_key}}][hosts][{{data.host_key}}][cookies_used]" id="hosts-cookies-used-{{data.host_key}}" class="regular-text" />
						<br>
						<span class="description"><?php esc_html_e( 'Comma separated list.', 'gdpr' ); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</script>
