import $ from 'jquery';
import {stringToSlug} from './includes/helpers';

import '../scss/admin.scss';

$( function() {
	$( document ).on( 'click', '.gdpr-settings-form .notice-dismiss', function() {
		$( this ).parent().parent().remove();
	} );


	$( document ).on( 'click', '.add-consent', function( e ) {
		e.preventDefault();
		const field = $( '#type-of-consent' );
		if ( '' === field.val() ) {
			return;
		}
		const consentID = stringToSlug( field.val() );
		const consentName = field.val();
		const template = wp.template( 'consents' );
		$( '#consent-tabs' ).append( template( {
			key: consentID,
			name: consentName,
			optionName: 'gdpr_consent_types'
		} ) );
		field.val( '' );
	} );

	$( '#consent-tabs, #gdpr-cookie-categories' ).sortable();

	$( document ).on( 'click', '.add-tab', function( e ) {
		e.preventDefault();
		const field = $( '#cookie-tabs' );
		if ( '' === field.val() ) {
			return;
		}
		const tabID = stringToSlug( field.val() );
		const tabName = field.val();
		const template = wp.template( 'cookie-tabs' );
		$( '#gdpr-cookie-categories' ).append( template( {
			key: tabID,
			name: tabName
		} ) );
		field.val( '' );
	} );

	$( document ).on( 'click', '.add-host', function( e ) {
		e.preventDefault();
		const field = $( this ).siblings( 'input' );
		if ( '' === field.val() ) {
			return;
		}
		const tabID = $( this ).data( 'tabid' );
		const hostID = field.val().toLowerCase().replace( ' ', '-' );
		const template = wp.template( 'cookie-tabs-hosts' );
		$( '.tab-hosts[data-tabid="' + tabID + '"]' ).append( template( {
			hostKey: hostID,
			tabKey: tabID,
			optionName: 'gdpr_cookie_popup_content'
		} ) );
		field.val( '' );
	} );

	$( document ).on( 'click', '#tabs .notice-dismiss', function( e ) {
		e.preventDefault();
		$( this ).closest( '.postbox' ).remove();
	} );

	$( document ).on( 'click', '.gdpr-request-table .gdpr-review', function( e ) {
		e.preventDefault();
		const target = $( this ).data( 'index' );
		$( 'tr[data-index=' + target + '] div' ).slideToggle();
	} );

	$( document ).on( 'click', '.gdpr .nav-tab-wrapper a', function( e ) {
		let target = $( this ).attr( 'href' );
		target = target.replace( '#', '' );
		$( this ).addClass( 'nav-tab-active' );
		$( this ).siblings().removeClass( 'nav-tab-active' );
		$( '.gdpr .gdpr-tab' ).addClass( 'hidden' );
		$( '.gdpr .gdpr-tab[data-id=' + target + ']' ).removeClass( 'hidden' );

		if ( -1 !== location.search.indexOf( 'page=gdpr-settings' ) ) {
			const referer = $( '.gdpr form input[name="_wp_http_referer"]' );
			const cleanReferer = referer.val().split( '#' )[0];
			referer.val( cleanReferer + '#' + target );
		}
	} );

	const hash = window.location.hash;
	if ( hash ) {
		$( '.gdpr .nav-tab-wrapper a[href="' + hash + '"]' ).addClass( 'nav-tab-active' );
		$( '.gdpr .gdpr-tab[data-id="' + hash.replace( '#', '' ) + '"]' ).removeClass( 'hidden' );
		if ( -1 !== location.search.indexOf( 'page=gdpr-settings' ) ) {
			const referer = $( '.gdpr form input[name="_wp_http_referer"]' );
			const cleanReferer = referer.val().split( '#' )[0];
			referer.val( cleanReferer + hash );
		}
	} else {
		$( '.gdpr .nav-tab-wrapper a:eq(0)' ).addClass( 'nav-tab-active' );
		$( '.gdpr .gdpr-tab:eq(0)' ).removeClass( 'hidden' );
	}

	$( document ).on( 'change', '.gdpr-reassign', function() {
		if ( 0 != $( this ).val() ) {
			$( this ).closest( 'tr' ).find( 'td:last .button-primary' ).attr( 'disabled', false );
			$( this ).closest( 'tr' ).find( 'td:last input[name="reassign_to"]' ).val( $( this ).val() );
		} else {
			$( this ).closest( 'tr' ).find( 'td:last .button-primary' ).attr( 'disabled', true );
			$( this ).closest( 'tr' ).find( 'td:last input[name="reassign_to"]' ).val( '' );
		}
	} );

	$( document ).on( 'submit', '.gdpr-reassign-content', function( e ) {
		e.preventDefault();
		const userEmail  = $( this ).find( 'input[name="user_email"]' ).val(),
			reassignTo = $( this ).find( 'input[name="reassign_to"]' ).val(),
			postType   = $( this ).find( 'input[name="post_type"]' ).val(),
			postCount  = $( this ).find( 'input[name="post_count"]' ).val(),
			nonce       = $( this ).find( 'input[name="gdpr_reassign_content_nonce"]' ).val(),
			button      = $( this ).find( '.button-primary' ),
			spinner     = $( this ).find( '.spinner' ),
			result      = $( this ).find( 'p.hidden' );

		if ( ! reassignTo ) {
			return;
		}

		button.addClass( 'hidden' );
		spinner.addClass( 'is-active' );
		spinner.css( 'display', 'block' );
		$.post(
			ajaxurl,
			{
				action: 'gdpr_reassign_content',
				userEmail,
				reassignTo,
				postType,
				postCount,
				nonce
			},
			function( response ) {
				spinner.removeClass( 'is-active' );
				spinner.hide();
				result.removeClass( 'hidden' );
				if ( ! response.success ) {
					result.text( response.data );
				}
			}
		);
	} );

	$( document ).on( 'submit', '.gdpr-anonymize-comments', function( e ) {
		e.preventDefault();
		const userEmail    = $( this ).find( 'input[name="user_email"]' ).val(),
			commentCount = $( this ).find( 'input[name="comment_count"]' ).val(),
			nonce         = $( this ).find( 'input[name="gdpr_anonymize_comments_nonce"]' ).val(),
			button        = $( this ).find( '.button-primary' ),
			spinner       = $( this ).find( '.spinner' ),
			result        = $( this ).find( 'p.hidden' );

		button.addClass( 'hidden' );
		spinner.addClass( 'is-active' );
		spinner.css( 'display', 'block' );
		$.post(
			ajaxurl,
			{
				action: 'gdpr_anonymize_comments',
				userEmail,
				commentCount,
				nonce
			},
			function( response ) {
				spinner.removeClass( 'is-active' );
				spinner.hide();
				result.removeClass( 'hidden' );
				if ( ! response.success ) {
					result.text( response.data );
				}
			}
		);
	} );

	$( document ).on( 'submit', '.gdpr-access-data-lookup', function( e ) {
		e.preventDefault();
		const userEmail    = $( this ).find( 'input[name="user_email"]' ),
			email         = userEmail.val(),
			nonce         = $( this ).find( 'input[name="gdpr_access_data_nonce"]' ).val(),
			button        = $( this ).find( '.button-primary' ),
			spinner       = $( this ).find( '.spinner' ),
			result        = $( '.gdpr-access-data-result' );

		button.addClass( 'hidden' );
		spinner.show();
		result.remove();
		userEmail.val( '' );

		$.post(
			ajaxurl,
			{
				action: 'gdpr_access_data',
				nonce,
				email
			},
			function( response ) {
				button.removeClass( 'hidden' );
				spinner.hide();
				if ( response.success ) {
					const template = wp.template( 'access-data-result-success' );
					$( '.gdpr div[data-id="access"]' ).append( template( {
						result: response.data.result,
						userEmail: response.data.user_email
					} ) );
				} else {
					const template = wp.template( 'access-data-result-error' );
					$( '.gdpr div[data-id="access"]' ).append( template() );
				}
			}
		);
	} );

	$( document ).on( 'submit', '.gdpr-audit-log-lookup', function( e ) {
		e.preventDefault();
		const userEmail    = $( this ).find( 'input[name="user_email"]' ),
			email         = userEmail.val(),
			token         = $( this ).find( 'input[name="token"]' ),
			tokenVal      = token.val(),
			nonce         = $( this ).find( 'input[name="gdpr_audit_log_nonce"]' ).val(),
			button        = $( this ).find( '.button-primary' ),
			spinner       = $( this ).find( '.spinner' ),
			result        = $( '.gdpr-audit-log-result' );

		button.addClass( 'hidden' );
		spinner.show();
		result.remove();
		userEmail.val( '' );
		token.val( '' );

		$.post(
			ajaxurl,
			{
				action: 'gdpr_audit_log',
				nonce,
				email,
				token: tokenVal
			},
			function( response ) {
				button.removeClass( 'hidden' );
				spinner.hide();
				if ( response.success ) {
					const template = wp.template( 'audit-log-result-success' );
					$( '.gdpr div[data-id="audit-log"]' ).append( template( {
						result: response.data
					} ) );
				} else {
					const template = wp.template( 'audit-log-result-error' );
					$( '.gdpr div[data-id="audit-log"]' ).append( template() );
				}
			}
		);
	} );

	$( document ).on( 'click', '.frm-export-data input[type="submit"]', function( e ) {
		e.preventDefault();

		const form = $( this ).parents( 'form' ),
			type = $( this ).val(),
			nonce = form.find( '#gdpr_export_data_nonce' ).val(),
			email = form.find( 'input[name="user_email"]' ).val(),
			extension = type.toLowerCase();

		$.post(
			ajaxurl,
			{
				action: 'gdpr_generate_data_export',
				nonce,
				type,
				email
			},
			function( response ) {
				if ( response.success ) {
					$( '<a />', {
						'href': 'data:text/plain;charset=utf-8,' + encodeURIComponent( response.data ),
						'download': email + '.' + extension,
						'text': 'click'
					} ).hide().appendTo( 'body' )[0].click();
				}
			}
		);
	} );

	$( document ).on( 'submit', '.frm-policy-updated', function( e ) {
		e.preventDefault();
		const action = $( this ).find( 'input[name="action"]' ).val(),
			policyId = $( this ).find( 'input[name="policy_id"]' ).val(),
			policyName = $( this ).find( 'input[name="policy_name"]' ).val(),
			nonce = $( this ).find( '[id$="nonce"]' ).val(),
			spinner = $( this ).parent().find( '.spinner' ),
			that = $( this );


		spinner.addClass( 'is-active' );
		$.post(
			ajaxurl,
			{
				action: action,
				nonce: nonce,
				policyId,
				policyName
			},
			function( res ) {
				spinner.removeClass( 'is-active' );
				that.parent().fadeOut();
			}
		);
	} );
	
	$( '.color_pick' ).wpColorPicker();

	$( document ).on( 'click', '.show-settings', function( e ) {
		e.preventDefault();
		const nextTr = $( this ).parents( 'tr' ).next( 'tr' );
		console.log( nextTr );
		nextTr.toggleClass( 'hide' );
	});

	$( document ).on( 'submit', '.gdpr-email-settings', function( e ) {
		e.preventDefault();
		const formData = new FormData( this );
		console.log( formData );

		$.ajax( {
			url: ajaxurl,
			type: 'post',
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			xhrFields: {
				withCredentials: true
			},
			success: ( response ) => {
				let successDiv = '<div id="message" class="updated inline"><p><strong>' + response.data + '</strong></p></div>';
				
				$( '#gdpr_heading' ).after( successDiv );
			}
		} );

	});

} );
