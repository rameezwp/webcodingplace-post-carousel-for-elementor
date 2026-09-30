/**
 * WebCodingPlace Post Carousel: classic engine (Slick).
 *
 * Used by carousels created before 2.0. Reads Slick options from the
 * data-slick attribute of the carousel wrapper and starts Slick on its track.
 */
( function ( $ ) {
	'use strict';

	var reducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// Slick only inserts custom arrows when they are given as an HTML string.
	var arrowButton = function ( className, label ) {
		return $( '<button type="button"></button>' )
			.addClass( className )
			.attr( 'aria-label', label )
			.text( label )
			.prop( 'outerHTML' );
	};

	var initCarousel = function ( $scope ) {
		var $carousel = $scope.find( '.dpce-carousel' ).first();
		if ( ! $carousel.length ) {
			$carousel = $scope.is( '.dpce-carousel' ) ? $scope : $();
		}
		if ( ! $carousel.length || $carousel.hasClass( 'dpce-engine-swiper' ) || typeof $.fn.slick !== 'function' ) {
			return;
		}

		var $track = $carousel.find( '.dpce-track' ).first();
		// Already running: the front end fires both DOM ready and Elementor's
		// element ready, so only start once.
		if ( ! $track.length || $track.hasClass( 'slick-initialized' ) ) {
			return;
		}

		var options = {};
		try {
			options = JSON.parse( $carousel.attr( 'data-slick' ) || '{}' );
		} catch ( e ) {
			options = {};
		}

		var i18n = options.i18n || {};
		delete options.i18n;
		if ( i18n.prev && i18n.next ) {
			options.prevArrow = arrowButton( 'slick-prev', i18n.prev );
			options.nextArrow = arrowButton( 'slick-next', i18n.next );
		}

		if ( reducedMotion ) {
			options.autoplay = false;
		}

		// Cloned slides are hidden copies: keep their links out of the tab order.
		$track.on( 'init reInit setPosition', function () {
			$track.find( '.slick-cloned' ).find( 'a, button, input, select, textarea, [tabindex]' ).attr( 'tabindex', '-1' );
		} );

		$track.slick( options );
	};

	$( window ).on( 'elementor/frontend/init', function () {
		if ( typeof elementorFrontend === 'undefined' ) {
			return;
		}
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/dpce_post_carousel.default',
			initCarousel
		);
	} );

	// Also start on plain front end pages (for example when Elementor's
	// frontend script is not loaded).
	$( function () {
		$( '.dpce-carousel' ).each( function () {
			initCarousel( $( this ) );
		} );
	} );
}( jQuery ) );
