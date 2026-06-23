/**
 * ZASO Before / After widget.
 *
 * A draggable image comparison slider. Supports mouse, touch, and pen via the
 * Pointer Events API, plus full keyboard control on the handle (arrow keys,
 * Home/End), exposed through the WAI-ARIA slider role.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since   1.3.0
 */
( function () {
	'use strict';

	/**
	 * Clamp a number to the 0-100 range.
	 *
	 * @param {number} value The value.
	 * @return {number} The clamped value.
	 */
	function clamp( value ) {
		return Math.max( 0, Math.min( 100, value ) );
	}

	/**
	 * Wire up a single comparison slider.
	 *
	 * @param {HTMLElement} container The .zaso-before-after__container element.
	 * @return {void}
	 */
	function setup( container ) {
		var handle      = container.querySelector( '.zaso-before-after__handle' );
		var beforeImg   = container.querySelector( '.zaso-before-after__before-img' );
		var orientation = container.getAttribute( 'data-orientation' ) || 'horizontal';
		var isVertical  = 'vertical' === orientation;
		var dragging    = false;

		if ( ! handle ) {
			return;
		}

		// Seed from the rendered aria-valuenow so JS and markup agree.
		var position = clamp( parseFloat( handle.getAttribute( 'aria-valuenow' ) ) || 50 );

		/**
		 * Apply a position to the reveal clip, the handle, and the ARIA value.
		 *
		 * @param {number} value The new position (0-100).
		 * @return {void}
		 */
		function setPosition( value ) {
			position = clamp( value );
			var reveal = 100 - position;
			if ( beforeImg ) {
				beforeImg.style.clipPath = isVertical
					? 'inset(0 0 ' + reveal + '% 0)'
					: 'inset(0 ' + reveal + '% 0 0)';
			}
			handle.style[ isVertical ? 'top' : 'left' ] = position + '%';
			handle.setAttribute( 'aria-valuenow', String( Math.round( position ) ) );
		}

		/**
		 * Convert a pointer event into a position along the slider axis.
		 *
		 * @param {PointerEvent} event The pointer event.
		 * @return {number} The position (0-100).
		 */
		function positionFromEvent( event ) {
			var rect = container.getBoundingClientRect();
			if ( isVertical ) {
				return ( ( event.clientY - rect.top ) / rect.height ) * 100;
			}
			return ( ( event.clientX - rect.left ) / rect.width ) * 100;
		}

		container.addEventListener( 'pointerdown', function ( event ) {
			dragging = true;
			if ( container.setPointerCapture ) {
				container.setPointerCapture( event.pointerId );
			}
			setPosition( positionFromEvent( event ) );
			event.preventDefault();
		} );

		container.addEventListener( 'pointermove', function ( event ) {
			if ( ! dragging ) {
				return;
			}
			setPosition( positionFromEvent( event ) );
		} );

		/**
		 * End a drag interaction.
		 *
		 * @return {void}
		 */
		function endDrag() {
			dragging = false;
		}

		container.addEventListener( 'pointerup', endDrag );
		container.addEventListener( 'pointercancel', endDrag );

		handle.addEventListener( 'keydown', function ( event ) {
			var step = event.shiftKey ? 10 : 1;
			var handled = true;

			switch ( event.key ) {
				case 'ArrowLeft':
				case 'ArrowDown':
					setPosition( position - step );
					break;
				case 'ArrowRight':
				case 'ArrowUp':
					setPosition( position + step );
					break;
				case 'Home':
					setPosition( 0 );
					break;
				case 'End':
					setPosition( 100 );
					break;
				default:
					handled = false;
			}

			if ( handled ) {
				event.preventDefault();
			}
		} );

		// Focus the handle when it is clicked so keyboard control is immediate.
		handle.addEventListener( 'pointerdown', function () {
			handle.focus();
		} );
	}

	/**
	 * Initialize every comparison slider on the page.
	 *
	 * @return {void}
	 */
	function init() {
		var nodes = document.querySelectorAll( '.zaso-before-after__container' );
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
