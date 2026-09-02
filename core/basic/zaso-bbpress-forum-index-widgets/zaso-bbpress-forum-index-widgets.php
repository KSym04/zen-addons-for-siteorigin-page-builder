<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - bbPress Forum Index
 * Widget ID: zen-addons-siteorigin-bbpress-forum-index
 * Description: Show the full bbPress forum index in your layout.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_BbPress_Forum_Index_Widget' ) ) :


class Zen_Addons_SiteOrigin_BbPress_Forum_Index_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_bbpress_forum_index_field_array = array(
			'bbpress_forum_index_theme_pagination' => array(
                'type' => 'select',
				'label' => __( 'Show Pagination', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'block',
				'options' => array(
                    'block'  => __( 'Yes', 'zen-addons-for-siteorigin-page-builder' ),
					'none'  => __( 'No', 'zen-addons-for-siteorigin-page-builder' )
				)
			),
			'bbpress_forum_index_theme_breadcrumbs' => array(
                'type' => 'select',
				'label' => __( 'Show Breadcrumbs', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'block',
				'options' => array(
                    'block'  => __( 'Yes', 'zen-addons-for-siteorigin-page-builder' ),
					'none'  => __( 'No', 'zen-addons-for-siteorigin-page-builder' )
				)
			),
			'bbpress_forum_index_theme_search' => array(
                'type' => 'select',
				'label' => __( 'Show Search', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'block',
				'options' => array(
                    'block'  => __( 'Yes', 'zen-addons-for-siteorigin-page-builder' ),
					'none'  => __( 'No', 'zen-addons-for-siteorigin-page-builder' )
				)
			),
			'extra_id' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra ID.', 'zen-addons-for-siteorigin-page-builder' )
			),
			'extra_class' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zen-addons-for-siteorigin-page-builder' )
			)
		);

		// Add filter.
		$zaso_bbpress_forum_index_fields = apply_filters( 'zaso_bbpress_forum_index_fields', $zaso_bbpress_forum_index_field_array );

		parent::__construct(
			'zen-addons-siteorigin-bbpress-forum-index',
			__( 'Zen Addons - bbPress Forum Index', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description' 	=> __( 'Display entire bbPress forum index.', 'zen-addons-for-siteorigin-page-builder' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_bbpress_forum_index_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// return the goodies.
		return apply_filters( 'zaso_bbpress_forum_index_less_variables', array(
			'bbpress_forum_index_theme_pagination' => $instance['bbpress_forum_index_theme_pagination'],
			'bbpress_forum_index_theme_search' => $instance['bbpress_forum_index_theme_search'],
			'bbpress_forum_index_theme_breadcrumbs' => $instance['bbpress_forum_index_theme_breadcrumbs']
		));

	}

	function get_template_variables( $instance, $args ) {

		// return the goodies.
		return apply_filters( 'zaso_bbpress_forum_index_template_variables', array(
			'bbpress_forum_index_theme_pagination' => $instance['bbpress_forum_index_theme_pagination'],
			'bbpress_forum_index_theme_search' => $instance['bbpress_forum_index_theme_search'],
			'bbpress_forum_index_theme_breadcrumbs' => $instance['bbpress_forum_index_theme_breadcrumbs']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-bbpress-forum-index', __FILE__, 'Zen_Addons_SiteOrigin_BbPress_Forum_Index_Widget' );


endif;