<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Video
 * Widget ID: zen-addons-siteorigin-video
 * Description: Embed a responsive video from YouTube, Vimeo, or other providers.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Video_Widget' ) ) :


class Zen_Addons_SiteOrigin_Video_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_video_field_array = array(
			'video_url' => array(
				'type'  => 'text',
				'label' => __( 'Video URL' , 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Insert your video URL, example: https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'video_content' => array(
				'type'    => 'tinymce',
				'label'   => __( 'Content' , 'zen-addons-for-siteorigin-page-builder' ),
				'row'   => 20
			),
			'video_width' => array(
				'type'  => 'measurement',
				'label' => __( 'Width', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => '640px'
			),
			'video_height' => array(
				'type'  => 'measurement',
				'label' => __( 'Height', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => '360px'
			),
			'video_controls' => array(
				'type'    => 'select',
				'label'   => __( 'Controls' , 'zen-addons-for-siteorigin-page-builder' ),
				'options' => array(
					'flex' => __( 'Show', 'zen-addons-for-siteorigin-page-builder' ),
					'none'  => __( 'Hide', 'zen-addons-for-siteorigin-page-builder' ),
				)
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
		$zaso_video_fields = apply_filters( 'zaso_video_fields', $zaso_video_field_array );

		parent::__construct(
			'zen-addons-siteorigin-video',
			__( 'Zen Addons - Video', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'Add video from YouTube, Vimeo or another provider.', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_video_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		return apply_filters( 'zaso_video_less_variables', array(
			'video_control_visibility' => $instance['video_controls'],
			'video_width' => $instance['video_width'],
			'video_height' => $instance['video_height']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-video', __FILE__, 'Zen_Addons_SiteOrigin_Video_Widget' );


endif;