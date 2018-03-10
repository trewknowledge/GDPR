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

		$(document).on('click', '.gdpr-request-table .gdpr-review', function(e) {
			e.preventDefault();
			var target = $(this).data('index');
			$('tr[data-index=' + target + '] div').slideToggle();
		});

		$(document).on('click', '.gdpr_page_gdpr-requests .nav-tab-wrapper a', function(e) {
			var target = $(this).attr('href');
			target = target.replace('#', '');
			$(this).addClass('nav-tab-active');
			$(this).siblings().removeClass('nav-tab-active');
			$('.gdpr_page_gdpr-requests .tab').addClass('hidden');
			$('.gdpr_page_gdpr-requests .tab[data-id='+ target +']').removeClass('hidden');
		});

		var hash = window.location.hash;
		$('.gdpr_page_gdpr-requests .nav-tab-wrapper a[href="'+ hash +'"]').addClass('nav-tab-active');
		$('.gdpr_page_gdpr-requests .tab[data-id="'+ hash.replace('#', '') +'"]').removeClass('hidden');

		$(document).on('change', '.gdpr-reassign', function() {
			if ( $(this).val() != 0 ) {
				$(this).closest('tr').find('td:last button').attr('disabled', false);
			} else {
				$(this).closest('tr').find('td:last button').attr('disabled', true);
			}
		});

	});
})( jQuery );
