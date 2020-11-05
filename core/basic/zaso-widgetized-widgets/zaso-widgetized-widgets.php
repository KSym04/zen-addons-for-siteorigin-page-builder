<?php
/**
 * Widget Name: ZASO - Widgetized
 * Widget ID: zen-addons-siteorigin-widgetized
 * Description: Get existing widget sidebars to display on the main content.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Widgetized_Widget' ) ) :


class Zen_Addons_SiteOrigin_Widgetized_Widget extends SiteOrigin_Widget {

	function __construct() {

        $all_sidebars = array();
        $sidebars_widgets = get_option( 'sidebars_widgets', array() );

        if( $sidebars_widgets ) {
            foreach( $sidebars_widgets as $swkey => $swval ) {
                if( 'wp_inactive_widgets' == $swkey || 'array_version' == $swkey )
                    continue;
                
                $all_sidebars[$swkey] = __( ucwords( str_replace( '-', ' ', $swkey ) ), 'zaso' );
            }
        }
        
		// ZASO field array.
		$zaso_widgetized_field_array = array(
			'sidebar_id' => array(
                'type' => 'select',
                'label' => __( 'Widget Sidebar', 'zaso' ),
                'options' => $all_sidebars
			),
			'extra_id' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type' 		  => 'text',
				'label' 	  => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			)
		);

		// add filter
		$zaso_widgetized_fields = apply_filters( 'zaso_widgetized_fields', $zaso_widgetized_field_array );

		parent::__construct(
			'zen-addons-siteorigin-widgetized',
			__( 'ZASO - Widgetized', 'zaso' ),
			array(
				'description' 	=> __( 'Get existing widget sidebars to display on the main content.', 'zaso' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_widgetized_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_template_variables( $instance, $args ) {

		// return the goodies.
		return apply_filters( 'zaso_widgetized_template_variables', array(
			'sidebar_id' => $instance['sidebar_id']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-widgetized', __FILE__, 'Zen_Addons_SiteOrigin_Widgetized_Widget' );


endif;