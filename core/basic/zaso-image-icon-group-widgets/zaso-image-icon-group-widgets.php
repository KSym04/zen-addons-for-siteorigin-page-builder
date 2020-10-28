<?php
/**
 * Widget Name: ZASO - Image Icon Group
 * Widget ID: zen-addons-siteorigin-image-icon-group
 * Description: Set group of image icon.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Image_Icon_Group_Widget' ) ) :


class Zen_Addons_SiteOrigin_Image_Icon_Group_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_image_icon_group_field_array = array(
			'image_icon_group' => array(
				'type' => 'repeater',
				'label' => __( 'Image Icon Group' , 'zaso' ),
				'item_name'  => __( 'Single Image Icon', 'zaso' ),
				'item_label' => array(
					'selector'      => "[name*='image_icon_group_title']",
					'update_event'  => 'change',
					'value_method'  => 'val'
				),
				'fields' => array(
					'image_icon_group_title' => array(
						'type'  => 'text',
						'label' => __( 'Title' , 'zaso' )
					),
                    'image_icon_group_photo' => array(
                        'type'  => 'media',
                        'label' => __( 'Image Icon', 'zaso' ),
                        'library' => 'image',
                        'fallback' => true
                    ),
                    'image_icon_group_link' => array(
                        'type'  => 'link',
                        'label' => __( 'Link', 'zaso' ),
                        'default' => '#'
                    ),
				)
            ),
            'image_icon_group_orientation' => array(
				'type'    => 'select',
				'label'   => __( 'Icon Group Layout', 'zaso' ),
				'default' => 'horizontal',
				'options' => array(
                    'horizontal'  => __( 'Horizontal', 'zaso' ),
                    'vertical'  => __( 'Vertical', 'zaso' )
				)
			),
			'image_icon_group_text_display' => array(
				'type' => 'select',
				'label' => __( 'Show Title', 'zaso' ),
				'default' => 'none',
				'options' => array(
					'block'  => __( 'Yes', 'zaso' ),
					'none'  => __( 'No', 'zaso' )
				)
			),
			'design' => array(
				'type' =>  'section',
				'label' => __( 'Icon Spacings', 'zaso' ),
				'hide' => true,
				'fields' => array(
					'spacings' => array(
						'type' => 'section',
						'label' => __( 'Settings', 'zaso' ),
						'hide' => true,
						'fields' => array(
							'single_icon_margin' => array(
								'type' => 'section',
								'label' => __( 'Margin', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zaso' ),
										'default' => '0px'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zaso' ),
										'default' => '0px'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zaso' ),
										'default' => '0px'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zaso' ),
										'default' => '0px'
									),
								),
							),
							'single_icon_padding' => array(
								'type' => 'section',
								'label' => __( 'Padding', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zaso' ),
										'default' => '0px'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zaso' ),
										'default' => '0px'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zaso' ),
										'default' => '0px'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zaso' ),
										'default' => '0px'
									),
								),
							),
						),
					),
				),
			),
			'extra_id' => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
				'description'	=> __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			)
		);

		// Add filter.
		$zaso_image_icon_group_fields = apply_filters( 'zaso_image_icon_group_fields', $zaso_image_icon_group_field_array );

		parent::__construct(
			'zen-addons-siteorigin-image-icon-group',
			__( 'ZASO - Image Icon Group', 'zaso' ),
			array(
				'description'   => __( 'Set group of image icon.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_image_icon_group_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// Variable pointers.
		$design = $instance['design'];
		$spacings = $design['spacings'];
		$spacings_margin = $spacings['single_icon_margin'];
		$spacings_padding = $spacings['single_icon_padding'];

		return apply_filters( 'zaso_image_icon_group_less_variables', array(
			'spacings_margin' => sprintf( '%1$s %2$s %3$s %4$s',
				$spacings_margin['top'],
				$spacings_margin['right'],
				$spacings_margin['bottom'],
				$spacings_margin['left'] ),
			'spacings_padding' => sprintf( '%1$s %2$s %3$s %4$s',
				$spacings_padding['top'],
				$spacings_padding['right'],
				$spacings_padding['bottom'],
				$spacings_padding['left'] ),
		) );

	}

	function get_template_variables( $instance, $args ) {

		// return the goodies.
		return apply_filters( 'zaso_image_icon_group_template_variables', array(
			'image_icon_group_text_display' => $instance['image_icon_group_text_display']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-image-icon-group', __FILE__, 'Zen_Addons_SiteOrigin_Image_Icon_Group_Widget' );


endif;