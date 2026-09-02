<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Alert Box
 * Widget ID: zen-addons-siteorigin-alert-box
 * Description: Show a styled info, success, warning, or error message.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! function_exists( 'zaso_alert_box_design_options' ) ) :
	/**
	 * Curated "designs" for the Alert Box widget.
	 *
	 * The free core ships six ready-made designs inline; Zen Addons Pro appends
	 * its twenty-four additional designs via the shared `zaso_alert_designs`
	 * filter (the Pro controller self-gates on a valid license, so an unlicensed
	 * or lapsed site only ever sees the six free entries). The empty-string key
	 * is the classic "Default" box and adds no class, keeping every existing
	 * instance byte-identical.
	 *
	 * @return array Map of design id => human label.
	 */
	function zaso_alert_box_design_options() {
		$zaso_alert_box_free_designs = array(
			''            => __( 'Default (classic box)', 'zen-addons-for-siteorigin-page-builder' ),
			'left-accent' => __( 'Left Accent (success)', 'zen-addons-for-siteorigin-page-builder' ),
			'soft-tint'   => __( 'Soft Tint (info)', 'zen-addons-for-siteorigin-page-builder' ),
			'outlined'    => __( 'Outlined (warning)', 'zen-addons-for-siteorigin-page-builder' ),
			'icon-badge'  => __( 'Icon Badge Card (error)', 'zen-addons-for-siteorigin-page-builder' ),
			'top-bar'     => __( 'Top Bar (neutral)', 'zen-addons-for-siteorigin-page-builder' ),
			'solid'       => __( 'Solid Fill (info)', 'zen-addons-for-siteorigin-page-builder' ),
		);

		return apply_filters( 'zaso_alert_designs', $zaso_alert_box_free_designs );
	}
endif;

if ( ! function_exists( 'zaso_alert_box_design_description' ) ) :
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
	function zaso_alert_box_design_description() {
		$white_label = class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label();

		if ( $white_label ) {
			return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. Leave on "Default (classic box)" to build your own look with the Layout, Style and Design colour settings instead.', 'zen-addons-for-siteorigin-page-builder' );
		}

		return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. The free core ships six; Zen Addons Pro unlocks twenty-four more (license required). Leave on "Default (classic box)" to build your own look with the Layout, Style and Design colour settings instead.', 'zen-addons-for-siteorigin-page-builder' );
	}
endif;

if( ! class_exists( 'Zen_Addons_SiteOrigin_Alert_Box_Widget' ) ) :


class Zen_Addons_SiteOrigin_Alert_Box_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_alert_box_field_array = array(
			'alert_message' => array(
				'type'  => 'tinymce',
				'label' => __( 'Messages' , 'zen-addons-for-siteorigin-page-builder' )
			),
			'alert_type' => array(
				'type'        => 'select',
				'label'       => __( 'Alert Type', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => 'none',
				'description' => __( 'Adds a leading icon and a screen-reader label so the alert type is not conveyed by colour alone.', 'zen-addons-for-siteorigin-page-builder' ),
				'options'     => array(
					'none'    => __( 'None', 'zen-addons-for-siteorigin-page-builder' ),
					'info'    => __( 'Info', 'zen-addons-for-siteorigin-page-builder' ),
					'success' => __( 'Success', 'zen-addons-for-siteorigin-page-builder' ),
					'warning' => __( 'Warning', 'zen-addons-for-siteorigin-page-builder' ),
					'error'   => __( 'Error', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'custom_icon' => array(
				'type'        => 'icon',
				'label'       => __( 'Custom Icon', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Optional. Overrides the alert type / design icon.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'alert_closebtn' => array(
				'type'    => 'select',
				'label'   => __( 'Close Button', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'show',
				'options' => array(
					'show'  => __( 'Show', 'zen-addons-for-siteorigin-page-builder' ),
					'hide' => __( 'Hide', 'zen-addons-for-siteorigin-page-builder' ),
				)
			),
			'width' => array(
				'type'        => 'select',
				'label'       => __( 'Width', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => 'full',
				'description' => __( 'Full width fills the container; Content width shrinks to fit the message.', 'zen-addons-for-siteorigin-page-builder' ),
				'options'     => array(
					'full'    => __( 'Full width', 'zen-addons-for-siteorigin-page-builder' ),
					'content' => __( 'Content width', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'extra_id' => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
				'description'	=> __( 'Add an extra ID.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'layout' => array(
				'type'        => 'select',
				'label'       => __( 'Layout', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => 'default',
				'description' => __( 'The structural shape of the alert: border, shadow, padding and icon placement. Layout sets the frame; Style (below) sets the colours.', 'zen-addons-for-siteorigin-page-builder' ),
				'options'     => array(
					'default'     => __( 'Default (bordered box)', 'zen-addons-for-siteorigin-page-builder' ),
					'card'        => __( 'Card (elevated, soft shadow)', 'zen-addons-for-siteorigin-page-builder' ),
					'left-accent' => __( 'Left Accent (flat bar)', 'zen-addons-for-siteorigin-page-builder' ),
					'banner'      => __( 'Banner (horizontal band)', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'design_variant' => array(
				'type'        => 'select',
				'label'       => __( 'Pre-made Design', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => '',
				'description' => zaso_alert_box_design_description(),
				'options'     => zaso_alert_box_design_options(),
			),
			'design' => array(
				'type' =>  'section',
				'label' => __( 'Design (custom colours)', 'zen-addons-for-siteorigin-page-builder' ),
				'hide' => true,
				'fields' => array(
					'message_box' => array(
						'type' => 'section',
						'label' => __( 'Alert Box', 'zen-addons-for-siteorigin-page-builder' ),
						'hide' => true,
						'fields' => array(
							'message_background_color' => array(
								'type' => 'color',
								'label' => __( 'Background Color',  'zen-addons-for-siteorigin-page-builder' ),
								'default' => '#e7e8ea',
							),
							'message_font_color' => array(
								'type'    => 'color',
								'label'   => __( 'Font Color', 'zen-addons-for-siteorigin-page-builder' ),
								'default' => '#464a4e',
							),
							'message_font_size' => array(
								'type'    => 'measurement',
								'label'   => __( 'Font Size', 'zen-addons-for-siteorigin-page-builder' ),
								'default' => '1rem',
							),
							'message_margin' => array(
								'type' => 'section',
								'label' => __( 'Margin', 'zen-addons-for-siteorigin-page-builder' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
								),
							),
							'message_padding' => array(
								'type' => 'section',
								'label' => __( 'Padding', 'zen-addons-for-siteorigin-page-builder' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1em'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '2.3em'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1em'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1.2em'
									),
								),
							),
							'message_border' => array(
								'type' => 'section',
								'label' => __( 'Border Settings', 'zen-addons-for-siteorigin-page-builder' ),
								'hide' => true,
								'fields' => array(
									'bw_top' => array(
										'type' => 'measurement',
										'label' => __( 'Top Border Width', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1px'
									),
									'bw_right' => array(
										'type' => 'measurement',
										'label' => __( 'Right Border Width', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1px'
									),
									'bw_bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom Border Width', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1px'
									),
									'bw_left' => array(
										'type' => 'measurement',
										'label' => __( 'Left Border Width', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '1px'
									),
									'br_top' => array(
										'type' => 'measurement',
										'label' => __( 'Top Border Radius', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'br_right' => array(
										'type' => 'measurement',
										'label' => __( 'Right Border Radius', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'br_bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom Border Radius', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'br_left' => array(
										'type' => 'measurement',
										'label' => __( 'Left Border Radius', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => '0px'
									),
									'border_style' => array(
										'type'    => 'select',
										'label'   => __( 'Border Style', 'zen-addons-for-siteorigin-page-builder' ),
										'default' => 'solid',
										'options' => array(
											'none'  => __( 'none', 'zen-addons-for-siteorigin-page-builder' ),
											'hidden' => __( 'Hidden', 'zen-addons-for-siteorigin-page-builder' ),
											'dotted' => __( 'Dotted', 'zen-addons-for-siteorigin-page-builder' ),
											'dashed' => __( 'Dashed', 'zen-addons-for-siteorigin-page-builder' ),
											'solid'  => __( 'Solid', 'zen-addons-for-siteorigin-page-builder' ),
											'double' => __( 'Double', 'zen-addons-for-siteorigin-page-builder' ),
											'groove' => __( 'Groove', 'zen-addons-for-siteorigin-page-builder' ),
											'ridge'  => __( 'Ridge', 'zen-addons-for-siteorigin-page-builder' ),
											'inset'  => __( 'Inset', 'zen-addons-for-siteorigin-page-builder' ),
											'outset' => __( 'Outset', 'zen-addons-for-siteorigin-page-builder' ),
										)
									),
									'border_color' => array(
										'type' => 'color',
										'label' => __( 'Border Color',  'zen-addons-for-siteorigin-page-builder' ),
										'default' => '#dddfe2',
									),
								),
							),
						),
					),
				),
			),
		);

		// add filter
		$zaso_alert_box_fields = apply_filters( 'zaso_alert_box_fields', $zaso_alert_box_field_array );

		parent::__construct(
			'zen-addons-siteorigin-alert-box',
			__( 'Zen Addons - Alert Box', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'Create contextual feedback and flexible alert messages.', 'zen-addons-for-siteorigin-page-builder' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_alert_box_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// Defensive: a design_style preset may fill only some of the message_box
		// sub-fields (e.g. colours + padding + border, but not margin). Default
		// any missing piece so a partially-filled design can never crash the LESS
		// output. A fully-saved Default instance already carries every key, so its
		// output is unchanged (these fallbacks only apply to absent keys).
		$design      = isset( $instance['design'] ) && is_array( $instance['design'] ) ? $instance['design'] : array();
		$message_box = isset( $design['message_box'] ) && is_array( $design['message_box'] ) ? $design['message_box'] : array();

		$margin  = wp_parse_args(
			( isset( $message_box['message_margin'] ) && is_array( $message_box['message_margin'] ) ) ? $message_box['message_margin'] : array(),
			array( 'top' => '0px', 'right' => '0px', 'bottom' => '0px', 'left' => '0px' )
		);
		$padding = wp_parse_args(
			( isset( $message_box['message_padding'] ) && is_array( $message_box['message_padding'] ) ) ? $message_box['message_padding'] : array(),
			array( 'top' => '1em', 'right' => '2.3em', 'bottom' => '1em', 'left' => '1.2em' )
		);
		$border  = wp_parse_args(
			( isset( $message_box['message_border'] ) && is_array( $message_box['message_border'] ) ) ? $message_box['message_border'] : array(),
			array(
				'bw_top' => '1px', 'bw_right' => '1px', 'bw_bottom' => '1px', 'bw_left' => '1px',
				'br_top' => '0px', 'br_right' => '0px', 'br_bottom' => '0px', 'br_left' => '0px',
				'border_style' => 'solid', 'border_color' => '#dddfe2',
			)
		);

		return apply_filters( 'zaso_alert_box_less_variables', array(
			// basic tabs title vars
			'message_background_color' => isset( $message_box['message_background_color'] ) ? $message_box['message_background_color'] : '#e7e8ea',
			'message_font_color'       => isset( $message_box['message_font_color'] ) ? $message_box['message_font_color'] : '#464a4e',
			'message_font_size'        => isset( $message_box['message_font_size'] ) ? $message_box['message_font_size'] : '1rem',
			'message_margin' => sprintf( '%1$s %2$s %3$s %4$s',
				$margin['top'], $margin['right'], $margin['bottom'], $margin['left'] ),
			'message_padding' => sprintf( '%1$s %2$s %3$s %4$s',
				$padding['top'], $padding['right'], $padding['bottom'], $padding['left'] ),
			'message_border_width' => sprintf( '%1$s %2$s %3$s %4$s',
				$border['bw_top'], $border['bw_right'], $border['bw_bottom'], $border['bw_left'] ),
			'message_border_radius' => sprintf( '%1$s %2$s %3$s %4$s',
				$border['br_top'], $border['br_right'], $border['br_bottom'], $border['br_left'] ),
			'message_border_style' => $border['border_style'],
			'message_border_color' => $border['border_color'],
		) );

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-alert-box',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array( 'jquery' ),
					ZASO_VERSION,
					true,
				)
			)
		);

		// Self-hosted Material Symbols Rounded @font-face. The design variants render
		// their per-variant glyph through this font via a ::before pseudo-element, so
		// it must load whenever an Alert Box renders (this free widget renders both the
		// six free designs and the twenty-four Pro designs). SiteOrigin enqueues this
		// only on pages where the widget is present, and skips it if already enqueued.
		$this->register_frontend_styles(
			array(
				array(
					'zaso-material-symbols',
					ZASO_BASE_DIR . 'assets/css/material-symbols.css',
					array(),
					ZASO_VERSION,
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-alert-box', __FILE__, 'Zen_Addons_SiteOrigin_Alert_Box_Widget' );


endif;