import $ from 'jquery';
import Cookies from 'js-cookie';
import { displayNotification, gdprFunctions } from './includes/helpers';

import '../scss/public.scss';

const queryArgs  = location.search;

const baseUrl    = location.protocol + '//' + location.host + location.pathname;

window.has_consent = function( consent ) {
	if ( Cookies.get( 'gdpr[consent_types]' ) ) {
		const consentArray = JSON.parse( Cookies.get( 'gdpr[consent_types]' ) );
		if ( -1 < consentArray.indexOf( consent ) ) {
			return true;
		}
	}

	return false;
};

window.is_allowed_cookie = function ( cookie ) {
	if ( Cookies.get( 'gdpr[allowed_cookies]' ) ) {
		const cookiesArray = JSON.parse( Cookies.get( 'gdpr[allowed_cookies]' ) );
		if ( -1 < cookiesArray.indexOf( cookie ) ) {
			return true;
		}
	}

	return false;
};

$( function() {
	const body = $( 'body' );
	const privacyPreferences = $( '.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs' );
	if ( -1 !== queryArgs.indexOf( 'notify=1' ) ) {
		window.history.replaceState( {}, document.title, baseUrl );
		body.addClass( 'gdpr-notification' );
	}

	$( document ).on( 'click', '.gdpr.gdpr-general-confirmation button', function( e ) {
		const callback = $( this ).data( 'callback' );
		gdprFunctions[callback]();
	} );

	$( document ).on( 'submit', '.gdpr-privacy-preferences-frm', function( e ) {
		$( '.gdpr.gdpr-privacy-preferences .gdpr-wrapper' ).fadeOut();
		$( '.gdpr-privacy-bar' ).fadeOut();
		e.preventDefault();
		const that = $( this );
		const formData = $( this ).serialize();
		$.post(
			GDPR.ajaxurl,
			formData,
			function( response ) {
				if ( response.success ) {
					Cookies.set( 'gdpr[privacy_bar]', GDPR.popUpVersion, { expires: 365 } );
					if ( GDPR.refresh ) {
						window.location.reload();
					} else {

						const scrollDistance = body.css( 'top' );
						$( '.gdpr-overlay' ).fadeOut();
						body.removeClass( 'gdpr-noscroll' );
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
	if ( ! Cookies.get( 'gdpr[privacy_bar]' ) || Cookies.get( 'gdpr[privacy_bar]' ) !== GDPR.popUpVersion ) {
		if ( 0 === $( '.gdpr-reconsent-bar, .gdpr-reconsent' ).length ) {
			$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
		}
	}

	if ( 0 < $( '.gdpr-reconsent-bar' ).length ) {
		$( '.gdpr.gdpr-reconsent-bar' ).delay( 1000 ).slideDown( 600 );
	}

	if ( 0 < $( '.gdpr-reconsent' ).length ) {
		$( '.gdpr-overlay' ).fadeIn( 400, function() {
			$( '.gdpr.gdpr-reconsent .gdpr-wrapper' ).fadeIn();
			body.addClass( 'gdpr-noscroll' ).delay( 1000 );
		} );
	}

	/**
	 * This runs when user clicks on privacy preferences bar agree button.
	 * It submits the form that is still hidden with the cookies and consent options.
	 *
	 **/
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
						if ( ! f.get( 'gdpr[privacy_bar]' || f.get( 'gdpr[privacy_bar]' ) !== GDPR.popUpVersion  ) ) {
							$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
						}
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
		$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
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

						const scrollDistance = body.css( 'top' );
						$( '.gdpr-overlay' ).fadeOut();
						body.removeClass( 'gdpr-noscroll' );
						$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
						$( '.gdpr.gdpr-reconsent .gdpr-wrapper' ).fadeOut();
						if ( ! Cookies.get( 'gdpr[privacy_bar]' ) || Cookies.get( 'gdpr[privacy_bar]' ) !==  GDPR.popUpVersion ) {
							$( '.gdpr.gdpr-privacy-bar' ).delay( 1000 ).slideDown( 600 );
						}
					}
				} else {
					displayNotification( res.data.title, res.data.content );
				}
			}
		);
	} );

	/**
	 * Close the privacy/reconsent bar.
	 * If user close the bar means that not accept cookies and you can't show it again
	 */
	$( document ).on( 'click', '.gdpr.gdpr-privacy-bar .gdpr-close, .gdpr.gdpr-reconsent-bar .gdpr-close', function() {
		const scrollDistance = body.css( 'top' );
		$( '.gdpr-overlay' ).fadeOut();
		body.removeClass( 'gdpr-noscroll' );
		$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
		$( '.gdpr.gdpr-privacy-bar, .gdpr.gdpr-reconsent-bar' ).slideUp( 600 );

		// If close means that accept all cookies
		if ( '1' === GDPR.closeAccept ) {

			$( '.gdpr-privacy-preferences-frm' ).submit();
		}
		Cookies.set( 'gdpr[privacy_bar]', GDPR.popUpVersion, { expires: 365 } );
	} );

	$( document ).on( 'click', '.gdpr.gdpr-general-confirmation .gdpr-close', function() {
		$( '.gdpr-overlay' ).fadeOut();
		body.removeClass( 'gdpr-noscroll' );
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).fadeOut();

		Cookies.set( 'gdpr[privacy_bar]', GDPR.popUpVersion, { expires: 365 } );
	} );

	/**
	 * Display the privacy preferences modal.
	 * By default if check preferences all cookies must be dissabled
	 */
	$( document ).on( 'click', '.gdpr-preferences', function( e ) {
		e.preventDefault();
		const scrollDistance = $( window ).scrollTop();
		const tab = $( this ).data( 'tab' );
		$( '.gdpr-overlay' ).fadeIn();
		$( '.gdpr-cookie-category' ).prop( 'checked', false );
		body.addClass( 'gdpr-noscroll' ).css( 'top', -scrollDistance );
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
		const scrollDistance = body.css( 'top' );
		if ( ! $( '.gdpr-reconsent .gdpr-wrapper' ).is( ':visible' ) ) {
			$( '.gdpr-overlay' ).fadeOut();
			body.removeClass( 'gdpr-noscroll' );
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
		if ( privacyPreferences.hasClass( 'gdpr-mobile-expanded' ) ) {
			$( '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button' ).removeClass( 'gdpr-active' );
			privacyPreferences.toggle();
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
		privacyPreferences.toggle().addClass( 'gdpr-mobile-expanded' );
	} );

	$( window ).resize( function() {
		if ( 640 < $( window ).width() && privacyPreferences.hasClass( 'gdpr-mobile-expanded' ) ) {
			$( '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button' ).removeClass( 'gdpr-active' );
			privacyPreferences.removeClass( 'gdpr-mobile-expanded' ).removeAttr( 'style' );
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

	if ( body.hasClass( 'gdpr-notification' ) ) {
		const scrollDistance = $( window ).scrollTop();
		$( '.gdpr-overlay' ).fadeIn( 400, function() {
			$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).css( {
				'display': 'flex'
			} ).hide().fadeIn();
			body.addClass( 'gdpr-noscroll' ).css( 'top', -scrollDistance );
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
