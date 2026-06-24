/**
 * ZASO Testimonial Slider — vanilla JS carousel.
 *
 * No external library. Custom pointer-events swipe (same pattern as Before/After).
 * Auto-play pauses on hover/focus, respects prefers-reduced-motion.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.4.0
 */
( function () {
	'use strict';

	var SWIPE_THRESHOLD = 50; // px horizontal delta required to advance

	/**
	 * Return true if the user prefers reduced motion.
	 *
	 * @return {boolean}
	 */
	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '( prefers-reduced-motion: reduce )' ).matches;
	}

	/**
	 * Clamp a value between min and max.
	 *
	 * @param {number} value
	 * @param {number} min
	 * @param {number} max
	 * @return {number}
	 */
	function clamp( value, min, max ) {
		return Math.min( Math.max( value, min ), max );
	}

	/**
	 * Initialise a single slider root element.
	 *
	 * @param {HTMLElement} root
	 */
	function setup( root ) {
		var track    = root.querySelector( '.zaso-testimonial-slider__track' );
		var slides   = root.querySelectorAll( '.zaso-testimonial-slider__slide' );
		var prevBtn  = root.querySelector( '.zaso-testimonial-slider__arrow--prev' );
		var nextBtn  = root.querySelector( '.zaso-testimonial-slider__arrow--next' );
		var dots     = root.querySelectorAll( '.zaso-testimonial-slider__dot' );
		var viewport = root.querySelector( '.zaso-testimonial-slider__viewport' );

		var count    = parseInt( root.getAttribute( 'data-count' ), 10 ) || slides.length;
		var autoplay = '1' === root.getAttribute( 'data-autoplay' );
		var duration = parseInt( root.getAttribute( 'data-duration' ), 10 ) || 5000;
		var reduced  = prefersReducedMotion();

		if ( count <= 1 ) {
			return; // Nothing to slide.
		}

		var current   = 0;
		var timer     = null;
		var pointerStartX = 0;
		var pointerStartY = 0;
		var isDragging    = false;

		/**
		 * Move to a specific slide index.
		 *
		 * @param {number} index
		 */
		function goTo( index ) {
			var next = ( index + count ) % count;

			// Update track position.
			if ( reduced ) {
				track.style.transition = 'none';
			}
			track.style.transform = 'translateX(-' + ( next * 100 ) + '%)';

			// Update aria-hidden on slides.
			for ( var i = 0; i < slides.length; i++ ) {
				if ( i === next ) {
					slides[ i ].removeAttribute( 'aria-hidden' );
				} else {
					slides[ i ].setAttribute( 'aria-hidden', 'true' );
				}
			}

			// Update dots.
			for ( var d = 0; d < dots.length; d++ ) {
				var isActive = d === next;
				dots[ d ].setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				if ( isActive ) {
					dots[ d ].classList.add( 'zaso-testimonial-slider__dot--active' );
				} else {
					dots[ d ].classList.remove( 'zaso-testimonial-slider__dot--active' );
				}
			}

			current = next;
		}

		/** Advance to the next slide. */
		function next() {
			goTo( current + 1 );
		}

		/** Go back to the previous slide. */
		function prev() {
			goTo( current - 1 );
		}

		// ── Auto-play ────────────────────────────────────────────────────────

		/** Start the auto-play interval (no-op if reduced motion). */
		function startAutoplay() {
			if ( ! autoplay || reduced ) {
				return;
			}
			stopAutoplay();
			timer = setInterval( next, duration );
		}

		/** Clear the auto-play interval. */
		function stopAutoplay() {
			if ( timer ) {
				clearInterval( timer );
				timer = null;
			}
		}

		// ── Arrow buttons ────────────────────────────────────────────────────

		if ( prevBtn ) {
			prevBtn.removeAttribute( 'aria-hidden' );
			prevBtn.removeAttribute( 'tabindex' );
			prevBtn.addEventListener( 'click', function () {
				stopAutoplay();
				prev();
				startAutoplay();
			} );
		}

		if ( nextBtn ) {
			nextBtn.removeAttribute( 'aria-hidden' );
			nextBtn.removeAttribute( 'tabindex' );
			nextBtn.addEventListener( 'click', function () {
				stopAutoplay();
				next();
				startAutoplay();
			} );
		}

		// ── Dot pagination ───────────────────────────────────────────────────

		for ( var d = 0; d < dots.length; d++ ) {
			( function ( dot, idx ) {
				dot.addEventListener( 'click', function () {
					stopAutoplay();
					goTo( idx );
					startAutoplay();
				} );
			}( dots[ d ], d ) );
		}

		// ── Keyboard navigation ──────────────────────────────────────────────

		root.setAttribute( 'tabindex', '0' );
		root.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowLeft' || e.key === 'ArrowUp' ) {
				e.preventDefault();
				stopAutoplay();
				prev();
				startAutoplay();
			} else if ( e.key === 'ArrowRight' || e.key === 'ArrowDown' ) {
				e.preventDefault();
				stopAutoplay();
				next();
				startAutoplay();
			}
		} );

		// ── Pointer / swipe ──────────────────────────────────────────────────

		viewport.addEventListener( 'pointerdown', function ( e ) {
			pointerStartX = e.clientX;
			pointerStartY = e.clientY;
			isDragging    = true;
			viewport.setPointerCapture( e.pointerId );
			stopAutoplay();
		} );

		viewport.addEventListener( 'pointermove', function ( e ) {
			if ( ! isDragging ) {
				return;
			}
			// Suppress vertical scroll while horizontal drag is dominant.
			var dx = Math.abs( e.clientX - pointerStartX );
			var dy = Math.abs( e.clientY - pointerStartY );
			if ( dx > dy ) {
				e.preventDefault();
			}
		} );

		viewport.addEventListener( 'pointerup', function ( e ) {
			if ( ! isDragging ) {
				return;
			}
			isDragging = false;
			var delta  = e.clientX - pointerStartX;
			if ( Math.abs( delta ) >= SWIPE_THRESHOLD ) {
				if ( delta < 0 ) {
					next();
				} else {
					prev();
				}
			}
			startAutoplay();
		} );

		viewport.addEventListener( 'pointercancel', function () {
			isDragging = false;
			startAutoplay();
		} );

		// ── Pause on hover / focus ───────────────────────────────────────────

		root.addEventListener( 'mouseenter', stopAutoplay );
		root.addEventListener( 'mouseleave', startAutoplay );
		root.addEventListener( 'focusin',    stopAutoplay );
		root.addEventListener( 'focusout',   startAutoplay );

		// ── Boot ─────────────────────────────────────────────────────────────

		goTo( 0 );
		startAutoplay();
	}

	/**
	 * Boot all sliders on the page.
	 */
	function init() {
		var roots = document.querySelectorAll( '.zaso-testimonial-slider' );
		for ( var i = 0; i < roots.length; i++ ) {
			setup( roots[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

}() );
