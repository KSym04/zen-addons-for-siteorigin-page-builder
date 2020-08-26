<?php
/**
 * Widget Name: ZASO - Contact Form 7
 * Widget ID: zen-addons-siteorigin-contact-form-7
 * Description: Display CF7 form.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Contact_Form_7_Widget' ) ) :


class Zen_Addons_SiteOrigin_Contact_Form_7_Widget extends SiteOrigin_Widget {

	function __construct() {

        $all_cf7 = array();
        $posts = get_posts(
            array(
                'numberposts' => -1,
                'post_type' => 'wpcf7_contact_form',
                'post_status' => 'publish'
            )
        );

        if( $posts ) {
            foreach( $posts as $post ) {
                $all_cf7[$post->ID] = __( $post->post_title, 'zaso' );
            }
        } else {
            $all_cf7 = array( '' => __( 'No existing form.', 'zaso' ) );
        }

		// ZASO field array
		$zaso_cf7_field_array = array(
			'cf7_id' => array(
                'type' => 'select',
                'label' => __( 'What CF7 form do you want to display?', 'zaso' ),
                'options' => $all_cf7
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

		// add filter
		$zaso_contact_form_7_fields = apply_filters( 'zaso_contact_form_7_fields', $zaso_cf7_field_array );

		parent::__construct(
			'zen-addons-siteorigin-contact-form-7',
			__( 'ZASO - Contact Form 7', 'zaso' ),
			array(
				'description' 	=> __( 'Display CF7 form.', 'zaso' ),
				'help' 			=> 'https://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_contact_form_7_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_template_variables( $instance, $args ) {

		// return the goodies.
		return apply_filters( 'zaso_contact_form_7_template_variables', array(
			'cf7_id' => $instance['cf7_id']
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-contact-form-7', __FILE__, 'Zen_Addons_SiteOrigin_Contact_Form_7_Widget' );


endif;