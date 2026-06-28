<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Image Gallery
 * Widget ID: zen-addons-siteorigin-image-gallery
 * Description: Display a responsive image gallery grid with an optional lightbox that opens full-size images in a pop-up overlay.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Image_Gallery_Widget' ) ) :


class Zen_Addons_SiteOrigin_Image_Gallery_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_image_gallery_field_array = array(
			'images' => array(
				'type'       => 'repeater',
				'label'      => __( 'Images', 'zaso' ),
				'item_name'  => __( 'Image', 'zaso' ),
				'item_label' => array(
					'selector'     => "[id*='[image]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'image' => array(
						'type'     => 'media',
						'label'    => __( 'Image', 'zaso' ),
						'library'  => 'image',
						'fallback' => true,
					),
					'caption' => array(
						'type'  => 'text',
						'label' => __( 'Caption', 'zaso' ),
					),
				),
			),
			'columns'    => array(
				'type'    => 'select',
				'label'   => __( 'Columns', 'zaso' ),
				'default' => '3',
				'options' => array(
					'2' => __( '2 Columns', 'zaso' ),
					'3' => __( '3 Columns', 'zaso' ),
					'4' => __( '4 Columns', 'zaso' ),
				),
			),
			'lightbox'   => array(
				'type'    => 'checkbox',
				'label'   => __( 'Enable Lightbox', 'zaso' ),
				'default' => true,
				'description' => __( 'Click an image to open it full-size in a pop-up overlay.', 'zaso' ),
			),
			'image_size' => array(
				'type'    => 'select',
				'label'   => __( 'Image Size', 'zaso' ),
				'default' => 'large',
				'options' => array(
					'thumbnail' => __( 'Thumbnail', 'zaso' ),
					'medium'    => __( 'Medium', 'zaso' ),
					'large'     => __( 'Large', 'zaso' ),
					'full'      => __( 'Full Size', 'zaso' ),
				),
			),
			'design'     => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'gap' => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Images', 'zaso' ),
						'default' => '8px',
					),
					'border_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Corner Radius', 'zaso' ),
						'default' => '4px',
					),
				),
			),
			'extra_id'   => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
			),
		);

		$zaso_image_gallery_fields = apply_filters( 'zaso_image_gallery_fields', $zaso_image_gallery_field_array );

		parent::__construct(
			'zen-addons-siteorigin-image-gallery',
			__( 'Zen Addons - Image Gallery', 'zaso' ),
			array(
				'description'   => __( 'Display a responsive image gallery grid with an optional lightbox that opens full-size images in a pop-up overlay.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_image_gallery_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Register the bundled lity lightbox assets for front-end use.
	 */
	function initialize() {
		$this->register_frontend_styles( array(
			array( 'lity', ZASO_BASE_DIR . 'assets/vendor/lity/lity.min.css', array(), ZASO_VERSION ),
		) );
		$this->register_frontend_scripts( array(
			array( 'lity', ZASO_BASE_DIR . 'assets/vendor/lity/lity.min.js', array( 'jquery' ), ZASO_VERSION ),
		) );
	}

	function get_template_variables( $instance, $args ) {
		$valid_sizes = array( 'thumbnail', 'medium', 'large', 'full' );
		$size        = isset( $instance['image_size'] ) ? sanitize_key( $instance['image_size'] ) : 'large';
		if ( ! in_array( $size, $valid_sizes, true ) ) {
			$size = 'large';
		}

		$images = array();
		if ( ! empty( $instance['images'] ) && is_array( $instance['images'] ) ) {
			foreach ( $instance['images'] as $raw ) {
				$id = isset( $raw['image'] ) ? absint( $raw['image'] ) : 0;
				if ( empty( $id ) ) {
					continue;
				}
				$thumb_src = siteorigin_widgets_get_attachment_image_src( $id, $size );
				if ( empty( $thumb_src[0] ) ) {
					continue;
				}
				$full_src = siteorigin_widgets_get_attachment_image_src( $id, 'full' );
				$alt      = get_post_meta( $id, '_wp_attachment_image_alt', true );

				$images[] = array(
					'thumb_src'    => $thumb_src[0],
					'thumb_width'  => ! empty( $thumb_src[1] ) ? absint( $thumb_src[1] ) : '',
					'thumb_height' => ! empty( $thumb_src[2] ) ? absint( $thumb_src[2] ) : '',
					'full_src'     => ! empty( $full_src[0] ) ? $full_src[0] : $thumb_src[0],
					'alt'          => $alt ? $alt : '',
					'caption'      => isset( $raw['caption'] ) ? $raw['caption'] : '',
				);
			}
		}

		$columns  = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$lightbox = ! empty( $instance['lightbox'] );

		$container_classes = array(
			'zaso-image-gallery',
			'zaso-image-gallery--cols-' . $columns,
		);
		if ( ! empty( $instance['extra_class'] ) ) {
			$container_classes[] = sanitize_html_class( $instance['extra_class'] );
		}

		return apply_filters( 'zaso_image_gallery_template_variables', array(
			'images'            => $images,
			'lightbox'          => $lightbox,
			'container_classes' => implode( ' ', $container_classes ),
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_image_gallery_less_variables', array(
			'gap'           => isset( $design['gap'] )           ? $design['gap']           : '8px',
			'border_radius' => isset( $design['border_radius'] ) ? $design['border_radius'] : '4px',
		) );
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-image-gallery', __FILE__, 'Zen_Addons_SiteOrigin_Image_Gallery_Widget' );


endif;
