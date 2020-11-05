<?php
/**
 * Widget Name: ZASO - bbPress Topic Index
 * Widget ID: zen-addons-siteorigin-bbpress-topic-index
 * Description: Display recent 15 topics across all forums with optional pagination and search.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_BbPress_Topic_Index_Widget' ) ) :


class Zen_Addons_SiteOrigin_BbPress_Topic_Index_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_bbpress_topic_index_field_array = array(
			// 'bbpress_topic_index_theme' => array(
            //     'type' => 'select',
			// 	'label' => __( 'Style Theme Preset', 'zaso' ),
			// 	'default' => 'default',
			// 	'options' => array(
            //         'default'  => __( 'Inherit theme defaults', 'zaso' ),
			// 		'dark'  => __( 'Dark Theme', 'zaso' ),
			// 		'light'  => __( 'Light Theme', 'zaso' )
			// 	)
			// ),
			'bbpress_topic_index_theme_pagination' => array(
                'type' => 'select',
				'label' => __( 'Show Pagination', 'zaso' ),
				'default' => 'block',
				'options' => array(
                    'block'  => __( 'Yes', 'zaso' ),
					'none'  => __( 'No', 'zaso' )
				)
			),
			'bbpress_topic_index_theme_search' => array(
                'type' => 'select',
				'label' => __( 'Show Search', 'zaso' ),
				'default' => 'block',
				'options' => array(
                    'block'  => __( 'Yes', 'zaso' ),
					'none'  => __( 'No', 'zaso' )
				)
			),
			'extra_id' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' )
			),
			'extra_class' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' )
			)
		);

		// Add filter.
		$zaso_bbpress_topic_index_fields = apply_filters( 'zaso_bbpress_topic_index_fields', $zaso_bbpress_topic_index_field_array );

		parent::__construct(
			'zen-addons-siteorigin-bbpress-topic-index',
			__( 'ZASO - bbPress Topic Index', 'zaso' ),
			array(
				'description' 	=> __( 'Display recent 15 topics across all forums with optional pagination and search.', 'zaso' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_bbpress_topic_index_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// return the goodies.
		return apply_filters( 'zaso_bbpress_topic_index_less_variables', array(
			//'bbpress_topic_index_theme' => $instance['bbpress_topic_index_theme'],
			'bbpress_topic_index_theme_pagination' => $instance['bbpress_topic_index_theme_pagination'],
			'bbpress_topic_index_theme_search' => $instance['bbpress_topic_index_theme_search']
		));

	}

	function get_template_variables( $instance, $args ) {

		// return the goodies.
		return apply_filters( 'zaso_bbpress_topic_index_template_variables', array(
			//'bbpress_topic_index_theme' => $instance['bbpress_topic_index_theme'],
			'bbpress_topic_index_theme_pagination' => $instance['bbpress_topic_index_theme_pagination'],
			'bbpress_topic_index_theme_search' => $instance['bbpress_topic_index_theme_search']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-bbpress-topic-index', __FILE__, 'Zen_Addons_SiteOrigin_BbPress_Topic_Index_Widget' );


endif;