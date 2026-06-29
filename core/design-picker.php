<?php
/**
 * Zen Addons "Visual Design Picker" editor enhancer (Alert Box).
 *
 * Replaces the Alert Box widget's plain "Design" ( design_variant ) dropdown in
 * the Page Builder / widgets editor with a "Browse designs" button that opens a
 * modal gallery of REAL rendered screenshots, one per design. Picking a card
 * stages the choice; the modal's Apply button writes the value to the native
 * <select> and dispatches a `change` event so SiteOrigin persists it. The
 * <select> stays in the DOM as the source of truth and the no-JS fallback.
 *
 * Free ships six design thumbnails; Zen Addons Pro contributes its twenty-four
 * via the `zaso_alert_design_previews` filter (license-gated on the Pro side).
 * On the free plugin (Pro off), those twenty-four are still shown, but as BLURRED,
 * LOCKED upsell cards fed by a separate render-only `lockedDesigns` channel built
 * from bundled previews plus a static label list. Locked cards are never written
 * to the design <select>; clicking one opens the upgrade page.
 *
 * This class extends ZASO_Widget_Design only to reuse its ensure_widget_class()
 * helper and PRO_URL constant. It deliberately does NOT call the parent
 * constructor, so no second admin menu is registered.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'zaso_alert_box_free_design_previews' ) ) :
	/**
	 * Supply the six free Alert Box design thumbnails to the preview filter.
	 *
	 * Keyed by design id; each maps to a webp rendered from the real design. Pro
	 * appends its twenty-four through the same filter at a later priority.
	 *
	 * @since  1.11.0
	 *
	 * @param  array $previews Existing id => URL map.
	 * @return array
	 */
	function zaso_alert_box_free_design_previews( $previews ) {
		$previews = (array) $previews;
		$free_ids = array( 'left-accent', 'soft-tint', 'outlined', 'icon-badge', 'top-bar', 'solid' );

		foreach ( $free_ids as $id ) {
			$previews[ $id ] = ZASO_BASE_DIR . 'assets/design-previews/' . $id . '.webp';
		}

		return $previews;
	}
	add_filter( 'zaso_alert_design_previews', 'zaso_alert_box_free_design_previews', 5 );
endif;

if ( ! class_exists( 'ZASO_Design_Picker' ) && class_exists( 'ZASO_Widget_Design' ) ) :

	/**
	 * Class ZASO_Design_Picker
	 *
	 * Enqueues the design-picker assets on editor screens and localizes the Alert
	 * Box design list, each entry carrying its rendered thumbnail URL.
	 *
	 * @since 1.11.0
	 */
	class ZASO_Design_Picker extends ZASO_Widget_Design {

		/**
		 * Script + style handle.
		 *
		 * @since 1.11.0
		 * @var string
		 */
		const HANDLE = 'zaso-design-picker';

		/**
		 * The six free Alert Box design ids. Everything else is a Pro design, so
		 * the picker can badge cards and gate the upsell without a license probe
		 * per card.
		 *
		 * @since 1.11.0
		 * @var array
		 */
		const FREE_IDS = array( 'left-accent', 'soft-tint', 'outlined', 'icon-badge', 'top-bar', 'solid' );

		/**
		 * The twenty-four Pro Alert Box designs ( id => label ), mirrored from the
		 * Pro plugin's Zanp_Alert_Designs::pro_designs() so the FREE plugin can show
		 * them as blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/{id}.webp. This
		 * list is render-only: these ids are NEVER added to designIds, so they are
		 * never matched against or written to the design_variant <select>.
		 *
		 * @since 1.11.0
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_designs() {
			return array(
				'gradient'      => esc_html__( 'Gradient', 'zaso' ),
				'dark'          => esc_html__( 'Dark Mode', 'zaso' ),
				'split-panel'   => esc_html__( 'Split Panel', 'zaso' ),
				'glass'         => esc_html__( 'Glass', 'zaso' ),
				'toast'         => esc_html__( 'Toast', 'zaso' ),
				'big-icon'      => esc_html__( 'Big Icon', 'zaso' ),
				'pill'          => esc_html__( 'Pill', 'zaso' ),
				'dashed'        => esc_html__( 'Dashed', 'zaso' ),
				'double'        => esc_html__( 'Double Border', 'zaso' ),
				'banner'        => esc_html__( 'Banner', 'zaso' ),
				'tag-label'     => esc_html__( 'Tag Label', 'zaso' ),
				'ghost'         => esc_html__( 'Ghost Tint', 'zaso' ),
				'underline'     => esc_html__( 'Underline', 'zaso' ),
				'round-badge'   => esc_html__( 'Round Badge', 'zaso' ),
				'two-tone'      => esc_html__( 'Two-tone', 'zaso' ),
				'right-accent'  => esc_html__( 'Right Accent', 'zaso' ),
				'floating-icon' => esc_html__( 'Floating Icon', 'zaso' ),
				'dotted'        => esc_html__( 'Dotted', 'zaso' ),
				'deep-solid'    => esc_html__( 'Deep Solid', 'zaso' ),
				'terminal'      => esc_html__( 'Terminal', 'zaso' ),
				'title-bar'     => esc_html__( 'Title Bar', 'zaso' ),
				'action-cta'    => esc_html__( 'Action CTA', 'zaso' ),
				'gradient-ring' => esc_html__( 'Gradient Ring', 'zaso' ),
				'centered'      => esc_html__( 'Centered', 'zaso' ),
			);
		}

		/**
		 * Hook only the editor asset enqueue. The parent constructor is NOT called
		 * on purpose: this subclass must not register the Design Library menu again.
		 *
		 * @since 1.11.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * Editor screens where a SiteOrigin widget form can appear.
		 *
		 * @since  1.11.0
		 *
		 * @param  string $hook Current admin page hook suffix.
		 * @return bool True when the design picker should load.
		 */
		protected function is_editor_screen( $hook ) {
			return in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php' ), true );
		}

		/**
		 * Build the localized data map the picker JS consumes.
		 *
		 * The design list ( zaso_alert_box_design_options() ) already reflects the
		 * license: six entries unlicensed, thirty when Pro is active. Each gets its
		 * thumbnail URL from the `zaso_alert_design_previews` filter. The empty-id
		 * "Default (classic box)" entry is dropped here; the JS renders a built-in
		 * reset card for it.
		 *
		 * @since  1.11.0
		 * @return array {
		 *     @type array  $designs       Cards: id, label, isPro, img.
		 *     @type array  $designIds     Non-empty design ids (for select binding).
		 *     @type array  $lockedDesigns Render-only Pro upsell cards: id, label, thumb.
		 *     @type string $proUrl        Upsell URL.
		 *     @type bool   $licensed  Whether Zen Addons Pro is licensed.
		 *     @type string $defaultLabel Label for the built-in reset card.
		 *     @type array  $i18n      Translated UI strings.
		 * }
		 */
		public function build_localized_data() {
			// The options + free-design helper live in the Alert Box widget file.
			if ( ! function_exists( 'zaso_alert_box_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Alert_Box_Widget', 'zaso-alert-box-widgets' );
			}
			if ( ! function_exists( 'zaso_alert_box_design_options' ) ) {
				return array();
			}

			$options  = zaso_alert_box_design_options();             // id => label.
			$previews = apply_filters( 'zaso_alert_design_previews', array() ); // id => url.

			if ( ! is_array( $options ) ) {
				return array();
			}

			$default_label = isset( $options[''] ) ? (string) $options[''] : esc_html__( 'Default (classic box)', 'zaso' );

			$cards = array();
			$ids   = array();
			foreach ( $options as $id => $label ) {
				$id = (string) $id;
				if ( '' === $id ) {
					continue; // Classic box: rendered as the built-in reset card client-side.
				}

				$cards[] = array(
					'id'    => $id,
					'label' => (string) $label,
					'isPro' => ! in_array( $id, self::FREE_IDS, true ),
					'img'   => ( is_array( $previews ) && isset( $previews[ $id ] ) ) ? esc_url( $previews[ $id ] ) : '',
				);
				$ids[] = $id;
			}

			if ( empty( $cards ) ) {
				return array();
			}

			$licensed    = ( class_exists( 'Zanp_Pro' ) && Zanp_Pro::is_licensed() );
			$white_label = ( class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label() );

			// Locked upsell cards: render-only previews of the twenty-four Pro
			// designs, shown blurred to unlicensed (non-white-label) sites to entice
			// an upgrade. Sourced from the bundled webp + a STATIC label list so no
			// Pro filter is required and an unlicensed site has no undefined access.
			// When licensed, the twenty-four are already real, usable cards above, so
			// the locked channel is empty. White-labelled sites never see an upsell.
			$locked = array();
			if ( ! $licensed && ! $white_label ) {
				foreach ( $this->locked_pro_designs() as $locked_id => $locked_label ) {
					$locked[] = array(
						'id'    => (string) $locked_id,
						'label' => (string) $locked_label,
						'thumb' => esc_url( ZASO_BASE_DIR . 'assets/design-previews/' . $locked_id . '.webp' ),
					);
				}
			}

			return array(
				'designs'       => $cards,
				'designIds'     => $ids,
				// Separate, render-only channel for the blurred Pro upsell cards. These
				// ids are deliberately NOT in designIds, so the JS never matches them
				// against, or writes them to, the design_variant <select>.
				'lockedDesigns' => $locked,
				'proUrl'        => self::PRO_URL,
				'licensed'      => $licensed,
				// White-labelled Pro sites must not expose the Free / Pro tier badges
				// to the agency's client. The JS hides every badge when this is true.
				'whiteLabel'    => $white_label,
				'defaultLabel'  => $default_label,
				'i18n'          => array(
					'browse'    => esc_html__( 'Browse designs', 'zaso' ),
					'choose'    => esc_html__( 'Choose a pre-made design', 'zaso' ),
					'subtitle'  => esc_html__( 'A pre-made design styles the whole alert in one click. Pick one, then Apply.', 'zaso' ),
					'free'      => esc_html__( 'Free', 'zaso' ),
					'pro'       => esc_html__( 'Pro', 'zaso' ),
					'locked'    => esc_html__( 'This design is part of Zen Addons Pro. Upgrade to use it.', 'zaso' ),
					'unlock'    => esc_html__( 'Unlock the full design library with Zen Addons Pro.', 'zaso' ),
					'unlockAll' => esc_html__( 'Unlock all designs with Pro', 'zaso' ),
					'close'     => esc_html__( 'Close', 'zaso' ),
					'apply'     => esc_html__( 'Apply', 'zaso' ),
					'cancel'    => esc_html__( 'Cancel', 'zaso' ),
				),
			);
		}

		/**
		 * Enqueue the picker script + style and localize the design map.
		 *
		 * Bails on every screen except the editors, and when the data map is empty
		 * (no Alert Box designs available), leaving the native dropdown untouched.
		 *
		 * @since 1.11.0
		 *
		 * @param string $hook Current admin page hook suffix.
		 */
		public function enqueue_assets( $hook ) {
			if ( ! $this->is_editor_screen( $hook ) ) {
				return;
			}

			$data = $this->build_localized_data();
			if ( empty( $data['designs'] ) ) {
				return;
			}

			wp_enqueue_style(
				self::HANDLE,
				ZASO_BASE_DIR . 'assets/css/design-picker.css',
				array(),
				ZASO_VERSION
			);

			wp_enqueue_script(
				self::HANDLE,
				ZASO_BASE_DIR . 'assets/js/design-picker.js',
				array(),
				ZASO_VERSION,
				true
			);

			wp_localize_script( self::HANDLE, 'ZasoDesignPicker', $data );
		}
	}

	new ZASO_Design_Picker();

endif; // class_exists checks.
