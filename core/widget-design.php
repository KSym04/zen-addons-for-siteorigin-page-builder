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
		 * Render the Widget Design gallery page.
		 *
		 * @since 1.10.2
		 */
		public function render_page() {
			if ( ! current_user_can( self::CAPABILITY ) ) {
				return;
			}

			$widgets = $this->get_supported_widgets();
			?>
			<div class="wrap zaso-wd">
				<style>
					.zaso-wd .zaso-wd-head h1 { color: #0f172a; margin-bottom: 4px; }
					.zaso-wd .zaso-wd-intro { color: #475569; font-size: 14px; margin: 0 0 24px; max-width: 720px; }
					.zaso-wd h2 { color: #0f172a; font-size: 18px; margin: 28px 0 12px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; }
					.zaso-wd .zaso-wd-grid { display: grid; grid-template-columns: repeat( auto-fill, minmax( 180px, 1fr ) ); gap: 16px; }
					.zaso-wd .zaso-wd-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #ffffff; }
					.zaso-wd .zaso-wd-swatch { height: 90px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; padding: 12px; box-sizing: border-box; overflow: hidden; }
					.zaso-wd .zaso-wd-swatch .zaso-wd-aa { font-size: 22px; font-weight: 700; line-height: 1; }
					.zaso-wd .zaso-wd-swatch .zaso-wd-line { display: block; height: 6px; width: 64px; max-width: 100%; border-radius: 3px; margin-top: 8px; background: currentColor; opacity: 0.5; }
					.zaso-wd .zaso-wd-meta { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 10px; }
					.zaso-wd .zaso-wd-label { color: #0f172a; font-size: 13px; font-weight: 600; }
					.zaso-wd .zaso-wd-badge { font-size: 11px; line-height: 1; font-weight: 600; color: #ffffff; padding: 4px 8px; border-radius: 999px; white-space: nowrap; }
					.zaso-wd .zaso-wd-badge.is-free { background: #64748b; }
					.zaso-wd .zaso-wd-badge.is-pro { background: #4f46e5; }
					.zaso-wd .zaso-wd-unlock { border: 1px dashed #c7d2fe; border-radius: 12px; padding: 12px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; gap: 8px; min-height: 140px; background: #f8fafc; text-decoration: none; }
					.zaso-wd .zaso-wd-unlock strong { color: #2563eb; font-size: 14px; }
					.zaso-wd .zaso-wd-unlock span { color: #475569; font-size: 12px; }
				</style>

				<div class="zaso-wd-head">
					<h1><?php echo esc_html__( 'Design Library', 'zaso' ); ?></h1>
				</div>
				<p class="zaso-wd-intro"><?php echo esc_html__( 'Browse every ready made design skin for the supported Zen Addons widgets. Pick one inside the widget editor to apply it, then fine tune the colors to taste.', 'zaso' ); ?></p>

				<?php
				foreach ( $widgets as $class => $meta ) {
					if ( ! $this->ensure_widget_class( $class, $meta['folder'] ) ) {
						continue;
					}

					$skins = $this->get_widget_skins( $class );
					if ( empty( $skins ) ) {
						continue;
					}

					$has_pro = false;
					foreach ( $skins as $preset_id => $preset ) {
						if ( is_string( $preset_id ) && 0 === strpos( $preset_id, 'pro_' ) ) {
							$has_pro = true;
							break;
						}
					}
					?>
					<h2><?php echo esc_html( $meta['label'] ); ?></h2>
					<div class="zaso-wd-grid">
						<?php
						foreach ( $skins as $preset_id => $preset ) {
							$preset_id = (string) $preset_id;
							$is_pro    = ( 0 === strpos( $preset_id, 'pro_' ) );
							$label     = isset( $preset['label'] ) ? (string) $preset['label'] : $preset_id;

							$bg     = null;
							$fg     = null;
							$accent = null;
							if ( isset( $preset['values'] ) ) {
								$this->scan_colors( $preset['values'], $bg, $fg, $accent );
							}

							$swatch_bg = ( null !== $bg ) ? $bg : '#ffffff';
							$swatch_fg = ( null !== $fg ) ? $fg : $this->readable_on( $swatch_bg );
							// Guarantee the sample text is legible on the swatch background.
							if ( $this->contrast( $swatch_fg, $swatch_bg ) < 3.0 ) {
								$swatch_fg = $this->readable_on( $swatch_bg );
							}
							// The accent bar shows the skin's brand color (button/featured/etc.).
							$swatch_accent = ( null !== $accent ) ? $accent : $swatch_fg;
							?>
							<div class="zaso-wd-card">
								<div class="zaso-wd-swatch" style="<?php echo esc_attr( 'background:' . $swatch_bg . ';color:' . $swatch_fg . ';' ); ?>">
									<span class="zaso-wd-aa"><?php echo esc_html__( 'Aa', 'zaso' ); ?></span>
									<span class="zaso-wd-line" style="<?php echo esc_attr( 'background:' . $swatch_accent . ';opacity:1;' ); ?>"></span>
								</div>
								<div class="zaso-wd-meta">
									<span class="zaso-wd-label"><?php echo esc_html( $label ); ?></span>
									<?php if ( $is_pro ) : ?>
										<span class="zaso-wd-badge is-pro"><?php echo esc_html__( 'Pro', 'zaso' ); ?></span>
									<?php else : ?>
										<span class="zaso-wd-badge is-free"><?php echo esc_html__( 'Free', 'zaso' ); ?></span>
									<?php endif; ?>
								</div>
							</div>
							<?php
						}

						if ( ! $has_pro ) {
							?>
							<a class="zaso-wd-unlock" href="<?php echo esc_url( self::PRO_URL ); ?>" target="_blank" rel="noopener">
								<strong><?php echo esc_html__( '8+ more styles with Pro', 'zaso' ); ?></strong>
								<span><?php echo esc_html__( 'Unlock the full skin library for this widget.', 'zaso' ); ?></span>
							</a>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}

	new ZASO_Widget_Design();

endif; // class_exists check.
