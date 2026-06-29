<?php
/**
 * Section template: Customer testimonials.
 *
 * A social-proof section built from the Zen Addons Testimonial Slider widget
 * (card layout, Soft Light skin). Returned to SiteOrigin Page Builder as a
 * prebuilt layout (name + description + screenshot + panels_data).
 *
 * The widget instance carries its full design subtree so it renders without
 * notices. Colours mirror the widget's AA-safe "Soft Light" preset: slate quote
 * text on a white card, with an amber-700 star colour that clears WCAG AA.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'name'        => __( 'Testimonials: Customer Quotes', 'zaso' ),
	'description' => __( 'A sliding row of customer testimonials with star ratings on clean cards.', 'zaso' ),
	'screenshot'  => defined( 'ZASO_BASE_DIR' ) ? ZASO_BASE_DIR . 'assets/img/sections/testimonials.png' : '',
	'widgets'     => array(
		array(
			'testimonials'      => array(
				array(
					'quote'        => __( 'Switching to Zen Addons cut our page-build time in half. The widgets just work, and the design controls are exactly what we needed.', 'zaso' ),
					'author_name'  => __( 'Maria Delgado', 'zaso' ),
					'author_title' => __( 'Marketing Lead at Brightpath', 'zaso' ),
					'author_photo' => '',
					'rating'       => '5',
				),
				array(
					'quote'        => __( 'The testimonial slider dropped straight into our SiteOrigin layout with zero fuss. Auto-play, swipe, and keyboard navigation were all handled.', 'zaso' ),
					'author_name'  => __( 'James Okonkwo', 'zaso' ),
					'author_title' => __( 'Founder, Studio North', 'zaso' ),
					'author_photo' => '',
					'rating'       => '5',
				),
				array(
					'quote'        => __( 'Clean markup, accessible by default, and the styling presets saved me an afternoon. Easily the best addon pack for SiteOrigin.', 'zaso' ),
					'author_name'  => __( 'Priya Raman', 'zaso' ),
					'author_title' => __( 'Freelance WordPress Developer', 'zaso' ),
					'author_photo' => '',
					'rating'       => '4',
				),
			),
			'autoplay'          => true,
			'autoplay_duration' => 5000,
			'show_arrows'       => true,
			'show_dots'         => true,
			'layout'            => 'card',
			'design'            => array(
				'quote_font_size'    => '18px',
				'quote_color'        => '#334155',
				'quote_italic'       => 'yes',
				'author_name_color'  => '#0f172a',
				'author_title_color' => '#475569',
				'star_color'         => '#b45309',
				'card_background'    => '#ffffff',
				'card_padding'       => array(
					'top'    => '32px',
					'right'  => '32px',
					'bottom' => '32px',
					'left'   => '32px',
				),
				'card_border_radius' => '12px',
				'arrow_color'        => '#0f172a',
				'dot_color'          => '#cbd5e1',
				'dot_active_color'   => '#4f46e5',
			),
			'extra_id'          => '',
			'extra_class'       => '',
			'panels_info'       => array(
				'class' => 'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget',
				'raw'   => false,
				'grid'  => 0,
				'cell'  => 0,
				'id'    => 0,
			),
		),
	),
	'grids'       => array(
		array( 'cells' => 1, 'style' => array() ),
	),
	'grid_cells'  => array(
		array( 'grid' => 0, 'weight' => 1 ),
	),
);
