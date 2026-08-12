/* [ZASO] Notification Banner - Main JS */

( function () {

	'use strict';

	var STORAGE_PREFIX = 'zaso-banner-';

	/**
	 * Read a dismissal flag, tolerating browsers where storage is unavailable.
	 *
	 * Private mode and cookie-blocking settings make localStorage throw on access
	 * rather than return null, so every call is guarded. A storage failure means
	 * the banner simply shows, which is the safe outcome for an announcement.
	 *
	 * @param {string} key      The per-message key.
	 * @param {string} remember Retention mode: none, session or forever.
	 * @return {boolean} True when this banner was already dismissed.
	 */
	function isDismissed( key, remember ) {
		if ( 'none' === remember ) {
			return false;
		}
		try {
			var store = 'session' === remember ? window.sessionStorage : window.localStorage;
			return '1' === store.getItem( STORAGE_PREFIX + key );
		} catch ( e ) {
			return false;
		}
	}

	/**
	 * Persist a dismissal.
	 *
	 * @param {string} key      The per-message key.
	 * @param {string} remember Retention mode: none, session or forever.
	 */
	function setDismissed( key, remember ) {
		if ( 'none' === remember ) {
			return;
		}
		try {
			var store = 'session' === remember ? window.sessionStorage : window.localStorage;
			store.setItem( STORAGE_PREFIX + key, '1' );
		} catch ( e ) {
			// Storage is full or blocked. The banner still closes for this page view.
		}
	}

	/**
	 * Offset the page so a pinned banner never covers the top of the content.
	 *
	 * @param {HTMLElement} banner The banner element.
	 */
	function applyBodyOffset( banner ) {
		if ( ! banner.classList.contains( 'zaso-notification-banner--top' ) ) {
			return;
		}
		document.body.style.paddingTop = banner.offsetHeight + 'px';
	}

	/**
	 * Wire up one banner.
	 *
	 * @param {HTMLElement} banner The banner element.
	 */
	function initBanner( banner ) {
		var key      = banner.getAttribute( 'data-zaso-banner-key' ) || '';
		var remember = banner.getAttribute( 'data-zaso-banner-remember' ) || 'forever';

		if ( key && isDismissed( key, remember ) ) {
			banner.parentNode.removeChild( banner );
			return;
		}

		applyBodyOffset( banner );

		var dismiss = banner.querySelector( '.zaso-notification-banner__dismiss' );
		if ( ! dismiss ) {
			return;
		}

		dismiss.addEventListener( 'click', function () {
			if ( key ) {
				setDismissed( key, remember );
			}
			if ( banner.classList.contains( 'zaso-notification-banner--top' ) ) {
				document.body.style.paddingTop = '';
			}
			banner.parentNode.removeChild( banner );
		} );
	}

	/**
	 * Find and initialise every banner on the page.
	 */
	function init() {
		var banners = document.querySelectorAll( '.zaso-notification-banner' );
		var i;
		for ( i = 0; i < banners.length; i++ ) {
			initBanner( banners[ i ] );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

} )();
