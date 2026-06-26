/* [ZASO] Social Share Bar - Main JS (vanilla, no dependencies)
 *
 * Handles the copy-link button only. Network links are plain anchors with
 * server-built share URLs and need no JavaScript.
 */
( function () {
	'use strict';

	function announce( button, message ) {
		var nav = button.closest ? button.closest( '.zaso-social-share' ) : null;
		var status = nav ? nav.querySelector( '.zaso-social-share__status' ) : null;
		if ( status ) {
			status.textContent = message;
		}
		button.classList.add( 'is-copied' );
		window.setTimeout( function () {
			button.classList.remove( 'is-copied' );
			if ( status ) {
				status.textContent = '';
			}
		}, 2000 );
	}

	function legacyCopy( text ) {
		var area = document.createElement( 'textarea' );
		area.value = text;
		area.setAttribute( 'readonly', '' );
		area.style.position = 'absolute';
		area.style.left = '-9999px';
		document.body.appendChild( area );
		area.select();
		var ok = false;
		try {
			ok = document.execCommand( 'copy' );
		} catch ( e ) {
			ok = false;
		}
		document.body.removeChild( area );
		return ok;
	}

	function copy( button ) {
		var url = button.getAttribute( 'data-zaso-share-url' );
		if ( ! url ) {
			return;
		}

		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( url ).then(
				function () {
					announce( button, 'Link copied to clipboard' );
				},
				function () {
					if ( legacyCopy( url ) ) {
						announce( button, 'Link copied to clipboard' );
					}
				}
			);
		} else if ( legacyCopy( url ) ) {
			announce( button, 'Link copied to clipboard' );
		}
	}

	function ready() {
		var buttons = document.querySelectorAll( '.zaso-social-share__btn--copy' );
		Array.prototype.forEach.call( buttons, function ( button ) {
			button.addEventListener( 'click', function () {
				copy( button );
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', ready );
	} else {
		ready();
	}
} )();
