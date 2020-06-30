<?php
/**
 * Widget Name: ZASO - Video
 * Widget ID: zen-addons-siteorigin-video
 * Description: Add video from YouTube, Vimeo or another provider.
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
				'label' => __( 'Video URL' , 'zaso' ),
				'description' => __( 'Insert your video URL, example: https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'zaso' ),
			),
			'video_content' => array(
				'type'    => 'tinymce',
				'label'   => __( 'Content' , 'zaso' ),
				'row'   => 20
			),
			'video_width' => array(
				'type'  => 'measurement',
				'label' => __( 'Width', 'zaso' ),
				'default' => '640px'
			),
			'video_height' => array(
				'type'  => 'measurement',
				'label' => __( 'Height', 'zaso' ),
				'default' => '360px'
			),
			'video_controls' => array(
				'type'    => 'select',
				'label'   => __( 'Controls' , 'zaso' ),
				'options' => array(
					'flex' => __( 'Show', 'zaso' ),
					'none'  => __( 'Hide', 'zaso' ),
				)
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
		$zaso_video_fields = apply_filters( 'zaso_video_fields', $zaso_video_field_array );

		parent::__construct(
			'zen-addons-siteorigin-video',
			__( 'ZASO - Video', 'zaso' ),
			array(
				'description'   => __( 'Add video from YouTube, Vimeo or another provider.', 'zaso' ),
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