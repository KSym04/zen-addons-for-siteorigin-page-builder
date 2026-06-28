<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Services Grid
 * Widget ID: zen-addons-siteorigin-services-grid
 * Description: Display a grid of services or features, each with an icon, title, description, and optional link.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Services_Grid_Widget' ) ) :


class Zen_Addons_SiteOrigin_Services_Grid_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_services_grid_field_array = array(
			'services' => array(
				'type'       => 'repeater',
				'label'      => __( 'Services', 'zaso' ),
				'item_name'  => __( 'Service', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[title]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'icon' => array(
						'type'  => 'icon',
						'label' => __( 'Icon', 'zaso' ),
					),
					'image' => array(
						'type'        => 'media',
						'label'       => __( 'Custom Icon Image', 'zaso' ),
						'description' => __( 'Override "Icon" with your own uploaded image.', 'zaso' ),
						'library'     => 'image',
						'fallback'    => true,
					),
					'title' => array(
						'type'  => 'text',
						'label' => __( 'Title', 'zaso' ),
					),
					'description' => array(
						'type'  => 'textarea',
						'label' => __( 'Description', 'zaso' ),
					),
					'link' => array(
						'type'  => 'link',
						'label' => __( 'Link URL', 'zaso' ),
					),
					'link_text' => array(
						'type'        => 'text',
						'label'       => __( 'Link Text', 'zaso' ),
						'description' => __( 'e.g. Learn more. Leave empty to hide the link.', 'zaso' ),
					),
					'link_new_tab' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Open Link in New Tab', 'zaso' ),
						'default' => false,
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
			'card_style' => array(
				'type'    => 'select',
				'label'   => __( 'Card Style', 'zaso' ),
				'default' => 'framed',
				'options' => array(
					'minimal' => __( 'Minimal', 'zaso' ),
					'framed'  => __( 'Framed', 'zaso' ),
				),
			),
			'alignment'  => array(
				'type'    => 'select',
				'label'   => __( 'Alignment', 'zaso' ),
				'default' => 'center',
				'options' => array(
					'left'   => __( 'Left', 'zaso' ),
					'center' => __( 'Center', 'zaso' ),
				),
			),
			'design'     => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'icon_color' => array(
						'type'    => 'color',
						'label'   => __( 'Icon Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'icon_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Icon Size', 'zaso' ),
						'default' => '2.5rem',
					),
					'icon_bg' => array(
						'type'    => 'color',
						'label'   => __( 'Icon Background', 'zaso' ),
						'default' => '',
						'description' => __( 'Optional circle behind the icon. Leave empty for none.', 'zaso' ),
					),
					'title_color' => array(
						'type'    => 'color',
						'label'   => __( 'Title Color', 'zaso' ),
						'default' => '#111111',
					),
					'description_color' => array(
						'type'    => 'color',
						'label'   => __( 'Description Color', 'zaso' ),
						'default' => '#444444',
					),
					'link_color' => array(
						'type'    => 'color',
						'label'   => __( 'Link Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'card_background' => array(
						'type'    => 'color',
						'label'   => __( 'Card Background', 'zaso' ),
						'default' => '#ffffff',
					),
					'card_padding' => array(
						'type'   => 'section',
						'label'  => __( 'Card Padding', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'top'    => array( 'type' => 'measurement', 'label' => __( 'Top', 'zaso' ),    'default' => '28px' ),
							'right'  => array( 'type' => 'measurement', 'label' => __( 'Right', 'zaso' ),  'default' => '28px' ),
							'bottom' => array( 'type' => 'measurement', 'label' => __( 'Bottom', 'zaso' ), 'default' => '28px' ),
							'left'   => array( 'type' => 'measurement', 'label' => __( 'Left', 'zaso' ),   'default' => '28px' ),
						),
					),
					'card_border_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Border Radius', 'zaso' ),
						'default' => '10px',
					),
					'gap' => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Cards', 'zaso' ),
						'default' => '24px',
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

		$zaso_services_grid_fields = apply_filters( 'zaso_services_grid_fields', $zaso_services_grid_field_array );

		parent::__construct(
			'zen-addons-siteorigin-services-grid',
			__( 'Zen Addons - Services Grid', 'zaso' ),
			array(
				'description'   => __( 'Display a grid of services or features, each with an icon, title, description, and optional link.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_services_grid_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Resolve a custom icon image for a service item.
	 *
	 * @param  mixed $attachment_id Attachment ID from the media field.
	 * @return array Image attributes (src/width/height/alt), empty if none.
	 */
	private function zaso_resolve_service_image( $attachment_id ) {
		$attr = array();
		if ( empty( $attachment_id ) ) {
			return $attr;
		}
		$src = siteorigin_widgets_get_attachment_image_src( $attachment_id, 'full' );
		if ( ! empty( $src[0] ) ) {
			$attr['src'] = $src[0];
			if ( ! empty( $src[1] ) ) {
				$attr['width'] = $src[1];
			}
			if ( ! empty( $src[2] ) ) {
				$attr['height'] = $src[2];
			}
			$alt         = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$attr['alt'] = $alt ? $alt : '';
		}
		return $attr;
	}

	function get_template_variables( $instance, $args ) {
		$services = array();
		if ( ! empty( $instance['services'] ) && is_array( $instance['services'] ) ) {
			foreach ( $instance['services'] as $raw ) {
				$link_text = isset( $raw['link_text'] ) ? $raw['link_text'] : '';
				$link_url  = isset( $raw['link'] ) ? $raw['link'] : '';

				$services[] = array(
					'icon'         => isset( $raw['icon'] ) ? $raw['icon'] : '',
					'image_attr'   => $this->zaso_resolve_service_image( isset( $raw['image'] ) ? $raw['image'] : '' ),
					'title'        => isset( $raw['title'] ) ? $raw['title'] : '',
					'description'  => isset( $raw['description'] ) ? $raw['description'] : '',
					'link_url'     => $link_url,
					'link_text'    => $link_text,
					'link_new_tab' => ! empty( $raw['link_new_tab'] ),
					'has_link'     => ( '' !== $link_url && '' !== trim( $link_text ) ),
				);
			}
		}

		$columns    = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$card_style = isset( $instance['card_style'] ) ? $instance['card_style'] : 'framed';
		$alignment  = isset( $instance['alignment'] ) ? $instance['alignment'] : 'center';

		$container_classes = array(
			'zaso-services-grid',
			'zaso-services-grid--cols-' . $columns,
			'zaso-services-grid--style-' . sanitize_html_class( $card_style ),
			'zaso-services-grid--align-' . sanitize_html_class( $alignment ),
		);
		if ( ! empty( $instance['extra_class'] ) ) {
			$container_classes[] = sanitize_html_class( $instance['extra_class'] );
		}

		return apply_filters( 'zaso_services_grid_template_variables', array(
			'services'          => $services,
			'container_classes' => implode( ' ', $container_classes ),
		) );
	}

	function get_less_variables( $instance ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$pad_top    = isset( $padding['top'] )    ? $padding['top']    : '28px';
		$pad_right  = isset( $padding['right'] )  ? $padding['right']  : '28px';
		$pad_bottom = isset( $padding['bottom'] ) ? $padding['bottom'] : '28px';
		$pad_left   = isset( $padding['left'] )   ? $padding['left']   : '28px';

		$icon_bg = isset( $design['icon_bg'] ) ? $design['icon_bg'] : '';

		return apply_filters( 'zaso_services_grid_less_variables', array(
			'icon_color'         => isset( $design['icon_color'] )         ? $design['icon_color']         : '#4f46e5',
			'icon_size'          => isset( $design['icon_size'] )          ? $design['icon_size']          : '2.5rem',
			'icon_bg'            => '' !== $icon_bg ? $icon_bg : 'transparent',
			'icon_pad'           => '' !== $icon_bg ? '0.9rem' : '0',
			'title_color'        => isset( $design['title_color'] )        ? $design['title_color']        : '#111111',
			'description_color'  => isset( $design['description_color'] )  ? $design['description_color']  : '#444444',
			'link_color'         => isset( $design['link_color'] )         ? $design['link_color']         : '#4f46e5',
			'card_background'    => isset( $design['card_background'] )    ? $design['card_background']    : '#ffffff',
			'card_padding'       => sprintf( '%s %s %s %s', $pad_top, $pad_right, $pad_bottom, $pad_left ),
			'card_border_radius' => isset( $design['card_border_radius'] ) ? $design['card_border_radius'] : '10px',
			'gap'                => isset( $design['gap'] )                ? $design['gap']                : '24px',
		) );
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-services-grid', __FILE__, 'Zen_Addons_SiteOrigin_Services_Grid_Widget' );


endif;
