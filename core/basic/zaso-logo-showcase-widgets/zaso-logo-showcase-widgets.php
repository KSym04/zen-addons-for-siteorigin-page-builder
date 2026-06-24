<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: ZASO - Logo Showcase
 * Widget ID: zen-addons-siteorigin-logo-showcase
 * Description: Display a responsive grid of client or partner logos with an optional greyscale-to-colour hover effect.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Logo_Showcase_Widget' ) ) :


class Zen_Addons_SiteOrigin_Logo_Showcase_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_logo_showcase_field_array = array(
			'logos' => array(
				'type'       => 'repeater',
				'label'      => __( 'Logos', 'zaso' ),
				'item_name'  => __( 'Logo', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[alt]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'image' => array(
						'type'     => 'media',
						'label'    => __( 'Logo Image', 'zaso' ),
						'library'  => 'image',
						'fallback' => true,
					),
					'alt' => array(
						'type'        => 'text',
						'label'       => __( 'Alt Text', 'zaso' ),
						'description' => __( 'Describe the logo for screen readers. Inherits the media library alt text if left empty.', 'zaso' ),
					),
					'link' => array(
						'type'  => 'link',
						'label' => __( 'Link URL', 'zaso' ),
					),
					'link_new_tab' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Open Link in New Tab', 'zaso' ),
						'default' => false,
					),
				),
			),
			'columns'   => array(
				'type'    => 'select',
				'label'   => __( 'Columns', 'zaso' ),
				'default' => '5',
				'options' => array(
					'2' => __( '2 Columns', 'zaso' ),
					'3' => __( '3 Columns', 'zaso' ),
					'4' => __( '4 Columns', 'zaso' ),
					'5' => __( '5 Columns', 'zaso' ),
					'6' => __( '6 Columns', 'zaso' ),
				),
			),
			'grayscale' => array(
				'type'        => 'checkbox',
				'label'       => __( 'Greyscale Logos', 'zaso' ),
				'default'     => true,
				'description' => __( 'Display logos in greyscale and switch to full colour on hover.', 'zaso' ),
			),
			'alignment' => array(
				'type'    => 'select',
				'label'   => __( 'Alignment', 'zaso' ),
				'default' => 'center',
				'options' => array(
					'left'   => __( 'Left', 'zaso' ),
					'center' => __( 'Center', 'zaso' ),
					'right'  => __( 'Right', 'zaso' ),
				),
			),
			'design'    => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'logo_height' => array(
						'type'        => 'measurement',
						'label'       => __( 'Logo Height', 'zaso' ),
						'default'     => '60px',
						'description' => __( 'Maximum height for each logo image.', 'zaso' ),
					),
					'gap' => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Logos', 'zaso' ),
						'default' => '40px',
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

		$zaso_logo_showcase_fields = apply_filters( 'zaso_logo_showcase_fields', $zaso_logo_showcase_field_array );

		parent::__construct(
			'zen-addons-siteorigin-logo-showcase',
			__( 'ZASO - Logo Showcase', 'zaso' ),
			array(
				'description'   => __( 'Display a responsive grid of client or partner logos with an optional greyscale-to-colour hover effect.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_logo_showcase_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Resolve attachment image attributes for a logo item.
	 *
	 * @param  mixed $attachment_id Attachment ID from the media field.
	 * @return array Image attributes (src/width/height/alt), empty array if none.
	 */
	private function zaso_resolve_logo_image( $attachment_id ) {
		if ( empty( $attachment_id ) ) {
			return array();
		}
		$src = siteorigin_widgets_get_attachment_image_src( $attachment_id, 'full' );
		if ( empty( $src[0] ) ) {
			return array();
		}
		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		$attr = array(
			'src' => $src[0],
			'alt' => $alt ? $alt : '',
		);
		if ( ! empty( $src[1] ) ) {
			$attr['width'] = $src[1];
		}
		if ( ! empty( $src[2] ) ) {
			$attr['height'] = $src[2];
		}
		return $attr;
	}

	function get_template_variables( $instance, $args ) {
		$logos = array();
		if ( ! empty( $instance['logos'] ) && is_array( $instance['logos'] ) ) {
			foreach ( $instance['logos'] as $raw ) {
				$img = $this->zaso_resolve_logo_image( isset( $raw['image'] ) ? $raw['image'] : '' );
				if ( empty( $img['src'] ) ) {
					continue; // Skip entries with no image.
				}
				// Prefer user-supplied alt; fall back to media library alt.
				if ( ! empty( $raw['alt'] ) ) {
					$img['alt'] = $raw['alt'];
				}
				if ( ! isset( $img['alt'] ) ) {
					$img['alt'] = '';
				}
				// Accessible name for a LINKED logo when no alt is available, so the
				// link is never nameless: fall back to the link host, then a generic.
				$link_url   = isset( $raw['link'] ) ? $raw['link'] : '';
				$link_label = $img['alt'];
				if ( '' === (string) $link_label && '' !== (string) $link_url ) {
					$host       = wp_parse_url( $link_url, PHP_URL_HOST );
					$link_label = $host ? $host : __( 'Visit link', 'zaso' );
				}
				$logos[] = array(
					'img'          => $img,
					'link_url'     => $link_url,
					'link_new_tab' => ! empty( $raw['link_new_tab'] ),
					'link_label'   => $link_label,
				);
			}
		}

		$columns   = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 5;
		$grayscale = ! empty( $instance['grayscale'] );
		$alignment = isset( $instance['alignment'] ) ? $instance['alignment'] : 'center';

		$container_classes = array(
			'zaso-logo-showcase',
			'zaso-logo-showcase--cols-' . $columns,
			'zaso-logo-showcase--align-' . sanitize_html_class( $alignment ),
		);
		if ( $grayscale ) {
			$container_classes[] = 'zaso-logo-showcase--grayscale';
		}
		if ( ! empty( $instance['extra_class'] ) ) {
			$container_classes[] = sanitize_html_class( $instance['extra_class'] );
		}

		return apply_filters( 'zaso_logo_showcase_template_variables', array(
			'logos'             => $logos,
			'container_classes' => implode( ' ', $container_classes ),
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_logo_showcase_less_variables', array(
			'logo_height' => isset( $design['logo_height'] ) ? $design['logo_height'] : '60px',
			'gap'         => isset( $design['gap'] )         ? $design['gap']         : '40px',
		) );
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-logo-showcase', __FILE__, 'Zen_Addons_SiteOrigin_Logo_Showcase_Widget' );


endif;
