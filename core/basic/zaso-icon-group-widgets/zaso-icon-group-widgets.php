<?php
/**
 * Widget Name: ZASO - Icon Group
 * Widget ID: zen-addons-siteorigin-icon-group
 * Description: Set group of icon.
 * Author: DopeThemes
 * Author URI: http://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Icon_Group_Widget' ) ) :


class Zen_Addons_SiteOrigin_Icon_Group_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_icon_group_field_array = array(
			'title' => array(
				'type'  => 'text',
				'label' => __( 'Title', 'zaso' )
			),
			'icon_group' => array(
				'type' => 'repeater',
				'label' => __( 'Icon List' , 'zaso' ),
				'item_name'  => __( 'Item', 'zaso' ),
				'item_label' => array(
					'selector'      => "[name*='icon_text']",
					'update_event'  => 'change',
					'value_method'  => 'val'
				),
				'fields' => array(
					'icon' => array(
						'type'  => 'icon',
						'label' => __( 'Icon', 'zaso' )
					),
					'image' => array(
						'type'  => 'media',
						'label' => __( 'Custom Icon', 'zaso' ),
						'description' => __( 'Override "Icon", Upload your custom icon here.', 'zaso' ),
						'library' => 'image',
						'fallback' => true
					),
					'color' => array(
						'type'  => 'color',
						'label' => __( 'Color', 'zaso' ),
						'default' => '#000000'
					),
					'size' => array(
						'type'  => 'measurement',
						'label' => __( 'Size', 'zaso' ),
						'default' => '1rem'
					),
					'alignment' => array(
						'type'  => 'select',
						'label' => __( 'Alignment', 'zaso' ),
						'default' => 'left',
						'options' => array(
							'left' => __( 'Left', 'zaso' ),
							'center' => __( 'Center', 'zaso' ),
							'right' => __( 'Right', 'zaso' ),
						)
					),
					'url' => array(
						'type'  => 'link',
						'label' => __( 'Destination URL', 'zaso' ),
					),
					'new_window' => array(
						'type'    => 'checkbox',
						'default' => false,
						'label'   => __( 'Open in a new window', 'zaso' ),
					),
					'icon_text' => array(
						'type'    => 'tinymce',
						'label'   => __( 'Text', 'zaso' ),
					)
				)
			),
			'extra_id' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			)
		);

		// add filter
		$zaso_icon_group_fields = apply_filters( 'zaso_icon_group_fields', $zaso_icon_group_field_array );

		parent::__construct(
			'zen-addons-siteorigin-icon-group',
			__( 'ZASO - Icon Group', 'zaso' ),
			array(
				'description' 	=> __( 'Set group of icon.', 'zaso' ),
				'help' 			=> 'http://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_icon_group_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// return apply_filters( 'zaso_icon_group_less_variables', array(
			// 'color'    => $instance['color'],
			// 'alignment'=> $instance['alignment'],
			// 'size'     => $instance['size']
		// ));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-icon-group', __FILE__, 'Zen_Addons_SiteOrigin_Icon_Group_Widget' );


endif;