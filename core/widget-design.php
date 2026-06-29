<?php
/**
 * Zen Addons "Widget Design" admin page.
 *
 * Renders a visual library of every supported widget's design skins. Free users
 * see the three free skins per widget plus a Pro upsell card; licensed users see
 * the full library (Zen Addons Pro appends its `pro_` prefixed presets through
 * the shared `zaso_design_presets` filter). It reads skins straight from each
 * widget class via form_options()['design_style']['options'] and stores nothing.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.10.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ZASO_Widget_Design' ) ) :

	/**
	 * Class ZASO_Widget_Design
	 *
	 * Registers and renders the read-only "Widget Design" skin gallery under the
	 * Zen Addons parent menu.
	 *
	 * @since 1.10.2
	 */
	class ZASO_Widget_Design {

		/**
		 * Parent menu slug (the Zen Addons top-level page).
		 *
		 * @since 1.10.2
		 * @var string
		 */
		const PARENT_SLUG = 'zen-addons';

		/**
		 * This page's submenu slug.
		 *
		 * @since 1.10.2
		 * @var string
		 */
		const MENU_SLUG = 'zen-addons-widget-design';

		/**
		 * Capability required to view the page. Matches the parent Zen Addons menu.
		 *
		 * @since 1.10.2
		 * @var string
		 */
		const CAPABILITY = 'manage_options';

		/**
		 * Upsell URL for the Pro library.
		 *
		 * @since 1.10.2
		 * @var string
		 */
		const PRO_URL = 'https://www.dopethemes.com/downloads/zen-addons-siteorigin/';

		/**
		 * Hook the submenu registration. Priority 11 ensures it runs after the
		 * parent Zen Addons menu (registered at the default priority 10).
		 *
		 * @since 1.10.2
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_menu' ), 11 );
		}

		/**
		 * Supported widgets: class name => array( label, folder basename ).
		 *
		 * The folder basename doubles as both the directory and the widget file
		 * stem under core/basic/, so a missing class can be lazily included.
		 *
		 * @since  1.10.2
		 * @return array
		 */
		public function get_supported_widgets() {
			return array(
				'Zen_Addons_SiteOrigin_Alert_Box_Widget'          => array(
					'label'  => esc_html__( 'Alert Box', 'zaso' ),
					'folder' => 'zaso-alert-box-widgets',
				),
				'Zen_Addons_SiteOrigin_Cta_Banner_Widget'         => array(
					'label'  => esc_html__( 'CTA Banner', 'zaso' ),
					'folder' => 'zaso-cta-banner-widgets',
				),
				'Zen_Addons_SiteOrigin_Pricing_Table_Widget'      => array(
					'label'  => esc_html__( 'Pricing Table', 'zaso' ),
					'folder' => 'zaso-pricing-table-widgets',
				),
				'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget' => array(
					'label'  => esc_html__( 'Testimonial Slider', 'zaso' ),
					'folder' => 'zaso-testimonial-slider-widgets',
				),
				'Zen_Addons_SiteOrigin_Counter_Widget'            => array(
					'label'  => esc_html__( 'Counter', 'zaso' ),
					'folder' => 'zaso-counter-widgets',
				),
				'Zen_Addons_SiteOrigin_Hover_Card_Widget'         => array(
					'label'  => esc_html__( 'Hover Card', 'zaso' ),
					'folder' => 'zaso-hover-card-widgets',
				),
				'Zen_Addons_SiteOrigin_Services_Grid_Widget'      => array(
					'label'  => esc_html__( 'Services Grid', 'zaso' ),
					'folder' => 'zaso-services-grid-widgets',
				),
			);
		}

		/**
		 * Register the "Widget Design" submenu under the Zen Addons parent.
		 *
		 * @since 1.10.2
		 */
		public function register_menu() {
			add_submenu_page(
				self::PARENT_SLUG,
				esc_html__( 'Templates', 'zaso' ),
				esc_html__( 'Templates', 'zaso' ),
				self::CAPABILITY,
				self::MENU_SLUG,
				array( $this, 'render_page' )
			);
		}

		/**
		 * Ensure a widget class is available, lazily including its file if needed.
		 *
		 * @since  1.10.2
		 *
		 * @param  string $class  Fully qualified widget class name.
		 * @param  string $folder Widget folder basename under core/basic/.
		 * @return bool True when the class is available.
		 */
		protected function ensure_widget_class( $class, $folder ) {
			if ( class_exists( $class ) ) {
				return true;
			}

			if ( defined( 'ZASO_WIDGET_BASIC_PATH' ) ) {
				$file = ZASO_WIDGET_BASIC_PATH . $folder . '/' . $folder . '.php';
				if ( file_exists( $file ) ) {
					include_once $file;
				}
			}

			return class_exists( $class );
		}

		/**
		 * Read the design_style preset options for a widget class.
		 *
		 * @since  1.10.2
		 *
		 * @param  string $class Widget class name (already confirmed to exist).
		 * @return array Preset options keyed by preset id, or an empty array.
		 */
		protected function get_widget_skins( $class ) {
			if ( ! is_subclass_of( $class, 'SiteOrigin_Widget' ) ) {
				return array();
			}

			$widget = new $class();
			$form   = $widget->form_options();

			if ( ! is_array( $form ) || empty( $form['design_style']['options'] ) || ! is_array( $form['design_style']['options'] ) ) {
				return array();
			}

			return $form['design_style']['options'];
		}

		/**
		 * Recursively pull a background-ish and a foreground-ish color from a
		 * preset's nested `values` array.
		 *
		 * Keys whose name contains background / bg / card_bg / caption_background
		 * map to the swatch background; keys containing font_color / text / title /
		 * number / quote / color (and not a background key) map to the foreground.
		 * The first valid hex color found for each role wins.
		 *
		 * @since  1.10.2
		 *
		 * @param  array       $values Nested preset values.
		 * @param  string|null $bg     Accumulated background color (by reference).
		 * @param  string|null $fg     Accumulated foreground color (by reference).
		 * @return void
		 */
		protected function scan_colors( $values, &$bg, &$fg, &$accent ) {
			if ( ! is_array( $values ) ) {
				return;
			}

			foreach ( $values as $key => $value ) {
				if ( is_array( $value ) ) {
					$this->scan_colors( $value, $bg, $fg, $accent );
					continue;
				}

				if ( ! is_string( $value ) || ! $this->is_hex_color( $value ) ) {
					continue;
				}

				$lower = is_string( $key ) ? strtolower( $key ) : '';

				// The colorful / brand surface (button fill, featured, accent, link, icon, star, active state).
				$is_accent = ( false !== strpos( $lower, 'button_bg' ) || false !== strpos( $lower, 'button_background' ) || false !== strpos( $lower, 'featured' ) || false !== strpos( $lower, 'accent' ) || false !== strpos( $lower, 'link' ) || false !== strpos( $lower, 'icon' ) || false !== strpos( $lower, 'star' ) || false !== strpos( $lower, 'active' ) );
				// The card / message background surface.
				$is_bg = ( false !== strpos( $lower, 'background' ) || false !== strpos( $lower, 'card_bg' ) || 'bg_color' === $lower || false !== strpos( $lower, '_bg' ) );
				// Body text. Exclude button text (its color pairs with the button fill, not the card).
				$is_btn_text = ( false !== strpos( $lower, 'button_text' ) || false !== strpos( $lower, 'button_font' ) );
				$is_fg       = ( ! $is_btn_text && ( false !== strpos( $lower, 'font_color' ) || false !== strpos( $lower, 'text' ) || false !== strpos( $lower, 'title' ) || false !== strpos( $lower, 'number' ) || false !== strpos( $lower, 'quote' ) || false !== strpos( $lower, 'description' ) || false !== strpos( $lower, 'name_color' ) ) );

				if ( $is_accent ) {
					if ( null === $accent ) {
						$accent = $value;
					}
				} elseif ( $is_bg ) {
					if ( null === $bg ) {
						$bg = $value;
					}
				} elseif ( $is_fg ) {
					if ( null === $fg ) {
						$fg = $value;
					}
				}
			}
		}

		/**
		 * Relative luminance of a hex color (WCAG).
		 *
		 * @param  string $hex Hex color.
		 * @return float
		 */
		protected function luminance( $hex ) {
			$hex = ltrim( (string) $hex, '#' );
			if ( 3 === strlen( $hex ) ) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}
			if ( 6 !== strlen( $hex ) ) {
				return 1.0;
			}
			$chan = array();
			foreach ( array( 0, 2, 4 ) as $i ) {
				$c      = hexdec( substr( $hex, $i, 2 ) ) / 255;
				$chan[] = ( $c <= 0.03928 ) ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
			}
			return 0.2126 * $chan[0] + 0.7152 * $chan[1] + 0.0722 * $chan[2];
		}

		/**
		 * WCAG contrast ratio between two hex colors.
		 *
		 * @param  string $a Hex color.
		 * @param  string $b Hex color.
		 * @return float
		 */
		protected function contrast( $a, $b ) {
			$la = $this->luminance( $a );
			$lb = $this->luminance( $b );
			return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
		}

		/**
		 * Pick a readable text color (dark or white) for a given background.
		 *
		 * @param  string $bg Background hex.
		 * @return string
		 */
		protected function readable_on( $bg ) {
			return ( $this->luminance( $bg ) > 0.4 ) ? '#0f172a' : '#ffffff';
		}

		/**
		 * Validate a 3 or 6 digit hex color string.
		 *
		 * @since  1.10.2
		 *
		 * @param  string $value Candidate value.
		 * @return bool
		 */
		protected function is_hex_color( $value ) {
			return (bool) preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', trim( $value ) );
		}

		/**
		 * Walk a nested preset `values` array along a dot path and return the leaf.
		 *
		 * Example: get_val( $values, 'design.message_box.message_background_color' ).
		 * Returns the default when any segment is missing or the leaf is not scalar.
		 *
		 * @since  1.10.3
		 *
		 * @param  array  $values  Nested preset values.
		 * @param  string $path    Dot separated key path.
		 * @param  string $default Fallback when the path is absent. Default empty string.
		 * @return string The leaf value cast to string, or the default.
		 */
		protected function get_val( $values, $path, $default = '' ) {
			$node = $values;

			foreach ( explode( '.', $path ) as $key ) {
				if ( is_array( $node ) && array_key_exists( $key, $node ) ) {
					$node = $node[ $key ];
					continue;
				}
				return $default;
			}

			if ( is_string( $node ) || is_numeric( $node ) ) {
				$value = (string) $node;
				return ( '' === $value ) ? $default : $value;
			}

			return $default;
		}

		/**
		 * Build a faithful, escaped mini HTML preview of a widget in a given skin.
		 *
		 * Each branch reads the preset's real colour values via get_val() and renders
		 * a roughly 110px tall facsimile of the widget. Every dynamic value lands
		 * inside esc_attr() before it touches a style attribute; the surrounding
		 * markup and copy are static, so the returned string is safe to echo raw.
		 *
		 * @since  1.10.3
		 *
		 * @param  string $slug   Widget slug (folder without the zaso- prefix and -widgets suffix).
		 * @param  array  $values Nested preset values array.
		 * @return string Preview HTML, or an empty string for an unknown slug.
		 */
		protected function render_preview( $slug, $values ) {
			switch ( $slug ) {

				case 'alert-box':
					$bg           = $this->get_val( $values, 'design.message_box.message_background_color', '#eff6ff' );
					$text         = $this->get_val( $values, 'design.message_box.message_font_color', '#1e3a8a' );
					$border_color = $this->get_val( $values, 'design.message_box.message_border.border_color', '#bfdbfe' );
					$border_style = $this->get_val( $values, 'design.message_box.message_border.border_style', 'solid' );
					$bw_left      = $this->get_val( $values, 'design.message_box.message_border.bw_left', '1px' );
					$radius       = $this->get_val( $values, 'design.message_box.message_border.br_top', '8px' );

					$base_border = ( 'none' === $border_style || '' === $border_style ) ? '0' : '1px ' . $border_style . ' ' . $border_color;
					$left_border = $bw_left . ' solid ' . $border_color;

					$style = 'background:' . esc_attr( $bg ) . ';color:' . esc_attr( $text ) . ';border:' . esc_attr( $base_border ) . ';border-left:' . esc_attr( $left_border ) . ';border-radius:' . esc_attr( $radius ) . ';';

					return '<div class="zaso-wd-pv zaso-wd-pv-alert" style="' . $style . '">'
						. '<strong>' . esc_html__( 'Heads up', 'zaso' ) . '</strong>'
						. '<span>' . esc_html__( 'This is an alert message.', 'zaso' ) . '</span>'
						. '</div>';

				case 'cta-banner':
					$bg            = $this->get_val( $values, 'design.background.bg_color', '#eff6ff' );
					$heading_color = $this->get_val( $values, 'design.typography.heading_color', '#1e3a8a' );
					$text_color    = $this->get_val( $values, 'design.typography.text_color', $heading_color );
					$button_bg     = $this->get_val( $values, 'design.button.button_bg', '#4f46e5' );
					$button_color  = $this->get_val( $values, 'design.button.button_color', '#ffffff' );
					$button_radius = $this->get_val( $values, 'design.button.button_radius', '6px' );

					$btn_style = 'background:' . esc_attr( $button_bg ) . ';color:' . esc_attr( $button_color ) . ';border-radius:' . esc_attr( $button_radius ) . ';';

					return '<div class="zaso-wd-pv zaso-wd-pv-cta" style="background:' . esc_attr( $bg ) . ';">'
						. '<span class="zaso-wd-pv-h" style="color:' . esc_attr( $heading_color ) . ';">' . esc_html__( 'Ready to start?', 'zaso' ) . '</span>'
						. '<span class="zaso-wd-pv-sub" style="color:' . esc_attr( $text_color ) . ';">' . esc_html__( 'Join us today.', 'zaso' ) . '</span>'
						. '<span class="zaso-wd-pv-btn" style="' . $btn_style . '">' . esc_html__( 'Get Started', 'zaso' ) . '</span>'
						. '</div>';

				case 'pricing-table':
					$card_bg     = $this->get_val( $values, 'design.card_bg', '#ffffff' );
					$card_border = $this->get_val( $values, 'design.card_border', '#e2e8f0' );
					$card_radius = $this->get_val( $values, 'design.card_radius', '12px' );
					$button_bg   = $this->get_val( $values, 'design.button_bg', '#4f46e5' );
					$button_text = $this->get_val( $values, 'design.button_text_color', '#ffffff' );
					$featured    = $this->get_val( $values, 'design.featured_color', '#4f46e5' );
					$price_color = $this->readable_on( $card_bg );

					$card_style = 'background:' . esc_attr( $card_bg ) . ';border:1px solid ' . esc_attr( $card_border ) . ';border-radius:' . esc_attr( $card_radius ) . ';';
					$btn_style  = 'background:' . esc_attr( $button_bg ) . ';color:' . esc_attr( $button_text ) . ';';

					return '<div class="zaso-wd-pv zaso-wd-pv-pricing" style="' . $card_style . '">'
						. '<span class="zaso-wd-pv-stripe" style="background:' . esc_attr( $featured ) . ';"></span>'
						. '<span class="zaso-wd-pv-price" style="color:' . esc_attr( $price_color ) . ';">$29</span>'
						. '<span class="zaso-wd-pv-feat"></span>'
						. '<span class="zaso-wd-pv-feat"></span>'
						. '<span class="zaso-wd-pv-btn" style="' . $btn_style . '">' . esc_html__( 'Choose', 'zaso' ) . '</span>'
						. '</div>';

				case 'testimonial-slider':
					$bg           = $this->get_val( $values, 'design.card_background', '#ffffff' );
					$quote_color  = $this->get_val( $values, 'design.quote_color', '#334155' );
					$author_color = $this->get_val( $values, 'design.author_name_color', '#0f172a' );
					$star_color   = $this->get_val( $values, 'design.star_color', '#b45309' );
					$radius       = $this->get_val( $values, 'design.card_border_radius', '12px' );

					$card_style = 'background:' . esc_attr( $bg ) . ';border-radius:' . esc_attr( $radius ) . ';';

					return '<div class="zaso-wd-pv zaso-wd-pv-testimonial" style="' . $card_style . '">'
						. '<span class="zaso-wd-pv-stars" style="color:' . esc_attr( $star_color ) . ';">&#9733;&#9733;&#9733;</span>'
						. '<span class="zaso-wd-pv-quote" style="color:' . esc_attr( $quote_color ) . ';">' . esc_html__( 'Great product, highly recommend.', 'zaso' ) . '</span>'
						. '<span class="zaso-wd-pv-author" style="color:' . esc_attr( $author_color ) . ';">' . esc_html__( 'Jane Doe', 'zaso' ) . '</span>'
						. '</div>';

				case 'counter':
					$number_color = $this->get_val( $values, 'design.number_color', '#1e293b' );
					$title_color  = $this->get_val( $values, 'design.title_color', '#475569' );
					$icon_color   = $this->get_val( $values, 'design.icon_color', '#4f46e5' );

					return '<div class="zaso-wd-pv zaso-wd-pv-counter" style="background:#ffffff;">'
						. '<span class="zaso-wd-pv-dot" style="background:' . esc_attr( $icon_color ) . ';"></span>'
						. '<span class="zaso-wd-pv-num" style="color:' . esc_attr( $number_color ) . ';">1,250</span>'
						. '<span class="zaso-wd-pv-lbl" style="color:' . esc_attr( $title_color ) . ';">' . esc_html__( 'Happy clients', 'zaso' ) . '</span>'
						. '</div>';

				case 'hover-card':
					$caption_bg   = $this->get_val( $values, 'design.hover_box.caption_background_color', '#4f46e5' );
					$caption_text = $this->get_val( $values, 'design.hover_box.caption_font_color', '#ffffff' );
					$button_bg    = $this->get_val( $values, 'design.modal_button.button_background_color', '#ffffff' );
					$button_text  = $this->get_val( $values, 'design.modal_button.button_font_color', '#4f46e5' );

					$caption_style = 'background:' . esc_attr( $caption_bg ) . ';color:' . esc_attr( $caption_text ) . ';';
					$btn_style     = 'background:' . esc_attr( $button_bg ) . ';color:' . esc_attr( $button_text ) . ';';

					return '<div class="zaso-wd-pv zaso-wd-pv-hover">'
						. '<span class="zaso-wd-pv-caption" style="' . $caption_style . '">'
						. '<span class="zaso-wd-pv-caption-t">' . esc_html__( 'Project title', 'zaso' ) . '</span>'
						. '<span class="zaso-wd-pv-btn" style="' . $btn_style . '">' . esc_html__( 'View', 'zaso' ) . '</span>'
						. '</span>'
						. '</div>';

				case 'services-grid':
					$bg          = $this->get_val( $values, 'design.card_background', '#f8fafc' );
					$icon_bg     = $this->get_val( $values, 'design.icon_bg', '' );
					$icon_color  = $this->get_val( $values, 'design.icon_color', '#4f46e5' );
					$title_color = $this->get_val( $values, 'design.title_color', '#0f172a' );
					$desc_color  = $this->get_val( $values, 'design.description_color', '#334155' );

					if ( '' !== $icon_bg ) {
						$chip_style = 'background:' . esc_attr( $icon_bg ) . ';color:' . esc_attr( $icon_color ) . ';border:0;';
					} else {
						$chip_style = 'background:transparent;color:' . esc_attr( $icon_color ) . ';border:2px solid ' . esc_attr( $icon_color ) . ';';
					}

					return '<div class="zaso-wd-pv zaso-wd-pv-services" style="background:' . esc_attr( $bg ) . ';">'
						. '<span class="zaso-wd-pv-chip" style="' . $chip_style . '">&#9679;</span>'
						. '<span class="zaso-wd-pv-title" style="color:' . esc_attr( $title_color ) . ';">' . esc_html__( 'Our Service', 'zaso' ) . '</span>'
						. '<span class="zaso-wd-pv-desc" style="color:' . esc_attr( $desc_color ) . ';">' . esc_html__( 'A short description line.', 'zaso' ) . '</span>'
						. '</div>';
			}

			return '';
		}

		/**
		 * Collect the Zen Addons section templates registered with SiteOrigin Page
		 * Builder's prebuilt "Layouts" browser.
		 *
		 * Reads the live `siteorigin_panels_prebuilt_layouts` filter and keeps only
		 * the ids namespaced with `zaso-section-` (so theme or third party layouts
		 * never leak in). Pro sections carry the `zaso-section-pro-` prefix and only
		 * appear once Zen Addons Pro is active and licensed, because the Pro plugin
		 * registers them through the same filter.
		 *
		 * @since  1.11.0
		 * @return array List of templates: id, name, desc, screenshot, isPro.
		 */
		public function get_section_templates() {
			$layouts = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );

			$templates = array();

			if ( ! is_array( $layouts ) ) {
				return $templates;
			}

			foreach ( $layouts as $id => $layout ) {
				$id = (string) $id;

				if ( 0 !== strpos( $id, 'zaso-section-' ) ) {
					continue;
				}

				if ( ! is_array( $layout ) ) {
					continue;
				}

				$templates[] = array(
					'id'         => $id,
					'name'       => isset( $layout['name'] ) ? (string) $layout['name'] : $id,
					'desc'       => isset( $layout['description'] ) ? (string) $layout['description'] : '',
					'screenshot' => isset( $layout['screenshot'] ) ? (string) $layout['screenshot'] : '',
					'isPro'      => ( 0 === strpos( $id, 'zaso-section-pro-' ) ),
				);
			}

			return $templates;
		}

		/**
		 * Render the Templates hub page.
		 *
		 * A showcase and quick-start guide for the Section Template Library. The
		 * sections themselves are inserted from Page Builder's own "Layouts"
		 * browser; this page is the gallery and the how-to, not the inserter.
		 *
		 * @since 1.11.0
		 */
		public function render_page() {
			if ( ! current_user_can( self::CAPABILITY ) ) {
				return;
			}

			$templates = $this->get_section_templates();

			// Pro is "unlocked" only when the Pro plugin is present and licensed.
			$licensed = ( class_exists( 'Zanp_Pro' ) && method_exists( 'Zanp_Pro', 'is_licensed' ) && Zanp_Pro::is_licensed() );

			$pro_count  = 0;
			$free_count = 0;
			foreach ( $templates as $tpl ) {
				if ( $tpl['isPro'] ) {
					++$pro_count;
				} else {
					++$free_count;
				}
			}

			$logo_url = defined( 'ZASO_BASE_DIR' )
				? ZASO_BASE_DIR . 'assets/img/zaso-logo.png'
				: plugins_url( 'assets/img/zaso-logo.png', dirname( __DIR__ ) . '/zen-addons-siteorigin.php' );
			?>
			<div class="wrap zaso-th">
				<style>
					.zaso-th { max-width: 1180px; }
					.zaso-th * { box-sizing: border-box; }

					/* ---- Hero ---- */
					.zaso-th .zaso-th-hero { background: linear-gradient( 180deg, #ffffff 0%, #f8fafc 100% ); border: 1px solid #e2e8f0; border-radius: 16px; padding: 28px 30px; margin: 16px 0 22px; box-shadow: 0 1px 2px rgba( 15, 23, 42, 0.04 ); }
					.zaso-th .zaso-th-brand { display: flex; align-items: center; gap: 11px; margin-bottom: 16px; }
					.zaso-th .zaso-th-brand img { width: 38px; height: 38px; border-radius: 9px; display: block; flex-shrink: 0; }
					.zaso-th .zaso-th-wordmark { font-size: 14.5px; font-weight: 700; color: #0f172a; letter-spacing: 0.2px; }
					.zaso-th .zaso-th-eyebrow { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #1d4ed8; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 999px; }
					.zaso-th .zaso-th-hero h1 { color: #0f172a; font-size: 28px; font-weight: 800; line-height: 1.15; margin: 0 0 7px; padding: 0; }
					.zaso-th .zaso-th-intro { color: #475569; font-size: 14.5px; line-height: 1.55; margin: 0; max-width: 720px; }

					/* ---- How-to strip ---- */
					.zaso-th .zaso-th-how { margin: 0 0 26px; }
					.zaso-th .zaso-th-how h2 { color: #0f172a; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; margin: 0 2px 12px; padding: 0; }
					.zaso-th .zaso-th-steps { display: grid; grid-template-columns: repeat( 3, 1fr ); gap: 14px; }
					.zaso-th .zaso-th-step { display: flex; align-items: flex-start; gap: 12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 16px; }
					.zaso-th .zaso-th-step .num { flex-shrink: 0; width: 26px; height: 26px; border-radius: 999px; background: #2563eb; color: #ffffff; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
					.zaso-th .zaso-th-step .txt { font-size: 13px; line-height: 1.45; color: #475569; }
					.zaso-th .zaso-th-step .txt b { color: #0f172a; font-weight: 700; }

					/* ---- Section heading ---- */
					.zaso-th .zaso-th-sec-head { display: flex; align-items: baseline; gap: 12px; margin: 0 2px 16px; }
					.zaso-th .zaso-th-sec-head h2 { color: #0f172a; font-size: 19px; font-weight: 800; margin: 0; padding: 0; border: 0; }
					.zaso-th .zaso-th-sec-head .meta { font-size: 13px; color: #64748b; }

					/* ---- Card grid ---- */
					.zaso-th .zaso-th-grid { display: grid; grid-template-columns: repeat( auto-fill, minmax( 296px, 1fr ) ); gap: 20px; }
					.zaso-th .zaso-th-card { display: flex; flex-direction: column; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba( 15, 23, 42, 0.04 ); transition: box-shadow 0.14s ease, transform 0.14s ease, border-color 0.14s ease; }
					.zaso-th .zaso-th-card:hover { box-shadow: 0 10px 26px rgba( 15, 23, 42, 0.10 ); transform: translateY( -3px ); border-color: #cbd5e1; }

					.zaso-th .zaso-th-thumb { position: relative; aspect-ratio: 16 / 10; background: #f1f5f9; overflow: hidden; border-bottom: 1px solid #e2e8f0; }
					.zaso-th .zaso-th-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: top center; display: block; }
					/* Top scrim keeps the corner badge legible over any screenshot. */
					.zaso-th .zaso-th-thumb::after { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 60px; z-index: 1; pointer-events: none; background: linear-gradient( 180deg, rgba( 15, 23, 42, 0.16 ) 0%, rgba( 15, 23, 42, 0 ) 100% ); }

					/* Placeholder sits behind the screenshot so a missing image still looks intentional. */
					.zaso-th .zaso-th-ph { position: absolute; inset: 0; display: flex; flex-direction: column; gap: 9px; padding: 22px; background: linear-gradient( 135deg, #eef2ff 0%, #f0f9ff 100% ); }
					.zaso-th .zaso-th-ph i { display: block; border-radius: 5px; background: #ffffff; box-shadow: 0 1px 2px rgba( 15, 23, 42, 0.06 ); }
					.zaso-th .zaso-th-ph i.b1 { height: 16px; width: 52%; }
					.zaso-th .zaso-th-ph i.b2 { height: 9px; width: 78%; background: #dbeafe; box-shadow: none; }
					.zaso-th .zaso-th-ph i.b3 { height: 9px; width: 64%; background: #dbeafe; box-shadow: none; }
					.zaso-th .zaso-th-ph i.b4 { margin-top: auto; height: 26px; width: 38%; background: #2563eb; box-shadow: none; }

					.zaso-th .zaso-th-badge { position: absolute; top: 12px; right: 12px; z-index: 2; display: inline-flex; align-items: center; gap: 5px; font-size: 10.5px; line-height: 1; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; padding: 5px 10px; border-radius: 999px; white-space: nowrap; backdrop-filter: saturate( 180% ) blur( 4px ); }
					.zaso-th .zaso-th-badge.is-free { color: #334155; background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba( 15, 23, 42, 0.16 ); }
					.zaso-th .zaso-th-badge.is-pro { color: #ffffff; background: #15803d; box-shadow: 0 1px 3px rgba( 21, 128, 61, 0.4 ); }
					.zaso-th .zaso-th-badge.is-pro .dot { width: 5px; height: 5px; border-radius: 999px; background: #bbf7d0; }

					.zaso-th .zaso-th-body { padding: 15px 16px 17px; display: flex; flex-direction: column; gap: 5px; }
					.zaso-th .zaso-th-body h3 { color: #0f172a; font-size: 15px; font-weight: 700; line-height: 1.3; margin: 0; padding: 0; }
					.zaso-th .zaso-th-body p { color: #475569; font-size: 13px; line-height: 1.5; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

					/* ---- Upsell card ---- */
					.zaso-th .zaso-th-upsell { display: flex; flex-direction: column; justify-content: center; gap: 8px; padding: 24px; text-decoration: none; background: linear-gradient( 160deg, #f0fdf4 0%, #ffffff 70% ); border: 1px dashed #86efac; border-radius: 12px; transition: border-color 0.14s ease, box-shadow 0.14s ease; }
					.zaso-th .zaso-th-upsell:hover { border-color: #22c55e; box-shadow: 0 10px 26px rgba( 21, 128, 61, 0.12 ); }
					.zaso-th .zaso-th-upsell .ic { width: 42px; height: 42px; border-radius: 11px; background: #15803d; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 4px; }
					.zaso-th .zaso-th-upsell strong { display: block; color: #166534; font-size: 16px; font-weight: 800; line-height: 1.25; }
					.zaso-th .zaso-th-upsell span { color: #475569; font-size: 13px; line-height: 1.5; }
					.zaso-th .zaso-th-upsell .cta { align-self: flex-start; margin-top: 6px; font-size: 13px; font-weight: 700; color: #ffffff; background: #15803d; padding: 9px 16px; border-radius: 10px; }

					/* ---- Empty state ---- */
					.zaso-th .zaso-th-empty { border: 1px dashed #cbd5e1; border-radius: 12px; padding: 40px 24px; text-align: center; color: #64748b; font-size: 14px; background: #f8fafc; }

					@media ( max-width: 782px ) {
						.zaso-th .zaso-th-hero { padding: 22px; }
						.zaso-th .zaso-th-hero h1 { font-size: 23px; }
						.zaso-th .zaso-th-steps { grid-template-columns: 1fr; }
						.zaso-th .zaso-th-grid { grid-template-columns: 1fr; gap: 16px; }
					}
				</style>

				<div class="zaso-th-hero">
					<div class="zaso-th-brand">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="" width="38" height="38" />
						<span class="zaso-th-wordmark"><?php echo esc_html__( 'Zen Addons', 'zaso' ); ?></span>
						<span class="zaso-th-eyebrow"><?php echo esc_html__( 'Templates', 'zaso' ); ?></span>
					</div>
					<h1><?php echo esc_html__( 'Templates', 'zaso' ); ?></h1>
					<p class="zaso-th-intro"><?php echo esc_html__( 'Ready-to-insert sections, designed with Zen Addons widgets.', 'zaso' ); ?></p>
				</div>

				<div class="zaso-th-how">
					<h2><?php echo esc_html__( 'How to add a template', 'zaso' ); ?></h2>
					<div class="zaso-th-steps">
						<div class="zaso-th-step">
							<span class="num">1</span>
							<span class="txt"><?php
								printf(
									/* translators: %s: the page editor name (Page Builder). */
									esc_html__( 'Edit a page with %s.', 'zaso' ),
									'<b>' . esc_html__( 'Page Builder', 'zaso' ) . '</b>'
								);
							?></span>
						</div>
						<div class="zaso-th-step">
							<span class="num">2</span>
							<span class="txt"><?php
								printf(
									/* translators: %s: the Page Builder browser tab name (Layouts). */
									esc_html__( 'Open the %s tab.', 'zaso' ),
									'<b>' . esc_html__( 'Layouts', 'zaso' ) . '</b>'
								);
							?></span>
						</div>
						<div class="zaso-th-step">
							<span class="num">3</span>
							<span class="txt"><?php echo wp_kses( __( 'Choose a <b>Zen Addons section</b> to insert it.', 'zaso' ), array( 'b' => array() ) ); ?></span>
						</div>
					</div>
				</div>

				<div class="zaso-th-sec-head">
					<h2><?php echo esc_html__( 'Section templates', 'zaso' ); ?></h2>
					<span class="meta"><?php
						printf(
							/* translators: 1: number of free templates, 2: number of Pro templates. */
							esc_html__( '%1$d free, %2$d Pro', 'zaso' ),
							(int) $free_count,
							(int) $pro_count
						);
					?></span>
				</div>

				<?php if ( empty( $templates ) && $licensed ) : ?>
					<div class="zaso-th-empty"><?php echo esc_html__( 'No section templates are registered yet.', 'zaso' ); ?></div>
				<?php else : ?>
					<div class="zaso-th-grid">
						<?php foreach ( $templates as $tpl ) : ?>
							<div class="zaso-th-card">
								<div class="zaso-th-thumb">
									<span class="zaso-th-ph" aria-hidden="true">
										<i class="b1"></i><i class="b2"></i><i class="b3"></i><i class="b4"></i>
									</span>
									<?php if ( '' !== $tpl['screenshot'] ) : ?>
										<img src="<?php echo esc_url( $tpl['screenshot'] ); ?>" alt="<?php echo esc_attr( $tpl['name'] ); ?>" loading="lazy" />
									<?php endif; ?>
									<?php if ( $tpl['isPro'] ) : ?>
										<span class="zaso-th-badge is-pro"><span class="dot"></span><?php echo esc_html__( 'Pro', 'zaso' ); ?></span>
									<?php else : ?>
										<span class="zaso-th-badge is-free"><?php echo esc_html__( 'Free', 'zaso' ); ?></span>
									<?php endif; ?>
								</div>
								<div class="zaso-th-body">
									<h3><?php echo esc_html( $tpl['name'] ); ?></h3>
									<?php if ( '' !== $tpl['desc'] ) : ?>
										<p><?php echo esc_html( $tpl['desc'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>

						<?php if ( ! $licensed ) : ?>
							<a class="zaso-th-upsell" href="<?php echo esc_url( self::PRO_URL ); ?>" target="_blank" rel="noopener">
								<span class="ic" aria-hidden="true">&#9889;</span>
								<strong><?php echo esc_html__( 'Unlock the full Pro template library', 'zaso' ); ?></strong>
								<span><?php echo esc_html__( 'Get premium sections built with Zen Addons widgets, plus every Pro widget and style.', 'zaso' ); ?></span>
								<span class="cta"><?php echo esc_html__( 'Get Zen Addons Pro', 'zaso' ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/*
	 * The standalone "Templates" admin page is retired: section templates are
	 * browsed + inserted natively in Page Builder's Layouts tab, skins via the
	 * in-editor style picker, and "what you get" lives on the Pro Widgets
	 * overview page. This class is intentionally NOT instantiated (no menu/page)
	 * but stays defined because ZASO_Style_Picker extends it to reuse its
	 * widget-skin/preview helpers.
	 */

endif; // class_exists check.
