(function( $ ) {
	'use strict';

	$(function(){

		if ( $('.gdpr-consent-modal').length > 0 ) {
			$('body').css('overflow', 'hidden');
		}

		$('.gdpr-disagree').click(function(e){
			e.preventDefault();
			const that = $(this);
			$.post(
				gdpr.ajaxurl,
				{
					action: 'disagree_with_terms',
					nonce: $(this).data('nonce')
				},
				function(res) {
					if ( res.success ) {
						location.reload();
					}
				}
			);
		});

		$('.gdpr-agree').click(function(e){
			e.preventDefault();
			const that = $(this);
			$.post(
				gdpr.ajaxurl,
				{
					action: 'agree_with_terms',
					nonce: $(this).data('nonce'),
					page: $(this).data('page')
				},
				function(res) {
					if ( res.success ) {
						that.closest('.gdpr-consent-modal').fadeOut(300, function(){
							$(this).remove();
							if ( $('.gdpr-consent-modal').length == 0 ) {
								$('body').css('overflow', 'auto');
							}
						});
					}
				}
			);
		});

		var uri = window.location.toString();
		if (uri.indexOf("?") > 0) {
				var clean_uri = uri.substring(0, uri.indexOf("?"));
				window.history.replaceState({}, document.title, clean_uri);
		}
	});

})( jQuery );
