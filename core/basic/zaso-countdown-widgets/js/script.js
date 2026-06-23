/**
 * ZASO Countdown widget.
 *
 * Counts down to a server-resolved deadline (a UTC millisecond timestamp set in
 * the markup). The browser clock is only used to measure the remaining interval,
 * never to decide the deadline itself.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since   1.3.0
 */
( function () {
	'use strict';

	var SECOND = 1000;
	var MINUTE = 60 * SECOND;
	var HOUR   = 60 * MINUTE;
	var DAY    = 24 * HOUR;

	/**
	 * Left-pad a number to at least two digits.
	 *
	 * @param {number} n The number.
	 * @return {string} The padded string.
	 */
	function pad( n ) {
		return n < 10 ? '0' + n : '' + n;
	}

	/**
	 * Wire up a single countdown element.
	 *
	 * @param {HTMLElement} el The .zaso-countdown element.
	 * @return {void}
	 */
	function setup( el ) {
		var deadline = parseInt( el.getAttribute( 'data-deadline' ), 10 ) || 0;
		var onExpire = el.getAttribute( 'data-on-expire' ) || 'hide';
		var unitsEl  = el.querySelector( '.zaso-countdown__units' );
		var msgEl    = el.querySelector( '.zaso-countdown__message' );
		var timer    = null;

		/**
		 * Apply the expiry behavior (hide the timer, or reveal the message).
		 *
		 * @return {void}
		 */
		function expire() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
			if ( 'message' === onExpire && msgEl ) {
				if ( unitsEl ) {
					unitsEl.style.display = 'none';
				}
				msgEl.style.display = '';
			} else {
				el.style.display = 'none';
			}
		}

		/**
		 * Update the displayed values for one tick.
		 *
		 * @return {void}
		 */
		function tick() {
			var remaining = deadline - Date.now();
			if ( remaining <= 0 ) {
				setValue( 'days', 0 );
				setValue( 'hours', 0 );
				setValue( 'minutes', 0 );
				setValue( 'seconds', 0 );
				expire();
				return;
			}
			setValue( 'days', Math.floor( remaining / DAY ) );
			setValue( 'hours', Math.floor( ( remaining % DAY ) / HOUR ) );
			setValue( 'minutes', Math.floor( ( remaining % HOUR ) / MINUTE ) );
			setValue( 'seconds', Math.floor( ( remaining % MINUTE ) / SECOND ) );
		}

		/**
		 * Write a value into a unit if that unit is displayed.
		 *
		 * @param {string} unit  The unit key (days/hours/minutes/seconds).
		 * @param {number} value The value to display.
		 * @return {void}
		 */
		function setValue( unit, value ) {
			var node = el.querySelector( '.zaso-countdown__unit[data-unit="' + unit + '"] .zaso-countdown__value' );
			if ( node ) {
				node.textContent = pad( value );
			}
		}

		// Already past the deadline (or no valid deadline): expire now.
		if ( deadline <= 0 || deadline - Date.now() <= 0 ) {
			expire();
			return;
		}

		tick();
		timer = window.setInterval( tick, SECOND );
	}

	/**
	 * Initialize every countdown on the page.
	 *
	 * @return {void}
	 */
	function init() {
		var nodes = document.querySelectorAll( '.zaso-countdown' );
		Array.prototype.forEach.call( nodes, function ( el ) {
			setup( el );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
