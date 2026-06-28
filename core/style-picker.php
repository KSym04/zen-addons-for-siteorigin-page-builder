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
						'isPro' => ( 0 === strpos( $preset_id, 'pro_' ) ),
						'html'  => $html,
					);
				}

				if ( empty( $cards ) ) {
					continue;
				}

				$widgets[ $slug ] = array(
					'label' => $meta['label'],
					'skins' => $cards,
				);
			}

			return array(
				'widgets' => $widgets,
				'proUrl'  => self::PRO_URL,
				'i18n'    => array(
					'browse' => esc_html__( 'Browse styles', 'zaso' ),
					'choose' => esc_html__( 'Choose a style', 'zaso' ),
					'free'   => esc_html__( 'Free', 'zaso' ),
					'pro'    => esc_html__( 'Pro', 'zaso' ),
					'unlock' => esc_html__( 'Unlock the full design library with Zen Addons Pro.', 'zaso' ),
					'close'  => esc_html__( 'Close', 'zaso' ),
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
