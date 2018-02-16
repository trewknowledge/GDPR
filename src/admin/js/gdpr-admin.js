(function( $ ) {
	'use strict';

	$(function() {
		$(document).on('click', '.gdpr-options .notice-dismiss', function() {
			$(this).parent().parent().remove();
		});

		$('.add-tab').click(function(e) {
			e.preventDefault();
			var field = $('#cookie-tabs');
			if ( field.val() === '' ) {
				return;
			}
			var tabID = field.val().toLowerCase().replace(/ /g, '-');
			var tabName = field.val();
			var tabTemplate = `<div class="postbox" id="cookie-tab-content-${tabID}">
				<h2 class="hndle">${tabName}<button class="notice-dismiss" type="button"><span class="screen-reader-text">Remove this tab.</span></button></h2>
				<input type="hidden" name="gdpr_options[cookies][tabs][${tabID}][name]" value="${tabName}" />
				<div class="inside">
					<table class="form-table">
						<tr>
							<th><label for="always-active-${tabID}">Always active</label></th>
							<td>
								<label class="switch">
									<input type="checkbox" name="gdpr_options[cookies][tabs][${tabID}][always_active]" id="always-active-${tabID}">
									<span class="slider round"></span>
								</label>
							</td>
						</tr>
						<tr>
							<th><label for="tab-how-we-use-${tabID}">How we use</label></th>
							<td><textarea name="gdpr_options[cookies][tabs][${tabID}][how_we_use]" id="tab-how-we-use-${tabID}" cols="53" rows="5"></textarea></td>
						</tr>
						<tr>
							<th><label for="cookies-used-${tabID}">Cookies used by the site</label></th>
							<td>
								<input type="text" name="gdpr_options[cookies][tabs][${tabID}][cookies_used]" id="cookies-used-${tabID}" class="regular-text" />
								<br>
								<span class="description">Comma separated list.</span>
							</td>
						</tr>
						<tr>
							<th><label for="hosts-${tabID}">Hosts</label></th>
							<td>
								<input type="text" id="hosts-${tabID}" class="regular-text" />
								<button class="button button-primary add-host" data-tabid="${tabID}">Add</button>
								<br>
								<span class="description">3rd party cookie hosts.</span>
							</td>
						</tr>
					</table>
					<div class="tab-hosts" data-tabid="${tabID}">

					</div>
				</div><!-- .inside -->
			</div><!-- .postbox -->`;
			var el = $('#tabs').append(tabTemplate);
			field.val('');
		});


		$(document).on('click', '.add-host', function(e) {
			e.preventDefault();
			var field = $(this).siblings('input');
			if ( field.val() === '' ) {
				return;
			}
			var tabID = $(this).data('tabid');
			var hostID = field.val().toLowerCase().replace(' ', '-');
			var hostsTemplate = `<div class="postbox">
					<h2 class="hndle">${hostID}<button class="notice-dismiss" type="button"><span class="screen-reader-text">Remove this host.</span></button></h2>
					<input type="hidden" name="gdpr_options[cookies][tabs][${tabID}][hosts][${hostID}][name]" value="${hostID}" />
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="hosts-cookies-used-${hostID}">Cookies used</label></th>
								<td>
									<input type="text" name="gdpr_options[cookies][tabs][${tabID}][hosts][${hostID}][cookies_used]" id="hosts-cookies-used-${hostID}" class="regular-text" />
									<br>
									<span class="description">Comma separated list.</span>
								</td>
							</tr>
						</table>
					</div>
				</div>`;

			var el = $('.tab-hosts[data-tabid="'+tabID+'"]').append(hostsTemplate);
			field.val('');
		});


	});
})( jQuery );
