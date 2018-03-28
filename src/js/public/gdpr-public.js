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
			switch(type) {
				case 'cookies':
					$('.gdpr.cookie-preferences .wrapper').fadeIn();
					break;
				case 'consents':
					$('.gdpr.consents-preferences .wrapper').fadeIn();
					break;
			}
		});

		$(document).on('click', '.gdpr.cookie-preferences .close', function() {
			$('.gdpr-overlay').fadeOut();
			$('.gdpr.cookie-preferences .wrapper').fadeOut();
		});
		$(document).on('click', '.gdpr.consents-preferences .close', function() {
			$('.gdpr-overlay').fadeOut();
			$('.gdpr.consents-preferences .wrapper').fadeOut();
		});

		$(document).on('click', '.gdpr.cookie-preferences .tabs button', function() {
			var target = '.' + $(this).data('target');
			$('.gdpr.cookie-preferences .tab-content > div').removeClass('active');
			$('.gdpr.cookie-preferences .tab-content ' + target).addClass('active');

			if ( $('.gdpr.cookie-preferences .tabs').hasClass('mobile-expanded') ) {
				$('.gdpr.cookie-preferences .mobile-menu button').removeClass('active');
				$('.gdpr.cookie-preferences .tabs').toggle();
			}

			$('.gdpr.cookie-preferences .tabs button').removeClass('active');
			$(this).addClass('active');
		});

		$(document).on('click', '.gdpr.cookie-preferences .mobile-menu button', function(e) {
			$(this).toggleClass('active');
			$('.gdpr.cookie-preferences .tabs').toggle().addClass('mobile-expanded');
		});

		$(window).resize( function() {
			if ( $(window).width() > 640 && $('.gdpr.cookie-preferences .tabs').hasClass('mobile-expanded') ) {
				$('.gdpr.cookie-preferences .mobile-menu button').removeClass('active');
				$('.gdpr.cookie-preferences .tabs').removeClass('mobile-expanded').removeAttr('style');
			}
		});

		$(document).on('submit', '.frm-gdpr-cookie-preferences', function(e) {
			e.preventDefault();
			createApprovedCookiesCookie();
		});

		$(document).on('click', '.gdpr.cookie-bar .accept-cookies', function() {
			createApprovedCookiesCookie();
		});

		function createApprovedCookiesCookie() {
			var checkboxes = $('input[type="checkbox"]:checked', '.frm-gdpr-cookie-preferences');
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
			$('.gdpr.cookie-preferences .wrapper, .gdpr-overlay, .gdpr.cookie-bar').fadeOut();
		}

		$('.confirm-delete-request-dialog').dialog({
			resizable: false,
			autoOpen: false,
			height: 'auto',
			width: 400,
			modal: true,
			buttons: {
				"Close my account": function() {
					$('form.gdpr-add-to-deletion-requests').addClass('confirmed');
					$('form.gdpr-add-to-deletion-requests.confirmed').submit();
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		// $(document).on('click', '.gdpr-add-to-deletion-requests-button', function() {
		// 	$('.confirm-delete-request-dialog').dialog('open');
		// });

		$('form.gdpr-add-to-deletion-requests').on('submit', function(e){
			if ( ! $(this).hasClass( 'confirmed' ) ) {
				e.preventDefault();
				$('.confirm-delete-request-dialog').dialog('open');
			}
		})

		if ( $('.gdpr-general-dialog').length > 0 ) {
			$('.gdpr-general-dialog').dialog({
				resizable: false,
				height: 'auto',
				width: 400,
				modal: true,
				buttons: {
					"Ok": function() {
						$( this ).dialog( "close" );
					}
				}
			});
		}

		$(document).on( 'submit', '.frm-gdpr-consents-preferences', function(e) {
			e.preventDefault();

			var checkboxes = $(this).find('input[type="checkbox"]:checked'),
					consents = [],
					action = $(this).find('input[name="action"]').val(),
					nonce = $(this).find('input[name="update-consents-nonce"]').val(),
					button = $(this).find('input[type="submit"]'),
					error = $(this).find('.error');


			checkboxes.each(function() {
				consents.push( $(this).val() );
			});

			button.prop( 'disabled', true );
			error.html('');

			$.post(
				GDPR.ajaxurl,
				{
					action: action,
					nonce: nonce,
					consents: consents
				},
				function( res ) {
					if( res.success ) {
						$('.gdpr-overlay').fadeOut();
						$('.gdpr.consents-preferences .wrapper').fadeOut();
					} else {
						error.html( res.data );
						console.log(res.data);
					}
					button.prop( 'disabled', false );
				}
			);

		});

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
