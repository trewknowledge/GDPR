(function( $ ) {
	'use strict';

	var query_args  = location.search,
			base_url = location.protocol + '//' + location.host + location.pathname;

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

	function displayNotification( title, text, deletion ) {
		deletion = typeof deletion !== 'undefined' ? true : false;

		$('.gdpr-general-confirmation .gdpr-box-title h3').html( title );
		$('.gdpr-general-confirmation .gdpr-content p').html( text );
		if ( deletion ) {
			$('.gdpr-general-confirmation').addClass('gdpr-delete-confirmation');
			$('.gdpr-general-confirmation footer').html('<button class="gdpr-delete-account">' + GDPR.i18n.continue + '</button> <button class="gdpr-cancel">' + GDPR.i18n.cancel + '</button>');
		} else {
			$('.gdpr-general-confirmation footer').html('<button class="gdpr-ok">' + GDPR.i18n.ok + '</button>');
		}

		$('.gdpr-overlay').fadeIn();
		$('body').addClass('gdpr-noscroll');
		$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').css({
			'display': 'flex',
		}).hide().fadeIn();
	}

	$(function() {

		if ( -1 !== query_args.indexOf( 'notify=1' ) ) {
			window.history.replaceState( {}, document.title, base_url );
			$('body').addClass('gdpr-notification');
		}

		$(document).on('submit', '.gdpr-privacy-preferences-frm', function(e) {
			e.preventDefault();
			var that = $(this);
			var formData = $(this).serialize();

			$.post(
				GDPR.ajaxurl,
				formData,
				function(response) {
					if ( response.success ) {
						Cookies.set('gdpr[privacy_bar]', 1, { expires: 365 });
						if ( GDPR.refresh ) {
							window.location.reload();
						} else {
							$('.gdpr-overlay').fadeOut();
							$('body').removeClass('gdpr-noscroll');
							$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeOut();
							$('.gdpr-privacy-bar').fadeOut();
						}
					} else {
						displayNotification( response.data.title, response.data.content );
					}
				}
			);

		});

		$(document).on('submit', '.gdpr-request-form', function(e) {
			e.preventDefault();
			var that = $(this);
			var type = $(this).find('input[name="type"]').val();
			var formData = $(this).serialize();

			$.post(
				GDPR.ajaxurl,
				formData,
				function(response) {
					displayNotification( response.data.title, response.data.content );
				}
			)
		});

		$(document).on('change', '.gdpr-cookie-category', function() {
			var target = $(this).data('category');
			var checked = $(this).prop('checked');
			$('[data-category="' + target + '"]').prop('checked', checked);
		});

		if ( ! Cookies.get('gdpr[privacy_bar]') ) {
			if ( $('.gdpr-reconsent-bar').length == 0 ) {
				$('.gdpr.gdpr-privacy-bar').delay(1000).slideDown(600);
			}
		};

		if ( $('.gdpr-reconsent-bar').length > 0 ) {
				$('.gdpr.gdpr-reconsent-bar').delay(1000).slideDown(600);
			}

		/**
		 * This runs when user clicks on privacy preferences bar agree button.
		 * It submits the form that is still hidden with the cookies and consent options.
		 */
		$(document).on('click', '.gdpr.gdpr-privacy-bar .gdpr-agreement', function() {
      $('.gdpr-privacy-preferences-frm').submit();
    });

    $(document).on('click', '.gdpr.gdpr-reconsent-bar .gdpr-agreement', function() {
    	var consents = [];
    	$('.gdpr-policy-list input[type="hidden"]').each(function(){
    		consents.push( $(this).val() );
    	});
    	$.post(
				GDPR.ajaxurl,
				{
					action: 'agree_with_new_policies',
					nonce: $(this).data('nonce'),
					consents: consents,
				},
				function(res) {
					if ( res.success ) {
						if ( GDPR.refresh ) {
							window.location.reload();
						} else {
							$('.gdpr-reconsent-bar').slideUp(600);
							if ( ! Cookies.get('gdpr[privacy_bar]') ) {
								$('.gdpr.gdpr-privacy-bar').delay(1000).slideDown(600);
							};
						}
					} else {
						displayNotification( res.data.title, res.data.content );
					}
				}
			);
    });

		/**
		 * Close the privacy/reconsent bar.
		 */
		$(document).on('click', '.gdpr.gdpr-privacy-bar .gdpr-close, .gdpr.gdpr-reconsent-bar .gdpr-close', function() {
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
			$('.gdpr.gdpr-privacy-bar, .gdpr.gdpr-reconsent-bar').slideUp(600);
		});

		/**
		 * Display the privacy preferences modal.
		 */
		$(document).on('click', '.gdpr-preferences', function(e) {
			e.preventDefault();
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

		$(document).on('click', '.gdpr.gdpr-delete-confirmation button.gdpr-delete-account', function() {
			$('form.gdpr-add-to-deletion-requests').addClass('confirmed');
			$('form.gdpr-add-to-deletion-requests.confirmed input[type="submit"]').click();
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
			$('.gdpr.gdpr-delete-confirmation .gdpr-wrapper').fadeOut();
		});

		if ( $('body').hasClass('gdpr-notification') ) {
			$('.gdpr-overlay').fadeIn();
			$('body').addClass('gdpr-noscroll');
			$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').css({
				'display': 'flex',
			}).hide().fadeIn();
		}

		$(document).on('click', '.gdpr.gdpr-general-confirmation button.gdpr-ok', function() {
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
			$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').fadeOut();
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
					GDPR.i18n.aborting
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
