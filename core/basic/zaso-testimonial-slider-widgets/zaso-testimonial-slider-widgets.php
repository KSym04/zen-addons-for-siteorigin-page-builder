<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Testimonial Slider
 * Widget ID: zen-addons-siteorigin-testimonial-slider
 * Description: A sliding testimonial carousel with auto-play, swipe, and keyboard support.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! function_exists( 'zaso_testimonial_slider_design_options' ) ) :
	/**
	 * Curated "designs" for the Testimonial Slider widget.
	 *
	 * The free core ships six ready-made designs inline; Zen Addons Pro appends its
	 * twenty-four additional designs via the shared `zaso_testimonial_slider_designs`
	 * filter (the Pro controller self-gates on a valid license, so an unlicensed or
	 * lapsed site only ever sees the six free entries). The empty-string key is the
	 * classic simple card and adds no class, keeping every existing instance
	 * byte-identical.
	 *
	 * @return array Map of design id => human label.
	 */
	function zaso_testimonial_slider_design_options() {
		$zaso_testimonial_slider_free_designs = array(
			''                   => __( 'Default (simple card)', 'zaso' ),
			'centered-indigo'    => __( 'Centered Quote (Indigo)', 'zaso' ),
			'centered-teal'      => __( 'Centered Quote (Teal)', 'zaso' ),
			'avatar-left-slate'  => __( 'Avatar Left (Slate)', 'zaso' ),
			'avatar-left-violet' => __( 'Avatar Left (Violet)', 'zaso' ),
			'quote-mark-rose'    => __( 'Big Quote Mark (Rose)', 'zaso' ),
			'quote-mark-amber'   => __( 'Big Quote Mark (Amber)', 'zaso' ),
		);

		return apply_filters( 'zaso_testimonial_slider_designs', $zaso_testimonial_slider_free_designs );
	}
endif;

if ( ! function_exists( 'zaso_testimonial_slider_design_description' ) ) :
	/**
	 * Help text for the "Pre-made Design" field.
	 *
	 * On a white-labelled Pro site the agency's client must never see the real
	 * product name or an upsell (they already have the full library), so the brand
	 * + "unlocks twenty-four more" sentence is dropped. Everywhere else the upsell
	 * line is kept.
	 *
	 * @return string Field description.
	 */
	function zaso_testimonial_slider_design_description() {
		$white_label = class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label();

		if ( $white_label ) {
			return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. Leave on "Default (simple card)" to build your own look with the Layout, Style and Design colour settings instead.', 'zaso' );
		}

		return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. The free core ships six; Zen Addons Pro unlocks twenty-four more (license required). Leave on "Default (simple card)" to build your own look with the Layout, Style and Design colour settings instead.', 'zaso' );
	}
endif;

if ( ! function_exists( 'zaso_testimonial_slider_icon' ) ) :
	/**
	 * Return an inline Material Symbols Rounded glyph as SVG.
	 *
	 * The pre-made designs draw stars, chevrons and badge marks with the exact
	 * Material Symbols Rounded outlines (Apache License 2.0) rendered inline as SVG
	 * rather than via an icon font. Inline SVG is immune to the missing-glyph "tofu"
	 * a curated font subset can produce, needs no @font-face, and recolours through
	 * `currentColor`, so each skin tints its icons with a plain `color` rule.
	 *
	 * @param string $zaso_name One of: star, chevron_left, chevron_right, hub, verified.
	 * @return string SVG markup, or an empty string for an unknown name.
	 */
	function zaso_testimonial_slider_icon( $zaso_name ) {
		$zaso_paths = array(
			'star'          => 'M480-269 314-169q-11 7-23 6t-21-8q-9-7-14-17.5t-2-23.5l44-189-147-127q-10-9-12.5-20.5T140-571q4-11 12-18t22-9l194-17 75-178q5-12 15.5-18t21.5-6q11 0 21.5 6t15.5 18l75 178 194 17q14 2 22 9t12 18q4 11 1.5 22.5T809-528L662-401l44 189q3 13-2 23.5T690-171q-9 7-21 8t-23-6L480-269Z',
			'chevron_left'  => 'm432-480 156 156q11 11 11 28t-11 28q-11 11-28 11t-28-11L348-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 28-11t28 11q11 11 11 28t-11 28L432-480Z',
			'chevron_right' => 'M504-480 348-636q-11-11-11-28t11-28q11-11 28-11t28 11l184 184q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L404-268q-11 11-28 11t-28-11q-11-11-11-28t11-28l156-156Z',
			'hub'           => 'M240-40q-50 0-85-35t-35-85q0-50 35-85t85-35q14 0 26 3t23 8l57-71q-28-31-39-70t-5-78l-81-27q-17 25-43 40t-58 15q-50 0-85-35T0-580q0-50 35-85t85-35q50 0 85 35t35 85v8l81 28q20-36 53.5-61t75.5-32v-87q-39-11-64.5-42.5T360-840q0-50 35-85t85-35q50 0 85 35t35 85q0 42-26 73.5T510-724v87q42 7 75.5 32t53.5 61l81-28v-8q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35q-32 0-58.5-15T739-515l-81 27q6 39-5 77.5T614-340l57 70q11-5 23-7.5t26-2.5q50 0 85 35t35 85q0 50-35 85t-85 35q-50 0-85-35t-35-85q0-20 6.5-38.5T624-232l-57-71q-41 23-87.5 23T392-303l-56 71q11 15 17.5 33.5T360-160q0 50-35 85t-85 35Z',
			'verified'      => 'm438-452-58-57q-11-11-27.5-11T324-508q-11 11-11 28t11 28l86 86q12 12 28 12t28-12l170-170q12-12 11.5-28T636-592q-12-12-28.5-12.5T579-593L438-452ZM326-90l-58-98-110-24q-15-3-24-15.5t-7-27.5l11-113-75-86q-10-11-10-26t10-26l75-86-11-113q-2-15 7-27.5t24-15.5l110-24 58-98q8-13 22-17.5t28 1.5l104 44 104-44q14-6 28-1.5t22 17.5l58 98 110 24q15 3 24 15.5t7 27.5l-11 113 75 86q10 11 10 26t-10 26l-75 86 11 113q2 15-7 27.5T802-212l-110 24-58 98q-8 13-22 17.5T584-74l-104-44-104 44q-14 6-28 1.5T326-90Z',
		);

		if ( ! isset( $zaso_paths[ $zaso_name ] ) ) {
			return '';
		}

		return '<svg class="zaso-testimonial-slider__icon" viewBox="0 -960 960 960" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . esc_attr( $zaso_paths[ $zaso_name ] ) . '"></path></svg>';
	}
endif;

if ( ! function_exists( 'zaso_testimonial_slider_stars' ) ) :
	/**
	 * Render a five-star rating row as inline SVG stars.
	 *
	 * Filled stars carry `--full` and inherit the skin's star colour; the remainder
	 * carry `--empty` and take a muted tint, matching the design's rating strip.
	 *
	 * @param int $zaso_filled Number of filled stars (already clamped 0-5).
	 * @return string Star markup.
	 */
	function zaso_testimonial_slider_stars( $zaso_filled ) {
		$zaso_filled = max( 0, min( 5, (int) $zaso_filled ) );
		$zaso_out    = '';
		for ( $zaso_i = 1; $zaso_i <= 5; $zaso_i++ ) {
			$zaso_state = ( $zaso_i <= $zaso_filled ) ? 'full' : 'empty';
			$zaso_out  .= '<span class="zaso-testimonial-slider__star zaso-testimonial-slider__star--' . $zaso_state . '">' . zaso_testimonial_slider_icon( 'star' ) . '</span>';
		}
		return $zaso_out;
	}
endif;

if ( ! function_exists( 'zaso_testimonial_slider_initials' ) ) :
	/**
	 * Derive up to two uppercase initials from an author name.
	 *
	 * Used for the tinted avatar disc when a testimonial has no photo, matching the
	 * design's initials avatars. Multibyte-safe.
	 *
	 * @param string $zaso_name Author name.
	 * @return string One or two uppercase letters (may be empty).
	 */
	function zaso_testimonial_slider_initials( $zaso_name ) {
		$zaso_name = trim( wp_strip_all_tags( (string) $zaso_name ) );
		if ( '' === $zaso_name ) {
			return '';
		}
		$zaso_parts = preg_split( '/\s+/', $zaso_name );
		$zaso_first = function_exists( 'mb_substr' ) ? mb_substr( $zaso_parts[0], 0, 1 ) : substr( $zaso_parts[0], 0, 1 );
		$zaso_last  = '';
		if ( count( $zaso_parts ) > 1 ) {
			$zaso_tail  = $zaso_parts[ count( $zaso_parts ) - 1 ];
			$zaso_last  = function_exists( 'mb_substr' ) ? mb_substr( $zaso_tail, 0, 1 ) : substr( $zaso_tail, 0, 1 );
		}
		$zaso_ini = $zaso_first . $zaso_last;
		return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $zaso_ini ) : strtoupper( $zaso_ini );
	}
endif;

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget' ) ) :


class Zen_Addons_SiteOrigin_Testimonial_Slider_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_testimonial_slider_field_array = array(
			'testimonials' => array(
				'type'       => 'repeater',
				'label'      => __( 'Testimonials', 'zaso' ),
				'item_name'  => __( 'Testimonial', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[author_name]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'quote' => array(
						'type'  => 'textarea',
						'label' => __( 'Quote', 'zaso' ),
					),
					'author_name' => array(
						'type'  => 'text',
						'label' => __( 'Author Name', 'zaso' ),
					),
					'author_title' => array(
						'type'        => 'text',
						'label'       => __( 'Role / Company', 'zaso' ),
						'description' => __( 'e.g. CEO at Acme Corp', 'zaso' ),
					),
					'company_name' => array(
						'type'        => 'text',
						'label'       => __( 'Company Name', 'zaso' ),
						'description' => __( 'Optional. Shown with a logo mark by the "Company logo" designs; ignored by every other design.', 'zaso' ),
					),
					'author_photo' => array(
						'type'     => 'media',
						'label'    => __( 'Author Photo', 'zaso' ),
						'library'  => 'image',
						'fallback' => true,
					),
					'rating' => array(
						'type'    => 'select',
						'label'   => __( 'Star Rating', 'zaso' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'None', 'zaso' ),
							'1' => '★',
							'2' => '★★',
							'3' => '★★★',
							'4' => '★★★★',
							'5' => '★★★★★',
						),
					),
				),
			),
			'autoplay'          => array(
				'type'    => 'checkbox',
				'label'   => __( 'Auto-play', 'zaso' ),
				'default' => true,
			),
			'autoplay_duration' => array(
				'type'        => 'number',
				'label'       => __( 'Auto-play Duration (ms)', 'zaso' ),
				'default'     => 5000,
				'description' => __( 'Time each slide is shown, in milliseconds.', 'zaso' ),
			),
			'show_arrows'       => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Navigation Arrows', 'zaso' ),
				'default' => true,
			),
			'show_dots'         => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Dot Pagination', 'zaso' ),
				'default' => true,
			),
			'layout'            => array(
				'type'        => 'select',
				'label'       => __( 'Layout', 'zaso' ),
				'default'     => 'default',
				'description' => __( 'Structural layout of each testimonial. The Style skin below still controls colours; Layout controls the shape (card elevation, decorative quote mark, minimal rule).', 'zaso' ),
				'options'     => array(
					'default' => __( 'Default (simple card)', 'zaso' ),
					'card'    => __( 'Card (elevated, soft shadow)', 'zaso' ),
					'quote'   => __( 'Quote (centered, decorative mark)', 'zaso' ),
					'minimal' => __( 'Minimal (no card, top rule)', 'zaso' ),
				),
			),
			'design_variant'    => array(
				'type'        => 'select',
				'label'       => __( 'Pre-made Design', 'zaso' ),
				'default'     => '',
				'description' => zaso_testimonial_slider_design_description(),
				'options'     => zaso_testimonial_slider_design_options(),
			),
			'agg_rating'        => array(
				'type'        => 'text',
				'label'       => __( 'Aggregate Rating', 'zaso' ),
				'description' => __( 'Optional. A headline score such as 4.9, shown by the "Stat highlight" designs; ignored by every other design.', 'zaso' ),
			),
			'agg_rating_label'  => array(
				'type'        => 'text',
				'label'       => __( 'Aggregate Rating Label', 'zaso' ),
				'description' => __( 'Optional. Sits under the score, e.g. from 1,200+ verified reviews.', 'zaso' ),
			),
			'design'            => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'quote_font_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Quote Font Size', 'zaso' ),
						'default' => '18px',
					),
					'quote_color' => array(
						'type'    => 'color',
						'label'   => __( 'Quote Color', 'zaso' ),
						'default' => '#333333',
					),
					'quote_italic' => array(
						'type'    => 'select',
						'label'   => __( 'Quote Italic', 'zaso' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'zaso' ),
							'no'  => __( 'No', 'zaso' ),
						),
					),
					'author_name_color' => array(
						'type'    => 'color',
						'label'   => __( 'Author Name Color', 'zaso' ),
						'default' => '#111111',
					),
					'author_title_color' => array(
						'type'    => 'color',
						'label'   => __( 'Author Title Color', 'zaso' ),
						'default' => '#888888',
					),
					'star_color' => array(
						'type'    => 'color',
						'label'   => __( 'Star Color', 'zaso' ),
						'default' => '#f5a623',
					),
					'card_background' => array(
						'type'    => 'color',
						'label'   => __( 'Card Background', 'zaso' ),
						'default' => '#ffffff',
					),
					'card_padding' => array(
						'type'   => 'section',
						'label'  => __( 'Card Padding', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'top' => array(
								'type'    => 'measurement',
								'label'   => __( 'Top', 'zaso' ),
								'default' => '32px',
							),
							'right' => array(
								'type'    => 'measurement',
								'label'   => __( 'Right', 'zaso' ),
								'default' => '32px',
							),
							'bottom' => array(
								'type'    => 'measurement',
								'label'   => __( 'Bottom', 'zaso' ),
								'default' => '32px',
							),
							'left' => array(
								'type'    => 'measurement',
								'label'   => __( 'Left', 'zaso' ),
								'default' => '32px',
							),
						),
					),
					'card_border_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Border Radius', 'zaso' ),
						'default' => '8px',
					),
					'arrow_color' => array(
						'type'    => 'color',
						'label'   => __( 'Arrow Color', 'zaso' ),
						'default' => '#111111',
					),
					'dot_color' => array(
						'type'    => 'color',
						'label'   => __( 'Dot Color', 'zaso' ),
						'default' => '#cccccc',
					),
					'dot_active_color' => array(
						'type'    => 'color',
						'label'   => __( 'Active Dot Color', 'zaso' ),
						'default' => '#111111',
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

		$zaso_testimonial_slider_fields = apply_filters( 'zaso_testimonial_slider_fields', $zaso_testimonial_slider_field_array );

		parent::__construct(
			'zen-addons-siteorigin-testimonial-slider',
			__( 'Zen Addons - Testimonial Slider', 'zaso' ),
			array(
				'description'   => __( 'A sliding testimonial carousel with auto-play, swipe, and keyboard support.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_testimonial_slider_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	function get_template_variables( $instance, $args ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$testimonials = array();
		if ( ! empty( $instance['testimonials'] ) && is_array( $instance['testimonials'] ) ) {
			foreach ( $instance['testimonials'] as $raw ) {
				$author_name = isset( $raw['author_name'] ) ? $raw['author_name'] : '';

				$photo_src = '';
				$photo_alt = $author_name;
				if ( ! empty( $raw['author_photo'] ) ) {
					$img = siteorigin_widgets_get_attachment_image_src( $raw['author_photo'], 'thumbnail' );
					if ( ! empty( $img[0] ) ) {
						$photo_src = $img[0];
					}
					$meta_alt = get_post_meta( $raw['author_photo'], '_wp_attachment_image_alt', true );
					if ( ! empty( $meta_alt ) ) {
						$photo_alt = $meta_alt;
					}
				}

				// Clamp to 0-5 so the star markup (str_repeat) can never receive a
				// negative count, which would fatal under PHP 8 on a corrupted instance.
				$rating = min( 5, max( 0, absint( isset( $raw['rating'] ) ? $raw['rating'] : 0 ) ) );

				$testimonials[] = array(
					'quote'        => isset( $raw['quote'] ) ? $raw['quote'] : '',
					'author_name'  => $author_name,
					'author_title' => isset( $raw['author_title'] ) ? $raw['author_title'] : '',
					'company_name' => isset( $raw['company_name'] ) ? $raw['company_name'] : '',
					'photo_src'    => $photo_src,
					'photo_alt'    => $photo_alt,
					'rating'       => $rating,
					/* translators: 1: number of stars, 2: max stars */
					'rating_label' => $rating > 0 ? sprintf( __( '%1$d out of %2$d stars', 'zaso' ), $rating, 5 ) : '',
				);
			}
		}

		$count             = count( $testimonials );
		$autoplay          = ! empty( $instance['autoplay'] );
		$autoplay_duration = absint( isset( $instance['autoplay_duration'] ) ? $instance['autoplay_duration'] : 5000 );
		$show_arrows       = ! empty( $instance['show_arrows'] );
		$show_dots         = ! empty( $instance['show_dots'] );

		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-testimonial-slider ' . $extra_class );

		$agg_rating       = isset( $instance['agg_rating'] ) ? trim( (string) $instance['agg_rating'] ) : '';
		$agg_rating_label = isset( $instance['agg_rating_label'] ) ? trim( (string) $instance['agg_rating_label'] ) : '';

		return apply_filters( 'zaso_testimonial_slider_template_variables', array(
			'testimonials'      => $testimonials,
			'count'             => $count,
			'autoplay'          => $autoplay,
			'autoplay_duration' => $autoplay_duration,
			'show_arrows'       => $show_arrows,
			'show_dots'         => $show_dots,
			'agg_rating'        => $agg_rating,
			'agg_rating_label'  => $agg_rating_label,
			'classes'           => $classes,
		) );
	}

	function get_less_variables( $instance ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$pad_top    = isset( $padding['top'] )    ? $padding['top']    : '32px';
		$pad_right  = isset( $padding['right'] )  ? $padding['right']  : '32px';
		$pad_bottom = isset( $padding['bottom'] ) ? $padding['bottom'] : '32px';
		$pad_left   = isset( $padding['left'] )   ? $padding['left']   : '32px';

		$quote_italic = ( isset( $design['quote_italic'] ) && 'no' === $design['quote_italic'] ) ? 'normal' : 'italic';

		return apply_filters( 'zaso_testimonial_slider_less_variables', array(
			'quote_font_size'    => isset( $design['quote_font_size'] )    ? $design['quote_font_size']    : '18px',
			'quote_color'        => isset( $design['quote_color'] )        ? $design['quote_color']        : '#333333',
			'quote_italic'       => $quote_italic,
			'author_name_color'  => isset( $design['author_name_color'] )  ? $design['author_name_color']  : '#111111',
			'author_title_color' => isset( $design['author_title_color'] ) ? $design['author_title_color'] : '#888888',
			'star_color'         => isset( $design['star_color'] )         ? $design['star_color']         : '#f5a623',
			'card_background'    => isset( $design['card_background'] )    ? $design['card_background']    : '#ffffff',
			'card_padding'       => sprintf( '%s %s %s %s', $pad_top, $pad_right, $pad_bottom, $pad_left ),
			'card_border_radius' => isset( $design['card_border_radius'] ) ? $design['card_border_radius'] : '8px',
			'arrow_color'        => isset( $design['arrow_color'] )        ? $design['arrow_color']        : '#111111',
			'dot_color'          => isset( $design['dot_color'] )          ? $design['dot_color']          : '#cccccc',
			'dot_active_color'   => isset( $design['dot_active_color'] )   ? $design['dot_active_color']   : '#111111',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-testimonial-slider',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);

		// Bundled webfonts (Hanken Grotesk + DM Mono) for the pre-made designs. The
		// faces are declared here but only applied inside a `--design-<id>` skin, so
		// the default widget keeps the theme's typography. Self-hosted, no CDN.
		$this->register_frontend_styles(
			array(
				array(
					'zaso-testimonial-slider-fonts',
					ZASO_BASE_DIR . 'assets/css/testimonial-slider-fonts.css',
					array(),
					ZASO_VERSION,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-testimonial-slider', __FILE__, 'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget' );


endif;
