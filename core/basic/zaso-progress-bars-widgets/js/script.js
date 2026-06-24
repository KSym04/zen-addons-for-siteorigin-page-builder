/**
 * ZASO Progress Bars widget.
 *
 * Each bar's fill renders at its target width as the default (no-JS / reduced-motion)
 * state. When animation is enabled, the fill is reset to zero and transitions up to
 * its target the first time it scrolls into view. Degrades gracefully when
 * IntersectionObserver is unavailable.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since   1.5.0
 */
( function () {
	'use strict';

	/**
	 * Animate a single root's fills from zero to their target widths.
	 *
	 * @param {HTMLElement} root The .zaso-progress-bars element.
	 * @return {void}
	 */
	function animate( root ) {
		var fills = root.querySelectorAll( '.zaso-progress-bars__fill' );
		Array.prototype.forEach.call( fills, function ( fill ) {
			var target = fill.getAttribute( 'data-percentage' ) || '0';
			// Force a reflow between the reset and the target so the transition runs.
			fill.style.width = '0%';
			// eslint-disable-next-line no-unused-expressions
			fill.offsetWidth;
			fill.style.width = target + '%';
		} );
	}

	/**
	 * Set the per-root transition duration from its data-duration attribute.
	 *
	 * @param {HTMLElement} root The .zaso-progress-bars element.
	 * @return {void}
	 */
	function applyDuration( root ) {
		var duration = parseInt( root.getAttribute( 'data-duration' ), 10 );
		if ( ! duration || duration < 0 ) {
			return;
		}
		var fills = root.querySelectorAll( '.zaso-progress-bars__fill' );
		Array.prototype.forEach.call( fills, function ( fill ) {
			fill.style.transitionDuration = duration + 'ms';
		} );
	}

	/**
	 * Initialize every progress-bars widget on the page.
	 *
	 * @return {void}
	 */
	function init() {
		var roots = document.querySelectorAll( '.zaso-progress-bars' );
		if ( ! roots.length ) {
			return;
		}

		var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		Array.prototype.forEach.call( roots, function ( root ) {
			var animateOn = '1' === root.getAttribute( 'data-animate' );

			// Animation off or reduced motion: leave the pre-rendered target widths.
			if ( ! animateOn || reduceMotion ) {
				return;
			}

			applyDuration( root );

			// No IntersectionObserver: reset and animate immediately.
			if ( ! ( 'IntersectionObserver' in window ) ) {
				animate( root );
				return;
			}

			// Reset to zero up front so the fill grows in once visible.
			var fills = root.querySelectorAll( '.zaso-progress-bars__fill' );
			Array.prototype.forEach.call( fills, function ( fill ) {
				fill.style.width = '0%';
			} );

			var observer = new IntersectionObserver( function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						animate( entry.target );
						obs.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.25 } );

			observer.observe( root );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
