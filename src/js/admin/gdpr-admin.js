(function( $ ) {
	'use strict';

	$(function() {
		$(document).on('click', '.gdpr-options .notice-dismiss', function() {
			$(this).parent().parent().remove();
		});

		$('.add-tab').click(function(e) {
			e.preventDefault();
			var field = $('#cookie-tabs');
			if ( field.val() === '' ) {
				return;
			}
			var tabID = field.val().toLowerCase().replace(/ /g, '-');
			var tabName = field.val();
			var template = wp.template( 'cookie-tabs' );
			$('#tabs').append( template( {
				key: tabID,
				name: tabName,
				option_name: GDPR.cookie_popup_content
			} ) );
			field.val('');
		});


		$(document).on('click', '.add-host', function(e) {
			e.preventDefault();
			var field = $(this).siblings('input');
			if ( field.val() === '' ) {
				return;
			}
			var tabID = $(this).data('tabid');
			var hostID = field.val().toLowerCase().replace(' ', '-');
			var template = wp.template( 'cookie-tabs-hosts' );
			$('.tab-hosts[data-tabid="'+tabID+'"]').append( template( {
				host_key: hostID,
				tab_key: tabID,
				option_name: GDPR.cookie_popup_content
			} ) );
			field.val('');
		});

		$(document).on('click', '#tabs .notice-dismiss', function(e) {
			e.preventDefault();
			$(this).closest('.postbox').remove();
		});


	});
})( jQuery );
