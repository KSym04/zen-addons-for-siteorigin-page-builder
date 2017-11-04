<?php
/**
 * Widget Name: ZASO - Spacer
 * Widget ID: zen-addons-siteorigin-spacer
 * Description: Create an empty space between elements.
 * Author: DopeThemes
 * Author URI: http://www.dopethemes.com/
 */

if( !class_exists( 'Zen_Addons_SiteOrigin_Spacer_Widget' ) ) :


class Zen_Addons_SiteOrigin_Spacer_Widget extends SiteOrigin_Widget {

	function __construct() {

		parent::__construct(
			'zen-addons-siteorigin-spacer',
			__( 'ZASO - Spacer', 'zaso' ),
			array(
				'description' 	=> __( 'Create an empty space between elements.', 'zaso' ),
				'help' 			=> 'http://www.dopethemes.com/',
				'panels_groups'	=> array('zaso-plugin-widgets')
			),
			array(),
			array(
				'height' => array(
					'type' 		  => 'measurement',
					'default' 	  => '20',
					'label' 	  => __( 'Height', 'zaso' ),
					'description' => __( 'Set empty space height.', 'zaso' ),
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
				),
				'design' => array(
					'type' =>  'section',
					'label' => __( 'Design', 'zaso' ),
					'hide' => true,
					'fields' => array(
						'background_color' => array(
							'type' => 'color',
							'label' => __( 'Background Color', 'zaso' ),
							'default' => ''
						)
					)
				)
			),
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		return array(
			'background_color' => $instance['design']['background_color']
		);

	}

	function initialize() {

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-spacer', __FILE__, 'Zen_Addons_SiteOrigin_Spacer_Widget' );


endif;