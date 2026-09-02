<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Info Box
 * Widget ID: zen-addons-siteorigin-info-box
 * Description: Feature an image, title, text, and call-to-action button together.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Info_Box_Widget' ) ) :


class Zen_Addons_SiteOrigin_Info_Box_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_info_box_field_array = array(
			'info_title' => array(
				'type'  => 'text',
				'label' => __( 'Info Title', 'zen-addons-for-siteorigin-page-builder' )
			),
			'info_description' => array(
				'type'  => 'tinymce',
				'label' => __( 'Info Short Description' , 'zen-addons-for-siteorigin-page-builder' )
			),
			'info_image' => array(
				'type'  => 'media',
				'label' => __( 'Info Featured Image', 'zen-addons-for-siteorigin-page-builder' ),
				'library' => 'image',
				'fallback' => true,
            ),
			'info_image_size' => array(
				'type' => 'image-size',
				'label' => __('Info Featured Image Size', 'zen-addons-for-siteorigin-page-builder'),
			),
			'info_button_text' => array(
				'type'  => 'text',
				'label' => __( 'Info Button text', 'zen-addons-for-siteorigin-page-builder' )
			),
			'info_button_url' => array(
				'type'  => 'link',
				'label' => __( 'Info Button Url', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_id' => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
				'description'	=> __( 'Add an extra ID.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zen-addons-for-siteorigin-page-builder' ),
			)
		);

		// Add filter.
		$zaso_info_box_fields = apply_filters( 'zaso_info_box_fields', $zaso_info_box_field_array );

		parent::__construct(
			'zen-addons-siteorigin-info-box',
			__( 'Zen Addons - Info Box', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'Display information box - image, title, description and learn more button link', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_info_box_fields,
			ZASO_WIDGET_BASIC_DIR
		);

    }
    
	function initialize() {

		$this->register_frontend_styles(
			array(
				array(
					'zen-addons-siteorigin-info-box',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/styles/style.css',
					array(),
					ZASO_VERSION
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-info-box', __FILE__, 'Zen_Addons_SiteOrigin_Info_Box_Widget' );


endif;