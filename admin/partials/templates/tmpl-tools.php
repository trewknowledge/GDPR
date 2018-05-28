<script type="text/html" id="tmpl-audit-log-result-success">
	<div class="gdpr-audit-log-result">
		<h2><?php echo _e( 'Result', 'gdpr' ); ?></h2>
		<div class="postbox">
			<div class="inside">
				<textarea readonly class="gdpr-audit-log-result large-text" rows="20">{{{data.result}}}</textarea>
			</div>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-audit-log-result-error">
	<div class="gdpr-audit-log-result">
		<h2><?php echo _e( 'Error', 'gdpr' ); ?></h2>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'We could not find a any logs for that email and token combination.', 'gdpr' ); ?></p>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-access-data-result-success">
	<div class="gdpr-access-data-result">
		<h2><?php echo _e( 'Result', 'gdpr' ); ?></h2>
		<p>
		<form method="post" class="frm-export-data">
			<?php wp_nonce_field( 'gdpr-export-data', 'gdpr_export_data_nonce' ); ?>
			<input type="hidden" name="user_email" value="{{data.user_email}}">
			<?php submit_button( 'XML', 'primary', 'download-data-xml', false ) ?>
			<?php submit_button( 'JSON', 'primary', 'download-data-json', false ) ?>
		</form>
		</p>
		<div class="postbox">
			<div class="inside">
				<div class="result">
					{{{data.result}}}
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-access-data-result-error">
	<div class="gdpr-access-data-result">
		<h2><?php echo _e( 'Error', 'gdpr' ); ?></h2>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'We could not find a user with that email.', 'gdpr' ); ?></p>
		</div>
	</div>
</script>
