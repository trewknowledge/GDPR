(function( $ ) {
	'use strict';

	var query_args  = location.search,
			base_url = location.protocol + '//' + location.host + location.pathname;

	if ( -1 !== query_args.indexOf( 'notify=1' ) ) {
		window.history.replaceState( {}, document.title, base_url );
	}

	$(function() {

		var approvedCookies = JSON.parse( readCookie('gdpr_approved_cookies') );

		function createCookie(name, value, days) {
	    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
	    } else {
	    	var expires = "";
	    }
	    document.cookie = name + "=" + value + expires + "; path=/";
		}

		function readCookie(name) {
	    var nameEQ = name + "=";
	    var ca = document.cookie.split(';');
	    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
        	c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
        	return c.substring(nameEQ.length, c.length);
        }
	    }
	    return null;
		}

		function deleteCookie(name) {
			createCookie(name, "", -1);
		}

		var cookieRegistry = [];

		function listenCookieChange(cookieName) {
	    setInterval(function() {
        if (cookieRegistry[cookieName]) {
          if (readCookie(cookieName) != cookieRegistry[cookieName]) {
            // update registry so we dont get triggered again
            cookieRegistry[cookieName] = readCookie(cookieName);
            cookieChanged( cookieName );
          }
        } else {
          cookieRegistry[cookieName] = readCookie(cookieName);
        }
	    }, 100);
		}

		var blockedCookies = ['__utma', '_gid'];
		blockedCookies.forEach( function( item ) {
			listenCookieChange(item);
		} );

		function cookieChanged( cookieName ) {
			if ( ! $.inArray( cookieName, approvedCookies.site_cookies ) ) {
				deleteCookie(cookieName);
			}
		}

		$(document).on('click', '.gdpr-preferences', function() {
			var type = $(this).data('type');
			$('.gdpr-overlay').fadeIn();
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeIn();
		});

		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-close', function() {
			$('.gdpr-overlay').fadeOut();
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeOut();
		});

		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-tabs button', function() {
			var target = '.' + $(this).data('target');
			$('.gdpr.gdpr-privacy-preferences .gdpr-tab-content > div').removeClass('gdpr-active');
			$('.gdpr.gdpr-privacy-preferences .gdpr-tab-content ' + target).addClass('gdpr-active');

			if ( $('.gdpr.gdpr-privacy-preferences .gdpr-tabs').hasClass('gdpr-mobile-expanded') ) {
				$('.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button').removeClass('gdpr-active');
				$('.gdpr.gdpr-privacy-preferences .gdpr-tabs').toggle();
			}

			$('.gdpr.gdpr-privacy-preferences .gdpr-tabs button').removeClass('gdpr-active');
			$('.gdpr-subtabs li button').removeClass('gdpr-active');

			if ( $(this).hasClass('gdpr-tab-button') ) {
				$(this).addClass('gdpr-active');
				if ( $(this).hasClass('gdpr-cookie-settings') ) {
					$('.gdpr-subtabs').find('li button').first().addClass('gdpr-active');
				}
			} else {
				$('.gdpr-cookie-settings').addClass('gdpr-active');
				$(this).addClass('gdpr-active');
			}
		});

		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button', function(e) {
			$(this).toggleClass('gdpr-active');
			$('.gdpr.gdpr-privacy-preferences .gdpr-tabs').toggle().addClass('gdpr-mobile-expanded');
		});

		$(window).resize( function() {
			if ( $(window).width() > 640 && $('.gdpr.gdpr-privacy-preferences .gdpr-tabs').hasClass('gdpr-mobile-expanded') ) {
				$('.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button').removeClass('gdpr-active');
				$('.gdpr.gdpr-privacy-preferences .gdpr-tabs').removeClass('gdpr-mobile-expanded').removeAttr('style');
			}
		});

		$(document).on('submit', '.gdpr-privacy-preferences-frm', function(e) {
			e.preventDefault();
			createApprovedCookiesCookie();
		});

		$(document).on('click', '.gdpr.gdpr-privacy-bar .gdpr-agreement', function() {
			createApprovedCookiesCookie();
		});

		function createApprovedCookiesCookie() {
			var checkboxes = $('input[type="checkbox"]:checked', '.gdpr-privacy-preferences-frm');
			var approvedCookies = [];
			checkboxes.each(function() {
				var value = JSON.parse( $(this).val() );
				if ( $.isArray( value ) ) {
					value.forEach(function( item ) {
						approvedCookies.push( item );
					});
				} else {
					var key = Object.keys( value );
					if (approvedCookies.hasOwnProperty( key )) {
						approvedCookies[key[0]].push( value[key[0]] );
					} else {
						approvedCookies[key[0]] = [value[key[0]]];
					}
				}
			});

			createCookie("gdpr_approved_cookies", JSON.stringify( approvedCookies ));
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper, .gdpr-overlay, .gdpr.gdpr-privacy-bar').fadeOut();
		}

		// $('.confirm-delete-confirmation').dialog({
		// 	resizable: false,
		// 	autoOpen: false,
		// 	height: 'auto',
		// 	width: 400,
		// 	modal: true,
		// 	buttons: {
		// 		"Close my account": function() {
		// 			$('form.gdpr-add-to-deletion-requests').addClass('confirmed');
		// 			$('form.gdpr-add-to-deletion-requests.confirmed').submit();
		// 			$( this ).dialog( "close" );
		// 		},
		// 		Cancel: function() {
		// 			$( this ).dialog( "close" );
		// 		}
		// 	}
		// });

		$('form.gdpr-add-to-deletion-requests').on('submit', function(e) {
			if ( ! $(this).hasClass( 'confirmed' ) ) {
				e.preventDefault();
				$('.gdpr-overlay').fadeIn();
				$('.gdpr.gdpr-delete-confirmation .gdpr-wrapper').css({
					'display': 'flex',
				}).hide().fadeIn();
			}
		} );

		$(document).on('click', '.gdpr.gdpr-delete-confirmation .gdpr-close', function() {
			$('.gdpr-overlay').fadeOut();
			$('.gdpr.gdpr-delete-confirmation .gdpr-wrapper').fadeOut();
		});

		$(document).on('click', '.gdpr.gdpr-delete-confirmation button.gdpr-delete-account', function() {
			$('form.gdpr-add-to-deletion-requests').addClass('confirmed');
			$('form.gdpr-add-to-deletion-requests.confirmed').submit();
			$('.gdpr-overlay').fadeOut();
			$('.gdpr.gdpr-delete-confirmation .gdpr-wrapper').fadeOut();
		});

		if ( $('.gdpr-general-confirmation').length > 0 ) {
			$('.gdpr-overlay').fadeIn();
			$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').css({
				'display': 'flex',
			}).hide().fadeIn();
			$(document).on('click', '.gdpr.gdpr-general-confirmation button.gdpr-accept-confirmation', function() {
				$('.gdpr-overlay').fadeOut();
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').fadeOut();
			});
		}

		if ( $('.gdpr-consent-modal').length > 0 ) {
			$('body').css('overflow', 'hidden');
		}

		$(document).on('click', '.gdpr-agree', function(e) {
			e.preventDefault();
			var that = $(this);
			$.post(
				GDPR.ajaxurl,
				{
					action: 'agree_with_terms',
					nonce: $(this).data('nonce'),
				},
				function(res) {
					if ( res.success ) {
						that.closest('.gdpr-consent-modal').fadeOut(300, function(){
							$(this).remove();
							if ( $('.gdpr-consent-modal').length == 0 ) {
								$('body').css('overflow', 'auto');
							}
						});
					}
				}
			);
		});

		$(document).on('click', '.gdpr-disagree', function(e) {
			e.preventDefault();
			$.post(
				GDPR.ajaxurl,
				{
					action: 'disagree_with_terms',
					nonce: $(this).data('nonce')
				},
				function(res) {
					if ( res.success ) {
						location.reload();
					}
				}
			);
		});
	});

})( jQuery );
