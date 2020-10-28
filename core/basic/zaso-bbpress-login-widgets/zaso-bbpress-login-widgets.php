<?php
/**
 * Widget Name: ZASO - bbPress Login
 * Widget ID: zen-addons-siteorigin-bbpress-login
 * Description: Display the bbPress login form.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_BbPress_Login_Widget' ) ) :


class Zen_Addons_SiteOrigin_BbPress_Login_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_bbpress_login_field_array = array(
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
		$zaso_bbpress_login_fields = apply_filters( 'zaso_bbpress_login_fields', $zaso_bbpress_login_field_array );

		parent::__construct(
			'zen-addons-siteorigin-bbpress-login',
			__( 'ZASO - bbPress Login', 'zaso' ),
			array(
				'description' 	=> __( 'Display the bbPress login form.', 'zaso' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_bbpress_login_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {
		return apply_filters( 'zaso_bbpress_login_less_variables', array());
	}

	function get_template_variables( $instance, $args ) {
		return apply_filters( 'zaso_bbpress_login_template_variables', array());
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-bbpress-login', __FILE__, 'Zen_Addons_SiteOrigin_BbPress_Login_Widget' );


endif;