import $ from 'jquery';

export const stringToSlug = function( str ) {
	str = str.replace( /^\s+|\s+$/g, '' ); // trim
	str = str.toLowerCase();

	// remove accents, swap ñ for n, etc
	const from = 'àáäâèéëêìíïîòóöôùúüûñňçčľĺšťžýďąćęłńóśźż·/_,:;';
	const to   = 'aaaaeeeeiiiioooouuuunnccllstzydacelnoszz------';
	for ( let i = 0, l = from.length ; i < l ; i++ ) {
		str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
	}

	str = str.replace( /[^a-z0-9 -]/g, '' ) // remove invalid chars
		.replace( /\s+/g, '-' ) // collapse whitespace and replace by -
		.replace( /-+/g, '-' ); // collapse dashes

	return str;
};

export const displayNotification = function( title, text, actions, hideCloseButton ) {
	hideCloseButton = 'undefined' !== typeof hideCloseButton ? true : false;
	actions = 'undefined' !== typeof actions ? actions : [
		{
			title: GDPR.i18n.ok,
			buttonClass: 'gdpr-ok',
			callback: 'closeNotification'
		}
	];
	const scrollDistance = $( window ).scrollTop();

	$( '.gdpr-general-confirmation .gdpr-box-title h3' ).html( title );
	$( '.gdpr-general-confirmation .gdpr-content p' ).html( text );
	$( '.gdpr-general-confirmation .gdpr-close' ).show();
	if ( hideCloseButton ) {
		$( '.gdpr-general-confirmation .gdpr-close' ).hide();
	}

	let html = '';
	actions.forEach( function( index ) {
		html += '<button class="' + index.buttonClass + '" data-callback="' + index.callback + '">' + index.title + '</button>';
	} );

	$( '.gdpr-general-confirmation footer' ).html( html );

	$( '.gdpr-overlay' ).fadeIn( 400, function() {
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).css( {
			'display': 'flex'
		} ).hide().fadeIn();
		$( 'body' ).addClass( 'gdpr-noscroll' ).css( 'top', -scrollDistance );
	} );
};

export const gdprFunctions = {
	closeNotification: function() {
		var scrollDistance = $( 'body' ).css( 'top' );
		$( '.gdpr-overlay' ).fadeOut();
		$( 'body' ).removeClass( 'gdpr-noscroll' );
		$( window ).scrollTop( Math.abs( parseInt( scrollDistance, 10 ) ) );
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).fadeOut();
	},
	addToDeletionConfirmed: function() {
		$( 'form.gdpr-add-to-deletion-requests' ).addClass( 'confirmed' );
		$( 'form.gdpr-add-to-deletion-requests.confirmed input[type="submit"]' ).click();
		gdprFunctions.closeNotification();
	},
	policyDisagreeOk: function() {
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper header .gdpr-box-title h3' ).html( GDPR.i18n.aborting );
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper .gdpr-content p' ).html( GDPR.i18n.logging_out );
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper footer button' ).hide();
		window.location.href = GDPR.logouturl;
	},
	policyDisagreeCancel: function() {
		$( '.gdpr.gdpr-general-confirmation .gdpr-wrapper' ).fadeOut();
		$( '.gdpr.gdpr-reconsent .gdpr-wrapper' ).fadeIn();
	}
};
