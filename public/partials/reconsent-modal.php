<div class="gdpr-reconsent-modal">
	<div class="gdpr-reconsent-modal-content">
		<h3><?php esc_html_e( 'Our Privacy Policy has been updated.', 'gdpr' ); ?></h3>
		<h4><?php esc_html_e( 'To continue using the site you need to read the revised version and agree to the terms.', 'gdpr' ); ?></h4>
		<div class="gdpr-privacy-viewer">
			<?php echo wp_kses_post( apply_filters( 'the_content', $page_obj->post_content ) ); ?>
		</div>
		<div class="gdpr-consent-buttons">
			<a href="#" class="gdpr-agree" data-nonce="<?php echo esc_attr( wp_create_nonce( 'user_agree_with_terms' ) ); ?>"><?php esc_html_e( 'Agree', 'gdpr' ); ?></a>
			<a href="#" class="gdpr-disagree" data-nonce="<?php echo esc_attr( wp_create_nonce( 'user_disagree_with_terms' ) ); ?>"><?php esc_html_e( 'Disagree', 'gdpr' ); ?></a>
		</div>
	</div>
</div>
