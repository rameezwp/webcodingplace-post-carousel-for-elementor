/**
 * WebCodingPlace Post Carousel: admin screens.
 *
 * - Review notice: remembers the choice (and the close icon) without a page reload.
 * - Deactivation feedback: only when a feedback address is configured.
 */
( function () {
	'use strict';

	var config = window.dpceAdmin || {};

	var post = function ( data ) {
		var body = new window.FormData();
		Object.keys( data ).forEach( function ( key ) {
			body.append( key, data[ key ] );
		} );
		return window.fetch( config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } );
	};

	// Review notice.
	document.addEventListener( 'click', function ( event ) {
		var notice = event.target.closest( '.dpce-review-notice' );
		if ( ! notice ) {
			return;
		}
		var button = event.target.closest( '[data-dpce-review]' );
		var choice = button ? button.getAttribute( 'data-dpce-review' ) : ( event.target.closest( '.notice-dismiss' ) ? 'later' : '' );
		if ( ! choice ) {
			return;
		}
		post( { action: 'dpce_review', nonce: config.reviewNonce, choice: choice } );
		if ( 'review' !== choice ) {
			event.preventDefault();
		}
		notice.remove();
	} );

	// Deactivation feedback (only when a feedback address is configured).
	var setupFeedback = function () {
		if ( ! config.feedbackOn ) {
			return;
		}

		var modal = document.querySelector( '.dpce-feedback' );
		var row = document.querySelector( 'tr[data-plugin="' + config.pluginFile + '"]' );
		var link = row ? row.querySelector( '.deactivate a' ) : null;
		if ( ! modal || ! link ) {
			return;
		}

		var target = link.getAttribute( 'href' );
		var lastFocus = null;

		var close = function () {
			modal.hidden = true;
			if ( lastFocus ) {
				lastFocus.focus();
			}
		};

		var deactivate = function () {
			window.location.href = target;
		};

		link.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			lastFocus = link;
			modal.hidden = false;
			var first = modal.querySelector( 'input, textarea, button' );
			if ( first ) {
				first.focus();
			}
		} );

		modal.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				close();
			}
		} );

		modal.addEventListener( 'click', function ( event ) {
			if ( event.target === modal ) {
				close();
			}
			if ( event.target.closest( '[data-dpce-feedback="skip"]' ) ) {
				deactivate();
			}
		} );

		modal.querySelector( 'form' ).addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			var form = event.target;
			var reason = form.querySelector( 'input[name="reason"]:checked' );
			post( {
				action: 'dpce_deactivation_feedback',
				nonce: config.feedbackNonce,
				reason: reason ? reason.value : '',
				details: form.details.value,
			} ).then( deactivate, deactivate );
		} );
	};

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', setupFeedback );
	} else {
		setupFeedback();
	}
}() );
