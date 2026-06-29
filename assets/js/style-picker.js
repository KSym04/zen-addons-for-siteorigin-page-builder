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

	// Option ids that mark a form whose Style select must be left as a native
	// dropdown (the Alert Box, which has its own dedicated design-picker gallery).
	// When a presets select shares a form with a select carrying every one of
	// these ids, enhancement is skipped. This is deterministic where matchWidget
	// is not: the Pro design library re-uses the SAME scheme ids across widgets,
	// so a presets select cannot be attributed to a widget by its ids alone.
	var SKIP_DESIGN_IDS = ( Array.isArray( data.skipFormDesignIds ) ? data.skipFormDesignIds : [] )
		.map( function( id ) { return String( id ); } )
		.filter( function( id ) { return id && id !== 'default'; } );

	/**
	 * Pre-index the localized widget map into a lookup-friendly shape.
	 * Each entry: { slug, label, ids, skins, hasPro, layouts, layoutKey, layoutIds }.
	 */
	var index = [];
	try {
		Object.keys( data.widgets ).forEach( function( slug ) {
			var w = data.widgets[ slug ];
			if ( ! w || ! Array.isArray( w.skins ) ) {
				return;
			}
			var ids = w.skins.map( function( s ) { return String( s.id ); } );
			var layouts = Array.isArray( w.layouts ) ? w.layouts : [];
			var layoutIds = Array.isArray( w.layoutIds )
				? w.layoutIds.map( function( id ) { return String( id ); } )
				: layouts.map( function( l ) { return String( l.id ); } );
			index.push( {
				slug: slug,
				label: w.label || slug,
				ids: ids,
				skins: w.skins,
				hasPro: w.skins.some( function( s ) { return !! s.isPro; } ),
				layouts: layouts,
				layoutKey: w.layoutKey || 'layout',
				layoutIds: layoutIds
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

	/**
	 * Locate the widget's structural layout <select> relative to its presets
	 * <select>. SiteOrigin renders every field of one widget instance inside a
	 * shared form container, so we climb the presets select's ancestors and, at
	 * each level, look for a sibling <select> whose <option> values are a superset
	 * of the widget's layout ids. This robustly binds the right control even when
	 * several widgets are open at once, and never touches the CTA Banner's
	 * button-placement select (its values are stacked / inline, not the layout ids).
	 *
	 * @param {HTMLSelectElement} presetSelect The matched presets select.
	 * @param {Object} entry The widget index entry.
	 * @return {HTMLSelectElement|null} The layout select, or null when none.
	 */
	function findLayoutSelect( presetSelect, entry ) {
		try {
			var wanted = ( entry.layoutIds || [] ).filter( function( id ) {
				return id && id !== 'default';
			} );
			if ( ! wanted.length ) {
				return null;
			}

			var node = presetSelect.parentNode;
			for ( var depth = 0; node && node.querySelectorAll && depth < 12; depth++ ) {
				var selects = node.querySelectorAll( 'select' );
				for ( var i = 0; i < selects.length; i++ ) {
					var s = selects[ i ];
					if ( s === presetSelect ) {
						continue;
					}
					var optVals = Array.prototype.map.call( s.options, function( o ) {
						return String( o.value );
					} );
					var isSuperset = wanted.every( function( id ) {
						return optVals.indexOf( id ) !== -1;
					} );
					if ( isSuperset ) {
						return s;
					}
				}
				node = node.parentNode;
			}
		} catch ( e ) {
			/* Layout section is simply omitted on failure. */
		}
		return null;
	}

	/**
	 * Find a sibling <select> of the presets select whose option values contain
	 * every id in `ids`. Climbs the presets select's ancestors (SiteOrigin renders
	 * all of one widget instance's fields inside a shared container), so it binds
	 * within the same widget form only. Used to detect the Alert Box form by its
	 * unique design_variant ids.
	 *
	 * @param {HTMLSelectElement} presetSelect The presets select.
	 * @param {string[]} ids Option ids that must all be present.
	 * @return {HTMLSelectElement|null} The matching sibling select, or null.
	 */
	function findSiblingSelectWithIds( presetSelect, ids ) {
		try {
			if ( ! ids || ! ids.length ) {
				return null;
			}
			// Scope to this widget instance's form so the search never crosses into
			// another open widget's fields (several forms can be in the DOM at once).
			var scope = ( typeof presetSelect.closest === 'function' )
				? presetSelect.closest( '.siteorigin-widget-form' )
				: null;
			if ( ! scope ) {
				scope = presetSelect.parentNode; // Fallback: immediate field wrapper only.
			}
			if ( ! scope || ! scope.querySelectorAll ) {
				return null;
			}

			var selects = scope.querySelectorAll( 'select' );
			for ( var i = 0; i < selects.length; i++ ) {
				var s = selects[ i ];
				if ( s === presetSelect ) {
					continue;
				}
				var optVals = Array.prototype.map.call( s.options, function( o ) {
					return String( o.value );
				} );
				var isSuperset = ids.every( function( id ) {
					return optVals.indexOf( id ) !== -1;
				} );
				if ( isSuperset ) {
					return s;
				}
			}
		} catch ( e ) {
			/* Treated as "not the skip form" on failure. */
		}
		return null;
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
	 * Apply a layout: set the layout select value and dispatch change so the
	 * widget form (and the live preview) restructures. Mirrors applySkin so the
	 * native <select> stays the source of truth and the fallback.
	 *
	 * @param {HTMLSelectElement} select The widget's layout select.
	 * @param {string} layoutId The chosen layout id.
	 */
	function applyLayout( select, layoutId ) {
		try {
			if ( ! select ) {
				return;
			}
			select.value = layoutId;
			if ( select.value !== layoutId ) {
				return; // The select rejected the value; do not fire a misleading event.
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

		// Resolve the widget's layout control (may be null for layout-less widgets).
		var layoutSelect = ( entry.layouts && entry.layouts.length )
			? findLayoutSelect( select, entry )
			: null;

		// Staged selection. Cards no longer write to the form on click; they only
		// update this object and their visual state. The footer's Apply button
		// commits everything at once, so the user always sees what they are about
		// to apply (and Cancel / Esc / outside-click discards it untouched). The
		// originals let Apply skip a redundant `change` event - re-applying an
		// unchanged style preset would needlessly clobber the user's manual tweaks.
		var originalLayout = layoutSelect ? String( layoutSelect.value ) : null;
		var originalStyle  = String( current );
		var staged = {
			layout: originalLayout,
			style:  originalStyle
		};

		var overlay = document.createElement( 'div' );
		overlay.className = 'zaso-sp-overlay';

		var dialog = document.createElement( 'div' );
		dialog.className = 'zaso-sp-dialog';
		dialog.setAttribute( 'role', 'dialog' );
		dialog.setAttribute( 'aria-modal', 'true' );
		dialog.setAttribute( 'aria-label', ( i18n.choose || 'Choose a design' ) + ': ' + entry.label );

		/* Header: eyebrow + title + subtitle, with a close button. */
		var head = document.createElement( 'div' );
		head.className = 'zaso-sp-head';

		var headText = document.createElement( 'div' );
		headText.className = 'zaso-sp-head-text';

		var eyebrow = document.createElement( 'span' );
		eyebrow.className = 'zaso-sp-eyebrow';
		eyebrow.textContent = entry.label;

		var title = document.createElement( 'h2' );
		title.className = 'zaso-sp-title';
		title.textContent = i18n.choose || 'Choose a design';

		var subtitle = document.createElement( 'p' );
		subtitle.className = 'zaso-sp-subtitle';
		subtitle.textContent = i18n.subtitle || 'Pick a layout and a colour style, then Apply.';

		headText.appendChild( eyebrow );
		headText.appendChild( title );
		headText.appendChild( subtitle );

		var closeBtn = document.createElement( 'button' );
		closeBtn.type = 'button';
		closeBtn.className = 'zaso-sp-close';
		closeBtn.setAttribute( 'aria-label', i18n.close || 'Close' );
		closeBtn.innerHTML = '&times;';
		closeBtn.addEventListener( 'click', closeModal );

		head.appendChild( headText );
		head.appendChild( closeBtn );

		/* Scrollable body holding the labelled sections. */
		var body = document.createElement( 'div' );
		body.className = 'zaso-sp-body';

		// Section 1: Layout (only when the widget exposes layouts AND we found the
		// control to drive). Falls back silently to a style-only modal otherwise,
		// preserving the original behaviour for layout-less widgets.
		if ( layoutSelect && entry.layouts.length ) {
			var layoutGrid = document.createElement( 'div' );
			layoutGrid.className = 'zaso-sp-grid zaso-sp-grid-layout';

			entry.layouts.forEach( function( layout ) {
				layoutGrid.appendChild(
					buildLayoutCard( layout, staged.layout, function( id ) {
						staged.layout = id;
					} )
				);
			} );

			body.appendChild( buildSection(
				i18n.layout || 'Layout',
				i18n.layoutHint || 'Structure and shape',
				layoutGrid
			) );
		}

		// Section 2: Style (colour skins) - always present.
		var grid = document.createElement( 'div' );
		grid.className = 'zaso-sp-grid';

		entry.skins.forEach( function( skin ) {
			grid.appendChild( buildCard( skin, staged.style, function( id ) {
				staged.style = id;
			} ) );
		} );

		// Unlicensed only: offer a single unlock card. A licensed user already has
		// the full library (and the map carries the Pro skins), so never upsell.
		if ( ! data.licensed && ! entry.hasPro && proUrl ) {
			grid.appendChild( buildUnlockCard( entry ) );
		}

		body.appendChild( buildSection(
			i18n.style || 'Style',
			i18n.styleHint || 'Colour scheme',
			grid
		) );

		/**
		 * Commit the staged selection to the native form controls, then close.
		 *
		 * Layout and style are written independently and only when they actually
		 * changed, so applying a style never re-fires its preset (which would wipe
		 * the user's manual Design tweaks) and applying a layout never touches the
		 * style. The native <select>s stay the source of truth: this just drives
		 * them, exactly as the per-card clicks used to.
		 */
		function commitStaged() {
			if (
				layoutSelect
				&& staged.layout !== null
				&& String( staged.layout ) !== String( originalLayout )
			) {
				applyLayout( layoutSelect, String( staged.layout ) );
			}

			if (
				staged.style !== null
				&& String( staged.style ) !== String( originalStyle )
			) {
				applySkin( select, String( staged.style ) );
			}

			refreshTriggerLabel( trigger, skinLabelById( entry, staged.style ) );
			closeModal();
		}

		/* Footer: Cancel (discard) + Apply (commit). */
		var foot = document.createElement( 'div' );
		foot.className = 'zaso-sp-foot';

		var cancelBtn = document.createElement( 'button' );
		cancelBtn.type = 'button';
		cancelBtn.className = 'zaso-sp-cancel';
		cancelBtn.textContent = i18n.cancel || 'Cancel';
		cancelBtn.addEventListener( 'click', closeModal );

		var applyBtn = document.createElement( 'button' );
		applyBtn.type = 'button';
		applyBtn.className = 'zaso-sp-apply';
		applyBtn.textContent = i18n.apply || 'Apply';
		applyBtn.addEventListener( 'click', commitStaged );

		foot.appendChild( cancelBtn );
		foot.appendChild( applyBtn );

		dialog.appendChild( head );
		dialog.appendChild( body );
		dialog.appendChild( foot );
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
	 * Build a labelled section wrapper (heading + hint + a card grid).
	 *
	 * @param {string} titleText Section title (e.g. "Layout").
	 * @param {string} hintText Short helper line under / beside the title.
	 * @param {HTMLElement} gridEl The pre-built card grid for this section.
	 * @return {HTMLElement} The section element.
	 */
	function buildSection( titleText, hintText, gridEl ) {
		var section = document.createElement( 'section' );
		section.className = 'zaso-sp-section';

		var headRow = document.createElement( 'div' );
		headRow.className = 'zaso-sp-section-head';

		var h = document.createElement( 'h3' );
		h.className = 'zaso-sp-section-title';
		h.textContent = titleText || '';

		var hint = document.createElement( 'span' );
		hint.className = 'zaso-sp-section-hint';
		hint.textContent = hintText || '';

		headRow.appendChild( h );
		if ( hintText ) {
			headRow.appendChild( hint );
		}

		section.appendChild( headRow );
		section.appendChild( gridEl );
		return section;
	}

	/**
	 * Build one layout card. Clicking it stages the layout choice (the footer's
	 * Apply button commits it); it does not write to the form on its own.
	 *
	 * @param {Object} layout { id, label, html }.
	 * @param {string} current Currently staged layout id.
	 * @param {Function} onPick Called with the chosen layout id to stage it.
	 * @return {HTMLElement} The card button.
	 */
	function buildLayoutCard( layout, current, onPick ) {
		var card = document.createElement( 'button' );
		card.type = 'button';
		card.className = 'zaso-sp-card zaso-sp-card-layout'
			+ ( String( layout.id ) === String( current ) ? ' is-selected' : '' );
		card.setAttribute( 'data-layout', String( layout.id ) );
		card.setAttribute( 'aria-pressed', String( layout.id ) === String( current ) ? 'true' : 'false' );

		var frame = document.createElement( 'div' );
		frame.className = 'zaso-sp-frame';
		// layout.html is trusted plugin markup (static copy + esc_attr'd colours),
		// produced server-side by ZASO_Widget_Design::render_preview().
		frame.innerHTML = layout.html || '';

		var meta = document.createElement( 'div' );
		meta.className = 'zaso-sp-meta';

		var label = document.createElement( 'span' );
		label.className = 'zaso-sp-label';
		label.textContent = layout.label || layout.id;

		meta.appendChild( label );
		card.appendChild( frame );
		card.appendChild( meta );

		card.addEventListener( 'click', function() {
			onPick( String( layout.id ) );

			// Reflect selection within this section's grid only.
			var prev = card.parentNode.querySelector( '.zaso-sp-card.is-selected' );
			if ( prev ) {
				prev.classList.remove( 'is-selected' );
				prev.setAttribute( 'aria-pressed', 'false' );
			}
			card.classList.add( 'is-selected' );
			card.setAttribute( 'aria-pressed', 'true' );
		} );

		return card;
	}

	/**
	 * Build one skin card. Clicking it stages the colour style (the footer's Apply
	 * button commits it); it does not write to the form or close the modal.
	 *
	 * @param {Object} skin { id, label, isPro, html }.
	 * @param {string} current Currently staged preset id.
	 * @param {Function} onPick Called with the chosen preset id to stage it.
	 * @return {HTMLElement} The card button.
	 */
	function buildCard( skin, current, onPick ) {
		var card = document.createElement( 'button' );
		card.type = 'button';
		card.className = 'zaso-sp-card' + ( String( skin.id ) === String( current ) ? ' is-selected' : '' );
		card.setAttribute( 'data-skin', String( skin.id ) );
		card.setAttribute( 'aria-pressed', String( skin.id ) === String( current ) ? 'true' : 'false' );

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
			onPick( String( skin.id ) );

			// Reflect selection in the grid.
			var prev = card.parentNode.querySelector( '.zaso-sp-card.is-selected' );
			if ( prev ) {
				prev.classList.remove( 'is-selected' );
				prev.setAttribute( 'aria-pressed', 'false' );
			}
			card.classList.add( 'is-selected' );
			card.setAttribute( 'aria-pressed', 'true' );
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
	 * Resolve a skin label from its preset id within a widget entry.
	 *
	 * @param {Object} entry Widget index entry.
	 * @param {string} id Preset id (may be null/'default').
	 * @return {string} Matching skin label, or empty string.
	 */
	function skinLabelById( entry, id ) {
		if ( id == null || id === 'default' || id === '' ) {
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

			// Alert Box guard: when this presets select shares a form with the
			// Alert Box's unique Design ( design_variant ) select, this is the Alert
			// Box form. Its Style stays a plain dropdown (the Alert Box has its own
			// design-picker gallery), so skip enhancement. This is deterministic;
			// matchWidget is not, because Pro re-uses the same scheme ids everywhere.
			if ( SKIP_DESIGN_IDS.length && findSiblingSelectWithIds( select, SKIP_DESIGN_IDS ) ) {
				select.setAttribute( PROCESSED, '1' );
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
			labelMain.textContent = i18n.browse || 'Browse designs';

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
