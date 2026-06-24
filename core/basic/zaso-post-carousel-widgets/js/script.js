/**
 * ZASO Post Carousel - dependency-free responsive carousel.
 *
 * Reads configuration from data-* attributes on each .zaso-post-carousel root,
 * lays out slides so the configured number are visible, and supports arrows,
 * dots, autoplay (pause on hover), touch swipe, responsive recompute, and
 * reduced-motion. Carousels with fewer slides than the visible count render
 * statically with no controls.
 */
( function() {
	'use strict';

	var REDUCED_MOTION = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/**
	 * Read the horizontal gap (px) applied to the track via CSS.
	 *
	 * @param {HTMLElement} track The flex track element.
	 * @return {number} Gap in pixels (fallback 24).
	 */
	function readGap( track ) {
		var style = window.getComputedStyle( track );
		var gap   = parseFloat( style.columnGap || style.gap );
		return isNaN( gap ) ? 24 : gap;
	}

	/**
	 * Resolve how many slides are visible at the current viewport width.
	 *
	 * @param {number} base Desired slides on desktop.
	 * @return {number} Slides visible now.
	 */
	function responsiveSlides( base ) {
		var w = window.innerWidth;
		if ( w <= 600 ) {
			return 1;
		}
		if ( w <= 980 ) {
			return Math.min( 2, base );
		}
		return base;
	}

	/**
	 * Construct a single carousel controller.
	 *
	 * @param {HTMLElement} root The .zaso-post-carousel element.
	 */
	function Carousel( root ) {
		this.root     = root;
		this.track    = root.querySelector( '.zaso-post-carousel__track' );
		this.slides   = this.track ? Array.prototype.slice.call( this.track.children ) : [];
		this.prevBtn  = root.querySelector( '.zaso-post-carousel__arrow--prev' );
		this.nextBtn  = root.querySelector( '.zaso-post-carousel__arrow--next' );
		this.dotsWrap = root.querySelector( '.zaso-post-carousel__dots' );

		this.baseSlides   = parseInt( root.getAttribute( 'data-slides' ), 10 ) || 3;
		this.useAutoplay  = '1' === root.getAttribute( 'data-autoplay' ) && ! REDUCED_MOTION;
		this.speed        = parseInt( root.getAttribute( 'data-speed' ), 10 ) || 4000;
		this.index        = 0;
		this.timer        = null;

		if ( ! this.track || this.slides.length === 0 ) {
			return;
		}

		this.bind();
		this.layout();
	}

	/**
	 * Wire up event listeners (arrows, autoplay hover, swipe, resize).
	 */
	Carousel.prototype.bind = function() {
		var self = this;

		if ( this.prevBtn ) {
			this.prevBtn.addEventListener( 'click', function() {
				self.go( self.index - 1 );
			} );
		}
		if ( this.nextBtn ) {
			this.nextBtn.addEventListener( 'click', function() {
				self.go( self.index + 1 );
			} );
		}

		this.root.addEventListener( 'mouseenter', function() {
			self.stopAutoplay();
		} );
		this.root.addEventListener( 'mouseleave', function() {
			self.startAutoplay();
		} );

		// Touch swipe.
		var startX = 0;
		var moved  = false;
		this.track.addEventListener( 'touchstart', function( e ) {
			startX = e.touches[0].clientX;
			moved  = false;
		}, { passive: true } );
		this.track.addEventListener( 'touchmove', function() {
			moved = true;
		}, { passive: true } );
		this.track.addEventListener( 'touchend', function( e ) {
			if ( ! moved ) {
				return;
			}
			var delta = e.changedTouches[0].clientX - startX;
			if ( Math.abs( delta ) > 40 ) {
				self.go( self.index + ( delta < 0 ? 1 : -1 ) );
			}
		} );

		var resizeTimer = null;
		window.addEventListener( 'resize', function() {
			window.clearTimeout( resizeTimer );
			resizeTimer = window.setTimeout( function() {
				self.layout();
			}, 150 );
		} );
	};

	/**
	 * Recompute slide widths, bounds, dots, and reposition.
	 */
	Carousel.prototype.layout = function() {
		this.visible  = responsiveSlides( this.baseSlides );
		this.gap      = readGap( this.track );
		this.maxIndex = Math.max( 0, this.slides.length - this.visible );

		var totalGap = this.gap * ( this.visible - 1 );
		var basis    = 'calc((100% - ' + totalGap + 'px) / ' + this.visible + ')';
		this.slides.forEach( function( slide ) {
			slide.style.flex = '0 0 ' + basis;
			slide.style.maxWidth = basis;
		} );

		if ( this.index > this.maxIndex ) {
			this.index = this.maxIndex;
		}

		this.buildDots();
		this.update();
		this.startAutoplay();
	};

	/**
	 * Build pagination dots for the current bounds.
	 */
	Carousel.prototype.buildDots = function() {
		if ( ! this.dotsWrap ) {
			return;
		}
		this.dotsWrap.innerHTML = '';
		this.dots = [];

		// No paging needed when everything fits.
		if ( this.maxIndex === 0 ) {
			return;
		}

		var self = this;
		for ( var i = 0; i <= this.maxIndex; i++ ) {
			( function( idx ) {
				var dot = document.createElement( 'button' );
				dot.type = 'button';
				dot.className = 'zaso-post-carousel__dot';
				dot.setAttribute( 'aria-label', 'Go to slide ' + ( idx + 1 ) );
				dot.addEventListener( 'click', function() {
					self.go( idx );
				} );
				self.dotsWrap.appendChild( dot );
				self.dots.push( dot );
			} )( i );
		}
	};

	/**
	 * Move to a target index (clamped to valid range).
	 *
	 * @param {number} target Desired index.
	 */
	Carousel.prototype.go = function( target ) {
		this.index = Math.min( this.maxIndex, Math.max( 0, target ) );
		this.update();
	};

	/**
	 * Apply the current index to the track transform and controls state.
	 */
	Carousel.prototype.update = function() {
		var slideWidth = this.slides[0].getBoundingClientRect().width;
		var offset     = this.index * ( slideWidth + this.gap );
		this.track.style.transform = 'translateX(' + ( -offset ) + 'px)';

		if ( this.prevBtn ) {
			this.prevBtn.disabled = ( this.index <= 0 );
		}
		if ( this.nextBtn ) {
			this.nextBtn.disabled = ( this.index >= this.maxIndex );
		}
		if ( this.dots ) {
			this.dots.forEach( function( dot, i ) {
				dot.classList.toggle( 'zaso-post-carousel__dot--active', i === this.index );
			}, this );
		}
	};

	/**
	 * Start autoplay if enabled and there is more than one page.
	 */
	Carousel.prototype.startAutoplay = function() {
		this.stopAutoplay();
		if ( ! this.useAutoplay || this.maxIndex === 0 ) {
			return;
		}
		var self = this;
		this.timer = window.setInterval( function() {
			var next = self.index >= self.maxIndex ? 0 : self.index + 1;
			self.go( next );
		}, this.speed );
	};

	/**
	 * Stop any running autoplay timer.
	 */
	Carousel.prototype.stopAutoplay = function() {
		if ( this.timer ) {
			window.clearInterval( this.timer );
			this.timer = null;
		}
	};

	/**
	 * Initialize every carousel on the page.
	 */
	function init() {
		var nodes = document.querySelectorAll( '.zaso-post-carousel' );
		Array.prototype.forEach.call( nodes, function( node ) {
			new Carousel( node );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
