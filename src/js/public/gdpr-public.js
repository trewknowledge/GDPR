(function( $ ) {
	'use strict';

	$(function() {
		$(document).on('click', '.gdpr-cookie-bar .cookie-settings', function() {
			$('.gdpr-cookie-preferences').show();
		});

		$(document).on('click', '.gdpr-cookie-bar .accept-cookies', function() {
			var date = new Date();
			var iso_date = date.toISOString();
			date.setFullYear(date.getFullYear() + 1);
			document.cookie = 'gpdr_cookie_bar_closed=' + iso_date + ';expires=' + date.toGMTString() + ';';
			// document.cookie = 'gdpr_cookie_consent=' + iso_date + ';expires=' + date.toGMTString() + ';';
		});
	});

})( jQuery );
