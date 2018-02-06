(function( $ ){
	'use strict';

	$(function(){
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
				},
				function( res ) {
					alert( gdpr.right_to_be_forgotten_confirmation_email );
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
					console.log(res);
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
