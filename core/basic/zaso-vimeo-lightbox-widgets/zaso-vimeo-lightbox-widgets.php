<?php
/**
 * Widget Name: ZASO - Vimeo Lightbox
 * Widget ID: zen-addons-siteorigin-vimeo-lightbox
 * Description: Pop-up lightbox for Vimeo videos.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Vimeo_Lightbox_Widget' ) ) :


class Zen_Addons_SiteOrigin_Vimeo_Lightbox_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_vimeo_lightbox_field_array = array(
			'video_url' => array(
				'type'  => 'text',
				'label' => __( 'Vimeo Video ID' , 'zaso' ),
				'description' => __( 'Insert Video ID, example: 12345678', 'zaso' )
            ),
			'video_loop' => array(
                'type' => 'select',
                'label' => __( 'Loop', 'zaso' ),
                'description' => __( 'Play the video again when it reaches the end, infinitely.', 'zaso' ),
                'default' => 0,
                'options' => array(
					'0' => __( 'No', 'zaso' ),
					'1' => __( 'Yes', 'zaso' )
				)
            ),
            'video_muted' => array(
                'type' => 'select',
                'label' => __( 'Muted', 'zaso' ),
                'description' => __( 'Set video to mute on load. Viewers can still adjust the volume preferences in the player.', 'zaso' ),
                'default' => 0,
                'options' => array(
					'0' => __( 'No', 'zaso' ),
					'1' => __( 'Yes', 'zaso' )
				)
            ),
            'video_do_not_track' => array(
                'type' => 'select',
                'label' => __( 'Do Not Track', 'zaso' ),
                'description' => __( 'Setting this parameter to "Yes" will block the player from tracking any session data, including all cookies and stats.', 'zaso' ),
                'default' => 0,
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
		$zaso_vimeo_lightbox_fields = apply_filters( 'zaso_vimeo_lightbox_fields', $zaso_vimeo_lightbox_field_array );

		parent::__construct(
			'zen-addons-siteorigin-vimeo-lightbox',
			__( 'ZASO - Vimeo Lightbox', 'zaso' ),
			array(
				'description'   => __( 'Pop-up lightbox for Viemo videos.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_vimeo_lightbox_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		return apply_filters( 'zaso_vimeo_lightbox_less_variables', array(
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
					'zen-addons-siteorigin-vimeo-lightbox',
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
siteorigin_widget_register( 'zen-addons-siteorigin-vimeo-lightbox', __FILE__, 'Zen_Addons_SiteOrigin_Vimeo_Lightbox_Widget' );


endif;