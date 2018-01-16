<?php
/**
 * Widget Name: ZASO - Icon
 * Widget ID: zen-addons-siteorigin-icon
 * Description: Set single icon on popular iconic font or upload your custom icon.
 * Author: DopeThemes
 * Author URI: http://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Icon_Widget' ) ) :


class Zen_Addons_SiteOrigin_Icon_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_icon_field_array = array(
			'icon' => array(
				'type'  => 'icon',
				'label' => __( 'Icon', 'zaso' )
			),
			'image' => array(
				'type'  => 'media',
				'label' => __( 'Custom Icon', 'zaso' ),
				'description' => __( 'Override "Icon", Upload your custom icon here.', 'zaso' ),
				'library' => 'image',
				'fallback' => true
			),
			'color' => array(
				'type'  => 'color',
				'label' => __( 'Color', 'zaso' ),
				'default' => '#000000'
			),
			'size' => array(
				'type'  => 'measurement',
				'label' => __( 'Size', 'zaso' ),
				'default' => '1rem'
			),
			'alignment' => array(
				'type'  => 'select',
				'label' => __( 'Alignment', 'zaso' ),
				'default' => 'left',
				'options' => array(
					'left' => __( 'Left', 'zaso' ),
					'center' => __( 'Center', 'zaso' ),
					'right' => __( 'Right', 'zaso' ),
				)
			),
			'url' => array(
				'type'  => 'link',
				'label' => __( 'Destination URL', 'zaso' ),
			),
			'new_window' => array(
				'type'    => 'checkbox',
				'default' => false,
				'label'   => __( 'Open in a new window', 'zaso' ),
			),
			'icon_text' => array(
				'type'    => 'tinymce',
				'label'   => __( 'Text', 'zaso' ),
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
			'zen-addons-siteorigin-icon',
			__( 'ZASO - Icon', 'zaso' ),
			array(
				'description' 	=> __( 'Set single icon on popular iconic font or upload your custom icon.', 'zaso' ),
				'help' 			=> 'http://www.dopethemes.com/',
				'panels_groups'	=> array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_icon_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		return apply_filters( 'zaso_icon_less_variables', array(
			'color'    => $instance['color'],
			'alignment'=> $instance['alignment'],
			'size'     => $instance['size']
		));

	}

	function get_template_variables( $instance, $args ) {

		// set custom icon src
		$src = siteorigin_widgets_get_attachment_image_src(
			$instance['image'],
			'full',
			! empty( $instance['image_fallback'] ) ? $instance['image_fallback'] : false
		);

		// set custom icon attributes
		$attr = array();
		if( !empty($src) ) {
			$attr = array( 'src' => $src[0] );

			if ( ! empty( $src[1] ) )
				$attr['width'] = $src[1];

			if ( ! empty( $src[2] ) )
				$attr['height'] = $src[2];

			if ( function_exists( 'wp_get_attachment_image_srcset' ) )
				$attr['srcset'] = wp_get_attachment_image_srcset( $instance['image'], 'full' );

			// Hotfix Photon
			if ( ! ( class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' ) ) ) {
				if ( function_exists( 'wp_get_attachment_image_sizes' ) ) {
					$attr['sizes'] = wp_get_attachment_image_sizes( $instance['image'], 'full' );
				}
			}
		}
		$attr = apply_filters( 'zaso_icon_template_variables_custom_icon_attr', $attr, $instance, $this );

		// set custom icon title
		$file_name = pathinfo( get_post_meta( $instance['image'], '_wp_attached_file', true ), PATHINFO_FILENAME );
		$title = get_the_title( $instance['image'] );

		if ( $title == $file_name )
			$title = '';

		$attr['title'] = $title;

		// set custom icon alt
		$attr['alt'] = get_post_meta( $instance['image'], '_wp_attachment_image_alt', true );

		// return the goodies
		return apply_filters( 'zaso_icon_template_variables', array(
			'icon' => $instance['icon'],
			'url' => $instance['url'],
			'new_window' => $instance['new_window'],
			'icon_text' => $instance['icon_text'],
			'attributes' => $attr,
			'image' => $instance['image'],
			'classes' => array( 'zaso-icon__image' )
		));

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-icon', __FILE__, 'Zen_Addons_SiteOrigin_Icon_Widget' );


endif;