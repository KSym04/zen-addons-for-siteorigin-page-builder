<?php
/**
 * Widget Name: ZASO - YouTube Lightbox
 * Widget ID: zen-addons-siteorigin-youtube-lightbox
 * Description: Pop-up lightbox for YouTube videos.
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
				'label' => __( 'YouTube Video URL' , 'zaso' ),
				'description' => __( 'Insert URL, example: https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'zaso' ),
			),
			'video_rel' => array(
                'type' => 'select',
                'label' => __( 'Show Related Videos', 'zaso' ),
                'options' => array(
					'0' => __( 'No', 'zaso' ),
					'1' => __( 'Yes', 'zaso' )
				)
			),
			'video_showinfo' => array(
                'type' => 'select',
                'label' => __( 'Show Info', 'zaso' ),
                'options' => array(
					'0' => __( 'No', 'zaso' ),
					'1' => __( 'Yes', 'zaso' )
				)
			),
			'video_play_button' => array(
				'type'  => 'media',
				'label' => __( 'Video Play Button Image', 'zaso' ),
				'library' => 'image',
				'fallback' => true
			),
			'video_play_button_hover' => array(
				'type'  => 'media',
				'label' => __( 'Video Play Button Image (Hover)', 'zaso' ),
				'library' => 'image',
				'fallback' => true
			),
			'video_thumb' => array(
				'type'  => 'media',
				'label' => __( 'Video Thumbnail', 'zaso' ),
				'library' => 'image',
				'fallback' => true
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
			),
		);

		// add filter
		$zaso_youtube_lightbox_fields = apply_filters( 'zaso_youtube_lightbox_fields', $zaso_youtube_lightbox_field_array );

		parent::__construct(
			'zen-addons-siteorigin-youtube-lightbox',
			__( 'ZASO - YouTube Lightbox', 'zaso' ),
			array(
				'description'   => __( 'Pop-up lightbox for YouTube videos.', 'zaso' ),
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
					'https://cdn.jsdelivr.net/npm/lity@2.4.1/dist/lity.min.css',
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
					'https://cdn.jsdelivr.net/npm/lity@2.4.1/dist/lity.min.js',
					array( 'jquery' ),
					ZASO_VERSION
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-youtube-lightbox', __FILE__, 'Zen_Addons_SiteOrigin_Youtube_Lightbox_Widget' );


endif;