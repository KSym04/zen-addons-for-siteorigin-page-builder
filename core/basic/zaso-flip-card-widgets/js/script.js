/* [ZASO] Flip Card - Main JS (vanilla, no dependencies)
 *
 * Hover and keyboard focus already flip the card via CSS (:hover / :focus-within).
 * This script is a progressive enhancement for touch / pointer users: tapping the
 * card toggles an `.is-flipped` class so the back content is reachable without hover.
 */
( function () {
	'use strict';

	function toggle( card ) {
		card.classList.toggle( 'is-flipped' );
	}

	function ready() {
		var cards = document.querySelectorAll( '.zaso-flip-card' );

		Array.prototype.forEach.call( cards, function ( card ) {
			var inner = card.querySelector( '.zaso-flip-card__inner' );
			if ( ! inner ) {
				return;
			}

			// Pointer / touch: tap anywhere on the card flips it, except on the
			// real call-to-action link, which handles its own activation.
			inner.addEventListener( 'click', function ( e ) {
				if ( e.target.closest && e.target.closest( '.zaso-flip-card__button' ) ) {
					return;
				}
				toggle( card );
			} );

			// Keyboard: Enter / Space on the focusable inner flips it. Escape flips back.
			inner.addEventListener( 'keydown', function ( e ) {
				if ( 'Enter' === e.key || ' ' === e.key || 'Spacebar' === e.key ) {
					if ( e.target === inner ) {
						e.preventDefault();
						toggle( card );
					}
				} else if ( 'Escape' === e.key ) {
					card.classList.remove( 'is-flipped' );
				}
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', ready );
	} else {
		ready();
	}
} )();
