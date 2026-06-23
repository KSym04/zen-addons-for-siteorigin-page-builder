/**
 * ZASO Counter widget.
 *
 * Animates a number from a start value up to an end value when the element
 * scrolls into view. Respects the user's reduced-motion preference and
 * degrades gracefully when IntersectionObserver is unavailable.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since   1.3.0
 */
( function () {
	'use strict';

	/**
	 * Format a number with a fixed number of decimals and a thousands separator.
	 *
	 * @param {number} value     The value to format.
	 * @param {number} decimals  Decimal places to keep.
	 * @param {string} separator Thousands separator ('' for none).
	 * @return {string} The formatted number.
	 */
	function formatNumber( value, decimals, separator ) {
		var parts = value.toFixed( decimals ).split( '.' );
		parts[ 0 ] = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, separator );
		return parts.join( '.' );
	}

	/**
	 * Ease-out-quad timing function.
	 *
	 * @param {number} t Progress from 0 to 1.
	 * @return {number} Eased progress from 0 to 1.
	 */
	function easeOutQuad( t ) {
		return t * ( 2 - t );
	}

	/**
	 * Run the count-up animation on a single value element.
	 *
	 * @param {HTMLElement} el The .zaso-counter__value element.
	 * @return {void}
	 */
	function animate( el ) {
		var start     = parseFloat( el.getAttribute( 'data-start' ) ) || 0;
		var end       = parseFloat( el.getAttribute( 'data-end' ) ) || 0;
		var duration  = parseInt( el.getAttribute( 'data-duration' ), 10 ) || 0;
		var decimals  = parseInt( el.getAttribute( 'data-decimals' ), 10 ) || 0;
		var separator = el.getAttribute( 'data-separator' ) || '';

		// No motion needed: just show the final formatted value.
		if ( duration <= 0 ) {
			el.textContent = formatNumber( end, decimals, separator );
			return;
		}

		var startTime = null;

		/**
		 * Per-frame step.
		 *
		 * @param {number} now Timestamp from requestAnimationFrame.
		 * @return {void}
		 */
		function step( now ) {
			if ( null === startTime ) {
				startTime = now;
			}
			var progress = Math.min( ( now - startTime ) / duration, 1 );
			var current  = start + ( end - start ) * easeOutQuad( progress );
			el.textContent = formatNumber( current, decimals, separator );

			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			} else {
				el.textContent = formatNumber( end, decimals, separator );
			}
		}

		// Begin from the start value, then animate up.
		el.textContent = formatNumber( start, decimals, separator );
		window.requestAnimationFrame( step );
	}

	/**
	 * Initialize all counters on the page.
	 *
	 * @return {void}
	 */
	function init() {
		var counters = document.querySelectorAll( '.zaso-counter__value' );
		if ( ! counters.length ) {
			return;
		}

		var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		// Reduced motion: leave the pre-rendered final value in place.
		if ( reduceMotion ) {
			return;
		}

		// No IntersectionObserver: animate immediately.
		if ( ! ( 'IntersectionObserver' in window ) ) {
			Array.prototype.forEach.call( counters, function ( el ) {
				animate( el );
			} );
			return;
		}

		var observer = new IntersectionObserver( function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					animate( entry.target );
					obs.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.25 } );

		Array.prototype.forEach.call( counters, function ( el ) {
			observer.observe( el );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
