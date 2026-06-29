<?php
/**
 * Section template: Centered CTA hero.
 *
 * A bold, centered call-to-action hero built from the Zen Addons CTA Banner
 * widget (centered layout). Returned to SiteOrigin Page Builder as a prebuilt
 * layout (name + description + screenshot + panels_data).
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'name'        => __( 'Hero: Centered Call to Action', 'zaso' ),
	'description' => __( 'A bold centered hero with heading, subheading, and a button.', 'zaso' ),
	'screenshot'  => '',
	'widgets'     => array(
		array(
			'heading'        => __( 'Build beautiful pages, faster', 'zaso' ),
			'subheading'     => __( 'Premium SiteOrigin widgets with zero bloat', 'zaso' ),
			'content'        => '<p>' . esc_html__( 'Drop in conversion-ready sections and fine-tune every detail without touching code.', 'zaso' ) . '</p>',
			'button_text'    => __( 'Get Started', 'zaso' ),
			'button_url'     => '#',
			'button_new_tab' => false,
			'layout'         => 'inline',
			'alignment'      => 'center',
			'block_layout'   => 'centered',
			'extra_id'       => '',
			'extra_class'    => '',
			'design'         => array(
				'background' => array(
					'bg_type'         => 'gradient',
					'bg_color'        => '#4f46e5',
					'gradient_start'  => '#4f46e5',
					'gradient_end'    => '#0ea5e9',
					'gradient_angle'  => 135,
					'bg_image'        => '',
					'overlay_color'   => '#0f172a',
					'overlay_opacity' => 0,
				),
				'typography' => array(
					'heading_color'    => '#ffffff',
					'heading_size'     => '2.4rem',
					'subheading_color' => '#e0e7ff',
					'subheading_size'  => '1.15rem',
					'text_color'       => '#eef2ff',
				),
				'button'     => array(
					'button_bg'       => '#ffffff',
					'button_bg_hover' => '#e0e7ff',
					'button_color'    => '#4f46e5',
					'button_radius'   => '10px',
				),
				'spacing'    => array(
					'padding_y'     => '4rem',
					'padding_x'     => '2rem',
					'border_radius' => '16px',
				),
			),
			'panels_info'    => array(
				'class' => 'Zen_Addons_SiteOrigin_Cta_Banner_Widget',
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
