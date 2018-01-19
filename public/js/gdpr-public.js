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

		$('.gdpr-right-to-be-forgotten').click(function(e){
			e.preventDefault();

			var confirmation = confirm( gdpr.right_to_be_forgotten_confirmation_message );
			if ( ! confirmation ) {
				return;
			}

			$.post(
				gdpr.ajaxurl,
				{
					action: 'process_right_to_be_forgotten',
					nonce: $(this).data('nonce')
				}
			);
		});

		$('.gdpr-right-to-access').click(function(e){
			e.preventDefault();

			var confirmation = confirm( gdpr.right_to_access_confirmation_message );
			if ( ! confirmation ) {
				return;
			}

			$.post(
				gdpr.ajaxurl,
				{
					action: 'process_right_to_access',
					nonce: $(this).data('nonce')
				},
				function(res) {
					$('<a />', {
						'href': 'data:text/plain;charset=utf-8,' + encodeURIComponent(res.data),
						'download': 'data.xml',
						'text': "click"
					}).hide().appendTo("body")[0].click();
				}
			);
		});
	});

})( jQuery );
