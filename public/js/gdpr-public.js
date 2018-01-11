(function( $ ) {
	'use strict';

	$(function(){
		$('.gdpr-forget-me').click(function(e){
			e.preventDefault();

			var confirmation = confirm( 'Are you sure you want to remove all your personal information from our site?' );
			if ( ! confirmation ) {
				return;
			}

			$(this).html('Loading');
			console.log($(this).data('nonce'));

			$.post(
				GDPR.ajaxurl,
				{
					action: 'request_to_be_forgotten',
					nonce: $(this).data('nonce')
				},
				function(res) {
					$(this).html('Done');
					// location.reload();
				}
			);
		});
	});

})( jQuery );
