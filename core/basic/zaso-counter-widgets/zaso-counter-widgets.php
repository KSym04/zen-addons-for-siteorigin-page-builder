<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Counter
 * Widget ID: zen-addons-siteorigin-counter
 * Description: An animated number that counts up to a target value when it scrolls into view.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! function_exists( 'zaso_counter_design_options' ) ) :
	/**
	 * Curated "designs" for the Counter widget.
	 *
	 * The free core ships six ready-made designs inline; Zen Addons Pro appends
	 * its twenty-four additional designs via the shared `zaso_counter_designs`
	 * filter (the Pro controller self-gates on a valid license, so an unlicensed
	 * or lapsed site only ever sees the six free entries). The empty-string key
	 * is the classic stacked counter and adds no class, keeping every existing
	 * instance byte-identical.
	 *
	 * @return array Map of design id => human label.
	 */
	function zaso_counter_design_options() {
		$zaso_counter_free_designs = array(
			''          => __( 'Default (classic counter)', 'zen-addons-for-siteorigin-page-builder' ),
			'icon-card' => __( 'Icon Card (success)', 'zen-addons-for-siteorigin-page-builder' ),
			'centered'  => __( 'Centered (info)', 'zen-addons-for-siteorigin-page-builder' ),
			'icon-top'  => __( 'Icon Top (installs)', 'zen-addons-for-siteorigin-page-builder' ),
			'badge'     => __( 'Badge (uptime)', 'zen-addons-for-siteorigin-page-builder' ),
			'divider'   => __( 'Divider (neutral)', 'zen-addons-for-siteorigin-page-builder' ),
			'underline' => __( 'Underline (rating)', 'zen-addons-for-siteorigin-page-builder' ),
		);

		return apply_filters( 'zaso_counter_designs', $zaso_counter_free_designs );
	}
endif;

if ( ! function_exists( 'zaso_counter_design_description' ) ) :
	/**
	 * Help text for the "Pre-made Design" field.
	 *
	 * On a white-labelled Pro site the agency's client must never see the real
	 * product name or an upsell (they already have the full library), so the brand
	 * + "unlocks twenty-four more" sentence is dropped. Everywhere else (free, or
	 * licensed-but-not-white-labelled) the upsell line is kept.
	 *
	 * @return string Field description.
	 */
	function zaso_counter_design_description() {
		$white_label = class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label();

		if ( $white_label ) {
			return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. Leave on "Default (classic counter)" to build your own look with the Layout, Style and Design colour settings instead.', 'zen-addons-for-siteorigin-page-builder' );
		}

		return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. The free core ships six; Zen Addons Pro unlocks twenty-four more (license required). Leave on "Default (classic counter)" to build your own look with the Layout, Style and Design colour settings instead.', 'zen-addons-for-siteorigin-page-builder' );
	}
endif;

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Counter_Widget' ) ) :


class Zen_Addons_SiteOrigin_Counter_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_counter_field_array = array(
			'start' => array(
				'type'    => 'number',
				'label'   => __( 'Start Value', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 0,
			),
			'end' => array(
				'type'    => 'number',
				'label'   => __( 'End Value', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 100,
			),
			'duration' => array(
				'type'        => 'number',
				'label'       => __( 'Animation Duration (ms)', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => 2000,
				'description' => __( 'How long the count-up takes, in milliseconds.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'decimals' => array(
				'type'    => 'number',
				'label'   => __( 'Decimal Places', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 0,
			),
			'separator' => array(
				'type'    => 'select',
				'label'   => __( 'Thousands Separator', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'none',
				'options' => array(
					'none'  => __( 'None', 'zen-addons-for-siteorigin-page-builder' ),
					'comma' => __( 'Comma (1,000)', 'zen-addons-for-siteorigin-page-builder' ),
					'space' => __( 'Space (1 000)', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'prefix' => array(
				'type'        => 'text',
				'label'       => __( 'Prefix', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Shown before the number, e.g. $.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'suffix' => array(
				'type'        => 'text',
				'label'       => __( 'Suffix', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Shown after the number, e.g. + or %.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'title' => array(
				'type'  => 'text',
				'label' => __( 'Title', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'icon' => array(
				'type'  => 'icon',
				'label' => __( 'Icon', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'image' => array(
				'type'        => 'media',
				'label'       => __( 'Custom Icon', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Override "Icon" with your own uploaded image.', 'zen-addons-for-siteorigin-page-builder' ),
				'library'     => 'image',
				'fallback'    => true,
			),
			'extra_id' => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra ID.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'layout' => array(
				'type'        => 'select',
				'label'       => __( 'Layout', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => 'default',
				'description' => __( 'The structural shape of the counter: stacked, boxed card, inline row or ringed circle. Layout sets the frame; Style (below) sets the colours.', 'zen-addons-for-siteorigin-page-builder' ),
				'options'     => array(
					'default' => __( 'Default (stacked)', 'zen-addons-for-siteorigin-page-builder' ),
					'card'    => __( 'Card (boxed, soft shadow)', 'zen-addons-for-siteorigin-page-builder' ),
					'inline'  => __( 'Inline (icon + number in a row)', 'zen-addons-for-siteorigin-page-builder' ),
					'circle'  => __( 'Circle (number in a ring)', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'design_variant' => array(
				'type'        => 'select',
				'label'       => __( 'Pre-made Design', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => '',
				'description' => zaso_counter_design_description(),
				'options'     => zaso_counter_design_options(),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design (custom colours)', 'zen-addons-for-siteorigin-page-builder' ),
				'hide'   => true,
				'fields' => array(
					'alignment' => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => 'center',
						'options' => array(
							'left'   => __( 'Left', 'zen-addons-for-siteorigin-page-builder' ),
							'center' => __( 'Center', 'zen-addons-for-siteorigin-page-builder' ),
							'right'  => __( 'Right', 'zen-addons-for-siteorigin-page-builder' ),
						),
					),
					'number_color' => array(
						'type'    => 'color',
						'label'   => __( 'Number Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#1e293b',
					),
					'number_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Number Size', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '3rem',
					),
					'title_color' => array(
						'type'    => 'color',
						'label'   => __( 'Title Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#64748b',
					),
					'title_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Title Size', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '1rem',
					),
					'icon_color' => array(
						'type'    => 'color',
						'label'   => __( 'Icon Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#4f46e5',
					),
					'icon_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Icon Size', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '2.5rem',
					),
				),
			),
		);

		// Add filter.
		$zaso_counter_fields = apply_filters( 'zaso_counter_fields', $zaso_counter_field_array );

		parent::__construct(
			'zen-addons-siteorigin-counter',
			__( 'Zen Addons - Counter', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'An animated number that counts up when scrolled into view.', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_counter_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// Defensive: a design_style preset fills the design fields, but default any
		// missing key so a partially-filled preset can never emit a notice. A
		// fully-saved Default instance already carries every key, so these
		// fallbacks only apply to absent keys and its output is byte-identical.
		// Fallback values equal the widget's own design field defaults.
		$design = isset( $instance['design'] ) && is_array( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_counter_less_variables', array(
			'number_color' => isset( $design['number_color'] ) ? $design['number_color'] : '#1e293b',
			'number_size'  => isset( $design['number_size'] )  ? $design['number_size']  : '3rem',
			'title_color'  => isset( $design['title_color'] )  ? $design['title_color']  : '#64748b',
			'title_size'   => isset( $design['title_size'] )   ? $design['title_size']   : '1rem',
			'icon_color'   => isset( $design['icon_color'] )   ? $design['icon_color']   : '#4f46e5',
			'icon_size'    => isset( $design['icon_size'] )    ? $design['icon_size']    : '2.5rem',
		) );

	}

	function get_template_variables( $instance, $args ) {

		// Resolve a custom icon image, mirroring the Icon widget approach.
		$src  = siteorigin_widgets_get_attachment_image_src(
			$instance['image'],
			'full',
			! empty( $instance['image_fallback'] ) ? $instance['image_fallback'] : false
		);
		$attr = array();
		if ( ! empty( $src ) ) {
			$attr['src'] = $src[0];
			if ( ! empty( $src[1] ) ) {
				$attr['width'] = $src[1];
			}
			if ( ! empty( $src[2] ) ) {
				$attr['height'] = $src[2];
			}
			$attr['alt'] = get_post_meta( $instance['image'], '_wp_attachment_image_alt', true );
		}

		// Map the separator choice to an actual character for both PHP and JS.
		$separators = array(
			'none'  => '',
			'comma' => ',',
			'space' => ' ',
		);
		$separator  = isset( $separators[ $instance['separator'] ] ) ? $separators[ $instance['separator'] ] : '';

		$decimals = max( 0, (int) $instance['decimals'] );
		$end      = (float) $instance['end'];

		// Pre-format the final value: shown as the default (no-JS / reduced-motion)
		// state and used for the accessible label.
		$formatted_end = number_format( $end, $decimals, '.', $separator );

		return apply_filters( 'zaso_counter_template_variables', array(
			'start'         => (float) $instance['start'],
			'end'           => $end,
			'duration'      => max( 0, (int) $instance['duration'] ),
			'decimals'      => $decimals,
			'separator'     => $separator,
			'prefix'        => (string) $instance['prefix'],
			'suffix'        => (string) $instance['suffix'],
			'title'         => (string) $instance['title'],
			'icon'          => $instance['icon'],
			'image'         => $instance['image'],
			'image_attr'    => $attr,
			'alignment'     => isset( $instance['design']['alignment'] ) ? $instance['design']['alignment'] : 'center',
			'formatted_end' => $formatted_end,
		) );

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-counter',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-counter', __FILE__, 'Zen_Addons_SiteOrigin_Counter_Widget' );


endif;
