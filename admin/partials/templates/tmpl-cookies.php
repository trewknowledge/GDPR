<script type="text/html" id="tmpl-cookie-tabs">
	<div class="postbox" id="cookie-tab-content-{{data.key}}">
		<h2 class="hndle">{{data.name}}<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this tab.', 'gdpr' ); ?></span></button></h2>
		<div class="inside">
			<table class="form-table">
				<tr>
					<th>
						<label for="rename-{{data.key}}">
							<?php esc_html_e( 'Category Name', 'gdpr' ); ?>:<span class="gdpr-required">*</span>
							<span class="screen-reader-text"><?php esc_attr_e( 'Change this value if you want to rename this category something different.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'Change this value if you want to rename this category something different.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td>
						<input type="text" name="gdpr_cookie_popup_content[{{data.key}}][name]" value="{{data.name}}" required>
					</td>
				</tr>
				<tr>
					<th>
						<label for="status-{{data.key}}">
							<?php esc_html_e( 'Status', 'gdpr' ); ?>:<span class="gdpr-required">*</span>
							<span class="screen-reader-text"><?php esc_attr_e( 'Required cookies are cookies that cannot be opted out of and are needed for the site to function properly. Soft opt-in will allow cookies on first landing but can be opted-out of. Checked means that the cookie category will be checked by default and will be set after the user agrees to them. Unchecked means the user needs to manually toggle the category on to allow these cookies.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'Required cookies are cookies that cannot be opted out of and are needed for the site to function properly. Soft opt-in will allow cookies on first landing but can be opted-out of. Checked means that the cookie category will be checked by default and will be set after the user agrees to them. Unchecked means the user needs to manually toggle the category on to allow these cookies.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td>
						<select name="gdpr_cookie_popup_content[{{data.key}}][status]" id="status-{{data.key}}" required>
							<option value=""></option>
							<option value="required"><?php esc_html_e( 'Required', 'gdpr' ); ?></option>
							<option value="soft"><?php esc_html_e( 'Soft Opt-in', 'gdpr' ); ?></option>
							<option value="on"><?php esc_html_e( 'Checked', 'gdpr' ); ?></option>
							<option value="off"><?php esc_html_e( 'Unchecked', 'gdpr' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="cookies-used-{{data.key}}">
							<?php esc_html_e( 'Cookies used', 'gdpr' ); ?>:
							<span class="screen-reader-text"><?php esc_attr_e( 'A comma-separated list of cookies that your site is using that fit this category.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'A comma-separated list of cookies that your site is using that fit this category.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td>
						<textarea cols="53" rows="3" name="gdpr_cookie_popup_content[{{data.key}}][cookies_used]" id="cookies-used-{{data.key}}"></textarea>
						<br>
						<span class="description"><?php esc_html_e( 'Comma separated list.', 'gdpr' ); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="tab-how-we-use-{{data.key}}">
							<?php esc_html_e( 'How are these used', 'gdpr' ); ?>:
							<span class="screen-reader-text"><?php esc_attr_e( 'A brief explanation on why you are requesting to use these cookies, what they are for and how you process them.', 'gdpr' ); ?></span>
							<span data-tooltip="<?php esc_attr_e( 'A brief explanation on why you are requesting to use these cookies, what they are for and how you process them.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</label>
					</th>
					<td><textarea name="gdpr_cookie_popup_content[{{data.key}}][how_we_use]" id="tab-how-we-use-{{data.key}}" cols="53" rows="3"></textarea></td>
				</tr>
				<tr>
			<th>
				<label for="hosts-{{data.key}}">
					<?php esc_html_e( 'Third party domain', 'gdpr' ); ?>:
					<span class="screen-reader-text"><?php esc_attr_e( 'E.g. youtube.com', 'gdpr' ); ?></span>
					<span data-tooltip="<?php esc_attr_e( 'E.g. youtube.com', 'gdpr' ); ?>">
						<span class="dashicons dashicons-info"></span>
					</span>
				</label>
			</th>
			<td>
			<input type="text" id="hosts-{{data.key}}" class="regular-text" placeholder="<?php esc_attr_e( 'domain.com', 'gdpr' ); ?>" />
			<button class="button button-primary add-host" data-tabid="{{data.key}}"><?php esc_html_e( 'Add', 'gdpr' ); ?></button>
			<br>
			<span class="description"><?php esc_html_e( 'Cookies that are set by a third party, like facebook.com.', 'gdpr' ); ?></span>
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
		<h2 class="hndle">{{data.host_key}}<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Remove this domain.', 'gdpr' ); ?></span></button></h2>
		<input type="hidden" name="{{data.option_name}}[{{data.tab_key}}][hosts][{{data.host_key}}][name]" value="{{data.host_key}}" />
		<div class="inside">
			<table class="form-table">
				<tr>
					<th><label for="hosts-cookies-used-{{data.host_key}}"><?php esc_html_e( 'Cookies used', 'gdpr' ); ?></label></th>
					<td>
						<textarea cols="53" rows="3" name="{{data.option_name}}[{{data.tab_key}}][hosts][{{data.host_key}}][cookies_used]" id="hosts-cookies-used-{{data.host_key}}" required></textarea>
						<br>
						<span class="description"><?php esc_html_e( 'Comma separated list.', 'gdpr' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="hosts-cookies-optout-{{data.host_key}}"><?php esc_html_e( 'How to Opt Out', 'gdpr' ); ?></label></th>
					<td>
						<input type="text" name="{{data.option_name}}[{{data.tab_key}}][hosts][{{data.host_key}}][optout]" id="hosts-cookies-optout-{{data.host_key}}" class="regular-text" required />
						<br>
						<span class="description"><?php esc_html_e( 'Url with instructions on how to opt out.', 'gdpr' ); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</script>
