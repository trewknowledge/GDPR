<div class="gdpr-reconsent-modal" style="display:none;">
	<div class="gdpr-reconsent-modal-content">
		<h3><?php esc_html_e( 'Our Privacy Policy has been updated.', 'gdpr' ); ?></h3>
		<h4><?php esc_html_e( 'To continue using the site you need to read the revised version and agree to the terms.', 'gdpr' ); ?></h4>
		<div class="gdpr-privacy-viewer">
			<?php echo apply_filters( 'the_content', $page_obj->post_content ); ?>
		</div>
		<div class="gdpr-consent-buttons">
			<a href="#" class="gdpr-agree" data-nonce="<?php echo esc_attr( wp_create_nonce( 'gdpr-user_agree_with_terms' ) ); ?>"><?php esc_html_e( 'Agree', 'gdpr' ); ?></a>
			<a href="#" class="gdpr-disagree"><?php esc_html_e( 'Disagree', 'gdpr' ); ?></a>
		</div>
		<div class="gdpr-consent-loading">
			<p class="gdpr-loading"><span class="gdpr-updating"><?php esc_html_e( 'Updating', 'gdpr' ); ?></span><span class="gdpr-ellipsis"></span></p>
		</div>
	</div>
</div>

<div class="gdpr gdpr-general-confirmation gdpr-disagree-confirmation">
	<div class="gdpr-wrapper">
		<header>
			<div class="gdpr-box-title">
				<h3><?php esc_attr_e( 'Are you sure?', 'gdpr' ); ?></h3>
				<span class="gdpr-close"></span>
			</div>
		</header>
		<div class="gdpr-content">
			<p><?php esc_html_e( 'By disagreeing you will no longer have access to our site and will be logged out.', 'gdpr' ); ?></p>
		</div>
		<footer>
			<button class="gdpr-disagree-confirm" data-nonce="<?php echo esc_attr( wp_create_nonce( 'gdpr-user_disagree_with_terms' ) ); ?>"><?php esc_html_e( 'Continue', 'gdpr' ); ?></button>
			<button class="gdpr-cancel"><?php esc_html_e( 'Cancel', 'gdpr' ); ?></button>
		</footer>
	</div>
</div>
