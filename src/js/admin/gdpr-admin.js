(function( $ ) {
	'use strict';

	$(function() {
		$(document).on('click', '.gdpr-settings-form .notice-dismiss', function() {
			$(this).parent().parent().remove();
		});

		function string_to_slug (str) {
	    str = str.replace(/^\s+|\s+$/g, ''); // trim
	    str = str.toLowerCase();

	    // remove accents, swap ñ for n, etc
	    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
	    var to   = "aaaaeeeeiiiioooouuuunc------";
	    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	    }

	    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

	    return str;
		}

		$('.add-tab').click(function(e) {
			e.preventDefault();
			var field = $('#cookie-tabs');
			if ( field.val() === '' ) {
				return;
			}
			var tabID = string_to_slug( field.val() );
			var tabName = field.val();
			var template = wp.template( 'cookie-tabs' );
			$('#tabs').append( template( {
				key: tabID,
				name: tabName,
				option_name: 'gdpr_cookie_popup_content'
			} ) );
			field.val('');
		});

		$('.add-consent').click(function(e) {
			e.preventDefault();
			var field = $('#type-of-consent');
			if ( field.val() === '' ) {
				return;
			}
			var consentID = string_to_slug( field.val() );
			var consentName = field.val();
			var template = wp.template( 'consents' );
			$('#consent-tabs').append( template( {
				key: consentID,
				name: consentName,
				option_name: 'gdpr_consent_types'
			} ) );
			field.val('');
		});

		$('#consent-tabs, #tabs').sortable();

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
				option_name: 'gdpr_cookie_popup_content'
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

		$(document).on('click', '.gdpr .nav-tab-wrapper a', function(e) {
			var target = $(this).attr('href');
			target = target.replace('#', '');
			$(this).addClass('nav-tab-active');
			$(this).siblings().removeClass('nav-tab-active');
			$('.gdpr .gdpr-tab').addClass('hidden');
			$('.gdpr .gdpr-tab[data-id='+ target +']').removeClass('hidden');

			if ( -1 !== location.search.indexOf( 'page=gdpr-settings' ) ) {
				var referer = $('.gdpr form input[name="_wp_http_referer"]');
				var cleanReferer = referer.val().split('#')[0];
				referer.val( cleanReferer + '#' + target );
			}
		});

		var hash = window.location.hash;
		if ( hash ) {
			$('.gdpr .nav-tab-wrapper a[href="'+ hash +'"]').addClass('nav-tab-active');
			$('.gdpr .gdpr-tab[data-id="'+ hash.replace('#', '') +'"]').removeClass('hidden');
			if ( -1 !== location.search.indexOf( 'page=gdpr-settings' ) ) {
				var referer = $('.gdpr form input[name="_wp_http_referer"]');
				var cleanReferer = referer.val().split('#')[0];
				referer.val( cleanReferer + hash);
			}
		} else {
			$('.gdpr .nav-tab-wrapper a:eq(0)').addClass('nav-tab-active');
			$('.gdpr .gdpr-tab:eq(0)').removeClass('hidden');
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

		$(document).on('submit', '.gdpr-access-data-lookup', function(e) {
			e.preventDefault();
			var user_email    = $(this).find('input[name="user_email"]'),
					email         = user_email.val(),
					nonce         = $(this).find('input[name="gdpr_access_data_nonce"]').val(),
					button        = $(this).find('.button-primary'),
					spinner       = $(this).find('.spinner'),
					result        = $('.gdpr-access-data-result');

			button.addClass('hidden');
			spinner.show();
			result.remove();
			user_email.val('');

			$.post(
				ajaxurl,
				{
					action: 'gdpr_access_data',
					nonce: nonce,
					email: email,
				},
				function( response ) {
					button.removeClass('hidden');
					spinner.hide();
					if ( response.success ) {
						var template = wp.template( 'access-data-result-success' );
						$('.gdpr div[data-id="access"]').append( template( {
							result: response.data.result,
							user_email: response.data.user_email
						} ) );
					} else {
						var template = wp.template( 'access-data-result-error' );
						$('.gdpr div[data-id="access"]').append( template() );
					}
				}
			)
		});

		$(document).on('submit', '.gdpr-audit-log-lookup', function(e) {
			e.preventDefault();
			var user_email    = $(this).find('input[name="user_email"]'),
					email         = user_email.val(),
					token         = $(this).find('input[name="token"]'),
					tokenVal      = token.val(),
					nonce         = $(this).find('input[name="gdpr_audit_log_nonce"]').val(),
					button        = $(this).find('.button-primary'),
					spinner       = $(this).find('.spinner'),
					result        = $('.gdpr-audit-log-result');

			button.addClass('hidden');
			spinner.show();
			result.remove();
			user_email.val('');
			token.val('');

			$.post(
				ajaxurl,
				{
					action: 'gdpr_audit_log',
					nonce: nonce,
					email: email,
					token: tokenVal
				},
				function( response ) {
					button.removeClass('hidden');
					spinner.hide();
					if ( response.success ) {
						var template = wp.template( 'audit-log-result-success' );
						$('.gdpr div[data-id="audit-log"]').append( template( {
							result: response.data
						} ) );
					} else {
						var template = wp.template( 'audit-log-result-error' );
						$('.gdpr div[data-id="audit-log"]').append( template() );
					}
				}
			)
		});

		$(document).on( 'click', '.frm-export-data input[type="submit"]', function(e) {
			e.preventDefault();

			var form = $(this).parents('form'),
					type = $(this).val(),
					nonce = form.find('#gdpr_export_data_nonce').val(),
					email = form.find('input[name="user_email"]').val(),
					extension = type.toLowerCase();

			$.post(
				ajaxurl,
				{
					action: 'gdpr_generate_data_export',
					nonce: nonce,
					type: type,
					email: email
				},
				function( response ) {
					if ( response.success ) {
						$('<a />', {
							'href': 'data:text/plain;charset=utf-8,' + encodeURIComponent(response.data),
							'download': email + '.' + extension,
							'text': "click"
						}).hide().appendTo("body")[0].click();
					}
				}
			);
		});

		$(document).on( 'submit', '.frm-ignore-privacy-update', function(e) {
			e.preventDefault();
			var action = $(this).find('input[name="action"]').val(),
					nonce = $(this).find('#privacy-policy-ignore-update-nonce').val();

			$('.privacy-page-updated-notice .notice-dismiss').click();

			$.post(
				ajaxurl,
				{
					action: action,
					nonce: nonce
				}
			);
		});

	});
})( jQuery );
