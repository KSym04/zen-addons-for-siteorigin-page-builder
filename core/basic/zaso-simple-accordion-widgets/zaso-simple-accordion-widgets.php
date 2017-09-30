<?php
/**
 * Widget Name: ZASO - Simple Accordion
 * Widget ID: zen-addons-siteorigin-simple-accordion
 * Description: Create vertically stacked list of items.
 * Author: DopeThemes
 * Author URI: http://wordpress.dopethemes.com
 */

if( !class_exists( 'Zen_Addons_SiteOrigin_Simple_Accordion_Widget' ) ) :


class Zen_Addons_SiteOrigin_Simple_Accordion_Widget extends SiteOrigin_Widget {

	function __construct() {

		parent::__construct(
			'zen-addons-siteorigin-simple-accordion',
			__( 'ZASO - Simple Accordion', 'zaso' ),
			array(
				'description'	=> __( 'Create vertically stacked list of items.', 'zaso' ),
				'help'			=> 'http://wordpress.dopethemes.com/',
				'panels_groups'	=> array('zaso-plugin-widgets')
			),
			array(),
			array(
				'accordion' => array(
					'type' => 'repeater',
					'label' => __( 'Accordion List' , 'zaso' ),
					'item_name'  => __( 'Single Item', 'zaso' ),
							'item_label' => array(
								'selector'     => "[name*='accordion_field_title']",
								'update_event' => 'change',
								'value_method' => 'val'
							),
					'fields' => array(
								'accordion_field_title' => array(
									'type' 	=> 'text',
									'label' => esc_html__( 'Accordion Title' )
								),
								'accordion_field_content' => array(
									'type' 	=> 'tinymce',
									'label' => esc_html__( 'Accordion Content' ),
									'row' 	=> 20
								),
							)
				),
				'list_opening_options' => array(
					'type' => 'select',
					'label' => __( 'List Opening Options', 'zaso' ),
					'description' => __( 'Select an opening options. [Default: Open first item]', 'zaso' ),
					'default' => '1',
					'options' => array(
					  'Open first item' => __( 'Open first item', 'zaso' ),
					  'Open all items'  => __( 'Open all items', 'zaso' ),
					  'Close all items' => __( 'Close all items', 'zaso' )
					),
				),
				'item_behavior_options' => array(
					'type' => 'select',
					'label' => __( 'Item Behavior Options', 'zaso' ),
					'description' => __( 'Select item behavior. [Default: Open single item only]', 'zaso' ),
					'default' => '1',
					'options' => array(
					  'single' => __( 'Open single item only', 'zaso' ),
					  'multiple' => __( 'Allow multiple items open', 'zaso' )
					),
				),
				'extra_class' => array(
					'type' 		  => 'text',
					'label' 	  => __( 'Extra Class', 'zaso' ),
					'description' => __( 'Add an extra class for styling overrides. [Default: none]', 'zaso' ),
				)
			),
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function initialize() {

		$this->register_frontend_styles(
			array(
				array(
					'zen-addons-siteorigin-simple-accordion',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/css/style.css',
					array(),
					ZASO_VERSION
				)
			)
		);

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-simple-accordion',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array( 'jquery' ),
					ZASO_VERSION,
					true,
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-simple-accordion', __FILE__, 'Zen_Addons_SiteOrigin_Simple_Accordion_Widget' );


endif;