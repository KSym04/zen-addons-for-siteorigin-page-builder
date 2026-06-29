/**
 * Zen Addons - Visual Design Picker (Alert Box).
 *
 * Replaces the Alert Box widget's plain "Design" ( design_variant ) <select> with
 * a "Browse designs" button that opens a modal gallery of REAL rendered design
 * screenshots. Cards stage a choice; the footer's Apply button writes it to the
 * native <select> and dispatches `change` so SiteOrigin persists it. Every step
 * is wrapped so any failure leaves the native dropdown fully functional.
 *
 * The widget form renders on demand, so a MutationObserver re-scans the DOM as
 * fields appear. Vanilla JS, no dependencies.
 *
 * @since 1.11.0
 */
( function() {
	'use strict';

	var data = window.ZasoDesignPicker;
	if ( ! data || ! Array.isArray( data.designs ) || ! data.designs.length ) {
		return; // No data: native <select> keeps working untouched.
	}

	// Render-only Pro upsell cards (blurred + locked). Never written to the select.
	var lockedDesigns = Array.isArray( data.lockedDesigns ) ? data.lockedDesigns : [];

	var PROCESSED = 'data-zaso-dp';
	var i18n = data.i18n || {};
	var proUrl = data.proUrl || '';
	var defaultLabel = data.defaultLabel || 'Default';
	// White-labelled Pro site: never show Free / Pro tier badges to the client.
	var whiteLabel = !! data.whiteLabel;

	// Design ids that identify the target <select> (its options must be a superset).
	var designIds = ( Array.isArray( data.designIds ) ? data.designIds : data.designs.map( function( d ) {
		return d.id;
	} ) ).map( function( id ) {
		return String( id );
	} ).filter( function( id ) {
		return id && id !== 'default';
	} );

	if ( ! designIds.length ) {
		return;
	}

	// id => label, for the trigger caption.
	var labelById = {};
	data.designs.forEach( function( d ) {
		labelById[ String( d.id ) ] = d.label || d.id;
	} );

	/**
	 * Escape a string for safe insertion as an attribute / text value.
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
	 * Test whether a <select> is the Alert Box design_variant control: its option
	 * values must contain every known design id. The design id set is large and
	 * unique (glass, terminal, ...), so no other field collides.
	 *
	 * @param {HTMLSelectElement} select Candidate select.
	 * @return {boolean} True when this is the design select.
	 */
	function isDesignSelect( select ) {
		try {
			var optVals = Array.prototype.map.call( select.options, function( o ) {
				return String( o.value );
			} );
			return designIds.every( function( id ) {
				return optVals.indexOf( id ) !== -1;
			} );
		} catch ( e ) {
			return false;
		}
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
	 * Write a design id to the native select and dispatch change so SiteOrigin
	 * persists it and the live preview restructures. The select stays the source
	 * of truth and the fallback.
	 *
	 * @param {HTMLSelectElement} select The design_variant select.
	 * @param {string} designId The chosen design id ( '' = classic default ).
	 */
	function applyDesign( select, designId ) {
		try {
			select.value = designId;
			if ( select.value !== designId ) {
				return; // The select rejected the value (e.g. a Pro id without a license).
			}
			select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} catch ( e ) {
			/* Native select remains usable. */
		}
	}

	/**
	 * Build and open the modal gallery for a design select.
	 *
	 * @param {HTMLSelectElement} select The native design_variant select.
	 * @param {HTMLElement} trigger The button that opened the modal.
	 */
	function openModal( select, trigger ) {
		closeModal();

		// Staged selection. Cards only update this + their visual state; the footer
		// Apply button commits, so the user sees what they are about to apply and
		// Cancel / Esc / outside-click discards untouched.
		var original = String( select.value );
		var staged = { design: original };

		var overlay = document.createElement( 'div' );
		overlay.className = 'zaso-dp-overlay';

		var dialog = document.createElement( 'div' );
		dialog.className = 'zaso-dp-dialog';
		dialog.setAttribute( 'role', 'dialog' );
		dialog.setAttribute( 'aria-modal', 'true' );
		dialog.setAttribute( 'aria-label', i18n.choose || 'Choose a pre-made design' );

		/* Header. */
		var head = document.createElement( 'div' );
		head.className = 'zaso-dp-head';

		var headText = document.createElement( 'div' );
		headText.className = 'zaso-dp-head-text';

		var title = document.createElement( 'h2' );
		title.className = 'zaso-dp-title';
		title.textContent = i18n.choose || 'Choose a pre-made design';

		var subtitle = document.createElement( 'p' );
		subtitle.className = 'zaso-dp-subtitle';
		subtitle.textContent = i18n.subtitle || 'A pre-made design styles the whole alert in one click. Pick one, then Apply.';

		headText.appendChild( title );
		headText.appendChild( subtitle );

		var closeBtn = document.createElement( 'button' );
		closeBtn.type = 'button';
		closeBtn.className = 'zaso-dp-close';
		closeBtn.setAttribute( 'aria-label', i18n.close || 'Close' );
		closeBtn.innerHTML = '&times;';
		closeBtn.addEventListener( 'click', closeModal );

		head.appendChild( headText );
		head.appendChild( closeBtn );

		/* Body: a single gallery grid. */
		var body = document.createElement( 'div' );
		body.className = 'zaso-dp-body';

		var grid = document.createElement( 'div' );
		grid.className = 'zaso-dp-grid';

		function stage( id ) {
			staged.design = String( id );
			var prev = grid.querySelector( '.zaso-dp-card.is-selected' );
			if ( prev ) {
				prev.classList.remove( 'is-selected' );
				prev.setAttribute( 'aria-pressed', 'false' );
			}
		}

		// Built-in reset card for the classic default ( design_variant = '' ).
		grid.appendChild( buildDefaultCard( staged.design, function( card ) {
			stage( '' );
			card.classList.add( 'is-selected' );
			card.setAttribute( 'aria-pressed', 'true' );
		} ) );

		// One card per real design.
		data.designs.forEach( function( design ) {
			grid.appendChild( buildDesignCard( design, staged.design, function( card ) {
				stage( design.id );
				card.classList.add( 'is-selected' );
				card.setAttribute( 'aria-pressed', 'true' );
			} ) );
		} );

		// Unlicensed (non-white-label): show the Pro designs as blurred, locked
		// upsell cards. They are render-only: clicking one opens the upgrade page
		// and never writes the select. A single "Unlock all" CTA card closes the
		// gallery. White-labelled sites get neither (handled server-side: empty).
		if ( lockedDesigns.length && proUrl ) {
			lockedDesigns.forEach( function( design ) {
				grid.appendChild( buildLockedCard( design ) );
			} );
			grid.appendChild( buildUnlockCard() );
		}

		body.appendChild( grid );

		/**
		 * Commit the staged design to the native select, then close. Only writes
		 * when it actually changed, so re-applying the same design never fires a
		 * redundant change event.
		 */
		function commitStaged() {
			if ( String( staged.design ) !== String( original ) ) {
				applyDesign( select, String( staged.design ) );
			}
			refreshTriggerLabel( trigger, staged.design );
			closeModal();
		}

		/* Footer: Cancel (discard) + Apply (commit). */
		var foot = document.createElement( 'div' );
		foot.className = 'zaso-dp-foot';

		var cancelBtn = document.createElement( 'button' );
		cancelBtn.type = 'button';
		cancelBtn.className = 'zaso-dp-cancel';
		cancelBtn.textContent = i18n.cancel || 'Cancel';
		cancelBtn.addEventListener( 'click', closeModal );

		var applyBtn = document.createElement( 'button' );
		applyBtn.type = 'button';
		applyBtn.className = 'zaso-dp-apply';
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
		var firstSelected = dialog.querySelector( '.zaso-dp-card.is-selected' );
		( firstSelected || closeBtn ).focus();
	}

	/**
	 * Build the built-in "Default (classic box)" reset card.
	 *
	 * @param {string} current Currently staged design id.
	 * @param {Function} onPick Called with the card element on click.
	 * @return {HTMLElement} The card button.
	 */
	function buildDefaultCard( current, onPick ) {
		var isSel = ( '' === String( current ) );
		var card = document.createElement( 'button' );
		card.type = 'button';
		card.className = 'zaso-dp-card zaso-dp-card-default' + ( isSel ? ' is-selected' : '' );
		card.setAttribute( 'data-design', '' );
		card.setAttribute( 'aria-pressed', isSel ? 'true' : 'false' );

		var frame = document.createElement( 'span' );
		frame.className = 'zaso-dp-frame zaso-dp-frame-default';
		var box = document.createElement( 'span' );
		box.className = 'zaso-dp-default-box';
		box.textContent = defaultLabel;
		frame.appendChild( box );

		var meta = document.createElement( 'span' );
		meta.className = 'zaso-dp-meta';
		var label = document.createElement( 'span' );
		label.className = 'zaso-dp-label';
		label.textContent = defaultLabel;
		meta.appendChild( label );

		card.appendChild( frame );
		card.appendChild( meta );

		card.addEventListener( 'click', function() {
			onPick( card );
		} );

		return card;
	}

	/**
	 * Build one real-design card (rendered screenshot + label + Free/Pro badge).
	 *
	 * @param {Object} design { id, label, isPro, img }.
	 * @param {string} current Currently staged design id.
	 * @param {Function} onPick Called with the card element on click.
	 * @return {HTMLElement} The card button.
	 */
	function buildDesignCard( design, current, onPick ) {
		var isSel = ( String( design.id ) === String( current ) );
		var card = document.createElement( 'button' );
		card.type = 'button';
		card.className = 'zaso-dp-card' + ( isSel ? ' is-selected' : '' );
		card.setAttribute( 'data-design', String( design.id ) );
		card.setAttribute( 'aria-pressed', isSel ? 'true' : 'false' );

		var frame = document.createElement( 'span' );
		frame.className = 'zaso-dp-frame';

		if ( design.img ) {
			var img = document.createElement( 'img' );
			img.className = 'zaso-dp-img';
			img.src = design.img;
			img.alt = design.label || design.id;
			img.loading = 'lazy';
			img.decoding = 'async';
			frame.appendChild( img );
		} else {
			// No thumbnail: fall back to the label so the card is never blank.
			var ph = document.createElement( 'span' );
			ph.className = 'zaso-dp-default-box';
			ph.textContent = design.label || design.id;
			frame.appendChild( ph );
		}

		var meta = document.createElement( 'span' );
		meta.className = 'zaso-dp-meta';

		var label = document.createElement( 'span' );
		label.className = 'zaso-dp-label';
		label.textContent = design.label || design.id;

		meta.appendChild( label );

		// Tier badges are hidden on white-labelled sites so the agency's client
		// never sees the Free / Pro distinction.
		if ( ! whiteLabel ) {
			var badge = document.createElement( 'span' );
			badge.className = 'zaso-dp-badge ' + ( design.isPro ? 'is-pro' : 'is-free' );
			badge.textContent = design.isPro ? ( i18n.pro || 'Pro' ) : ( i18n.free || 'Free' );
			meta.appendChild( badge );
		}
		card.appendChild( frame );
		card.appendChild( meta );

		card.addEventListener( 'click', function() {
			onPick( card );
		} );

		return card;
	}

	/**
	 * Build one blurred, locked Pro upsell card. The preview thumbnail is shown
	 * dimmed and blurred under a PRO badge + lock glyph. The card is NOT
	 * selectable: it never stages a value or writes the <select>; clicking it
	 * opens the upgrade page in a new tab.
	 *
	 * @param {Object} design { id, label, thumb }.
	 * @return {HTMLElement} The anchor card.
	 */
	function buildLockedCard( design ) {
		var card = document.createElement( 'a' );
		card.className = 'zaso-dp-card is-locked';
		card.href = proUrl;
		card.target = '_blank';
		card.rel = 'noopener';
		card.setAttribute( 'data-locked', '1' );
		card.setAttribute( 'aria-label', ( design.label || design.id ) + ' (' + ( i18n.pro || 'Pro' ) + ')' );
		card.title = i18n.locked || 'This design is part of Zen Addons Pro. Upgrade to use it.';

		var frame = document.createElement( 'span' );
		frame.className = 'zaso-dp-frame';

		if ( design.thumb ) {
			var img = document.createElement( 'img' );
			img.className = 'zaso-dp-img zaso-dp-img-locked';
			img.src = design.thumb;
			img.alt = '';
			img.setAttribute( 'aria-hidden', 'true' );
			img.loading = 'lazy';
			img.decoding = 'async';
			frame.appendChild( img );
		} else {
			var ph = document.createElement( 'span' );
			ph.className = 'zaso-dp-default-box';
			ph.textContent = design.label || design.id;
			frame.appendChild( ph );
		}

		// Lock glyph overlaid on the blurred preview.
		var lock = document.createElement( 'span' );
		lock.className = 'zaso-dp-lock-ic';
		lock.setAttribute( 'aria-hidden', 'true' );
		lock.innerHTML = '&#128274;';
		frame.appendChild( lock );

		var meta = document.createElement( 'span' );
		meta.className = 'zaso-dp-meta';

		var label = document.createElement( 'span' );
		label.className = 'zaso-dp-label';
		label.textContent = design.label || design.id;
		meta.appendChild( label );

		// The PRO badge is the whole point of the locked card, so it is shown even
		// though the locked channel is already empty on white-labelled sites.
		var badge = document.createElement( 'span' );
		badge.className = 'zaso-dp-badge is-pro';
		badge.textContent = i18n.pro || 'Pro';
		meta.appendChild( badge );

		card.appendChild( frame );
		card.appendChild( meta );
		return card;
	}

	/**
	 * Build the "Unlock all designs with Pro" CTA card shown after the locked
	 * gallery to unlicensed users.
	 *
	 * @return {HTMLElement} The anchor card.
	 */
	function buildUnlockCard() {
		var card = document.createElement( 'a' );
		card.className = 'zaso-dp-card zaso-dp-unlock';
		card.href = proUrl;
		card.target = '_blank';
		card.rel = 'noopener';

		var ic = document.createElement( 'span' );
		ic.className = 'zaso-dp-unlock-ic';
		ic.setAttribute( 'aria-hidden', 'true' );
		ic.innerHTML = '&#128274;';

		var txt = document.createElement( 'span' );
		txt.className = 'zaso-dp-unlock-txt';
		txt.textContent = i18n.unlock || 'Unlock the full design library with Zen Addons Pro.';

		var cta = document.createElement( 'span' );
		cta.className = 'zaso-dp-unlock-cta';
		cta.textContent = i18n.unlockAll || 'Unlock all designs with Pro';

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
	 * Update the trigger button's caption to reflect the active design.
	 *
	 * @param {HTMLElement} trigger The button.
	 * @param {string} designId Active design id ( '' = default ).
	 */
	function refreshTriggerLabel( trigger, designId ) {
		if ( ! trigger ) {
			return;
		}
		var caption = trigger.querySelector( '.zaso-dp-btn-current' );
		if ( ! caption ) {
			return;
		}
		var id = String( designId == null ? '' : designId );
		caption.textContent = ( '' === id ) ? defaultLabel : ( labelById[ id ] || '' );
	}

	/**
	 * Enhance the Alert Box design_variant <select> with a Browse-designs button.
	 *
	 * @param {HTMLSelectElement} select Candidate select.
	 */
	function enhance( select ) {
		try {
			if ( ! select || select.getAttribute( PROCESSED ) || select.tagName !== 'SELECT' ) {
				return;
			}

			// Mark every evaluated select once, up front: non-design selects must not
			// be re-tested on every MutationObserver pass, and a failure below must
			// not re-trigger on this node.
			select.setAttribute( PROCESSED, '1' );

			if ( ! isDesignSelect( select ) ) {
				return; // Not the design control.
			}

			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'zaso-dp-btn';

			var icon = document.createElement( 'span' );
			icon.className = 'zaso-dp-btn-icon';
			icon.setAttribute( 'aria-hidden', 'true' );
			icon.innerHTML = '&#9638;';

			var labelWrap = document.createElement( 'span' );
			labelWrap.className = 'zaso-dp-btn-text';

			var labelMain = document.createElement( 'span' );
			labelMain.className = 'zaso-dp-btn-main';
			labelMain.textContent = i18n.browse || 'Browse designs';

			var labelCur = document.createElement( 'span' );
			labelCur.className = 'zaso-dp-btn-current';

			labelWrap.appendChild( labelMain );
			labelWrap.appendChild( labelCur );
			btn.appendChild( icon );
			btn.appendChild( labelWrap );

			btn.addEventListener( 'click', function( ev ) {
				ev.preventDefault();
				openModal( select, btn );
			} );

			// Keep the caption in sync if the value changes elsewhere.
			select.addEventListener( 'change', function() {
				refreshTriggerLabel( btn, select.value );
			} );

			select.parentNode.insertBefore( btn, select );
			select.style.display = 'none';

			refreshTriggerLabel( btn, select.value );
		} catch ( e ) {
			/* Any failure leaves the native dropdown intact. */
		}
	}

	/**
	 * Scan a root node for unprocessed selects.
	 *
	 * @param {Element|Document} root Scan root.
	 */
	function scan( root ) {
		try {
			var scope = root && root.querySelectorAll ? root : document;
			var selects = scope.querySelectorAll( 'select:not([' + PROCESSED + '])' );
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
