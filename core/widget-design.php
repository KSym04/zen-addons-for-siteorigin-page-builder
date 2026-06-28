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
				esc_html__( 'Design Library', 'zaso' ),
				esc_html__( 'Design Library', 'zaso' ),
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
		 * Render the Widget Design gallery page.
		 *
		 * @since 1.10.2
		 */
		public function render_page() {
			if ( ! current_user_can( self::CAPABILITY ) ) {
				return;
			}

			// Build the section model up front so the hero can report accurate totals.
			$widgets    = $this->get_supported_widgets();
			$sections   = array();
			$total_free = 0;
			$total_pro  = 0;

			foreach ( $widgets as $class => $meta ) {
				if ( ! $this->ensure_widget_class( $class, $meta['folder'] ) ) {
					continue;
				}

				$skins = $this->get_widget_skins( $class );
				if ( empty( $skins ) ) {
					continue;
				}

				// Widget slug: folder basename without the zaso- prefix and -widgets suffix.
				$slug = preg_replace( array( '/^zaso-/', '/-widgets$/' ), '', $meta['folder'] );

				$free = array();
				$pro  = array();
				foreach ( $skins as $preset_id => $preset ) {
					$preset_id = (string) $preset_id;
					if ( 0 === strpos( $preset_id, 'pro_' ) ) {
						$pro[ $preset_id ] = $preset;
					} else {
						$free[ $preset_id ] = $preset;
					}
				}

				$total_free += count( $free );
				$total_pro  += count( $pro );

				$sections[] = array(
					'label'  => $meta['label'],
					'slug'   => $slug,
					'anchor' => 'zaso-wd-w-' . $slug,
					'free'   => $free,
					'pro'    => $pro,
				);
			}

			$logo_url = defined( 'ZASO_BASE_DIR' )
				? ZASO_BASE_DIR . 'assets/img/zaso-logo.png'
				: plugins_url( 'assets/img/zaso-logo.png', dirname( __DIR__ ) . '/zen-addons-siteorigin.php' );
			?>
			<div class="wrap zaso-wd">
				<style>
					.zaso-wd { max-width: 1240px; }
					.zaso-wd * { box-sizing: border-box; }

					/* ---- Hero ---- */
					.zaso-wd .zaso-wd-hero { background: linear-gradient( 180deg, #ffffff 0%, #f8fafc 100% ); border: 1px solid #e2e8f0; border-radius: 18px; padding: 26px 28px; margin: 16px 0 8px; box-shadow: 0 1px 2px rgba( 15, 23, 42, 0.04 ); }
					.zaso-wd .zaso-wd-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
					.zaso-wd .zaso-wd-brand img { width: 40px; height: 40px; border-radius: 10px; display: block; flex-shrink: 0; }
					.zaso-wd .zaso-wd-wordmark { font-size: 15px; font-weight: 700; color: #0f172a; letter-spacing: 0.2px; }
					.zaso-wd .zaso-wd-eyebrow { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 999px; }
					.zaso-wd .zaso-wd-hero h1 { color: #0f172a; font-size: 28px; font-weight: 800; line-height: 1.15; margin: 0 0 6px; padding: 0; }
					.zaso-wd .zaso-wd-intro { color: #475569; font-size: 14.5px; line-height: 1.55; margin: 0; max-width: 760px; }

					/* How-to steps */
					.zaso-wd .zaso-wd-steps { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
					.zaso-wd .zaso-wd-step { display: flex; align-items: flex-start; gap: 10px; flex: 1 1 200px; min-width: 200px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; }
					.zaso-wd .zaso-wd-step .num { flex-shrink: 0; width: 24px; height: 24px; border-radius: 999px; background: #15803d; color: #ffffff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
					.zaso-wd .zaso-wd-step .txt { font-size: 12.5px; line-height: 1.4; color: #334155; }
					.zaso-wd .zaso-wd-step .txt b { color: #0f172a; }

					/* Stat + legend bar */
					.zaso-wd .zaso-wd-stats { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 18px; margin-top: 18px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
					.zaso-wd .zaso-wd-stat { font-size: 13px; color: #475569; }
					.zaso-wd .zaso-wd-stat b { color: #0f172a; font-weight: 700; }
					.zaso-wd .zaso-wd-legend { display: flex; align-items: center; gap: 12px; margin-left: auto; }
					.zaso-wd .zaso-wd-legend span { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #475569; }

					/* ---- Sticky widget nav ---- */
					.zaso-wd .zaso-wd-nav { position: sticky; top: 32px; z-index: 20; display: flex; flex-wrap: wrap; gap: 8px; padding: 12px; margin: 16px 0 8px; background: rgba( 255, 255, 255, 0.9 ); backdrop-filter: saturate( 180% ) blur( 8px ); border: 1px solid #e2e8f0; border-radius: 14px; }
					.zaso-wd .zaso-wd-nav a { display: inline-flex; align-items: center; gap: 7px; text-decoration: none; font-size: 13px; font-weight: 600; color: #334155; background: #f1f5f9; border: 1px solid transparent; padding: 7px 12px; border-radius: 999px; transition: all 0.12s ease; }
					.zaso-wd .zaso-wd-nav a:hover { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
					.zaso-wd .zaso-wd-nav a .ct { font-size: 11px; font-weight: 700; color: #64748b; background: #ffffff; border-radius: 999px; padding: 1px 7px; }

					/* ---- Section ---- */
					.zaso-wd .zaso-wd-section { scroll-margin-top: 96px; margin: 28px 0 0; }
					.zaso-wd .zaso-wd-section-head { display: flex; align-items: baseline; gap: 12px; margin: 0 0 14px; }
					.zaso-wd h2 { color: #0f172a; font-size: 20px; font-weight: 800; margin: 0; padding: 0; border: 0; }
					.zaso-wd .zaso-wd-section-head .meta { font-size: 13px; color: #64748b; }

					/* Free / Pro groups */
					.zaso-wd .zaso-wd-group { border-radius: 16px; padding: 16px 16px 18px; margin-bottom: 14px; }
					.zaso-wd .zaso-wd-group.is-free { background: #f8fafc; border: 1px solid #eef2f6; }
					.zaso-wd .zaso-wd-group.is-pro { background: linear-gradient( 180deg, #f0fdf4 0%, #f7fef9 100% ); border: 1px solid #dcfce7; }
					.zaso-wd .zaso-wd-group-label { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.6px; margin: 0 2px 12px; }
					.zaso-wd .zaso-wd-group.is-pro .zaso-wd-group-label { color: #166534; }
					.zaso-wd .zaso-wd-group-label .hint { margin-left: auto; font-size: 11px; font-weight: 500; letter-spacing: 0; text-transform: none; color: #64748b; }

					.zaso-wd .zaso-wd-grid { display: grid; grid-template-columns: repeat( auto-fill, minmax( 224px, 1fr ) ); gap: 16px; }
					.zaso-wd .zaso-wd-card { border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px; background: #ffffff; box-shadow: 0 1px 2px rgba( 15, 23, 42, 0.04 ); transition: box-shadow 0.12s ease, transform 0.12s ease; }
					.zaso-wd .zaso-wd-card:hover { box-shadow: 0 6px 18px rgba( 15, 23, 42, 0.08 ); transform: translateY( -2px ); }
					.zaso-wd .zaso-wd-pv-frame { border-radius: 10px; padding: 12px; background: linear-gradient( 180deg, #f8fafc 0%, #f1f5f9 100% ); border: 1px solid #eef2f6; }

					/* Shared preview frame. Each skin renders a roughly 140px facsimile of the real widget. */
					.zaso-wd .zaso-wd-pv { height: 140px; border-radius: 8px; box-sizing: border-box; overflow: hidden; display: flex; flex-direction: column; font-family: -apple-system, "Segoe UI", Roboto, sans-serif; box-shadow: 0 1px 3px rgba( 15, 23, 42, 0.10 ); }

					/* Alert Box. */
					.zaso-wd .zaso-wd-pv-alert { justify-content: center; gap: 4px; padding: 14px; }
					.zaso-wd .zaso-wd-pv-alert strong { font-size: 13px; line-height: 1.2; }
					.zaso-wd .zaso-wd-pv-alert span { font-size: 12px; line-height: 1.4; }

					/* CTA Banner. */
					.zaso-wd .zaso-wd-pv-cta { justify-content: center; align-items: center; text-align: center; gap: 6px; padding: 12px; }
					.zaso-wd .zaso-wd-pv-cta .zaso-wd-pv-h { font-size: 14px; font-weight: 700; line-height: 1.2; }
					.zaso-wd .zaso-wd-pv-cta .zaso-wd-pv-sub { font-size: 11px; line-height: 1.3; opacity: 0.9; }
					.zaso-wd .zaso-wd-pv-cta .zaso-wd-pv-btn { margin-top: 2px; font-size: 11px; font-weight: 600; padding: 6px 12px; }

					/* Pricing Table. */
					.zaso-wd .zaso-wd-pv-pricing { position: relative; align-items: center; justify-content: center; gap: 5px; padding: 16px 12px 12px; }
					.zaso-wd .zaso-wd-pv-pricing .zaso-wd-pv-stripe { position: absolute; top: 0; left: 0; right: 0; height: 4px; }
					.zaso-wd .zaso-wd-pv-pricing .zaso-wd-pv-price { font-size: 22px; font-weight: 800; line-height: 1; }
					.zaso-wd .zaso-wd-pv-pricing .zaso-wd-pv-feat { width: 70%; height: 5px; border-radius: 3px; background: #e2e8f0; }
					.zaso-wd .zaso-wd-pv-pricing .zaso-wd-pv-btn { margin-top: 4px; width: 82%; text-align: center; font-size: 11px; font-weight: 600; padding: 6px 0; border-radius: 6px; }

					/* Testimonial Slider. */
					.zaso-wd .zaso-wd-pv-testimonial { justify-content: center; gap: 6px; padding: 14px; }
					.zaso-wd .zaso-wd-pv-testimonial .zaso-wd-pv-stars { font-size: 13px; letter-spacing: 3px; line-height: 1; }
					.zaso-wd .zaso-wd-pv-testimonial .zaso-wd-pv-quote { font-size: 12px; line-height: 1.4; font-style: italic; }
					.zaso-wd .zaso-wd-pv-testimonial .zaso-wd-pv-author { font-size: 11px; font-weight: 700; }

					/* Counter. */
					.zaso-wd .zaso-wd-pv-counter { justify-content: center; align-items: center; gap: 4px; padding: 12px; border: 1px solid #e2e8f0; }
					.zaso-wd .zaso-wd-pv-counter .zaso-wd-pv-dot { width: 10px; height: 10px; border-radius: 999px; margin-bottom: 2px; }
					.zaso-wd .zaso-wd-pv-counter .zaso-wd-pv-num { font-size: 26px; font-weight: 800; line-height: 1; }
					.zaso-wd .zaso-wd-pv-counter .zaso-wd-pv-lbl { font-size: 11px; }

					/* Hover Card. */
					.zaso-wd .zaso-wd-pv-hover { justify-content: flex-end; background: linear-gradient( 135deg, #cbd5e1, #94a3b8 ); }
					.zaso-wd .zaso-wd-pv-hover .zaso-wd-pv-caption { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 10px; }
					.zaso-wd .zaso-wd-pv-hover .zaso-wd-pv-caption-t { font-size: 12px; font-weight: 600; }
					.zaso-wd .zaso-wd-pv-hover .zaso-wd-pv-btn { font-size: 10px; font-weight: 600; padding: 4px 8px; border-radius: 4px; white-space: nowrap; }

					/* Services Grid. */
					.zaso-wd .zaso-wd-pv-services { justify-content: center; gap: 5px; padding: 14px; }
					.zaso-wd .zaso-wd-pv-services .zaso-wd-pv-chip { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; line-height: 1; box-sizing: border-box; }
					.zaso-wd .zaso-wd-pv-services .zaso-wd-pv-title { font-size: 13px; font-weight: 700; }
					.zaso-wd .zaso-wd-pv-services .zaso-wd-pv-desc { font-size: 11px; line-height: 1.4; }

					.zaso-wd .zaso-wd-meta { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 11px; }
					.zaso-wd .zaso-wd-label { color: #0f172a; font-size: 13px; font-weight: 600; line-height: 1.2; }
					.zaso-wd .zaso-wd-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; line-height: 1; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; padding: 5px 9px; border-radius: 999px; white-space: nowrap; }
					.zaso-wd .zaso-wd-badge.is-free { color: #475569; background: #eef2f6; border: 1px solid #e2e8f0; }
					.zaso-wd .zaso-wd-badge.is-pro { color: #ffffff; background: #15803d; box-shadow: 0 1px 2px rgba( 21, 128, 61, 0.35 ); }
					.zaso-wd .zaso-wd-badge.is-pro .dot { width: 5px; height: 5px; border-radius: 999px; background: #bbf7d0; }

					/* Locked / unlock affordance for Pro when unlicensed. */
					.zaso-wd .zaso-wd-unlock { grid-column: 1 / -1; display: flex; align-items: center; gap: 16px; border: 1px dashed #86efac; border-radius: 14px; padding: 18px 20px; background: #ffffff; text-decoration: none; }
					.zaso-wd .zaso-wd-unlock .ic { flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px; background: #15803d; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 20px; }
					.zaso-wd .zaso-wd-unlock .body { flex: 1 1 auto; }
					.zaso-wd .zaso-wd-unlock strong { display: block; color: #166534; font-size: 15px; margin-bottom: 2px; }
					.zaso-wd .zaso-wd-unlock span { color: #475569; font-size: 13px; }
					.zaso-wd .zaso-wd-unlock .cta { flex-shrink: 0; font-size: 13px; font-weight: 700; color: #ffffff; background: #15803d; padding: 9px 16px; border-radius: 10px; }

					@media ( max-width: 782px ) {
						.zaso-wd .zaso-wd-hero { padding: 20px; }
						.zaso-wd .zaso-wd-hero h1 { font-size: 23px; }
						.zaso-wd .zaso-wd-legend { margin-left: 0; }
						.zaso-wd .zaso-wd-nav { top: 46px; }
						.zaso-wd .zaso-wd-grid { grid-template-columns: repeat( auto-fill, minmax( 160px, 1fr ) ); gap: 12px; }
						.zaso-wd .zaso-wd-unlock { flex-direction: column; align-items: flex-start; }
					}
				</style>

				<div class="zaso-wd-hero">
					<div class="zaso-wd-brand">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="" width="40" height="40" />
						<span class="zaso-wd-wordmark"><?php echo esc_html__( 'Zen Addons', 'zaso' ); ?></span>
						<span class="zaso-wd-eyebrow"><?php echo esc_html__( 'Design Library', 'zaso' ); ?></span>
					</div>
					<h1><?php echo esc_html__( 'Design Library', 'zaso' ); ?></h1>
					<p class="zaso-wd-intro"><?php echo esc_html__( 'A live gallery of every ready made skin for your Zen Addons widgets. Browse the looks below, then apply one in the Page Builder editor and fine tune the colors to match your brand.', 'zaso' ); ?></p>

					<div class="zaso-wd-steps">
						<div class="zaso-wd-step">
							<span class="num">1</span>
							<span class="txt"><?php
								printf(
									/* translators: %s: the Page Builder widget tab name. */
									esc_html__( 'Add a Zen Addons widget in %s.', 'zaso' ),
									'<b>' . esc_html__( 'Page Builder', 'zaso' ) . '</b>'
								);
							?></span>
						</div>
						<div class="zaso-wd-step">
							<span class="num">2</span>
							<span class="txt"><?php
								printf(
									/* translators: %s: the widget field label that holds the skins. */
									esc_html__( 'Open its %s dropdown.', 'zaso' ),
									'<b>' . esc_html__( 'Design Style', 'zaso' ) . '</b>'
								);
							?></span>
						</div>
						<div class="zaso-wd-step">
							<span class="num">3</span>
							<span class="txt"><?php echo wp_kses( __( 'Pick a skin, then <b>tweak the colors</b> to taste.', 'zaso' ), array( 'b' => array() ) ); ?></span>
						</div>
					</div>

					<div class="zaso-wd-stats">
						<span class="zaso-wd-stat"><b><?php echo esc_html( number_format_i18n( count( $sections ) ) ); ?></b> <?php echo esc_html__( 'widgets', 'zaso' ); ?></span>
						<span class="zaso-wd-stat"><b><?php echo esc_html( number_format_i18n( $total_free ) ); ?></b> <?php echo esc_html__( 'free skins', 'zaso' ); ?></span>
						<span class="zaso-wd-stat"><b><?php echo esc_html( number_format_i18n( $total_pro ) ); ?></b> <?php echo esc_html__( 'Pro skins', 'zaso' ); ?></span>
						<span class="zaso-wd-legend">
							<span><span class="zaso-wd-badge is-free"><?php echo esc_html__( 'Free', 'zaso' ); ?></span><?php echo esc_html__( 'included', 'zaso' ); ?></span>
							<span><span class="zaso-wd-badge is-pro"><span class="dot"></span><?php echo esc_html__( 'Pro', 'zaso' ); ?></span><?php echo esc_html__( 'with a license', 'zaso' ); ?></span>
						</span>
					</div>
				</div>

				<?php if ( ! empty( $sections ) ) : ?>
				<nav class="zaso-wd-nav" aria-label="<?php echo esc_attr__( 'Jump to widget', 'zaso' ); ?>">
					<?php foreach ( $sections as $section ) : ?>
						<a href="#<?php echo esc_attr( $section['anchor'] ); ?>">
							<?php echo esc_html( $section['label'] ); ?>
							<span class="ct"><?php echo esc_html( number_format_i18n( count( $section['free'] ) + count( $section['pro'] ) ) ); ?></span>
						</a>
					<?php endforeach; ?>
				</nav>
				<?php endif; ?>

				<?php foreach ( $sections as $section ) : ?>
					<section id="<?php echo esc_attr( $section['anchor'] ); ?>" class="zaso-wd-section">
						<div class="zaso-wd-section-head">
							<h2><?php echo esc_html( $section['label'] ); ?></h2>
							<span class="meta"><?php
								printf(
									/* translators: 1: number of free skins, 2: number of Pro skins. */
									esc_html__( '%1$d free, %2$d Pro', 'zaso' ),
									(int) count( $section['free'] ),
									(int) count( $section['pro'] )
								);
							?></span>
						</div>

						<?php if ( ! empty( $section['free'] ) ) : ?>
						<div class="zaso-wd-group is-free">
							<div class="zaso-wd-group-label">
								<?php echo esc_html__( 'Free skins', 'zaso' ); ?>
								<span class="hint"><?php echo esc_html__( 'included in Zen Addons', 'zaso' ); ?></span>
							</div>
							<div class="zaso-wd-grid">
								<?php $this->render_cards( $section['free'], $section['slug'], false ); ?>
							</div>
						</div>
						<?php endif; ?>

						<div class="zaso-wd-group is-pro">
							<div class="zaso-wd-group-label">
								<?php echo esc_html__( 'Pro library', 'zaso' ); ?>
								<span class="hint"><?php echo esc_html__( 'Zen Addons Pro', 'zaso' ); ?></span>
							</div>
							<div class="zaso-wd-grid">
								<?php if ( ! empty( $section['pro'] ) ) : ?>
									<?php $this->render_cards( $section['pro'], $section['slug'], true ); ?>
								<?php else : ?>
									<a class="zaso-wd-unlock" href="<?php echo esc_url( self::PRO_URL ); ?>" target="_blank" rel="noopener">
										<span class="ic" aria-hidden="true">&#128274;</span>
										<span class="body">
											<strong><?php
												printf(
													/* translators: %s: widget label. */
													esc_html__( '8 more %s skins with Pro', 'zaso' ),
													esc_html( $section['label'] )
												);
											?></strong>
											<span><?php echo esc_html__( 'Unlock the full design library for every widget, plus premium styles and presets.', 'zaso' ); ?></span>
										</span>
										<span class="cta"><?php echo esc_html__( 'Get Pro', 'zaso' ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
			<?php
		}

		/**
		 * Render a grid of skin cards for one Free or Pro group.
		 *
		 * @since  1.10.4
		 *
		 * @param  array  $presets Preset id => preset array (label + nested values).
		 * @param  string $slug    Widget slug for render_preview().
		 * @param  bool   $is_pro  Whether the group is the Pro library.
		 * @return void
		 */
		protected function render_cards( $presets, $slug, $is_pro ) {
			foreach ( $presets as $preset_id => $preset ) {
				$preset_id = (string) $preset_id;
				$label     = isset( $preset['label'] ) ? (string) $preset['label'] : $preset_id;
				$values    = ( isset( $preset['values'] ) && is_array( $preset['values'] ) ) ? $preset['values'] : array();
				// render_preview() returns trusted HTML: static markup with every dynamic colour run through esc_attr().
				$preview = $this->render_preview( $slug, $values );
				?>
				<div class="zaso-wd-card">
					<div class="zaso-wd-pv-frame">
						<?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped inside render_preview(). ?>
					</div>
					<div class="zaso-wd-meta">
						<span class="zaso-wd-label"><?php echo esc_html( $label ); ?></span>
						<?php if ( $is_pro ) : ?>
							<span class="zaso-wd-badge is-pro"><span class="dot"></span><?php echo esc_html__( 'Pro', 'zaso' ); ?></span>
						<?php else : ?>
							<span class="zaso-wd-badge is-free"><?php echo esc_html__( 'Free', 'zaso' ); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php
			}
		}
	}

	new ZASO_Widget_Design();

endif; // class_exists check.
