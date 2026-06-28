/**
 * Zen Addons - Visual Style Picker.
 *
 * Progressively enhances SiteOrigin `design_style` presets <select> fields with
 * a "Browse styles" button that opens a modal gallery of rendered skin previews.
 * Picking a card sets the native <select> value and dispatches a `change` event,
 * letting SiteOrigin's own presets-field script apply the preset. The <select>
 * is the single source of truth and the fallback: every step is wrapped so that
 * any failure leaves the native dropdown fully functional.
 *
 * Vanilla JS, no dependencies. The widget form renders on demand, so a
 * MutationObserver re-scans the DOM as fields appear.
 *
 * @since 1.10.5
 */
( function() {
	'use strict';

	var data = window.ZasoStylePicker;
	if ( ! data || ! data.widgets || typeof data.widgets !== 'object' ) {
		return; // No data: native <select> keeps working untouched.
	}

	var PROCESSED = 'data-zaso-sp';
	var i18n = data.i18n || {};
	var proUrl = data.proUrl || '';

	/**
	 * Pre-index the localized widget map into a lookup-friendly shape.
	 * Each entry: { slug, label, ids: [presetId...], skins: [...], hasPro: bool }.
	 */
	var index = [];
	try {
		Object.keys( data.widgets ).forEach( function( slug ) {
			var w = data.widgets[ slug ];
			if ( ! w || ! Array.isArray( w.skins ) ) {
				return;
			}
			var ids = w.skins.map( function( s ) { return String( s.id ); } );
			index.push( {
				slug: slug,
				label: w.label || slug,
				ids: ids,
				skins: w.skins,
				hasPro: w.skins.some( function( s ) { return !! s.isPro; } )
			} );
		} );
	} catch ( e ) {
		return;
	}

	if ( ! index.length ) {
		return;
	}

	/**
	 * Escape a string for safe insertion as text content (defensive; labels come
	 * from trusted localized data but we never inject them as raw HTML).
	 *
	 * @param {string} str Raw string.
	 * @return {string} Escaped string.
	 */
	function esc( str ) {
		return String( str == null ? '' : str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' );
	}

	/**
	 * Find the widget whose skin-id set is a superset of the select's preset ids.
	 * That uniquely identifies which Zen Addons widget a presets <select> belongs
	 * to. Returns null when nothing matches (so foreign presets fields are left
	 * alone).
	 *
	 * @param {string[]} presetIds Preset ids parsed from data-presets.
	 * @return {Object|null} Matching index entry or null.
	 */
	function matchWidget( presetIds ) {
		var wanted = presetIds.filter( function( id ) {
			return id && id !== 'default';
		} );
		if ( ! wanted.length ) {
			return null;
		}

		var best = null;
		for ( var i = 0; i < index.length; i++ ) {
			var entry = index[ i ];
			var isSuperset = wanted.every( function( id ) {
				return entry.ids.indexOf( id ) !== -1;
			} );
			if ( isSuperset ) {
				// Prefer the tightest match (fewest extra skins).
				if ( ! best || entry.ids.length < best.ids.length ) {
					best = entry;
				}
			}
		}
		return best;
	}

	/* ------------------------------------------------------------------ *
	 * Modal
	 * ------------------------------------------------------------------ */

	var openState = null; // { overlay, dialog, trigger, onKeydown }

	/**
	 * Close the open modal, restore focus and tear down listeners.
	 */
	function closeModal() {
		if ( ! openState ) {
			return;
		}
		document.removeEventListener( 'keydown', openState.onKeydown, true );
		if ( openState.overlay && openState.overlay.parentNode ) {
			openState.overlay.parentNode.removeChild( openState.overlay );
		}
		var trigger = openState.trigger;
		openState = null;
		if ( trigger && typeof trigger.focus === 'function' ) {
			trigger.focus();
		}
	}

	/**
	 * Apply a skin: set the native select value and dispatch change so SiteOrigin
	 * copies the preset values into the rest of the form.
	 *
	 * @param {HTMLSelectElement} select The presets select.
	 * @param {string} skinId The chosen preset id.
	 */
	function applySkin( select, skinId ) {
		try {
			select.value = skinId;
			// Confirm the option exists; if the select rejected the value, bail
			// without firing a misleading change event.
			if ( select.value !== skinId ) {
				return;
			}
			select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} catch ( e ) {
			/* Native select remains usable. */
		}
	}

	/**
	 * Build and open the modal gallery for a widget/select pair.
	 *
	 * @param {Object} entry The matched widget index entry.
	 * @param {HTMLSelectElement} select The native presets select.
	 * @param {HTMLElement} trigger The button that opened the modal.
	 */
	function openModal( entry, select, trigger ) {
		closeModal();

		var current = select.value;

		var overlay = document.createElement( 'div' );
		overlay.className = 'zaso-sp-overlay';

		var dialog = document.createElement( 'div' );
		dialog.className = 'zaso-sp-dialog';
		dialog.setAttribute( 'role', 'dialog' );
		dialog.setAttribute( 'aria-modal', 'true' );
		dialog.setAttribute( 'aria-label', ( i18n.choose || 'Choose a style' ) + ': ' + entry.label );

		/* Header. */
		var head = document.createElement( 'div' );
		head.className = 'zaso-sp-head';

		var title = document.createElement( 'h2' );
		title.className = 'zaso-sp-title';
		title.textContent = ( i18n.choose || 'Choose a style' ) + ': ' + entry.label;

		var closeBtn = document.createElement( 'button' );
		closeBtn.type = 'button';
		closeBtn.className = 'zaso-sp-close';
		closeBtn.setAttribute( 'aria-label', i18n.close || 'Close' );
		closeBtn.innerHTML = '&times;';
		closeBtn.addEventListener( 'click', closeModal );

		head.appendChild( title );
		head.appendChild( closeBtn );

		/* Scrollable body with the card grid. */
		var body = document.createElement( 'div' );
		body.className = 'zaso-sp-body';

		var grid = document.createElement( 'div' );
		grid.className = 'zaso-sp-grid';

		entry.skins.forEach( function( skin ) {
			grid.appendChild( buildCard( skin, current, select, trigger ) );
		} );

		// Unlicensed: no Pro skins in the map. Offer a single unlock card.
		if ( ! entry.hasPro && proUrl ) {
			grid.appendChild( buildUnlockCard( entry ) );
		}

		body.appendChild( grid );
		dialog.appendChild( head );
		dialog.appendChild( body );
		overlay.appendChild( dialog );
		document.body.appendChild( overlay );

		/* Dismissal: click outside the dialog. */
		overlay.addEventListener( 'mousedown', function( ev ) {
			if ( ev.target === overlay ) {
				closeModal();
			}
		} );

		/* ESC + focus trap. */
		var onKeydown = function( ev ) {
			if ( ev.key === 'Escape' || ev.keyCode === 27 ) {
				ev.preventDefault();
				closeModal();
				return;
			}
			if ( ev.key === 'Tab' || ev.keyCode === 9 ) {
				trapTab( ev, dialog );
			}
		};
		document.addEventListener( 'keydown', onKeydown, true );

		openState = { overlay: overlay, dialog: dialog, trigger: trigger, onKeydown: onKeydown };

		/* Move focus into the dialog. */
		var firstSelected = dialog.querySelector( '.zaso-sp-card.is-selected' );
		( firstSelected || closeBtn ).focus();
	}

	/**
	 * Build one skin card.
	 *
	 * @param {Object} skin { id, label, isPro, html }.
	 * @param {string} current Currently selected preset id.
	 * @param {HTMLSelectElement} select Native select.
	 * @param {HTMLElement} trigger Opening button (for label refresh).
	 * @return {HTMLElement} The card button.
	 */
	function buildCard( skin, current, select, trigger ) {
		var card = document.createElement( 'button' );
		card.type = 'button';
		card.className = 'zaso-sp-card' + ( String( skin.id ) === String( current ) ? ' is-selected' : '' );
		card.setAttribute( 'data-skin', String( skin.id ) );

		var frame = document.createElement( 'div' );
		frame.className = 'zaso-sp-frame';
		// skin.html is trusted plugin markup (static copy + esc_attr'd colours),
		// produced server-side by ZASO_Widget_Design::render_preview().
		frame.innerHTML = skin.html || '';

		var meta = document.createElement( 'div' );
		meta.className = 'zaso-sp-meta';

		var label = document.createElement( 'span' );
		label.className = 'zaso-sp-label';
		label.textContent = skin.label || skin.id;

		var badge = document.createElement( 'span' );
		badge.className = 'zaso-sp-badge ' + ( skin.isPro ? 'is-pro' : 'is-free' );
		badge.textContent = skin.isPro ? ( i18n.pro || 'Pro' ) : ( i18n.free || 'Free' );

		meta.appendChild( label );
		meta.appendChild( badge );
		card.appendChild( frame );
		card.appendChild( meta );

		card.addEventListener( 'click', function() {
			applySkin( select, String( skin.id ) );

			// Reflect selection in the grid.
			var prev = card.parentNode.querySelector( '.zaso-sp-card.is-selected' );
			if ( prev ) {
				prev.classList.remove( 'is-selected' );
			}
			card.classList.add( 'is-selected' );

			refreshTriggerLabel( trigger, skin.label || skin.id );
			closeModal();
		} );

		return card;
	}

	/**
	 * Build the locked "unlock Pro" card shown to unlicensed users.
	 *
	 * @param {Object} entry Widget index entry (for the label).
	 * @return {HTMLElement} The anchor card.
	 */
	function buildUnlockCard( entry ) {
		var card = document.createElement( 'a' );
		card.className = 'zaso-sp-card zaso-sp-unlock';
		card.href = proUrl;
		card.target = '_blank';
		card.rel = 'noopener';

		var ic = document.createElement( 'span' );
		ic.className = 'zaso-sp-unlock-ic';
		ic.setAttribute( 'aria-hidden', 'true' );
		ic.innerHTML = '&#128274;';

		var txt = document.createElement( 'span' );
		txt.className = 'zaso-sp-unlock-txt';
		txt.textContent = i18n.unlock || 'Unlock the full design library with Zen Addons Pro.';

		var cta = document.createElement( 'span' );
		cta.className = 'zaso-sp-unlock-cta';
		cta.textContent = i18n.pro || 'Pro';

		card.appendChild( ic );
		card.appendChild( txt );
		card.appendChild( cta );
		return card;
	}

	/**
	 * Keep focus inside the dialog while tabbing.
	 *
	 * @param {KeyboardEvent} ev The Tab keydown.
	 * @param {HTMLElement} dialog The dialog container.
	 */
	function trapTab( ev, dialog ) {
		var focusable = dialog.querySelectorAll(
			'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
		);
		if ( ! focusable.length ) {
			return;
		}
		var first = focusable[ 0 ];
		var last = focusable[ focusable.length - 1 ];

		if ( ev.shiftKey && document.activeElement === first ) {
			ev.preventDefault();
			last.focus();
		} else if ( ! ev.shiftKey && document.activeElement === last ) {
			ev.preventDefault();
			first.focus();
		}
	}

	/* ------------------------------------------------------------------ *
	 * Enhancement
	 * ------------------------------------------------------------------ */

	/**
	 * Update the trigger button's caption to reflect the active skin.
	 *
	 * @param {HTMLElement} trigger The button.
	 * @param {string} skinLabel Active skin label.
	 */
	function refreshTriggerLabel( trigger, skinLabel ) {
		if ( ! trigger ) {
			return;
		}
		var caption = trigger.querySelector( '.zaso-sp-btn-current' );
		if ( caption ) {
			caption.textContent = skinLabel || '';
		}
	}

	/**
	 * Read the label of the currently selected option in a select.
	 *
	 * @param {HTMLSelectElement} select Native select.
	 * @param {Object} entry Widget index entry.
	 * @return {string} Current skin label, or empty string.
	 */
	function currentLabel( select, entry ) {
		var id = select.value;
		if ( ! id || id === 'default' ) {
			return '';
		}
		for ( var i = 0; i < entry.skins.length; i++ ) {
			if ( String( entry.skins[ i ].id ) === String( id ) ) {
				return entry.skins[ i ].label || id;
			}
		}
		return '';
	}

	/**
	 * Enhance a single presets <select> if it belongs to a supported widget.
	 *
	 * @param {HTMLSelectElement} select Candidate select[data-presets].
	 */
	function enhance( select ) {
		try {
			if ( ! select || select.getAttribute( PROCESSED ) ) {
				return;
			}

			var raw = select.getAttribute( 'data-presets' );
			if ( ! raw ) {
				return;
			}

			var presets;
			try {
				presets = JSON.parse( raw );
			} catch ( e ) {
				return; // Malformed: leave the native select alone.
			}
			if ( ! presets || typeof presets !== 'object' ) {
				return;
			}

			var entry = matchWidget( Object.keys( presets ) );
			if ( ! entry ) {
				return; // Not one of ours.
			}

			// Mark first so a failure below cannot re-trigger on this node.
			select.setAttribute( PROCESSED, '1' );

			// Build the trigger button.
			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'zaso-sp-btn';

			var icon = document.createElement( 'span' );
			icon.className = 'zaso-sp-btn-icon';
			icon.setAttribute( 'aria-hidden', 'true' );
			icon.innerHTML = '&#9638;';

			var labelWrap = document.createElement( 'span' );
			labelWrap.className = 'zaso-sp-btn-text';

			var labelMain = document.createElement( 'span' );
			labelMain.className = 'zaso-sp-btn-main';
			labelMain.textContent = i18n.browse || 'Browse styles';

			var labelCur = document.createElement( 'span' );
			labelCur.className = 'zaso-sp-btn-current';
			labelCur.textContent = currentLabel( select, entry );

			labelWrap.appendChild( labelMain );
			labelWrap.appendChild( labelCur );
			btn.appendChild( icon );
			btn.appendChild( labelWrap );

			btn.addEventListener( 'click', function( ev ) {
				ev.preventDefault();
				openModal( entry, select, btn );
			} );

			// Keep the button caption in sync if the value changes elsewhere
			// (e.g. the native select or SiteOrigin's Undo link).
			select.addEventListener( 'change', function() {
				refreshTriggerLabel( btn, currentLabel( select, entry ) );
			} );

			// Insert the button right before the select; hide the select via inline
			// style only (never touch its class, which SiteOrigin matches exactly
			// with select[class="siteorigin-widget-input"]).
			select.parentNode.insertBefore( btn, select );
			select.style.display = 'none';
		} catch ( e ) {
			/* Any failure leaves the native dropdown intact. */
		}
	}

	/**
	 * Scan a root node for unprocessed presets selects.
	 *
	 * @param {Element|Document} root Scan root.
	 */
	function scan( root ) {
		try {
			var scope = root && root.querySelectorAll ? root : document;
			var selects = scope.querySelectorAll( 'select[data-presets]:not([' + PROCESSED + '])' );
			for ( var i = 0; i < selects.length; i++ ) {
				enhance( selects[ i ] );
			}
		} catch ( e ) {
			/* Ignore. */
		}
	}

	/* Initial pass. */
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function() { scan( document ); } );
	} else {
		scan( document );
	}

	/* Widget forms render on demand: watch for new nodes, debounced. */
	try {
		var pending = false;
		var observer = new MutationObserver( function() {
			if ( pending ) {
				return;
			}
			pending = true;
			window.setTimeout( function() {
				pending = false;
				scan( document );
			}, 120 );
		} );
		observer.observe( document.body, { childList: true, subtree: true } );
	} catch ( e ) {
		/* MutationObserver unsupported: initial scan still covers static forms. */
	}
} )();
