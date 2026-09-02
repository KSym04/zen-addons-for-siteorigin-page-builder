<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - YouTube Lightbox
 * Widget ID: zen-addons-siteorigin-youtube-lightbox
 * Description: Open a YouTube video in a clickable pop-up lightbox.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Youtube_Lightbox_Widget' ) ) :


class Zen_Addons_SiteOrigin_Youtube_Lightbox_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_youtube_lightbox_field_array = array(
			'video_url' => array(
				'type'  => 'text',
				'label' => __( 'YouTube Video URL' , 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Insert URL, example: https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'video_rel' => array(
                'type' => 'select',
                'label' => __( 'Show Related Videos', 'zen-addons-for-siteorigin-page-builder' ),
                'options' => array(
					'0' => __( 'No', 'zen-addons-for-siteorigin-page-builder' ),
					'1' => __( 'Yes', 'zen-addons-for-siteorigin-page-builder' )
				)
			),
			'video_showinfo' => array(
                'type' => 'select',
                'label' => __( 'Show Info', 'zen-addons-for-siteorigin-page-builder' ),
                'options' => array(
					'0' => __( 'No', 'zen-addons-for-siteorigin-page-builder' ),
					'1' => __( 'Yes', 'zen-addons-for-siteorigin-page-builder' )
				)
			),
			'video_play_button' => array(
				'type'  => 'media',
				'label' => __( 'Video Play Button Image', 'zen-addons-for-siteorigin-page-builder' ),
				'library' => 'image',
				'fallback' => true
			),
			'video_play_button_hover' => array(
				'type'  => 'media',
				'label' => __( 'Video Play Button Image (Hover)', 'zen-addons-for-siteorigin-page-builder' ),
				'library' => 'image',
				'fallback' => true
			),
			'video_thumb' => array(
				'type'  => 'media',
				'label' => __( 'Video Thumbnail', 'zen-addons-for-siteorigin-page-builder' ),
				'library' => 'image',
				'fallback' => true
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
			),
		);

		// add filter
		$zaso_youtube_lightbox_fields = apply_filters( 'zaso_youtube_lightbox_fields', $zaso_youtube_lightbox_field_array );

		parent::__construct(
			'zen-addons-siteorigin-youtube-lightbox',
			__( 'Zen Addons - YouTube Lightbox', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'Pop-up lightbox for YouTube videos.', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_youtube_lightbox_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		return apply_filters( 'zaso_youtube_lightbox_less_variables', array(
			//'video_width' => $instance['video_width'],
			//'video_height' => $instance['video_height']
		));

	}

	function initialize() {

		$this->register_frontend_styles(
			array(
				array(
					'lity',
					ZASO_BASE_DIR . 'assets/vendor/lity/lity.min.css',
					array(),
					ZASO_VERSION
				)
			)
		);

		$this->register_frontend_styles(
			array(
				array(
					'zen-addons-siteorigin-youtube-lightbox',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/styles/style.css',
					array( 'lity' ),
					ZASO_VERSION
				)
			)
		);

		$this->register_frontend_scripts(
			array(
				array(
					'lity',
					ZASO_BASE_DIR . 'assets/vendor/lity/lity.min.js',
					array( 'jquery' ),
					ZASO_VERSION
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-youtube-lightbox', __FILE__, 'Zen_Addons_SiteOrigin_Youtube_Lightbox_Widget' );


endif;