<?php
/**
 * Zen Addons "Visual Style Picker" editor enhancer.
 *
 * Progressively enhances every supported widget's `design_style` presets
 * <select> inside the Page Builder / widgets editor with a "Browse styles"
 * button that opens a modal gallery of rendered skin previews. Picking a card
 * sets the native <select> value and dispatches a `change` event so SiteOrigin's
 * own presets-field JS applies the preset values. The <select> stays in the DOM
 * as the source of truth and the fallback: if the JS or the data ever fails,
 * the native dropdown still works untouched.
 *
 * This class extends ZASO_Widget_Design purely to reuse its already-shipping,
 * audited preview helpers ( get_supported_widgets(), ensure_widget_class(),
 * get_widget_skins(), render_preview() ). It deliberately does NOT call the
 * parent constructor, so no second admin menu is registered.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.10.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ZASO_Style_Picker' ) && class_exists( 'ZASO_Widget_Design' ) ) :

	/**
	 * Class ZASO_Style_Picker
	 *
	 * Enqueues the style-picker assets on editor screens and localizes a map of
	 * every supported widget's skins, each with a rendered HTML preview.
	 *
	 * @since 1.10.5
	 */
	class ZASO_Style_Picker extends ZASO_Widget_Design {

		/**
		 * Script + style handle.
		 *
		 * @since 1.10.5
		 * @var string
		 */
		const HANDLE = 'zaso-style-picker';

		/**
		 * Widgets whose structural layout field is keyed `block_layout` instead of
		 * the usual `layout`. The CTA Banner already uses `layout` for the button
		 * placement (stacked / inline), so its STRUCTURAL layout lives under
		 * `block_layout` and must be read from there.
		 *
		 * @since 1.11.1
		 * @var array
		 */
		const BLOCK_LAYOUT_SLUGS = array( 'cta-banner' );

		/**
		 * Hook only the editor asset enqueue. The parent constructor is NOT called
		 * on purpose: this subclass must not register the Design Library menu again.
		 *
		 * @since 1.10.5
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * Editor screens where a SiteOrigin widget form (and thus a design_style
		 * presets field) can appear: the classic/block post editors and the
		 * legacy Widgets screen.
		 *
		 * @since  1.10.5
		 *
		 * @param  string $hook Current admin page hook suffix.
		 * @return bool True when the style picker should load.
		 */
		protected function is_editor_screen( $hook ) {
			return in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php' ), true );
		}

		/**
		 * Read the structural layout <select> options for a widget class.
		 *
		 * Mirrors get_widget_skins() but targets the layout field. The CTA Banner
		 * keys its structural layouts under `block_layout`; every other supported
		 * widget uses `layout`. Returns an ordered map of option id => label, or an
		 * empty array when the widget has no usable layout field.
		 *
		 * @since  1.11.1
		 *
		 * @param  string $class      Widget class name (already confirmed to exist).
		 * @param  string $layout_key The form_options key to read ( 'layout' | 'block_layout' ).
		 * @return array Ordered map of layout id => label, or an empty array.
		 */
		protected function get_widget_layouts( $class, $layout_key ) {
			if ( ! is_subclass_of( $class, 'SiteOrigin_Widget' ) ) {
				return array();
			}

			$widget = new $class();
			$form   = $widget->form_options();

			if (
				! is_array( $form )
				|| empty( $form[ $layout_key ]['options'] )
				|| ! is_array( $form[ $layout_key ]['options'] )
			) {
				return array();
			}

			return $form[ $layout_key ]['options'];
		}

		/**
		 * Pick the neutral preset values used to render the layout previews.
		 *
		 * Layout cards must show STRUCTURE, not colour, so every one is rendered
		 * with the same skin. The first preset (the canonical free "Indigo" scheme
		 * across the library) is a clean, light, neutral choice. Falls back to an
		 * empty array, in which case render_preview() uses its built-in defaults.
		 *
		 * @since  1.11.1
		 *
		 * @param  array $skins Ordered map of preset id => preset (label, values).
		 * @return array Nested preset values for the neutral skin.
		 */
		protected function neutral_layout_values( $skins ) {
			foreach ( $skins as $preset ) {
				if ( isset( $preset['values'] ) && is_array( $preset['values'] ) ) {
					return $preset['values'];
				}
			}

			return array();
		}

		/**
		 * Build the localized data map the picker JS consumes.
		 *
		 * Mirrors the Design Library loop: for every supported widget, derive its
		 * slug, ensure the class, read its live design_style presets (which already
		 * reflect the `zaso_design_presets` filter, so Pro skins only appear when
		 * licensed) and render an escaped HTML preview for each one.
		 *
		 * @since  1.10.5
		 * @return array {
		 *     @type array  $widgets Map of slug => array( label, skins[] ).
		 *     @type string $proUrl  Upsell URL for the locked Pro card.
		 *     @type array  $i18n    Translated UI strings.
		 * }
		 */
		public function build_localized_data() {
			$widgets = array();

			foreach ( $this->get_supported_widgets() as $class => $meta ) {
				if ( ! $this->ensure_widget_class( $class, $meta['folder'] ) ) {
					continue;
				}

				$skins = $this->get_widget_skins( $class );
				if ( empty( $skins ) ) {
					continue;
				}

				// Widget slug: folder basename without the zaso- prefix and -widgets suffix.
				$slug = preg_replace( array( '/^zaso-/', '/-widgets$/' ), '', $meta['folder'] );

				$cards = array();
				foreach ( $skins as $preset_id => $preset ) {
					$preset_id = (string) $preset_id;
					$label     = isset( $preset['label'] ) ? (string) $preset['label'] : $preset_id;
					$values    = ( isset( $preset['values'] ) && is_array( $preset['values'] ) ) ? $preset['values'] : array();
					$html      = $this->render_preview( $slug, $values );

					if ( '' === $html ) {
						continue; // Unknown slug; nothing to preview.
					}

					$cards[] = array(
						'id'    => $preset_id,
						'label' => $label,
						// The free core ships exactly these four palette schemes; every
						// other scheme is Pro. (The old 'pro_' prefix test broke when the
						// palette re-keyed to canonical scheme ids.)
						'isPro' => ! in_array( $preset_id, array( 'saas_indigo', 'dark_midnight', 'min_mono', 'bold_sunset' ), true ),
						'html'  => $html,
					);
				}

				if ( empty( $cards ) ) {
					continue;
				}

				// Structural layout variants (orthogonal to the colour skin). The
				// CTA Banner keys its structural layouts under `block_layout`; every
				// other widget uses `layout`. Each card renders the SAME neutral skin
				// so the preview shows the STRUCTURE, not the colours.
				$layout_key     = in_array( $slug, self::BLOCK_LAYOUT_SLUGS, true ) ? 'block_layout' : 'layout';
				$layout_options = $this->get_widget_layouts( $class, $layout_key );
				$neutral_values = $this->neutral_layout_values( $skins );

				$layout_cards = array();
				$layout_ids   = array();
				foreach ( $layout_options as $layout_id => $layout_label ) {
					$layout_id   = (string) $layout_id;
					$layout_html = $this->render_preview( $slug, $neutral_values, $layout_id );

					if ( '' === $layout_html ) {
						continue; // Unknown slug; nothing to preview.
					}

					$layout_cards[] = array(
						'id'    => $layout_id,
						'label' => (string) $layout_label,
						'html'  => $layout_html,
					);
					$layout_ids[] = $layout_id;
				}

				$widgets[ $slug ] = array(
					'label'     => $meta['label'],
					'skins'     => $cards,
					// Empty when a widget exposes no layout field, so the picker JS
					// simply omits the Layout section and behaves exactly as before.
					'layouts'   => $layout_cards,
					'layoutKey' => $layout_key,
					'layoutIds' => $layout_ids,
				);
			}

			return array(
				'widgets'  => $widgets,
				'proUrl'   => self::PRO_URL,
				// When a valid Pro license is active the user already has the full
				// library, so the JS hides the upsell footer.
				'licensed' => ( class_exists( 'Zanp_Pro' ) && Zanp_Pro::is_licensed() ),
				'i18n'    => array(
					'browse'     => esc_html__( 'Browse designs', 'zaso' ),
					'choose'     => esc_html__( 'Choose a design', 'zaso' ),
					'subtitle'   => esc_html__( 'Pick a layout structure, then a colour style.', 'zaso' ),
					'layout'     => esc_html__( 'Layout', 'zaso' ),
					'layoutHint' => esc_html__( 'Structure and shape', 'zaso' ),
					'style'      => esc_html__( 'Style', 'zaso' ),
					'styleHint'  => esc_html__( 'Colour scheme', 'zaso' ),
					'free'       => esc_html__( 'Free', 'zaso' ),
					'pro'        => esc_html__( 'Pro', 'zaso' ),
					'unlock'     => esc_html__( 'Unlock the full design library with Zen Addons Pro.', 'zaso' ),
					'close'      => esc_html__( 'Close', 'zaso' ),
				),
			);
		}

		/**
		 * Enqueue the picker script + style and localize the skin map.
		 *
		 * Bails on every screen except the editors. If the data map comes back
		 * empty (no supported widgets / classes), nothing is enqueued and the
		 * native presets dropdown is left exactly as SiteOrigin renders it.
		 *
		 * @since 1.10.5
		 *
		 * @param string $hook Current admin page hook suffix.
		 */
		public function enqueue_assets( $hook ) {
			if ( ! $this->is_editor_screen( $hook ) ) {
				return;
			}

			$data = $this->build_localized_data();
			if ( empty( $data['widgets'] ) ) {
				return;
			}

			wp_enqueue_style(
				self::HANDLE,
				ZASO_BASE_DIR . 'assets/css/style-picker.css',
				array(),
				ZASO_VERSION
			);

			wp_enqueue_script(
				self::HANDLE,
				ZASO_BASE_DIR . 'assets/js/style-picker.js',
				array(),
				ZASO_VERSION,
				true
			);

			// wp_localize_script json-encodes safely; render_preview() output is
			// trusted plugin markup (static copy + esc_attr'd colour values).
			wp_localize_script( self::HANDLE, 'ZasoStylePicker', $data );
		}
	}

	new ZASO_Style_Picker();

endif; // class_exists checks.
