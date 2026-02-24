import $ from 'jquery';
import Cookies from 'js-cookie';
import { displayNotification, gdprFunctions } from './includes/helpers';

import '../scss/public.scss';

const queryArgs  = location.search;
const baseUrl    = location.protocol + '//' + location.host + location.pathname;

window.has_consent = function( consent ) {
	let consentArray = [];
	if ( Cookies.get( 'gdpr_consent_types' ) ) {
		consentArray = JSON.parse( Cookies.get( 'gdpr_consent_types' ) );
	} else if ( Cookies.get( 'gdpr[consent_types]' ) ) {
		consentArray = JSON.parse( Cookies.get( 'gdpr[consent_types]' ) );
	}

	if ( -1 < consentArray.indexOf( consent ) ) {
		return true;
	}
	return false;
};

window.is_allowed_cookie = function ( cookie ) {
	let cookiesArray = [];
	if ( Cookies.get( 'gdpr_allowed_cookies' ) ) {
		cookiesArray = JSON.parse( Cookies.get( 'gdpr_allowed_cookies' ) );
	} else if ( Cookies.get( 'gdpr[allowed_cookies]' ) ) {
		cookiesArray = JSON.parse( Cookies.get( 'gdpr[allowed_cookies]' ) );
	}

	if ( -1 < cookiesArray.indexOf( cookie ) || $.inArray( cookie, cookiesArray ) ) {
		return true;
	}
	return false;
};

function init_plugin_js() {

	//consent

	let consentArray = [];
	let difference = [];
	if ( Cookies.get( 'gdpr_consent_types' ) ) {
		consentArray = JSON.parse( Cookies.get( 'gdpr_consent_types' ) );
	} else if ( Cookies.get( 'gdpr[consent_types]' ) ) {
		consentArray = JSON.parse( Cookies.get( 'gdpr[consent_types]' ) );
	}

	if ( 0 < consentArray.length ) {
		difference = $( GDPR.user_consent ).not( consentArray ).get();

		if ( 0 === difference.length ) {
			difference = $( consentArray ).not( GDPR.user_consent ).get();
		} else if ( difference ) {
			Cookies.set( 'gdpr_consent_types', JSON.stringify( GDPR.user_consent ), { expires: 365 } );
		}

		$.each( GDPR.consent_types, function( consent_key, consent_data ) {
			if ( -1 < consentArray.indexOf( consent_key ) ) {
				$( '#' + consent_key ).attr( 'checked', true );
			} else {
				$( '#' + consent_key ).attr( 'checked', false );
			}
		});
	} else {
		if ( 0 < GDPR.user_consent.length ) {
			Cookies.set( 'gdpr_consent_types', JSON.stringify( GDPR.user_consent ), { expires: 365 } );
		} else {
			Cookies.set( 'gdpr_consent_types', '[]', { expires: 365 } );
		}
		$.each( GDPR.consent_types, function( consent_key, consent_data ) {
			$( '#' + consent_key ).attr( 'checked', true );
		});
	}

	// Cookie
	let allowed_cookies = [];
	let cookies         = [];
	let privacy_bar     = true;
	if ( Cookies.get( 'gdpr_allowed_cookies' ) ) {
		allowed_cookies = JSON.parse( Cookies.get( 'gdpr_allowed_cookies' ) );
	} else if ( Cookies.get( 'gdpr[allowed_cookies]' ) ) {
		allowed_cookies = JSON.parse( Cookies.get( 'gdpr[allowed_cookies]' ) );
	}

	if ( Cookies.get( 'gdpr_privacy_bar' ) ) {
		privacy_bar = false;
	}

	let registered_used_cookies = [];
	let used_cookie = '';
	let accepted_cookie = [];
	if ( GDPR.registered_cookies ) {
		$.each( GDPR.registered_cookies, function( key, value ) {
			if ( 'required' === value.status || 'soft' === value.status ) {
				used_cookie = value.cookies_used.split( ',' );
				$.each( used_cookie, function( cookie_key, cookie_value ) {
					cookies.push( cookie_value );
				});
			}

			registered_used_cookies = value.cookies_used.split( ',' );
			if ( 0 < allowed_cookies.length ) {
				$.each( registered_used_cookies, function ( used_cookies_key, used_cookies_val ) {
					if ( -1 < allowed_cookies.indexOf( $.trim( used_cookies_val ) ) || ( 'on' === value.status  || 'required' === value.status || 'soft' === value.status ) ) {
						$( '[data-category=' + key + ']' ).attr( 'checked', true );
					} else {
						$( '[data-category=' + key + ']' ).attr( 'checked', false );
					}
				});
			} else {
				if ( 'required' === value.status || 'soft' === value.status || 'on' === value.status ) {
					$( '[data-category=' + key + ']' ).attr( 'checked', true );
				} else {
					$( '[data-category=' + key + ']' ).attr( 'checked', false );
				}
			}
		});
	}

	if ( 0 === allowed_cookies.length ) {
		if ( 0 < cookies.length ) {
			Cookies.set( 'gdpr_allowed_cookies', JSON.stringify( cookies ), { expires: 365 } );
		} else {
			Cookies.set( 'gdpr_allowed_cookies', '[]', { expires: 365 } );
		}
	}

	const scriptsTags = $('script[type*="plain"]');

	$.each( scriptsTags, function( key, value ) {
		let cookieCategory = $( this ).attr( 'data-gdpr' );
		if ( is_allowed_cookie( cookieCategory ) ) {
			$( this ).replaceWith( eval( $( this ).text() ) ); // phpcs:ignore
		}
	} );

}

$( function() {

	init_plugin_js();

	if ( -1 !== queryArgs.indexOf( 'notify=1' ) ) {
		window.history.replaceState( {}, document.title, baseUrl );
		$( 'body' ).addClass( 'gdpr-notification' );
	}

	$( document ).on( 'click', '.gdpr.gdpr-general-confirmation button', function( e ) {
		const callback = $( this ).data( 'callback' );
		gdprFunctions[callback]();
	} );

	$( document ).on( 'submit', '.gdpr-privacy-preferences-frm', function( e ) {
		e.preventDefault();
		const that = $( this );
		const formData = $( this ).serialize();

		$.post(
			GDPR.ajaxurl,
			formData,
			function( response ) {
				if ( response.success ) {
					Cookies.set( 'gdpr_privacy_bar', 1, { expires: 365 } );

					if ( response.data.cookies ) {
						Cookies.set( 'gdpr_allowed_cookies', JSON.stringify( response.data.cookies ), { expires: 365 } );
					}
					if ( response.data.consents ) {
						Cookies.set( 'gdpr_consent_types', JSON.stringify( response.data.consents ), { expires: 365 } );
					}

					if ( response.data.removed_cookies ) {
						for ( let i = 0, l = response.data.removed_cookies.length; i < l; i++ ) {
							let cookie_name = response.data.removed_cookies[ i ];
							Cookies.remove( cookie_name, { path: '' } );
						}
					}
					if ( GDPR.refresh ) {
						window.location.reload();
					} else {
						const scrollDistance = $( 'body' ).css( 'top' );
						$( '.gdpr-overlay' ).fadeOut();
						$( 'body' ).removeClass( 'gdpr-noscroll' );
						$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
						$( '.gdpr.gdpr-privacy-preferences .gdpr-wrapper' ).fadeOut();
						$( '.gdpr-privacy-bar' ).fadeOut();
					}
				} else {
					displayNotification( response.data.title, response.data.content );
				}
			}
		);

	} );

	$( document ).on( 'submit', '.gdpr-request-form', function( e ) {
		e.preventDefault();
		if ( $( this ).hasClass( 'confirmed' ) ) {
			const formData = $( this ).serialize();

			$.post(
				GDPR.ajaxurl,
				formData,
				function( response ) {
					displayNotification( response.data.title, response.data.content );
				}
			);
		}
	} );

	$( document ).on( 'change', '.gdpr-cookie-category', function() {
		const target = $( this ).data( 'category' );
		const checked = $( this ).prop( 'checked' );
		$( '[data-category="' + target + '"]' ).prop( 'checked', checked );
	} );

	if ( ! Cookies.get( 'gdpr[privacy_bar]' ) && ! Cookies.get( 'gdpr_privacy_bar' ) ) {
		if ( 0 == $( '.gdpr-reconsent-bar, .gdpr-reconsent' ).length ) {
			$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
		}
	};

	if ( 0 < $( '.gdpr-reconsent-bar' ).length ) {
		$( '.gdpr.gdpr-reconsent-bar' ).delay( 1000 ).slideDown( 600 );
	}

	if ( 0 < $( '.gdpr-reconsent' ).length ) {
		$( '.gdpr-overlay' ).fadeIn( 400, function() {
			$( '.gdpr.gdpr-reconsent .gdpr-wrapper' ).fadeIn();
			$( 'body' ).addClass( 'gdpr-noscroll' ).delay( 1000 );
		} );
	}

	/**
	 * This runs when user clicks on privacy preferences bar agree button.
	 * It submits the form that is still hidden with the cookies and consent options.
	 */
	$( document ).on( 'click', '.gdpr.gdpr-privacy-bar .gdpr-agreement', function() {
		$( '.gdpr-privacy-preferences-frm' ).submit();
	} );

	$( document ).on( 'click', '.gdpr.gdpr-reconsent-bar .gdpr-agreement', function() {
		let consents = [];
		$( '.gdpr-policy-list input[type="hidden"]' ).each( function() {
			consents.push( $( this ).val() );
		} );
		const nonce = $( this ).data( 'nonce' );
		$.post(
			GDPR.ajaxurl,
			{
				action: 'agree_with_new_policies',
				nonce,
				consents
			},
			function( res ) {
				if ( res.success ) {
					if ( GDPR.refresh ) {
						window.location.reload();
					} else {
						$( '.gdpr-reconsent-bar' ).slideUp( 600 );
						if ( ! Cookies.get( 'gdpr[privacy_bar]' ) || ! Cookies.get( 'gdpr_privacy_bar_' ) ) {
							$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
						};
					}
				} else {
					displayNotification( res.data.title, res.data.content );
				}
			}
		);
	} );

	$( document ).on( 'submit', '.gdpr-reconsent-frm', function( e ) {
		e.preventDefault();
		let consents = [];
		const nonce = $( this ).find( '#agree-with-new-policies-nonce' ).val();
		$( this ).find( '[name="gdpr-updated-policy"]' ).each( function() {
			consents.push( $( this ).val() );
		} );

		$.post(
			GDPR.ajaxurl,
			{
				action: 'agree_with_new_policies',
				nonce,
				consents
			},
			function( res ) {
				if ( res.success ) {
					if ( GDPR.refresh ) {
						window.location.reload();
					} else {
						const scrollDistance = $( 'body' ).css( 'top' );
						$( '.gdpr-overlay' ).fadeOut();
						$( 'body' ).removeClass( 'gdpr-noscroll' );
						$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
						$( '.gdpr.gdpr-reconsent .gdpr-wrapper' ).fadeOut();
						if ( ! Cookies.get( 'gdpr[privacy_bar]' ) || ! Cookies.get( 'gdpr_privacy_bar_' ) ) {
							$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
						};
					}
				} else {
					displayNotification( res.data.title, res.data.content );
				}
			}
		);
	} );

	/**
	 * Close the privacy/reconsent bar.
	 */
	$( document ).on( 'click', '.gdpr.gdpr-privacy-bar .gdpr-close, .gdpr.gdpr-reconsent-bar .gdpr-close', function() {
		const scrollDistance = $( 'body' ).css( 'top' );
		$( '.gdpr-overlay' ).fadeOut();
		$( 'body' ).removeClass( 'gdpr-noscroll' );
		$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
		$( '.gdpr.gdpr-privacy-bar, .gdpr.gdpr-reconsent-bar' ).slideUp( 600 );
	} );

	$( document ).on( 'click', '.gdpr.gdpr-general-confirmation .gdpr-close', function() {
		$( '.gdpr-overlay' ).fadeOut();
		$( 'body' ).removeClass( 'gdpr-noscroll' );
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).fadeOut();
	} );

	/**
	 * Display the privacy preferences modal.
	 */
	$( document ).on( 'click', '.gdpr-preferences', function( e ) {
		e.preventDefault();
		const scrollDistance = $( window ).scrollTop();
		const tab = $( this ).data( 'tab' );
		$( '.gdpr-overlay' ).fadeIn();
		$( 'body' ).addClass( 'gdpr-noscroll' ).css( 'top', -scrollDistance );
		$( '.gdpr.gdpr-privacy-preferences .gdpr-wrapper' ).fadeIn();
		if ( tab ) {
			$( '.gdpr.gdpr-privacy-preferences .gdpr-wrapper .gdpr-tabs [data-target="' + tab + '"]' ).click();
		}
	} );

	/**
	 * Close the privacy/reconsent preferences modal.
	 */
	$( document ).on( 'click', '.gdpr.gdpr-privacy-preferences .gdpr-close', function( e ) {
		e.preventDefault();
		const scrollDistance = $( 'body' ).css( 'top' );
		if ( ! $( '.gdpr-reconsent .gdpr-wrapper' ).is( ':visible' ) ) {
			$( '.gdpr-overlay' ).fadeOut();
			$( 'body' ).removeClass( 'gdpr-noscroll' );
			$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
		}
		$( '.gdpr.gdpr-privacy-preferences .gdpr-wrapper' ).fadeOut();
	} );

	/**
	 * Tab navigation for the privacy preferences modal.
	 */
	$( document ).on( 'click', '.gdpr.gdpr-privacy-preferences .gdpr-tabs button, .gdpr.gdpr-reconsent .gdpr-tabs button', function() {
		const target = '.' + $( this ).data( 'target' );
		$( '.gdpr.gdpr-privacy-preferences .gdpr-tab-content > div, .gdpr.gdpr-reconsent .gdpr-tab-content > div' ).removeClass( 'gdpr-active' );
		$( '.gdpr.gdpr-privacy-preferences .gdpr-tab-content ' + target + ', .gdpr.gdpr-reconsent .gdpr-tab-content ' + target ).addClass( 'gdpr-active' );

		if ( $( '.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs' ).hasClass( 'gdpr-mobile-expanded' ) ) {
			$( '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button' ).removeClass( 'gdpr-active' );
			$( '.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs' ).toggle();
		}

		$( '.gdpr.gdpr-privacy-preferences .gdpr-tabs button, .gdpr.gdpr-reconsent .gdpr-tabs button' ).removeClass( 'gdpr-active' );
		$( '.gdpr-subtabs li button' ).removeClass( 'gdpr-active' );

		if ( $( this ).hasClass( 'gdpr-tab-button' ) ) {
			$( this ).addClass( 'gdpr-active' );
			if ( $( this ).hasClass( 'gdpr-cookie-settings' ) ) {
				$( '.gdpr-subtabs' ).find( 'li button' ).first().addClass( 'gdpr-active' );
			}
		} else {
			$( '.gdpr-cookie-settings' ).addClass( 'gdpr-active' );
			$( this ).addClass( 'gdpr-active' );
		}
	} );

	/**
	 * Mobile menu for privacy preferences modal.
	 */
	$( document ).on( 'click', '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button', function( e ) {
		$( this ).toggleClass( 'gdpr-active' );
		$( '.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs' ).toggle().addClass( 'gdpr-mobile-expanded' );
	} );

	$( window ).resize( function() {
		if ( 640 < $( window ).width() && $( '.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs' ).hasClass( 'gdpr-mobile-expanded' ) ) {
			$( '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button' ).removeClass( 'gdpr-active' );
			$( '.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs' ).removeClass( 'gdpr-mobile-expanded' ).removeAttr( 'style' );
		}
	} );

	$( 'form.gdpr-add-to-deletion-requests' ).on( 'submit', function( e ) {
		if ( ! $( this ).hasClass( 'confirmed' ) ) {
			e.preventDefault();
			const actions = [
				{
					title: GDPR.i18n.ok,
					buttonClass: 'gdpr-ok',
					callback: 'addToDeletionConfirmed'
				},
				{
					title: GDPR.i18n.cancel,
					buttonClass: 'gdpr-cancel',
					callback: 'closeNotification'
				}
			];
			displayNotification( GDPR.i18n.close_account, GDPR.i18n.close_account_warning, actions );
		}
	} );

	if ( $( 'body' ).hasClass( 'gdpr-notification' ) ) {
		const scrollDistance = $( window ).scrollTop();
		$( '.gdpr-overlay' ).fadeIn( 400, function() {
			$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).css( {
				'display': 'flex'
			} ).hide().fadeIn();
			$( 'body' ).addClass( 'gdpr-noscroll' ).css( 'top', -scrollDistance );
		} );
	}

	$( document ).on( 'click', '.gdpr-disagree a', function( e ) {
		$( '.gdpr.gdpr-reconsent .gdpr-wrapper' ).fadeOut();
		const actions = [
			{
				title: GDPR.i18n.ok,
				buttonClass: 'gdpr-ok',
				callback: 'policyDisagreeOk'
			},
			{
				title: GDPR.i18n.cancel,
				buttonClass: 'gdpr-cancel',
				callback: 'policyDisagreeCancel'
			}
		];
		displayNotification( GDPR.i18n.are_you_sure, GDPR.i18n.policy_disagree, actions, true );
	} );
} );
