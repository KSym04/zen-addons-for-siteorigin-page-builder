<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Section Divider
 * Widget ID: zen-addons-siteorigin-section-divider
 * Description: Add an SVG shape divider (wave, curve, tilt, triangle) to the top or bottom of a section.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Section_Divider_Widget' ) ) :


class Zen_Addons_SiteOrigin_Section_Divider_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_section_divider_field_array = array(
			'style'  => array(
				'type'    => 'select',
				'label'   => __( 'Shape Style', 'zaso' ),
				'default' => 'waves',
				'options' => array(
					'waves'    => __( 'Waves', 'zaso' ),
					'curve'    => __( 'Curve', 'zaso' ),
					'tilt'     => __( 'Tilt', 'zaso' ),
					'triangle' => __( 'Triangle', 'zaso' ),
				),
			),
			'color'  => array(
				'type'    => 'color',
				'label'   => __( 'Shape Color', 'zaso' ),
				'default' => '#4f46e5',
			),
			'height' => array(
				'type'        => 'measurement',
				'label'       => __( 'Height', 'zaso' ),
				'default'     => '100px',
				'description' => __( 'The vertical height of the shape.', 'zaso' ),
			),
			'width'  => array(
				'type'        => 'measurement',
				'label'       => __( 'Width', 'zaso' ),
				'default'     => '100%',
				'description' => __( 'The horizontal width of the shape.', 'zaso' ),
			),
			'flip_horizontal' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Flip Horizontally', 'zaso' ),
				'default' => false,
			),
			'flip_vertical'   => array(
				'type'        => 'checkbox',
				'label'       => __( 'Flip Vertically', 'zaso' ),
				'default'     => false,
				'description' => __( 'Flip the shape to sit at the top of a section instead of the bottom.', 'zaso' ),
			),
			'extra_id'    => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			),
		);

		// Add filter.
		$zaso_section_divider_fields = apply_filters( 'zaso_section_divider_fields', $zaso_section_divider_field_array );

		parent::__construct(
			'zen-addons-siteorigin-section-divider',
			__( 'Zen Addons - Section Divider', 'zaso' ),
			array(
				'description'   => __( 'Add an SVG shape divider to the top or bottom of a section.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_section_divider_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		return apply_filters( 'zaso_section_divider_less_variables', array(
			'divider_color'  => $instance['color'],
			'divider_height' => $instance['height'],
			'divider_width'  => $instance['width'],
		) );

	}

	function get_template_variables( $instance, $args ) {

		// Whitelist the shape so only known, static SVG markup is ever rendered.
		$allowed = array( 'waves', 'curve', 'tilt', 'triangle' );
		$style   = in_array( $instance['style'], $allowed, true ) ? $instance['style'] : 'waves';

		return apply_filters( 'zaso_section_divider_template_variables', array(
			'style'           => $style,
			'flip_horizontal' => ! empty( $instance['flip_horizontal'] ),
			'flip_vertical'   => ! empty( $instance['flip_vertical'] ),
		) );

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-section-divider', __FILE__, 'Zen_Addons_SiteOrigin_Section_Divider_Widget' );


endif;
