<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Pricing Table
 * Widget ID: zen-addons-siteorigin-pricing-table
 * Description: Showcase your plans side-by-side with a features list, highlighted tier, and call-to-action button.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Pricing_Table_Widget' ) ) :


class Zen_Addons_SiteOrigin_Pricing_Table_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_pricing_table_field_array = array(
			'plans'   => array(
				'type'       => 'repeater',
				'label'      => __( 'Plans', 'zaso' ),
				'item_name'  => __( 'Plan', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[name]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'name'        => array(
						'type'  => 'text',
						'label' => __( 'Plan Name', 'zaso' ),
					),
					'price'       => array(
						'type'        => 'text',
						'label'       => __( 'Price', 'zaso' ),
						'description' => __( 'e.g. 29 or Free', 'zaso' ),
					),
					'period'      => array(
						'type'        => 'text',
						'label'       => __( 'Billing Period', 'zaso' ),
						'description' => __( 'e.g. /month', 'zaso' ),
					),
					'description' => array(
						'type'  => 'text',
						'label' => __( 'Short Description', 'zaso' ),
					),
					'features'    => array(
						'type'        => 'textarea',
						'label'       => __( 'Features', 'zaso' ),
						'description' => __( 'One feature per line.', 'zaso' ),
					),
					'cta_text'    => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'zaso' ),
						'default' => __( 'Get Started', 'zaso' ),
					),
					'cta_url'     => array(
						'type'  => 'link',
						'label' => __( 'Button URL', 'zaso' ),
					),
					'cta_new_tab' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Open in New Tab', 'zaso' ),
						'default' => false,
					),
					'featured'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Featured / Highlighted', 'zaso' ),
						'default' => false,
					),
				),
			),
			'columns' => array(
				'type'    => 'select',
				'label'   => __( 'Columns', 'zaso' ),
				'default' => '3',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			),
			'currency' => array(
				'type'    => 'text',
				'label'   => __( 'Currency Symbol', 'zaso' ),
				'default' => '$',
			),
			'design_style' => array(
				'type'           => 'presets',
				'label'          => __( 'Style', 'zaso' ),
				'default_preset' => '',
				/**
				 * Curated design presets ("skins") for this widget. The free core
				 * ships three; Zen Addons Pro appends its full library via the
				 * shared `zaso_design_presets` filter (gated on a valid license).
				 * Selecting one fills the Design fields below; users can still tweak.
				 */
				'options'        => apply_filters( 'zaso_design_presets', array(
					'soft'    => array(
						'label'  => __( 'Soft', 'zaso' ),
						'values' => array(
							'design' => array(
								'card_bg'           => '#ffffff',
								'card_border'       => '#e2e8f0',
								'card_radius'       => '12px',
								'button_bg'         => '#4f46e5',
								'button_text_color' => '#ffffff',
								'featured_color'    => '#4f46e5',
							),
						),
					),
					'bold'    => array(
						'label'  => __( 'Bold', 'zaso' ),
						'values' => array(
							'design' => array(
								'card_bg'           => '#ffffff',
								'card_border'       => '#cbd5e1',
								'card_radius'       => '14px',
								'button_bg'         => '#0f172a',
								'button_text_color' => '#ffffff',
								'featured_color'    => '#0f172a',
							),
						),
					),
					'minimal' => array(
						'label'  => __( 'Minimal', 'zaso' ),
						'values' => array(
							'design' => array(
								'card_bg'           => '#f8fafc',
								'card_border'       => '#e5e7eb',
								'card_radius'       => '8px',
								'button_bg'         => '#15803d',
								'button_text_color' => '#ffffff',
								'featured_color'    => '#15803d',
							),
						),
					),
				), 'pricing-table' ),
			),
			'design'  => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'featured_color'    => array(
						'type'    => 'color',
						'label'   => __( 'Featured Accent Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'card_bg'           => array(
						'type'    => 'color',
						'label'   => __( 'Card Background', 'zaso' ),
						'default' => '#ffffff',
					),
					'card_border'       => array(
						'type'    => 'color',
						'label'   => __( 'Card Border', 'zaso' ),
						'default' => '#e5e7eb',
					),
					'card_radius'       => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Corner Radius', 'zaso' ),
						'default' => '12px',
					),
					'button_bg'         => array(
						'type'    => 'color',
						'label'   => __( 'Button Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'button_text_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Text Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'gap'               => array(
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

		$zaso_pricing_table_fields = apply_filters( 'zaso_pricing_table_fields', $zaso_pricing_table_field_array );

		parent::__construct(
			'zen-addons-siteorigin-pricing-table',
			__( 'Zen Addons - Pricing Table', 'zaso' ),
			array(
				'description'   => __( 'Showcase your plans side-by-side with a features list, highlighted tier, and call-to-action button.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_pricing_table_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	function get_template_variables( $instance, $args ) {
		$plans = array();

		if ( ! empty( $instance['plans'] ) && is_array( $instance['plans'] ) ) {
			foreach ( $instance['plans'] as $raw ) {
				$features_raw  = isset( $raw['features'] ) ? $raw['features'] : '';
				$features_list = array_values(
					array_filter(
						array_map(
							'sanitize_text_field',
							explode( "\n", $features_raw )
						)
					)
				);

				$plans[] = array(
					'name'        => isset( $raw['name'] )        ? sanitize_text_field( $raw['name'] )        : '',
					'price'       => isset( $raw['price'] )       ? sanitize_text_field( $raw['price'] )       : '',
					'period'      => isset( $raw['period'] )      ? sanitize_text_field( $raw['period'] )      : '',
					'description' => isset( $raw['description'] ) ? sanitize_text_field( $raw['description'] ) : '',
					'features'    => $features_list,
					'cta_text'    => isset( $raw['cta_text'] )    ? sanitize_text_field( $raw['cta_text'] )    : '',
					'cta_url'     => isset( $raw['cta_url'] )     ? esc_url_raw( $raw['cta_url'] )             : '',
					'cta_new_tab' => ! empty( $raw['cta_new_tab'] ),
					'featured'    => ! empty( $raw['featured'] ),
				);
			}
		}

		$columns = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$columns = max( 1, min( 4, $columns ) );

		$currency    = isset( $instance['currency'] ) ? sanitize_text_field( $instance['currency'] ) : '$';
		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-pricing-table zaso-pricing-table--cols-' . $columns . ' ' . $extra_class );

		return apply_filters( 'zaso_pricing_table_template_variables', array(
			'plans'    => $plans,
			'currency' => $currency,
			'classes'  => $classes,
		), $instance );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_pricing_table_less_variables', array(
			'featured_color'    => isset( $design['featured_color'] )    ? $design['featured_color']    : '#4f46e5',
			'card_bg'           => isset( $design['card_bg'] )           ? $design['card_bg']           : '#ffffff',
			'card_border'       => isset( $design['card_border'] )       ? $design['card_border']       : '#e5e7eb',
			'card_radius'       => isset( $design['card_radius'] )       ? $design['card_radius']       : '12px',
			'button_bg'         => isset( $design['button_bg'] )         ? $design['button_bg']         : '#4f46e5',
			'button_text_color' => isset( $design['button_text_color'] ) ? $design['button_text_color'] : '#ffffff',
			'gap'               => isset( $design['gap'] )               ? $design['gap']               : '24px',
		) );
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-pricing-table', __FILE__, 'Zen_Addons_SiteOrigin_Pricing_Table_Widget' );


endif;
