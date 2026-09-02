<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - bbPress Login
 * Widget ID: zen-addons-siteorigin-bbpress-login
 * Description: Embed the bbPress member login form.
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
		$zaso_bbpress_login_fields = apply_filters( 'zaso_bbpress_login_fields', $zaso_bbpress_login_field_array );

		parent::__construct(
			'zen-addons-siteorigin-bbpress-login',
			__( 'Zen Addons - bbPress Login', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description' 	=> __( 'Display the bbPress login form.', 'zen-addons-for-siteorigin-page-builder' ),
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