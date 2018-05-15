(function( $ ) {
	'use strict';

	var query_args  = location.search,
			base_url = location.protocol + '//' + location.host + location.pathname;

	if ( -1 !== query_args.indexOf( 'notify=1' ) ) {
		window.history.replaceState( {}, document.title, base_url );
	}

	window.has_consent = function( consent ) {
		if ( Cookies.get('gdpr[consent_types]') ) {
			var consentArray = JSON.parse( Cookies.get('gdpr[consent_types]') );
			if ( consentArray.indexOf( consent ) > -1 ) {
				return true;
			}
		}

		return false;
	}

	window.is_allowed_cookie = function ( cookie ) {
		if ( Cookies.get('gdpr[allowed_cookies]') ) {
			var cookiesArray = JSON.parse( Cookies.get('gdpr[allowed_cookies]') );
			if ( cookiesArray.indexOf( cookie ) > -1 ) {
				return true;
			}
		}

		return false;
	}

	$(function() {

		if ( ! Cookies.get('gdpr[privacy_bar]') ) {
			$('.gdpr.gdpr-privacy-bar').delay(1000).slideDown(600);
		};

		if ( ! has_consent( 'privacy-policy' ) && GDPR.is_user_logged_in && GDPR.privacy_page_id != 0 ) {
			$('.gdpr-reconsent-modal').show();
			$('body').addClass('gdpr-noscroll');
			$('.wpadminbar').hide();
		}

		/**
		 * This runs when user clicks on privacy preferences bar agree button.
		 * It submits the form that is still hidden with the cookies and consent options.
		 */
		$(document).on('click', '.gdpr.gdpr-privacy-bar .gdpr-agreement', function() {
      $('.gdpr-privacy-preferences-frm').submit();
    });

		/**
		 * Set the privacy bar cookie after privacy preference submission.
		 * This hides the privacy bar from showing after saving privacy preferences.
		 */
    $(document).on('submit', '.gdpr-privacy-preferences-frm', function() {
    	Cookies.set('gdpr[privacy_bar]', 1, { expires: 365 });
    });

		/**
		 * Display the privacy preferences modal.
		 */
		$(document).on('click', '.gdpr-preferences', function() {
			var type = $(this).data('type');
			$('.gdpr-overlay').fadeIn();
			$('body').addClass('gdpr-noscroll');
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeIn();
		});

		/**
		 * Close the privacy preferences modal.
		 */
		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-close, .gdpr-overlay', function() {
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeOut();
		});

		/**
		 * Tab navigation for the privacy preferences modal.
		 */
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

		/**
		 * Mobile menu for privacy preferences modal.
		 */
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

		$('form.gdpr-add-to-deletion-requests').on('submit', function(e) {
			if ( ! $(this).hasClass( 'confirmed' ) ) {
				e.preventDefault();
				$('.gdpr-overlay').fadeIn();
				$('body').addClass('gdpr-noscroll');
				$('.gdpr.gdpr-delete-confirmation .gdpr-wrapper').css({
					'display': 'flex',
				}).hide().fadeIn();
			}
		} );

		$(document).on('click', '.gdpr.gdpr-general-confirmation .gdpr-close, .gdpr-overlay, .gdpr-cancel', function() {
			$('.gdpr-overlay').fadeOut();
			if ( ! $('.gdpr-reconsent-modal').is(':visible') ) {
				$('body').removeClass('gdpr-noscroll');
			}
			$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').fadeOut();
		});

		$(document).on('click', '.gdpr.gdpr-delete-confirmation button.gdpr-delete-account', function() {
			$('form.gdpr-add-to-deletion-requests').addClass('confirmed');
			$('form.gdpr-add-to-deletion-requests.confirmed input[type="submit"]').click();
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
			$('.gdpr.gdpr-delete-confirmation .gdpr-wrapper').fadeOut();
		});

		if ( $('.gdpr-accept-confirmation').length > 0 ) {
			$('.gdpr-overlay').fadeIn();
			$('body').addClass('gdpr-noscroll');
			$('.gdpr.gdpr-accept-confirmation .gdpr-wrapper').css({
				'display': 'flex',
			}).hide().fadeIn();
			$(document).on('click', '.gdpr.gdpr-accept-confirmation button.gdpr-ok', function() {
				$('.gdpr-overlay').fadeOut();
				$('body').removeClass('gdpr-noscroll');
				$('.gdpr.gdpr-accept-confirmation .gdpr-wrapper').fadeOut();
			});
		}

		$(document).on('click', '.gdpr-agree', function(e) {
			e.preventDefault();
			var that = $(this);
			$('.gdpr-consent-buttons').fadeOut(300, function() {
				$('.gdpr-consent-loading').fadeIn(300);
			});
			var dotCount = 0;
			var dotsAnimation = setInterval(function() {
				var dots = $('.gdpr-ellipsis').html();
				if ( dotCount < 3 ) {
					$('.gdpr-ellipsis').append('.');
					dotCount++;
				} else {
					$('.gdpr-ellipsis').html('');
					dotCount = 0;
				}
			}, 600);
			$.post(
				GDPR.ajaxurl,
				{
					action: 'agree_with_terms',
					nonce: $(this).data('nonce'),
				},
				function(res) {
					if ( res.success ) {
						$('.gdpr-reconsent-modal').fadeOut(300, function(){
							$(this).remove();
							$('.wpadminbar').show();
						});
						$('body').removeClass('gdpr-noscroll');
					}
				}
			);
		});

		$(document).on('click', '.gdpr-disagree', function(e) {
			$('.gdpr-overlay').fadeIn();
			$('body').addClass('gdpr-noscroll');
			$('.gdpr.gdpr-disagree-confirmation .gdpr-wrapper').css({
				'display': 'flex',
			}).hide().fadeIn();
		});

		$(document).on('click', '.gdpr-disagree-confirm', function(e) {
			e.preventDefault();
			$('.gdpr-overlay').fadeOut();
			$('.gdpr.gdpr-disagree-confirmation .gdpr-wrapper').fadeOut();
			$('.gdpr-consent-buttons').fadeOut(300, function() {
				$('.gdpr-updating').html(
					GDPR.aborting
				);
				$('.gdpr-consent-loading').fadeIn(300);
			});
			var dotCount = 0;
			var dotsAnimation = setInterval(function() {
				var dots = $('.gdpr-ellipsis').html();
				if ( dotCount < 3 ) {
					$('.gdpr-ellipsis').append('.');
					dotCount++;
				} else {
					$('.gdpr-ellipsis').html('');
					dotCount = 0;
				}
			}, 600);
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
