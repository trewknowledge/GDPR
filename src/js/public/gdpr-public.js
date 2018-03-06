(function( $ ) {
	'use strict';

	$(function() {

		var approvedCookies = JSON.parse( readCookie('gdpr_approved_cookies') );
		console.log(approvedCookies);

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

		setTimeout(function(){
			createCookie('__utma', 'juliana');
		}, 1000);


		$(document).on('click', '.gdpr-cookie-preferences', function() {
			$('.gdpr.overlay').fadeIn();
			$('.gdpr.cookie-preferences .wrapper').fadeIn();
		});

		$(document).on('click', '.gdpr.cookie-preferences .tabs button', function() {
			var target = '.' + $(this).data('target');
			$('.gdpr.cookie-preferences .tab-content > div').removeClass('active');
			$('.gdpr.cookie-preferences .tab-content ' + target).addClass('active');

			$('.gdpr.cookie-preferences .tabs button').removeClass('active');
			$(this).addClass('active');
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
			$('.gdpr.cookie-preferences').fadeOut();
			$('.gdpr.cookie-bar').fadeOut();
		}
	});

})( jQuery );
