<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Pricing Table
 * Widget ID: zen-addons-siteorigin-pricing-table
 * Description: Showcase your plans side-by-side with a features list, highlighted tier, and call-to-action button.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! function_exists( 'zaso_pricing_hex_to_rgb' ) ) {
	/**
	 * Parse a hex colour string into an [ R, G, B ] array (0-255 each).
	 *
	 * Accepts #rgb and #rrggbb (with or without the leading #). Returns null on
	 * anything it cannot parse, so callers can fall back to the original value.
	 *
	 * @param string $hex Hex colour string.
	 * @return array|null { @type int $0 Red, @type int $1 Green, @type int $2 Blue } or null.
	 */
	function zaso_pricing_hex_to_rgb( $hex ) {
		$hex = ltrim( (string) $hex, '#' );

		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			return null;
		}

		return array(
			hexdec( substr( $hex, 0, 2 ) ),
			hexdec( substr( $hex, 2, 2 ) ),
			hexdec( substr( $hex, 4, 2 ) ),
		);
	}
}

if ( ! function_exists( 'zaso_pricing_relative_luminance' ) ) {
	/**
	 * Compute the WCAG 2.1 sRGB relative luminance of an [ R, G, B ] colour.
	 *
	 * @param array $rgb { @type int $0 Red, @type int $1 Green, @type int $2 Blue } 0-255 each.
	 * @return float Relative luminance in the range 0..1.
	 */
	function zaso_pricing_relative_luminance( $rgb ) {
		$channels = array();

		foreach ( $rgb as $value ) {
			$c          = $value / 255;
			$channels[] = ( $c <= 0.03928 ) ? ( $c / 12.92 ) : pow( ( ( $c + 0.055 ) / 1.055 ), 2.4 );
		}

		return ( 0.2126 * $channels[0] ) + ( 0.7152 * $channels[1] ) + ( 0.0722 * $channels[2] );
	}
}

if ( ! function_exists( 'zaso_pricing_contrast_ratio' ) ) {
	/**
	 * WCAG contrast ratio between two relative luminances.
	 *
	 * @param float $lum_a First relative luminance (0..1).
	 * @param float $lum_b Second relative luminance (0..1).
	 * @return float Contrast ratio (1..21).
	 */
	function zaso_pricing_contrast_ratio( $lum_a, $lum_b ) {
		$lighter = max( $lum_a, $lum_b );
		$darker  = min( $lum_a, $lum_b );

		return ( $lighter + 0.05 ) / ( $darker + 0.05 );
	}
}

if ( ! function_exists( 'zaso_pricing_accessible_accent' ) ) {
	/**
	 * Return an accent colour guaranteed to be readable as a foreground on a
	 * given surface (check icons, featured border, focus ring, elevated ring).
	 *
	 * If the supplied accent already meets the target contrast against the
	 * surface it is returned UNCHANGED, byte-for-byte, so light skins and the
	 * default (no-skin) instance compile to identical CSS. Only when the accent
	 * fails, e.g. a dark indigo accent on a dark card, is it mixed toward white
	 * (dark surface) or black (light surface) in 5% steps until it passes. The
	 * button background keeps the original accent, so its already-validated label
	 * contrast (button_text_color on button_bg) is never disturbed.
	 *
	 * @param string $accent  Accent hex colour (featured / icon foreground).
	 * @param string $surface Card background hex colour the accent sits on.
	 * @param float  $target  Minimum contrast ratio to reach. Default 3.5 (above
	 *                        the WCAG 3:1 floor for non-text graphics, with margin).
	 * @return string A hex colour that meets the target, or the original accent
	 *                if it already passes or cannot be parsed.
	 */
	function zaso_pricing_accessible_accent( $accent, $surface, $target = 3.5 ) {
		$accent_rgb  = zaso_pricing_hex_to_rgb( $accent );
		$surface_rgb = zaso_pricing_hex_to_rgb( $surface );

		if ( null === $accent_rgb || null === $surface_rgb ) {
			return $accent;
		}

		$surface_lum = zaso_pricing_relative_luminance( $surface_rgb );
		$accent_lum  = zaso_pricing_relative_luminance( $accent_rgb );

		if ( zaso_pricing_contrast_ratio( $accent_lum, $surface_lum ) >= $target ) {
			return $accent;
		}

		// Lighten toward white on dark surfaces; darken toward black on light ones.
		$mix_to = ( $surface_lum < 0.5 ) ? 255 : 0;

		for ( $step = 1; $step <= 20; $step++ ) {
			$fraction = $step / 20; // 5% increments.
			$mixed    = array(
				(int) round( $accent_rgb[0] + ( ( $mix_to - $accent_rgb[0] ) * $fraction ) ),
				(int) round( $accent_rgb[1] + ( ( $mix_to - $accent_rgb[1] ) * $fraction ) ),
				(int) round( $accent_rgb[2] + ( ( $mix_to - $accent_rgb[2] ) * $fraction ) ),
			);

			if ( zaso_pricing_contrast_ratio( zaso_pricing_relative_luminance( $mixed ), $surface_lum ) >= $target ) {
				return sprintf( '#%02x%02x%02x', $mixed[0], $mixed[1], $mixed[2] );
			}
		}

		return ( 255 === $mix_to ) ? '#ffffff' : '#000000';
	}
}

if ( ! function_exists( 'zaso_pricing_table_design_options' ) ) :
	/**
	 * Curated "designs" for the Pricing Table widget.
	 *
	 * The free core ships six ready-made designs inline; Zen Addons Pro appends
	 * its twenty-four additional designs via the shared `zaso_pricing_table_designs`
	 * filter (the Pro controller self-gates on a valid license, so an unlicensed or
	 * lapsed site only ever sees the six free entries). The empty-string key is the
	 * classic bordered table and adds no class, keeping every existing instance
	 * byte-identical.
	 *
	 * @return array Map of design id => human label.
	 */
	function zaso_pricing_table_design_options() {
		$zaso_pricing_table_free_designs = array(
			''               => __( 'Default (classic table)', 'zaso' ),
			'classic-indigo' => __( 'Classic (Indigo)', 'zaso' ),
			'classic-teal'   => __( 'Classic (Teal)', 'zaso' ),
			'accent-indigo'  => __( 'Accent Header (Indigo)', 'zaso' ),
			'accent-rose'    => __( 'Accent Header (Rose)', 'zaso' ),
			'minimal-slate'  => __( 'Minimal Outline (Slate)', 'zaso' ),
			'minimal-violet' => __( 'Minimal Outline (Violet)', 'zaso' ),
		);

		return apply_filters( 'zaso_pricing_table_designs', $zaso_pricing_table_free_designs );
	}
endif;

if ( ! function_exists( 'zaso_pricing_table_design_description' ) ) :
	/**
	 * Help text for the "Pre-made Design" field.
	 *
	 * On a white-labelled Pro site the agency's client must never see the real
	 * product name or an upsell (they already have the full library), so the brand
	 * + "unlocks twenty-four more" sentence is dropped. Everywhere else (free, or
	 * licensed-but-not-white-labelled) the upsell line is kept.
	 *
	 * @return string Field description.
	 */
	function zaso_pricing_table_design_description() {
		$white_label = class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label();

		if ( $white_label ) {
			return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. Leave on "Default (classic table)" to build your own look with the Layout, Columns and Design colour settings instead.', 'zaso' );
		}

		return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. The free core ships six; Zen Addons Pro unlocks twenty-four more (license required). Leave on "Default (classic table)" to build your own look with the Layout, Columns and Design colour settings instead.', 'zaso' );
	}
endif;

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Pricing_Table_Widget' ) ) :


class Zen_Addons_SiteOrigin_Pricing_Table_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_pricing_table_field_array = array(
			'plans'   => array(
				'type'       => 'repeater',
				'label'      => __( 'Plans', 'zaso' ),
				'item_name'  => __( 'Plan', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[name]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'name'        => array(
						'type'  => 'text',
						'label' => __( 'Plan Name', 'zaso' ),
					),
					'price'       => array(
						'type'        => 'text',
						'label'       => __( 'Price', 'zaso' ),
						'description' => __( 'e.g. 29 or Free', 'zaso' ),
					),
					'period'      => array(
						'type'        => 'text',
						'label'       => __( 'Billing Period', 'zaso' ),
						'description' => __( 'e.g. /month', 'zaso' ),
					),
					'description' => array(
						'type'  => 'text',
						'label' => __( 'Short Description', 'zaso' ),
					),
					'features'    => array(
						'type'        => 'textarea',
						'label'       => __( 'Features', 'zaso' ),
						'description' => __( 'One feature per line.', 'zaso' ),
					),
					'cta_text'    => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'zaso' ),
						'default' => __( 'Get Started', 'zaso' ),
					),
					'cta_url'     => array(
						'type'  => 'link',
						'label' => __( 'Button URL', 'zaso' ),
					),
					'cta_new_tab' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Open in New Tab', 'zaso' ),
						'default' => false,
					),
					'featured'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Featured / Highlighted', 'zaso' ),
						'default' => false,
					),
				),
			),
			'columns' => array(
				'type'    => 'select',
				'label'   => __( 'Columns', 'zaso' ),
				'default' => '3',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			),
			'currency' => array(
				'type'    => 'text',
				'label'   => __( 'Currency Symbol', 'zaso' ),
				'default' => '$',
			),
			'layout' => array(
				'type'        => 'select',
				'label'       => __( 'Layout', 'zaso' ),
				'default'     => 'default',
				'description' => __( 'Structural layout of the table. The Style skin below still controls colours; Layout controls the shape (card border, shadow, radius, padding, density).', 'zaso' ),
				'options'     => array(
					'default'  => __( 'Default (bordered cards)', 'zaso' ),
					'bordered' => __( 'Bordered (flat connected columns)', 'zaso' ),
					'elevated' => __( 'Elevated (floating cards, soft shadow)', 'zaso' ),
					'compact'  => __( 'Compact (dense padding, smaller type)', 'zaso' ),
				),
			),
			'design_variant' => array(
				'type'        => 'select',
				'label'       => __( 'Pre-made Design', 'zaso' ),
				'default'     => '',
				'description' => zaso_pricing_table_design_description(),
				'options'     => zaso_pricing_table_design_options(),
			),
			'design'  => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'featured_color'    => array(
						'type'    => 'color',
						'label'   => __( 'Featured Accent Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'card_bg'           => array(
						'type'    => 'color',
						'label'   => __( 'Card Background', 'zaso' ),
						'default' => '#ffffff',
					),
					'text_color'        => array(
						'type'    => 'color',
						'label'   => __( 'Text Color', 'zaso' ),
						'default' => '#111111',
					),
					'text_muted'        => array(
						'type'    => 'color',
						'label'   => __( 'Muted Text Color', 'zaso' ),
						'default' => '#6b7280',
					),
					'card_border'       => array(
						'type'    => 'color',
						'label'   => __( 'Card Border', 'zaso' ),
						'default' => '#e5e7eb',
					),
					'card_radius'       => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Corner Radius', 'zaso' ),
						'default' => '12px',
					),
					'button_bg'         => array(
						'type'    => 'color',
						'label'   => __( 'Button Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'button_text_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Text Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'gap'               => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Cards', 'zaso' ),
						'default' => '24px',
					),
				),
			),
			'extra_id'   => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
			),
		);

		$zaso_pricing_table_fields = apply_filters( 'zaso_pricing_table_fields', $zaso_pricing_table_field_array );

		parent::__construct(
			'zen-addons-siteorigin-pricing-table',
			__( 'Zen Addons - Pricing Table', 'zaso' ),
			array(
				'description'   => __( 'Showcase your plans side-by-side with a features list, highlighted tier, and call-to-action button.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_pricing_table_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Register the widget's front-end assets.
	 *
	 * SiteOrigin enqueues these only on pages where the widget is present, and
	 * skips a handle that is already enqueued (so a page with several ZASO widgets
	 * loads the shared Material Symbols font once).
	 *
	 * @since 1.11.0
	 */
	function initialize() {

		// Self-hosted Material Symbols Rounded @font-face. The pre-made design
		// variants draw their feature-check glyph through this font via a ::before
		// pseudo-element ( and hide the default inline SVG ), so the font must load
		// whenever a Pricing Table renders. Without it the check glyph falls back to
		// a missing-glyph box. No Google CDN: the font ships in the plugin.
		$this->register_frontend_styles(
			array(
				array(
					'zaso-material-symbols',
					ZASO_BASE_DIR . 'assets/css/material-symbols.css',
					array(),
					ZASO_VERSION,
				)
			)
		);

	}

	function get_template_variables( $instance, $args ) {
		$plans = array();

		if ( ! empty( $instance['plans'] ) && is_array( $instance['plans'] ) ) {
			foreach ( $instance['plans'] as $raw ) {
				$features_raw  = isset( $raw['features'] ) ? $raw['features'] : '';
				$features_list = array_values(
					array_filter(
						array_map(
							'sanitize_text_field',
							explode( "\n", $features_raw )
						)
					)
				);

				$plans[] = array(
					'name'        => isset( $raw['name'] )        ? sanitize_text_field( $raw['name'] )        : '',
					'price'       => isset( $raw['price'] )       ? sanitize_text_field( $raw['price'] )       : '',
					'period'      => isset( $raw['period'] )      ? sanitize_text_field( $raw['period'] )      : '',
					'description' => isset( $raw['description'] ) ? sanitize_text_field( $raw['description'] ) : '',
					'features'    => $features_list,
					'cta_text'    => isset( $raw['cta_text'] )    ? sanitize_text_field( $raw['cta_text'] )    : '',
					'cta_url'     => isset( $raw['cta_url'] )     ? esc_url_raw( $raw['cta_url'] )             : '',
					'cta_new_tab' => ! empty( $raw['cta_new_tab'] ),
					'featured'    => ! empty( $raw['featured'] ),
				);
			}
		}

		$columns = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$columns = max( 1, min( 4, $columns ) );

		$currency    = isset( $instance['currency'] ) ? sanitize_text_field( $instance['currency'] ) : '$';
		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-pricing-table zaso-pricing-table--cols-' . $columns . ' ' . $extra_class );

		return apply_filters( 'zaso_pricing_table_template_variables', array(
			'plans'    => $plans,
			'currency' => $currency,
			'classes'  => $classes,
		), $instance );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		$featured_color = isset( $design['featured_color'] ) ? $design['featured_color'] : '#4f46e5';
		$card_bg        = isset( $design['card_bg'] )        ? $design['card_bg']        : '#ffffff';

		return apply_filters( 'zaso_pricing_table_less_variables', array(
			'featured_color'    => $featured_color,
			'card_bg'           => $card_bg,
			'text_color'        => isset( $design['text_color'] )        ? $design['text_color']        : '#111111',
			'text_muted'        => isset( $design['text_muted'] )        ? $design['text_muted']        : '#6b7280',
			'card_border'       => isset( $design['card_border'] )       ? $design['card_border']       : '#e5e7eb',
			'card_radius'       => isset( $design['card_radius'] )       ? $design['card_radius']       : '12px',
			'button_bg'         => isset( $design['button_bg'] )         ? $design['button_bg']         : '#4f46e5',
			'button_text_color' => isset( $design['button_text_color'] ) ? $design['button_text_color'] : '#ffffff',
			'gap'               => isset( $design['gap'] )               ? $design['gap']               : '24px',
			// Accent reused as an on-surface foreground (check icons, featured
			// border, focus + elevated rings). Auto-lightened only when the chosen
			// accent fails contrast on the card surface, so dark skins (e.g.
			// Midnight indigo on a near-black card) stay readable. Identical to
			// featured_color whenever the accent already passes, keeping light
			// skins and the default no-skin output byte-identical.
			'accent_on_surface' => zaso_pricing_accessible_accent( $featured_color, $card_bg ),
		) );
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-pricing-table', __FILE__, 'Zen_Addons_SiteOrigin_Pricing_Table_Widget' );


endif;
