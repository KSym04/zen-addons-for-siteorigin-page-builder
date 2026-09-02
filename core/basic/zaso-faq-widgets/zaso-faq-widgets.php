<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - FAQ
 * Widget ID: zen-addons-siteorigin-faq
 * Description: Display a collapsible FAQ list with optional Schema.org FAQPage structured data for rich results in Google Search.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_FAQ_Widget' ) ) :


class Zen_Addons_SiteOrigin_FAQ_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_faq_field_array = array(
			'items'      => array(
				'type'       => 'repeater',
				'label'      => __( 'FAQ Items', 'zen-addons-for-siteorigin-page-builder' ),
				'item_name'  => __( 'Item', 'zen-addons-for-siteorigin-page-builder' ),
				'item_label' => array(
					'selector'     => "[name*='[question]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'question' => array(
						'type'  => 'text',
						'label' => __( 'Question', 'zen-addons-for-siteorigin-page-builder' ),
					),
					'answer'   => array(
						'type'  => 'textarea',
						'label' => __( 'Answer', 'zen-addons-for-siteorigin-page-builder' ),
					),
				),
			),
			'schema'     => array(
				'type'        => 'checkbox',
				'label'       => __( 'Add FAQ Schema Markup', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => true,
				'description' => __( 'Adds Schema.org FAQPage structured data for search engine rich results.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'open_first' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Open First Item', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => false,
			),
			'design'     => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zen-addons-for-siteorigin-page-builder' ),
				'hide'   => true,
				'fields' => array(
					'question_color' => array(
						'type'    => 'color',
						'label'   => __( 'Question Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#111111',
					),
					'answer_color'   => array(
						'type'    => 'color',
						'label'   => __( 'Answer Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#444444',
					),
					'border_color'   => array(
						'type'    => 'color',
						'label'   => __( 'Border Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#e5e7eb',
					),
					'question_size'  => array(
						'type'    => 'measurement',
						'label'   => __( 'Question Font Size', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '1rem',
					),
					'item_spacing'   => array(
						'type'    => 'measurement',
						'label'   => __( 'Spacing Between Items', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '0px',
					),
				),
			),
			'extra_id'   => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
			),
		);

		$zaso_faq_fields = apply_filters( 'zaso_faq_fields', $zaso_faq_field_array );

		parent::__construct(
			'zen-addons-siteorigin-faq',
			__( 'Zen Addons - FAQ', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'Display a collapsible FAQ list with optional Schema.org FAQPage structured data for rich results in Google Search.', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_faq_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	function get_template_variables( $instance, $args ) {
		$items = array();

		if ( ! empty( $instance['items'] ) && is_array( $instance['items'] ) ) {
			foreach ( $instance['items'] as $raw ) {
				$question = isset( $raw['question'] ) ? sanitize_text_field( $raw['question'] ) : '';
				$answer   = isset( $raw['answer'] )   ? wp_kses_post( $raw['answer'] )           : '';

				if ( '' === $question && '' === $answer ) {
					continue;
				}

				$items[] = array(
					'question' => $question,
					'answer'   => $answer,
				);
			}
		}

		$schema      = ! empty( $instance['schema'] );
		$open_first  = ! empty( $instance['open_first'] );
		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-faq ' . $extra_class );

		$schema_json = '';
		if ( $schema && ! empty( $items ) ) {
			$schema_data = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => array_map(
					function( $item ) {
						return array(
							'@type'          => 'Question',
							'name'           => wp_strip_all_tags( $item['question'] ),
							'acceptedAnswer' => array(
								'@type' => 'Answer',
								'text'  => wp_strip_all_tags( $item['answer'] ),
							),
						);
					},
					$items
				),
			);
			$schema_json = wp_json_encode( $schema_data );
		}

		return apply_filters( 'zaso_faq_template_variables', array(
			'items'       => $items,
			'schema'      => $schema,
			'open_first'  => $open_first,
			'classes'     => $classes,
			'schema_json' => $schema_json,
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_faq_less_variables', array(
			'question_color' => isset( $design['question_color'] ) ? $design['question_color'] : '#111111',
			'answer_color'   => isset( $design['answer_color'] )   ? $design['answer_color']   : '#444444',
			'border_color'   => isset( $design['border_color'] )   ? $design['border_color']   : '#e5e7eb',
			'question_size'  => isset( $design['question_size'] )  ? $design['question_size']  : '1rem',
			'item_spacing'   => isset( $design['item_spacing'] )   ? $design['item_spacing']   : '0px',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-faq',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-faq', __FILE__, 'Zen_Addons_SiteOrigin_FAQ_Widget' );


endif;
