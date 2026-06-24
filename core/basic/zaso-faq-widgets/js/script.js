( function () {
	/**
	 * Initialise accordion behaviour for a single FAQ root element.
	 *
	 * @param {Element} root - The .zaso-faq dl element.
	 */
	function initFAQ( root ) {
		root.querySelectorAll( '.zaso-faq__question' ).forEach( function ( dt ) {
			dt.addEventListener( 'click', function () {
				toggle( dt );
			} );
			dt.addEventListener( 'keydown', function ( e ) {
				if ( 'Enter' === e.key || ' ' === e.key ) {
					e.preventDefault();
					toggle( dt );
				}
			} );
		} );
	}

	/**
	 * Toggle the open state of an FAQ item, closing all others.
	 *
	 * @param {Element} dt - The clicked/activated question dt element.
	 */
	function toggle( dt ) {
		var item   = dt.parentElement;
		var dl     = item.parentElement;
		var isOpen = item.classList.contains( 'zaso-faq__item--open' );

		// Close all open items.
		dl.querySelectorAll( '.zaso-faq__item--open' ).forEach( function ( open ) {
			open.classList.remove( 'zaso-faq__item--open' );
			open.querySelector( '.zaso-faq__question' ).setAttribute( 'aria-expanded', 'false' );
		} );

		// Open this item if it was previously closed.
		if ( ! isOpen ) {
			item.classList.add( 'zaso-faq__item--open' );
			dt.setAttribute( 'aria-expanded', 'true' );
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.zaso-faq' ).forEach( initFAQ );
	} );
} )();
