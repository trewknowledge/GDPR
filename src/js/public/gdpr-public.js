(function( $ ) {
	'use strict';

	var query_args  = location.search,
			base_url = location.protocol + '//' + location.host + location.pathname;

	window.has_consent = function( consent ) {
		consent = consent.substring( 0, consent.indexOf('{') );
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

	function displayNotification( title, text, actions, hideCloseButton ) {
		hideCloseButton = typeof hideCloseButton !== 'undefined' ? true : false;
		actions = typeof actions !== 'undefined' ? actions : [
			{
				title: GDPR.i18n.ok,
				buttonClass: 'gdpr-ok',
				callback: 'closeNotification',
			}
		];
    var scrollDistance = $(window).scrollTop();

		$('.gdpr-general-confirmation .gdpr-box-title h3').html( title );
		$('.gdpr-general-confirmation .gdpr-content p').html( text );
		$('.gdpr-general-confirmation .gdpr-close').show();
		if ( hideCloseButton ) {
			$('.gdpr-general-confirmation .gdpr-close').hide();
		}

		var html = '';
		actions.forEach( function( index ) {
			html += '<button class="'+ index.buttonClass +'" data-callback="' + index.callback + '">' + index.title + '</button>';
		});

		$('.gdpr-general-confirmation footer').html(html);

		$('.gdpr-overlay').fadeIn(400, function() {
			$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').css({
				'display': 'flex',
			}).hide().fadeIn();
      $('body').addClass('gdpr-noscroll').css('top', -scrollDistance);
		});
	}

	$(function() {

		var gdprFunctions = {
			closeNotification: function() {
        var scrollDistance = $('body').css('top');
				$('.gdpr-overlay').fadeOut();
				$('body').removeClass('gdpr-noscroll');
        $(window).scrollTop(Math.abs( parseInt( scrollDistance, 10 ) ) );
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').fadeOut();
			},
			addToDeletionConfirmed: function() {
				$('form.gdpr-add-to-deletion-requests').addClass('confirmed');
				$('form.gdpr-add-to-deletion-requests.confirmed input[type="submit"]').click();
				gdprFunctions.closeNotification();
			},
			policyDisagreeOk: function() {
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper header .gdpr-box-title h3').html( GDPR.i18n.aborting );
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper .gdpr-content p').html( GDPR.i18n.logging_out );
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper footer button').hide();
				window.location.href = GDPR.logouturl;
			},
			policyDisagreeCancel: function() {
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').fadeOut();
				$('.gdpr.gdpr-reconsent .gdpr-wrapper').fadeIn();
			}
		}

		if ( -1 !== query_args.indexOf( 'notify=1' ) ) {
			window.history.replaceState( {}, document.title, base_url );
			$('body').addClass('gdpr-notification');
		}

		$(document).on('click', '.gdpr.gdpr-general-confirmation button', function(e) {
			var callback = $(this).data('callback');
			gdprFunctions[callback]();
		});

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
              var scrollDistance = $('body').css('top');
							$('.gdpr-overlay').fadeOut();
							$('body').removeClass('gdpr-noscroll');
							$(window).scrollTop(Math.abs( parseInt( scrollDistance, 10 ) ) );
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
			if ( $(this).hasClass('confirmed') ) {
				var formData = $(this).serialize();

				$.post(
					GDPR.ajaxurl,
					formData,
					function(response) {
						displayNotification( response.data.title, response.data.content );
					}
				)
			}
		});

		$(document).on('change', '.gdpr-cookie-category', function() {
			var target = $(this).data('category');
			var checked = $(this).prop('checked');
			$('[data-category="' + target + '"]').prop('checked', checked);
		});

		if ( ! Cookies.get('gdpr[privacy_bar]') ) {
			if ( $('.gdpr-reconsent-bar, .gdpr-reconsent').length == 0 ) {
				$('.gdpr.gdpr-privacy-bar').delay(1000).slideDown(600);
			}
		};

		if ( $('.gdpr-reconsent-bar').length > 0 ) {
			$('.gdpr.gdpr-reconsent-bar').delay(1000).slideDown(600);
		}

		if ( $('.gdpr-reconsent').length > 0 ) {
			$('.gdpr-overlay').fadeIn(400, function() {
				$('.gdpr.gdpr-reconsent .gdpr-wrapper').fadeIn();
				$('body').addClass('gdpr-noscroll').delay(1000);
			});
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

		$(document).on('submit', '.gdpr-reconsent-frm', function(e) {
			e.preventDefault();
			var consents = [];
			var nonce = $(this).find('#agree-with-new-policies-nonce').val();
			$(this).find('[name="gdpr-updated-policy"]').each(function(){
				consents.push( $(this).val() );
			});

			$.post(
				GDPR.ajaxurl,
				{
					action: 'agree_with_new_policies',
					nonce: nonce,
					consents: consents,
				},
				function(res) {
					if ( res.success ) {
						if ( GDPR.refresh ) {
							window.location.reload();
						} else {
              var scrollDistance = $('body').css('top');
							$('.gdpr-overlay').fadeOut();
							$('body').removeClass('gdpr-noscroll');
              $(window).scrollTop(Math.abs( parseInt( scrollDistance, 10 ) ) );
							$('.gdpr.gdpr-reconsent .gdpr-wrapper').fadeOut();
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
      var scrollDistance = $('body').css('top');
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
      $(window).scrollTop(Math.abs( parseInt( scrollDistance, 10 ) ) );
			$('.gdpr.gdpr-privacy-bar, .gdpr.gdpr-reconsent-bar').slideUp(600);
		});

		$(document).on('click', '.gdpr.gdpr-general-confirmation .gdpr-close', function() {
      var scrollDistance = $('body').css('top');
			$('.gdpr-overlay').fadeOut();
			$('body').removeClass('gdpr-noscroll');
      $(window).scrollTop(Math.abs( parseInt( scrollDistance, 10 ) ) );
			$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').fadeOut();
		});

		/**
		 * Display the privacy preferences modal.
		 */
		$(document).on('click', '.gdpr-preferences', function(e) {
			e.preventDefault();
			var scrollDistance = $(window).scrollTop();
			var tab = $(this).data('tab');
			$('.gdpr-overlay').fadeIn();
			$('body').addClass('gdpr-noscroll').css('top', -scrollDistance);
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeIn();
			if ( tab ) {
				$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper .gdpr-tabs [data-target="' + tab + '"]').click();
			}
		});

		/**
		 * Close the privacy/reconsent preferences modal.
		 */
		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-close', function(e) {
      e.preventDefault();
      var scrollDistance = $('body').css('top');
			if ( ! $('.gdpr-reconsent .gdpr-wrapper').is(':visible') ) {
				$('.gdpr-overlay').fadeOut();
				$('body').removeClass('gdpr-noscroll');
        $(window).scrollTop(Math.abs( parseInt( scrollDistance, 10 ) ) );
			}
			$('.gdpr.gdpr-privacy-preferences .gdpr-wrapper').fadeOut();
		});

		/**
		 * Tab navigation for the privacy preferences modal.
		 */
		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-tabs button, .gdpr.gdpr-reconsent .gdpr-tabs button', function() {
			var target = '.' + $(this).data('target');
			$('.gdpr.gdpr-privacy-preferences .gdpr-tab-content > div, .gdpr.gdpr-reconsent .gdpr-tab-content > div').removeClass('gdpr-active');
			$('.gdpr.gdpr-privacy-preferences .gdpr-tab-content ' + target + ', .gdpr.gdpr-reconsent .gdpr-tab-content ' + target).addClass('gdpr-active');

			if ( $('.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs').hasClass('gdpr-mobile-expanded') ) {
				$('.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button').removeClass('gdpr-active');
				$('.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs').toggle();
			}

			$('.gdpr.gdpr-privacy-preferences .gdpr-tabs button, .gdpr.gdpr-reconsent .gdpr-tabs button').removeClass('gdpr-active');
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
		$(document).on('click', '.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button', function(e) {
			$(this).toggleClass('gdpr-active');
			$('.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs').toggle().addClass('gdpr-mobile-expanded');
		});

		$(window).resize( function() {
			if ( $(window).width() > 640 && $('.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs').hasClass('gdpr-mobile-expanded') ) {
				$('.gdpr.gdpr-privacy-preferences .gdpr-mobile-menu button, .gdpr.gdpr-reconsent .gdpr-mobile-menu button').removeClass('gdpr-active');
				$('.gdpr.gdpr-privacy-preferences .gdpr-tabs, .gdpr.gdpr-reconsent .gdpr-tabs').removeClass('gdpr-mobile-expanded').removeAttr('style');
			}
		});

		$('form.gdpr-add-to-deletion-requests').on('submit', function(e) {
			if ( ! $(this).hasClass( 'confirmed' ) ) {
				e.preventDefault();
				var actions = [
					{
						title: GDPR.i18n.ok,
						buttonClass: 'gdpr-ok',
						callback: 'addToDeletionConfirmed',
					},
					{
						title: GDPR.i18n.cancel,
						buttonClass: 'gdpr-cancel',
						callback: 'closeNotification',
					}
				]
				displayNotification( GDPR.i18n.close_account, GDPR.i18n.close_account_warning, actions );
			}
		} );

		if ( $('body').hasClass('gdpr-notification') ) {
      var scrollDistance = $(window).scrollTop();
			$('.gdpr-overlay').fadeIn(400, function() {
				$('.gdpr.gdpr-general-confirmation .gdpr-wrapper').css({
					'display': 'flex',
				}).hide().fadeIn();
        $('body').addClass('gdpr-noscroll').css('top', -scrollDistance);
			});
		}

		$(document).on('click', '.gdpr-disagree a', function(e) {
			$('.gdpr.gdpr-reconsent .gdpr-wrapper').fadeOut();
			var actions = [
				{
					title: GDPR.i18n.ok,
					buttonClass: 'gdpr-ok',
					callback: 'policyDisagreeOk',
				},
				{
					title: GDPR.i18n.cancel,
					buttonClass: 'gdpr-cancel',
					callback: 'policyDisagreeCancel',
				}
			]
			displayNotification( GDPR.i18n.are_you_sure, GDPR.i18n.policy_disagree, actions, true );
		});
	});

})( jQuery );
