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
		if ( hash ) {
			$('.gdpr_page_gdpr-requests .nav-tab-wrapper a[href="'+ hash +'"]').addClass('nav-tab-active');
			$('.gdpr_page_gdpr-requests .tab[data-id="'+ hash.replace('#', '') +'"]').removeClass('hidden');
		} else {
			$('.gdpr_page_gdpr-requests .nav-tab-wrapper a:eq(0)').addClass('nav-tab-active');
			$('.gdpr_page_gdpr-requests .tab:eq(0)').removeClass('hidden');
		}

		$(document).on('change', '.gdpr-reassign', function() {
			if ( $(this).val() != 0 ) {
				$(this).closest('tr').find('td:last .button-primary').attr('disabled', false);
				$(this).closest('tr').find('td:last input[name="reassign_to"]').val( $(this).val() );
			} else {
				$(this).closest('tr').find('td:last .button-primary').attr('disabled', true);
				$(this).closest('tr').find('td:last input[name="reassign_to"]').val('');
			}
		});

		$(document).on('submit', '.gdpr-reassign-content', function( e ) {
			e.preventDefault();
			var user_email  = $(this).find('input[name="user_email"]').val(),
					reassign_to = $(this).find('input[name="reassign_to"]').val(),
					post_type   = $(this).find('input[name="post_type"]').val(),
					post_count  = $(this).find('input[name="post_count"]').val(),
					nonce       = $(this).find('input[name="gdpr_reassign_content_nonce"]').val(),
					button      = $(this).find('.button-primary'),
					spinner     = $(this).find('.spinner'),
					result      = $(this).find('p.hidden');

			if ( ! reassign_to ) {
				return;
			}

			button.addClass('hidden');
			spinner.addClass('is-active');
			spinner.css('display', 'block');
			$.post(
				ajaxurl,
				{
					action: 'gdpr_reassign_content',
					user_email: user_email,
					reassign_to: reassign_to,
					post_type: post_type,
					post_count: post_count,
					nonce: nonce
				},
				function( response ) {
					spinner.removeClass('is-active');
					spinner.hide();
					result.removeClass('hidden');
					if ( ! response.success ) {
						result.text( response.data );
					}
				}
			);
		});

		$(document).on('submit', '.gdpr-anonymize-comments', function( e ) {
			e.preventDefault();
			var user_email    = $(this).find('input[name="user_email"]').val(),
					comment_count = $(this).find('input[name="comment_count"]').val(),
					nonce         = $(this).find('input[name="gdpr_anonymize_comments_nonce"]').val(),
					button        = $(this).find('.button-primary'),
					spinner       = $(this).find('.spinner'),
					result        = $(this).find('p.hidden');

			button.addClass('hidden');
			spinner.addClass('is-active');
			spinner.css('display', 'block');
			$.post(
				ajaxurl,
				{
					action: 'gdpr_anonymize_comments',
					user_email: user_email,
					comment_count: comment_count,
					nonce: nonce
				},
				function( response ) {
					spinner.removeClass('is-active');
					spinner.hide();
					result.removeClass('hidden');
					if ( ! response.success ) {
						result.text( response.data );
					}
				}
			);
		});

	});
})( jQuery );
