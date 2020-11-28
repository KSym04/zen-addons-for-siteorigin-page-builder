<?php
/**
 * Widget Name: ZASO - bbPress Lost Password
 * Widget ID: zen-addons-siteorigin-bbpress-lost-password
 * Description: Display the bbPress password retrieval form.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_BbPress_Lost_Password_Widget' ) ) :


class Zen_Addons_SiteOrigin_BbPress_Lost_Password_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_bbpress_lost_password_field_array = array(
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
		$zaso_bbpress_lost_password_fields = apply_filters( 'zaso_bbpress_lost_password_fields', $zaso_bbpress_lost_password_field_array );

		parent::__construct(
			'zen-addons-siteorigin-bbpress-lost-password',
			__( 'ZASO - bbPress Lost Password', 'zaso' ),
			array(
				'description' 	=> __( 'Display the bbPress password retrieval form.', 'zaso' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_bbpress_lost_password_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {
		return apply_filters( 'zaso_bbpress_lost_password_less_variables', array());
	}

	function get_template_variables( $instance, $args ) {
		return apply_filters( 'zaso_bbpress_lost_password_template_variables', array());
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-bbpress-lost-password', __FILE__, 'Zen_Addons_SiteOrigin_BbPress_Lost_Password_Widget' );


endif;