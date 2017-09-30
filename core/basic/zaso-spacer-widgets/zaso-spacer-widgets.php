<?php
/**
 * Widget Name: ZASO - Spacer
 * Widget ID: zen-addons-siteorigin-spacer
 * Description: Create empty space between elements.
 * Author: DopeThemes
 * Author URI: http://wordpress.dopethemes.com
 */

if( !class_exists( 'Zen_Addons_SiteOrigin_Spacer_Widget' ) ) :


class Zen_Addons_SiteOrigin_Spacer_Widget extends SiteOrigin_Widget {

	function __construct() {

		parent::__construct(
			'zen-addons-siteorigin-spacer',
			__( 'ZASO - Spacer', 'zaso' ),
			array(
				'description' 	=> __( 'Create empty space between elements.', 'zaso' ),
				'help' 			=> 'http://wordpress.dopethemes.com/',
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
				'extra_class' => array(
					'type' 		  => 'text',
					'label' 	  => __( 'Extra Class', 'zaso' ),
					'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
				)
			),
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function initialize() {

		$this->register_frontend_styles(
			array(
				array(
					'zen-addons-siteorigin-spacer',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/css/style.css',
					array(),
					ZASO_VERSION
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-spacer', __FILE__, 'Zen_Addons_SiteOrigin_Spacer_Widget' );


endif;