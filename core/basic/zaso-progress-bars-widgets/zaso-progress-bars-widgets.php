<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Progress Bars
 * Widget ID: zen-addons-siteorigin-progress-bars
 * Description: Show a set of labeled progress or skill bars that fill when they scroll into view.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Progress_Bars_Widget' ) ) :


class Zen_Addons_SiteOrigin_Progress_Bars_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_progress_bars_field_array = array(
			'bars' => array(
				'type'       => 'repeater',
				'label'      => __( 'Progress Bars', 'zaso' ),
				'item_name'  => __( 'Bar', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[label]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'label' => array(
						'type'  => 'text',
						'label' => __( 'Label', 'zaso' ),
					),
					'percentage' => array(
						'type'        => 'number',
						'label'       => __( 'Percentage', 'zaso' ),
						'default'     => 75,
						'description' => __( 'A value from 0 to 100.', 'zaso' ),
					),
					'bar_color' => array(
						'type'        => 'color',
						'label'       => __( 'Bar Color', 'zaso' ),
						'default'     => '',
						'description' => __( 'Optional. Overrides the default fill color for this bar.', 'zaso' ),
					),
				),
			),
			'show_percentage'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Percentage', 'zaso' ),
				'default' => true,
			),
			'animate'            => array(
				'type'    => 'checkbox',
				'label'   => __( 'Animate on Scroll', 'zaso' ),
				'default' => true,
			),
			'animation_duration' => array(
				'type'        => 'number',
				'label'       => __( 'Animation Duration (ms)', 'zaso' ),
				'default'     => 1200,
				'description' => __( 'How long each bar takes to fill, in milliseconds.', 'zaso' ),
			),
			'design'             => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'label_color' => array(
						'type'    => 'color',
						'label'   => __( 'Label Color', 'zaso' ),
						'default' => '#111111',
					),
					'label_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Label Size', 'zaso' ),
						'default' => '0.95rem',
					),
					'percentage_color' => array(
						'type'    => 'color',
						'label'   => __( 'Percentage Color', 'zaso' ),
						'default' => '#64748b',
					),
					'track_color' => array(
						'type'    => 'color',
						'label'   => __( 'Track Color', 'zaso' ),
						'default' => '#e5e7eb',
					),
					'fill_color' => array(
						'type'    => 'color',
						'label'   => __( 'Default Fill Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'bar_height' => array(
						'type'    => 'measurement',
						'label'   => __( 'Bar Height', 'zaso' ),
						'default' => '10px',
					),
					'bar_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Bar Corner Radius', 'zaso' ),
						'default' => '6px',
					),
					'bar_spacing' => array(
						'type'    => 'measurement',
						'label'   => __( 'Spacing Between Bars', 'zaso' ),
						'default' => '20px',
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

		$zaso_progress_bars_fields = apply_filters( 'zaso_progress_bars_fields', $zaso_progress_bars_field_array );

		parent::__construct(
			'zen-addons-siteorigin-progress-bars',
			__( 'Zen Addons - Progress Bars', 'zaso' ),
			array(
				'description'   => __( 'Show a set of labeled progress or skill bars that fill when they scroll into view.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_progress_bars_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	function get_template_variables( $instance, $args ) {
		$bars = array();
		if ( ! empty( $instance['bars'] ) && is_array( $instance['bars'] ) ) {
			foreach ( $instance['bars'] as $raw ) {
				// Clamp to 0-100 so the fill width and aria value are always valid.
				$percentage = isset( $raw['percentage'] ) ? (int) $raw['percentage'] : 0;
				$percentage = max( 0, min( 100, $percentage ) );

				$bars[] = array(
					'label'      => isset( $raw['label'] ) ? $raw['label'] : '',
					'percentage' => $percentage,
					'bar_color'  => ( isset( $raw['bar_color'] ) && '' !== $raw['bar_color'] ) ? $raw['bar_color'] : '',
				);
			}
		}

		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-progress-bars ' . $extra_class );

		return apply_filters( 'zaso_progress_bars_template_variables', array(
			'bars'               => $bars,
			'show_percentage'    => ! empty( $instance['show_percentage'] ),
			'animate'            => ! empty( $instance['animate'] ),
			'animation_duration' => absint( isset( $instance['animation_duration'] ) ? $instance['animation_duration'] : 1200 ),
			'classes'            => $classes,
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_progress_bars_less_variables', array(
			'label_color'      => isset( $design['label_color'] )      ? $design['label_color']      : '#111111',
			'label_size'       => isset( $design['label_size'] )       ? $design['label_size']       : '0.95rem',
			'percentage_color' => isset( $design['percentage_color'] ) ? $design['percentage_color'] : '#64748b',
			'track_color'      => isset( $design['track_color'] )      ? $design['track_color']      : '#e5e7eb',
			'fill_color'       => isset( $design['fill_color'] )       ? $design['fill_color']       : '#4f46e5',
			'bar_height'       => isset( $design['bar_height'] )       ? $design['bar_height']       : '10px',
			'bar_radius'       => isset( $design['bar_radius'] )       ? $design['bar_radius']       : '6px',
			'bar_spacing'      => isset( $design['bar_spacing'] )      ? $design['bar_spacing']      : '20px',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-progress-bars',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-progress-bars', __FILE__, 'Zen_Addons_SiteOrigin_Progress_Bars_Widget' );


endif;
