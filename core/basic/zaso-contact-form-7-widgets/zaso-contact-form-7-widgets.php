<?php
/**
 * Widget Name: ZASO - Contact Form 7
 * Widget ID: zen-addons-siteorigin-contact-form-7
 * Description: Display CF7 forms.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Contact_Form_7_Widget' ) ) :


class Zen_Addons_SiteOrigin_Contact_Form_7_Widget extends SiteOrigin_Widget {

	function __construct() {

        $all_sidebars = array();
        $sidebar_id_options = array();
        $sidebars_widgets = get_option( 'sidebars_widgets', array() );

        if( $sidebars_widgets ) {
            foreach( $sidebars_widgets as $swkey => $swval ) {
                if( 'wp_inactive_widgets' == $swkey || 'array_version' == $swkey )
                    continue;
                
                $all_sidebars[$swkey] = __( ucwords( str_replace( '-', ' ', $swkey ) ), 'zaso' );
            }
        }
        
		// ZASO field array
		$zaso_icon_field_array = array(
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
		$zaso_icon_fields = apply_filters( 'zaso_icon_fields', $zaso_icon_field_array );

		parent::__construct(
			'zen-addons-siteorigin-contact-form-7',
			__( 'ZASO - Contact Form 7', 'zaso' ),
			array(
				'description' 	=> __( 'Get widget sidebar.', 'zaso' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_icon_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_template_variables( $instance, $args ) {

		// return the goodies.
		return apply_filters( 'zaso_contact_form_7_template_variables', array(
			'sidebar_id' => $instance['sidebar_id']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-contact-form-7', __FILE__, 'Zen_Addons_SiteOrigin_Contact_Form_7_Widget' );


endif;