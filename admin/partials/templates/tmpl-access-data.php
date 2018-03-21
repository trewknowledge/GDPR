<script type="text/html" id="tmpl-access-data-result-success">
	<div class="gdpr-access-data-result">
		<h2>
			<?php echo _e( 'Result', 'gdpr' ); ?>
			<span class="float-right">
				<a href="#" class="button-primary download-data-xml"><?php esc_html_e( 'XML', 'gdpr' ); ?></a>
				<a href="#" class="button-primary download-data-json"><?php esc_html_e( 'JSON', 'gdpr' ); ?></a>
				<a href="#" class="button-primary download-data-markdown"><?php esc_html_e( 'Markdown', 'gdpr' ); ?></a>
			</span>
		</h2>
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
