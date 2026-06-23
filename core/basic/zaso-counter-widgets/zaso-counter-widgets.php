<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: ZASO - Counter
 * Widget ID: zen-addons-siteorigin-counter
 * Description: An animated number that counts up to a target value when it scrolls into view.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Counter_Widget' ) ) :


class Zen_Addons_SiteOrigin_Counter_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_counter_field_array = array(
			'start' => array(
				'type'    => 'number',
				'label'   => __( 'Start Value', 'zaso' ),
				'default' => 0,
			),
			'end' => array(
				'type'    => 'number',
				'label'   => __( 'End Value', 'zaso' ),
				'default' => 100,
			),
			'duration' => array(
				'type'        => 'number',
				'label'       => __( 'Animation Duration (ms)', 'zaso' ),
				'default'     => 2000,
				'description' => __( 'How long the count-up takes, in milliseconds.', 'zaso' ),
			),
			'decimals' => array(
				'type'    => 'number',
				'label'   => __( 'Decimal Places', 'zaso' ),
				'default' => 0,
			),
			'separator' => array(
				'type'    => 'select',
				'label'   => __( 'Thousands Separator', 'zaso' ),
				'default' => 'none',
				'options' => array(
					'none'  => __( 'None', 'zaso' ),
					'comma' => __( 'Comma (1,000)', 'zaso' ),
					'space' => __( 'Space (1 000)', 'zaso' ),
				),
			),
			'prefix' => array(
				'type'        => 'text',
				'label'       => __( 'Prefix', 'zaso' ),
				'description' => __( 'Shown before the number, e.g. $.', 'zaso' ),
			),
			'suffix' => array(
				'type'        => 'text',
				'label'       => __( 'Suffix', 'zaso' ),
				'description' => __( 'Shown after the number, e.g. + or %.', 'zaso' ),
			),
			'title' => array(
				'type'  => 'text',
				'label' => __( 'Title', 'zaso' ),
			),
			'icon' => array(
				'type'  => 'icon',
				'label' => __( 'Icon', 'zaso' ),
			),
			'image' => array(
				'type'        => 'media',
				'label'       => __( 'Custom Icon', 'zaso' ),
				'description' => __( 'Override "Icon" with your own uploaded image.', 'zaso' ),
				'library'     => 'image',
				'fallback'    => true,
			),
			'extra_id' => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'alignment' => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'zaso' ),
						'default' => 'center',
						'options' => array(
							'left'   => __( 'Left', 'zaso' ),
							'center' => __( 'Center', 'zaso' ),
							'right'  => __( 'Right', 'zaso' ),
						),
					),
					'number_color' => array(
						'type'    => 'color',
						'label'   => __( 'Number Color', 'zaso' ),
						'default' => '#1e293b',
					),
					'number_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Number Size', 'zaso' ),
						'default' => '3rem',
					),
					'title_color' => array(
						'type'    => 'color',
						'label'   => __( 'Title Color', 'zaso' ),
						'default' => '#64748b',
					),
					'title_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Title Size', 'zaso' ),
						'default' => '1rem',
					),
					'icon_color' => array(
						'type'    => 'color',
						'label'   => __( 'Icon Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'icon_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Icon Size', 'zaso' ),
						'default' => '2.5rem',
					),
				),
			),
		);

		// Add filter.
		$zaso_counter_fields = apply_filters( 'zaso_counter_fields', $zaso_counter_field_array );

		parent::__construct(
			'zen-addons-siteorigin-counter',
			__( 'ZASO - Counter', 'zaso' ),
			array(
				'description'   => __( 'An animated number that counts up when scrolled into view.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_counter_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		$design = $instance['design'];

		return apply_filters( 'zaso_counter_less_variables', array(
			'number_color' => $design['number_color'],
			'number_size'  => $design['number_size'],
			'title_color'  => $design['title_color'],
			'title_size'   => $design['title_size'],
			'icon_color'   => $design['icon_color'],
			'icon_size'    => $design['icon_size'],
		) );

	}

	function get_template_variables( $instance, $args ) {

		// Resolve a custom icon image, mirroring the Icon widget approach.
		$src  = siteorigin_widgets_get_attachment_image_src(
			$instance['image'],
			'full',
			! empty( $instance['image_fallback'] ) ? $instance['image_fallback'] : false
		);
		$attr = array();
		if ( ! empty( $src ) ) {
			$attr['src'] = $src[0];
			if ( ! empty( $src[1] ) ) {
				$attr['width'] = $src[1];
			}
			if ( ! empty( $src[2] ) ) {
				$attr['height'] = $src[2];
			}
			$attr['alt'] = get_post_meta( $instance['image'], '_wp_attachment_image_alt', true );
		}

		// Map the separator choice to an actual character for both PHP and JS.
		$separators = array(
			'none'  => '',
			'comma' => ',',
			'space' => ' ',
		);
		$separator  = isset( $separators[ $instance['separator'] ] ) ? $separators[ $instance['separator'] ] : '';

		$decimals = max( 0, (int) $instance['decimals'] );
		$end      = (float) $instance['end'];

		// Pre-format the final value: shown as the default (no-JS / reduced-motion)
		// state and used for the accessible label.
		$formatted_end = number_format( $end, $decimals, '.', $separator );

		return apply_filters( 'zaso_counter_template_variables', array(
			'start'         => (float) $instance['start'],
			'end'           => $end,
			'duration'      => max( 0, (int) $instance['duration'] ),
			'decimals'      => $decimals,
			'separator'     => $separator,
			'prefix'        => (string) $instance['prefix'],
			'suffix'        => (string) $instance['suffix'],
			'title'         => (string) $instance['title'],
			'icon'          => $instance['icon'],
			'image'         => $instance['image'],
			'image_attr'    => $attr,
			'alignment'     => $instance['design']['alignment'],
			'formatted_end' => $formatted_end,
		) );

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-counter',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-counter', __FILE__, 'Zen_Addons_SiteOrigin_Counter_Widget' );


endif;
