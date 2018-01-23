(function( $ ) {
	'use strict';

	$(function(){

		$('.add-another-consent').click(function(e){
			e.preventDefault();
			var i = $('.postbox.repeater').length;
			var source = $('.postbox.repeater:eq(0)').clone();
			source.find('input, textarea').val('');
			source.find('input[data-id="title"]').attr('name', 'gdpr_options[consents][' + i + '][title]').prop('readonly', '');
			source.find('input[data-id="id"]').attr('name', 'gdpr_options[consents][' + i + '][id]').prop('readonly', '');
			source.find('textarea').attr('name', 'gdpr_options[consents][' + i + '][description]');
			source.find('table').after('<button class="button delete-consent">' + gdpr.delete_text + '</button>')
			$('.postbox.repeater:last').after( source );
		});

		$(document).on('click', '.delete-consent', function(e){
			e.preventDefault();
			$(this).closest('.repeater').remove();
			var i = 0;
			$('.postbox.repeater').each(function(){
				$(this).find('input[data-id="title"]').attr('name', 'gdpr_options[consents][' + i + '][title]')
				$(this).find('input[data-id="id"]').attr('name', 'gdpr_options[consents][' + i + '][id]')
				$(this).find('textarea').attr('name', 'gdpr_options[consents][' + i + '][description]')
				i++;
			});
		});

		$('.gdpr-audit-log-email-lookup').submit(function(e){
			e.preventDefault();

			const nonce = $(this).find('#_gdpr_email_lookup').val();
			const email = $(this).find('#gdpr-email-lookup-field').val();
			const token = $(this).find('#gdpr-token-lookup-field').val();

			const data = {
				action: 'gdpr_audit_log_email_lookup',
				nonce: nonce,
				email: email,
				token: token
			}

			$('.gdpr-audit-log-error').hide();
			$('.gdpr-audit-log-result').hide();
			$.post(
				ajaxurl,
				data,
				function( res ) {
					if ( ! res.success ) {
						$('.gdpr-audit-log-error').show();
					} else {
						$('.gdpr-audit-log-result').show();
						$('.gdpr-audit-log-result textarea').text( res.data );
					}
				}
			)

		});

		$('.gdpr-right-to-access-email-lookup').submit(function(e){
			e.preventDefault();

			const nonce = $(this).find('#_gdpr_email_lookup').val();
			const email = $(this).find('#gdpr-email-lookup-field').val();

			const data = {
				action: 'gdpr_right_to_access_email_lookup',
				nonce: nonce,
				email: email
			}

			$('.gdpr-right-to-access-error').hide();
			$('.gdpr-right-to-access-result').hide();
			$.post(
				ajaxurl,
				data,
				function( res ) {
					if ( ! res.success ) {
						$('.gdpr-right-to-access-error').show();
					} else {
						$('.gdpr-right-to-access-result').show();
						$('.gdpr-right-to-access-result textarea').text( res.data );
						$('.download-data')
							.attr('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res.data))
							.prop('download', email + '.xml');
					}
				}
			)

		});

		$('.gdpr-review').click(function() {
			const uid = parseInt( $(this).parent().data('uid') );
			$('.gdpr-review-table-' + uid).slideToggle();
		});

		$('.gdpr-reassign').change(function() {
			const uid = parseInt( $(this).data('uid') );
			const pt = $(this).data('pt');

			$('.gdpr-form-process-request[data-uid="' + uid + '"][data-pt="' + pt + '"]').attr('data-reassign', $(this).val());
		});

		$('.gdpr-reassign-button').click(function(e) {
			e.preventDefault();

			const form = $(this).parent();
			const uid = parseInt( form.data('uid') );
			const pt = form.data('pt');
			const select = $('tr[data-uid="' + uid + '"][data-pt="' + pt + '"] select');
			const reassign_to = form.data('reassign');
			const post_count = form.data('count');
			const nonce = form.find('#_reassign-nonce').val();

			const data = {
				action: 'gdpr_reassign_content',
				nonce: nonce,
				uid: uid,
				pt: pt,
				post_count: post_count,
				reassign_to: reassign_to
			};

			$(this).hide();
			form.find('.spinner').css('display', 'block');

			$.post(
				ajaxurl,
				data,
				function( res ){
					if ( res.success ) {
						form.find('button').remove();
						form.find('p').show();
						select.prop( {'disabled': true } );
					} else {
						$(this).show();
					}

					form.find('.spinner').hide();
				}
			);
		});

		$('.gdpr-anonymize-button').click(function(e) {
			e.preventDefault();

			const form = $(this).parent();
			const uid = parseInt( form.data('uid') );
			const comment_count = form.data('count');
			const nonce = form.find('#_anonymize-nonce').val();

			const data = {
				action: 'gdpr_anonymize_content',
				nonce: nonce,
				uid: uid,
				comment_count: comment_count,
			};

			$(this).hide();
			form.find('.spinner').css('display', 'block');

			$.post(
				ajaxurl,
				data,
				function( res ){
					if ( res.success ) {
						form.find('button').remove();
						form.find('p').show();
					} else {
						$(this).show();
					}

					form.find('.spinner').hide();
				}
			);
		});

		$('.gdpr-request-delete').click(function(e){
			e.preventDefault();

			var confirmation = confirm( 'You are about to remove a user from the site and have all remaining data deleted. Do you want to proceed?');

			if ( ! confirmation ) {
				return;
			}

			const form = $(this).parent();
			const uid = parseInt( form.data('uid') );
			const nonce = form.find('#_delete-without-review-nonce').val() || form.find('#_delete-nonce').val();

			const data = {
				action: 'gdpr_forget_user',
				nonce: nonce,
				uid: uid,
			};

			form.find('button').hide();
			form.find('.spinner').css('display', 'block');

			$.post(
				ajaxurl,
				data,
				function( res ){
					if ( res.success ) {
						$('#gdpr-requests-table').find('tr[data-uid="' + uid + '"]').remove();
					} else {
						form.find('.spinner').hide();
						form.find('button').show();
					}

				}
			);
		});

		$('.gdpr-page-updated-ignore').click(function(e){
			e.preventDefault();

			const that = $(this);

			const data = {
				action: 'gdpr_ignore_updated_page',
				nonce: $(this).data('nonce'),
				page: $(this).data('page')
			};

			$.post(
				ajaxurl,
				data,
				function( res ){
					if ( res.success ) {
						that.closest('.page-updated-notice').remove();
					}
				}
			);

		});

		$('.gdpr-page-updated-notify').click(function(e){
			e.preventDefault();

			const that = $(this);

			const data = {
				action: 'gdpr_notify_updated_page',
				nonce: $(this).data('nonce'),
				page: $(this).data('page')
			};

			$.post(
				ajaxurl,
				data,
				function( res ){
					if ( res.success ) {
						that.closest('.page-updated-notice').remove();
					}
				}
			);
		});

		$('.gdpr-data-breach-email').submit(function(e){
			e.preventDefault();

			const data = {
				action: 'gdpr_send_confirmation_email_data_breach',
				nonce: $(this).find('#_gdpr_data_breach').val(),
				nature: $(this).find('textarea[name="gdpr-data-breach-nature"]').val(),
				contact: $(this).find('textarea[name="gdpr-name-contact-details-protection-officer"]').val(),
				consequences: $(this).find('textarea[name="gdpr-likely-consequences"]').val(),
				measures: $(this).find('textarea[name="gdpr-measures-taken"]').val()
			};

			$.post(
				ajaxurl,
				data
			);
		});
	});
})( jQuery );
