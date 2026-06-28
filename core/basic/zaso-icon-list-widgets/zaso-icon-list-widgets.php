<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Icon List
 * Widget ID: zen-addons-siteorigin-icon-list
 * Description: A vertical or horizontal list of items, each with an icon and text.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Icon_List_Widget' ) ) :


class Zen_Addons_SiteOrigin_Icon_List_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_icon_list_field_array = array(
			'items'        => array(
				'type'       => 'repeater',
				'label'      => __( 'List Items', 'zaso' ),
				'item_name'  => __( 'Item', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[text]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields'     => array(
					'text' => array(
						'type'  => 'text',
						'label' => __( 'Text', 'zaso' ),
					),
					'icon' => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'zaso' ),
						'description' => __( 'Leave empty to use the default icon below.', 'zaso' ),
					),
					'link' => array(
						'type'  => 'link',
						'label' => __( 'Link', 'zaso' ),
					),
				),
			),
			'default_icon' => array(
				'type'        => 'icon',
				'label'       => __( 'Default Icon', 'zaso' ),
				'description' => __( 'Used for any item that does not set its own icon.', 'zaso' ),
			),
			'layout'       => array(
				'type'    => 'select',
				'label'   => __( 'Layout', 'zaso' ),
				'default' => 'vertical',
				'options' => array(
					'vertical'   => __( 'Vertical', 'zaso' ),
					'horizontal' => __( 'Horizontal', 'zaso' ),
				),
			),
			'design'       => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'icon_color' => array(
						'type'    => 'color',
						'label'   => __( 'Icon Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'icon_size'  => array(
						'type'    => 'measurement',
						'label'   => __( 'Icon Size', 'zaso' ),
						'default' => '1rem',
					),
					'text_color' => array(
						'type'    => 'color',
						'label'   => __( 'Text Color', 'zaso' ),
						'default' => '#333333',
					),
					'text_size'  => array(
						'type'    => 'measurement',
						'label'   => __( 'Text Size', 'zaso' ),
						'default' => '1rem',
					),
					'gap'        => array(
						'type'        => 'measurement',
						'label'       => __( 'Spacing Between Items', 'zaso' ),
						'default'     => '0.75rem',
					),
				),
			),
			'extra_id'     => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class'  => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			),
		);

		// Add filter.
		$zaso_icon_list_fields = apply_filters( 'zaso_icon_list_fields', $zaso_icon_list_field_array );

		parent::__construct(
			'zen-addons-siteorigin-icon-list',
			__( 'Zen Addons - Icon List', 'zaso' ),
			array(
				'description'   => __( 'Display a list of items, each with an icon and text.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_icon_list_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		$design = $instance['design'];

		return apply_filters( 'zaso_icon_list_less_variables', array(
			'icon_color' => $design['icon_color'],
			'icon_size'  => $design['icon_size'],
			'text_color' => $design['text_color'],
			'text_size'  => $design['text_size'],
			'gap'        => $design['gap'],
		) );

	}

	function get_template_variables( $instance, $args ) {

		$layout = ( 'horizontal' === $instance['layout'] ) ? 'horizontal' : 'vertical';

		return apply_filters( 'zaso_icon_list_template_variables', array(
			'items'        => ! empty( $instance['items'] ) ? $instance['items'] : array(),
			'default_icon' => $instance['default_icon'],
			'layout'       => $layout,
		) );

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-icon-list', __FILE__, 'Zen_Addons_SiteOrigin_Icon_List_Widget' );


endif;
