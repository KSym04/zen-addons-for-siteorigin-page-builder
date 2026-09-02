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
	'name'        => __( 'Testimonials: Customer Quotes', 'zen-addons-for-siteorigin-page-builder' ),
	'description' => __( 'A sliding row of customer testimonials with star ratings on clean cards.', 'zen-addons-for-siteorigin-page-builder' ),
	'screenshot'  => defined( 'ZASO_BASE_DIR' ) ? ZASO_BASE_DIR . 'assets/img/sections/testimonials.png' : '',
	'widgets'     => array(
		array(
			'testimonials'      => array(
				array(
					'quote'        => __( 'Switching to Zen Addons cut our page-build time in half. The widgets just work, and the design controls are exactly what we needed.', 'zen-addons-for-siteorigin-page-builder' ),
					'author_name'  => __( 'Maria Delgado', 'zen-addons-for-siteorigin-page-builder' ),
					'author_title' => __( 'Marketing Lead at Brightpath', 'zen-addons-for-siteorigin-page-builder' ),
					'author_photo' => '',
					'rating'       => '5',
				),
				array(
					'quote'        => __( 'The testimonial slider dropped straight into our SiteOrigin layout with zero fuss. Auto-play, swipe, and keyboard navigation were all handled.', 'zen-addons-for-siteorigin-page-builder' ),
					'author_name'  => __( 'James Okonkwo', 'zen-addons-for-siteorigin-page-builder' ),
					'author_title' => __( 'Founder, Studio North', 'zen-addons-for-siteorigin-page-builder' ),
					'author_photo' => '',
					'rating'       => '5',
				),
				array(
					'quote'        => __( 'Clean markup, accessible by default, and the styling presets saved me an afternoon. Easily the best addon pack for SiteOrigin.', 'zen-addons-for-siteorigin-page-builder' ),
					'author_name'  => __( 'Priya Raman', 'zen-addons-for-siteorigin-page-builder' ),
					'author_title' => __( 'Freelance WordPress Developer', 'zen-addons-for-siteorigin-page-builder' ),
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
