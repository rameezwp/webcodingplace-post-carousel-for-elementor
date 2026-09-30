/**
 * WebCodingPlace Post Carousel: modern engine (Swiper).
 *
 * No jQuery. Uses the Swiper library that ships with Elementor. Markup and
 * class names mirror the classic engine (slick-prev, slick-next, slick-dots,
 * slick-active) so every template and arrow or dot style works unchanged.
 */
( function () {
	'use strict';

	var reducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function parseOptions( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-dpce-swiper' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	/**
	 * Get the Swiper constructor, loading it through Elementor when needed.
	 *
	 * @return {Promise<Function|null>} Swiper class.
	 */
	function getSwiperClass() {
		if ( 'function' === typeof window.Swiper ) {
			return Promise.resolve( window.Swiper );
		}
		var frontend = window.elementorFrontend;
		if ( frontend && frontend.utils && frontend.utils.assetsLoader ) {
			return frontend.utils.assetsLoader.load( 'script', 'swiper' ).then( function () {
				return 'function' === typeof window.Swiper ? window.Swiper : null;
			} );
		}
		return Promise.resolve( null );
	}

	/**
	 * Duplicated loop slides are copies: hide them from assistive technology
	 * and take their links out of the tab order.
	 *
	 * @param {Object} swiper Swiper instance.
	 */
	function markDuplicates( swiper ) {
		Array.prototype.forEach.call( swiper.slides, function ( slide ) {
			var duplicate = slide.classList.contains( 'swiper-slide-duplicate' );
			if ( duplicate ) {
				slide.setAttribute( 'aria-hidden', 'true' );
				slide.setAttribute( 'inert', '' );
			}
		} );
	}

	/**
	 * Keep the classic engine's "current slide" class so template CSS that
	 * relies on it keeps working.
	 *
	 * @param {Object} swiper Swiper instance.
	 */
	function markCurrent( swiper ) {
		Array.prototype.forEach.call( swiper.slides, function ( slide, index ) {
			slide.classList.toggle( 'slick-current', index === swiper.activeIndex );
		} );
	}

	/**
	 * Number of "pages" and the current page, the same way dots count them.
	 *
	 * @param {Object} swiper Swiper instance.
	 * @return {{pages: number, active: number}} Page info.
	 */
	function pageInfo( swiper ) {
		var perGroup = Math.max( 1, swiper.params.slidesPerGroup || 1 );
		var realCount = Array.prototype.filter.call( swiper.slides, function ( slide ) {
			return ! slide.classList.contains( 'swiper-slide-duplicate' );
		} ).length;
		var pages = swiper.params.loop ? Math.ceil( realCount / perGroup ) : swiper.snapGrid.length;
		var active = swiper.params.loop ? Math.floor( swiper.realIndex / perGroup ) : swiper.snapIndex;
		return { pages: Math.max( 1, pages ), active: Math.min( active, Math.max( 0, pages - 1 ) ) };
	}

	/**
	 * Numbers ("2 / 8") and progress bar pagination.
	 *
	 * @param {Object}      swiper Swiper instance.
	 * @param {HTMLElement} el     Carousel element.
	 */
	function updateAltPagination( swiper, el ) {
		var info = pageInfo( swiper );
		var fraction = el.querySelector( '.dpce-fraction' );
		var progress = el.querySelector( '.dpce-progress span' );
		if ( fraction ) {
			fraction.textContent = ( info.active + 1 ) + ' / ' + info.pages;
		}
		if ( progress ) {
			progress.style.transform = 'scaleX(' + ( ( info.active + 1 ) / info.pages ) + ')';
		}
	}

	/**
	 * Freeze a news ticker exactly where it is.
	 *
	 * @param {Object} swiper Swiper instance.
	 */
	function freezeTicker( swiper ) {
		var style = window.getComputedStyle( swiper.wrapperEl ).transform;
		swiper.autoplay.stop();
		if ( style && 'none' !== style && window.DOMMatrixReadOnly ) {
			var matrix = new window.DOMMatrixReadOnly( style );
			swiper.setTransition( 0 );
			swiper.setTranslate( swiper.isHorizontal() ? matrix.m41 : matrix.m42 );
		}
		// The interrupted transition never fires its end event, so tell Swiper
		// it is no longer animating; otherwise a looped carousel will not move again.
		swiper.animating = false;
	}

	/**
	 * Dots: one per "page", using the classic engine's markup.
	 *
	 * @param {Object}      swiper  Swiper instance.
	 * @param {HTMLElement} list    The <ul class="slick-dots"> element.
	 * @param {Object}      options Carousel options.
	 */
	function buildDots( swiper, list, options ) {
		var perGroup = Math.max( 1, swiper.params.slidesPerGroup || 1 );
		var realCount = Array.prototype.filter.call( swiper.slides, function ( slide ) {
			return ! slide.classList.contains( 'swiper-slide-duplicate' );
		} ).length;
		var pages = swiper.params.loop ? Math.ceil( realCount / perGroup ) : swiper.snapGrid.length;

		if ( pages <= 1 ) {
			list.innerHTML = '';
			list.hidden = true;
			return;
		}
		list.hidden = false;

		if ( list.children.length !== pages ) {
			list.innerHTML = '';
			for ( var i = 0; i < pages; i++ ) {
				var li = document.createElement( 'li' );
				var button = document.createElement( 'button' );
				button.type = 'button';
				button.textContent = String( i + 1 );
				button.setAttribute( 'aria-label', ( options.i18n.goToSlide || 'Go to slide %d' ).replace( '%d', String( i + 1 ) ) );
				button.setAttribute( 'data-page', String( i ) );
				li.appendChild( button );
				list.appendChild( li );
			}
		}

		var active = swiper.params.loop ? Math.floor( swiper.realIndex / perGroup ) : swiper.snapIndex;
		Array.prototype.forEach.call( list.children, function ( li, index ) {
			var isActive = index === active;
			li.classList.toggle( 'slick-active', isActive );
			li.firstChild.setAttribute( 'aria-current', isActive ? 'true' : 'false' );
		} );
	}

	function init( el ) {
		if ( ! el || el.dpceSwiper || el.dpceSwiperPending ) {
			return;
		}
		var container = el.querySelector( '.dpce-swiper' );
		if ( ! container ) {
			return;
		}
		el.dpceSwiperPending = true;

		var options = parseOptions( el );
		options.i18n = options.i18n || {};
		var config = options.swiper || {};
		var prev = el.querySelector( '.slick-prev' );
		var next = el.querySelector( '.slick-next' );
		var dots = el.querySelector( '.slick-dots' );
		var pause = el.querySelector( '.dpce-pause' );
		var userPaused = false;

		if ( reducedMotion ) {
			config.autoplay = false;
		}

		if ( prev && next ) {
			config.navigation = { prevEl: prev, nextEl: next, disabledClass: 'slick-disabled' };
		}

		config.a11y = {
			enabled: true,
			prevSlideMessage: options.i18n.prev,
			nextSlideMessage: options.i18n.next,
			firstSlideMessage: options.i18n.first,
			lastSlideMessage: options.i18n.last,
			slideLabelMessage: options.i18n.slideLabel,
		};
		config.keyboard = { enabled: true, onlyInViewport: true };
		config.watchSlidesProgress = true;

		// Vertical mode needs a fixed height: fit the tallest slide.
		if ( 'vertical' === config.direction ) {
			var tallest = 0;
			container.querySelectorAll( '.swiper-slide' ).forEach( function ( slide ) {
				tallest = Math.max( tallest, slide.offsetHeight );
			} );
			var perView = Math.max( 1, parseInt( config.slidesPerView, 10 ) || 1 );
			container.style.height = ( tallest * perView ) + 'px';
		}

		getSwiperClass().then( function ( SwiperClass ) {
			el.dpceSwiperPending = false;
			if ( ! SwiperClass ) {
				return;
			}

			var swiper = new SwiperClass( container, config );
			el.dpceSwiper = swiper;

			var refresh = function () {
				markDuplicates( swiper );
				markCurrent( swiper );
				if ( dots ) {
					buildDots( swiper, dots, options );
				}
				updateAltPagination( swiper, el );
			};
			refresh();
			swiper.on( 'slideChange', refresh );
			swiper.on( 'resize', refresh );
			swiper.on( 'breakpoint', refresh );

			if ( dots ) {
				dots.addEventListener( 'click', function ( event ) {
					var button = event.target.closest( 'button[data-page]' );
					if ( ! button ) {
						return;
					}
					var page = parseInt( button.getAttribute( 'data-page' ), 10 );
					var perGroup = Math.max( 1, swiper.params.slidesPerGroup || 1 );
					if ( swiper.params.loop ) {
						swiper.slideToLoop( page * perGroup );
					} else {
						swiper.slideTo( Math.min( page * perGroup, swiper.slides.length - 1 ) );
					}
				} );
			}

			if ( ! swiper.autoplay || ! config.autoplay ) {
				if ( pause ) {
					pause.hidden = true;
				}
				return;
			}

			var isTicker = el.classList.contains( 'dpce-ticker' );
			var stop = function () {
				if ( isTicker ) {
					freezeTicker( swiper );
				} else {
					swiper.autoplay.stop();
				}
			};
			var start = function () {
				if ( ! userPaused ) {
					swiper.autoplay.start();
				}
			};

			if ( options.pauseOnHover ) {
				el.addEventListener( 'mouseenter', stop );
				el.addEventListener( 'mouseleave', start );
			}
			// Always pause while keyboard focus is inside the carousel.
			el.addEventListener( 'focusin', stop );
			el.addEventListener( 'focusout', function ( event ) {
				if ( ! el.contains( event.relatedTarget ) ) {
					start();
				}
			} );

			if ( pause ) {
				pause.addEventListener( 'click', function () {
					userPaused = ! userPaused;
					pause.setAttribute( 'aria-pressed', userPaused ? 'true' : 'false' );
					pause.setAttribute( 'aria-label', userPaused ? options.i18n.play : options.i18n.pause );
					pause.classList.toggle( 'is-paused', userPaused );
					if ( userPaused ) {
						stop();
					} else {
						swiper.autoplay.start();
					}
				} );
			}
		} );
	}

	function initAll( root ) {
		( root || document ).querySelectorAll( '.dpce-engine-swiper' ).forEach( init );
	}

	window.addEventListener( 'elementor/frontend/init', function () {
		var frontend = window.elementorFrontend;
		if ( frontend && frontend.hooks ) {
			frontend.hooks.addAction( 'frontend/element_ready/dpce_post_carousel.default', function ( $scope ) {
				var scope = $scope && $scope[ 0 ] ? $scope[ 0 ] : $scope;
				if ( scope && scope.querySelectorAll ) {
					initAll( scope );
				}
			} );
		}
	} );

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initAll();
		} );
	} else {
		initAll();
	}
}() );
