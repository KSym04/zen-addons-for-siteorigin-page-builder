<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Flip Card
 * Widget ID: zen-addons-siteorigin-flip-card
 * Description: A card that flips on hover or keyboard focus to reveal back-side content and a call-to-action.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Flip_Card_Widget' ) ) :


class Zen_Addons_SiteOrigin_Flip_Card_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_flip_card_field_array = array(
			'flip_direction' => array(
				'type'    => 'select',
				'label'   => __( 'Flip Direction', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'zen-addons-for-siteorigin-page-builder' ),
					'vertical'   => __( 'Vertical', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'card_height'    => array(
				'type'        => 'measurement',
				'label'       => __( 'Card Height', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => '320px',
				'description' => __( 'Fixed height for both faces of the card.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'front'          => array(
				'type'   => 'section',
				'label'  => __( 'Front', 'zen-addons-for-siteorigin-page-builder' ),
				'hide'   => false,
				'fields' => array(
					'image'    => array(
						'type'     => 'media',
						'label'    => __( 'Front Image', 'zen-addons-for-siteorigin-page-builder' ),
						'library'  => 'image',
						'fallback' => true,
					),
					'title'    => array(
						'type'  => 'text',
						'label' => __( 'Front Title', 'zen-addons-for-siteorigin-page-builder' ),
					),
					'subtitle' => array(
						'type'  => 'text',
						'label' => __( 'Front Subtitle', 'zen-addons-for-siteorigin-page-builder' ),
					),
				),
			),
			'back'           => array(
				'type'   => 'section',
				'label'  => __( 'Back', 'zen-addons-for-siteorigin-page-builder' ),
				'hide'   => false,
				'fields' => array(
					'heading'     => array(
						'type'  => 'text',
						'label' => __( 'Back Heading', 'zen-addons-for-siteorigin-page-builder' ),
					),
					'text'        => array(
						'type'  => 'textarea',
						'label' => __( 'Back Text', 'zen-addons-for-siteorigin-page-builder' ),
					),
					'button_text' => array(
						'type'  => 'text',
						'label' => __( 'Button Text', 'zen-addons-for-siteorigin-page-builder' ),
					),
					'button_url'  => array(
						'type'    => 'link',
						'label'   => __( 'Button URL', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#',
					),
				),
			),
			'design'         => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zen-addons-for-siteorigin-page-builder' ),
				'hide'   => true,
				'fields' => array(
					'front_bg_color'    => array(
						'type'    => 'color',
						'label'   => __( 'Front Background Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#01949a',
					),
					'front_text_color'  => array(
						'type'    => 'color',
						'label'   => __( 'Front Text Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#ffffff',
					),
					'back_bg_color'     => array(
						'type'    => 'color',
						'label'   => __( 'Back Background Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#0f172a',
					),
					'back_text_color'   => array(
						'type'    => 'color',
						'label'   => __( 'Back Text Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#e2e8f0',
					),
					'button_bg_color'   => array(
						'type'    => 'color',
						'label'   => __( 'Button Background Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#00cdac',
					),
					'button_text_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Text Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#0f172a',
					),
					'border_radius'     => array(
						'type'    => 'measurement',
						'label'   => __( 'Border Radius', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '12px',
					),
				),
			),
			'extra_id'       => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_class'    => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
			),
		);

		$zaso_flip_card_fields = apply_filters( 'zaso_flip_card_fields', $zaso_flip_card_field_array );

		parent::__construct(
			'zen-addons-siteorigin-flip-card',
			__( 'Zen Addons - Flip Card', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'A card that flips on hover or keyboard focus to reveal back-side content and a call-to-action.', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_flip_card_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	function get_template_variables( $instance, $args ) {
		$front = isset( $instance['front'] ) ? $instance['front'] : array();
		$back  = isset( $instance['back'] )  ? $instance['back']  : array();

		$image_id  = isset( $front['image'] ) ? $front['image'] : '';
		$image_src = '';
		if ( ! empty( $image_id ) ) {
			$resolved = siteorigin_widgets_get_attachment_image_src( $image_id, 'full' );
			if ( ! empty( $resolved[0] ) ) {
				$image_src = $resolved[0];
			}
		}

		$direction   = ( isset( $instance['flip_direction'] ) && 'vertical' === $instance['flip_direction'] ) ? 'vertical' : 'horizontal';
		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-flip-card zaso-flip-card--' . $direction . ' ' . $extra_class );

		return apply_filters( 'zaso_flip_card_template_variables', array(
			'image_src'      => $image_src,
			'front_title'    => isset( $front['title'] ) ? $front['title'] : '',
			'front_subtitle' => isset( $front['subtitle'] ) ? $front['subtitle'] : '',
			'back_heading'   => isset( $back['heading'] ) ? $back['heading'] : '',
			'back_text'      => isset( $back['text'] ) ? $back['text'] : '',
			'button_text'    => isset( $back['button_text'] ) ? $back['button_text'] : '',
			'button_url'     => isset( $back['button_url'] ) ? $back['button_url'] : '',
			'classes'        => $classes,
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_flip_card_less_variables', array(
			'card_height'       => isset( $instance['card_height'] ) ? $instance['card_height'] : '320px',
			'front_bg_color'    => isset( $design['front_bg_color'] )    ? $design['front_bg_color']    : '#01949a',
			'front_text_color'  => isset( $design['front_text_color'] )  ? $design['front_text_color']  : '#ffffff',
			'back_bg_color'     => isset( $design['back_bg_color'] )     ? $design['back_bg_color']     : '#0f172a',
			'back_text_color'   => isset( $design['back_text_color'] )   ? $design['back_text_color']   : '#e2e8f0',
			'button_bg_color'   => isset( $design['button_bg_color'] )   ? $design['button_bg_color']   : '#00cdac',
			'button_text_color' => isset( $design['button_text_color'] ) ? $design['button_text_color'] : '#0f172a',
			'border_radius'     => isset( $design['border_radius'] )     ? $design['border_radius']     : '12px',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-flip-card',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-flip-card', __FILE__, 'Zen_Addons_SiteOrigin_Flip_Card_Widget' );


endif;
