<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Hover Card
 * Widget ID: zen-addons-siteorigin-hover-card
 * Description: Show an image card with a title and button that animate on hover.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! function_exists( 'zaso_hover_card_design_options' ) ) :
	/**
	 * Curated "designs" for the Hover Card widget.
	 *
	 * The free core ships six ready-made designs inline; Zen Addons Pro appends
	 * its twenty-four additional designs via the shared `zaso_hover_card_designs`
	 * filter (the Pro controller self-gates on a valid license, so an unlicensed or
	 * lapsed site only ever sees the six free entries). The empty-string key is the
	 * classic "Default" hover card and adds no class, keeping every existing
	 * instance byte-identical.
	 *
	 * @since 1.10.10
	 *
	 * @return array Map of design id => human label.
	 */
	function zaso_hover_card_design_options() {
		$zaso_hover_card_free_designs = array(
			''                 => __( 'Default (classic hover card)', 'zaso' ),
			'slide-up-frosted' => __( 'Slide Up - Frosted (teal)', 'zaso' ),
			'slide-up-dark'    => __( 'Slide Up - Dark (green)', 'zaso' ),
			'slide-up-tinted'  => __( 'Slide Up - Tinted (cyan)', 'zaso' ),
			'overlay-scrim'    => __( 'Overlay - Dark Scrim (blue)', 'zaso' ),
			'overlay-solid'    => __( 'Overlay - Vivid Solid (blue)', 'zaso' ),
			'overlay-gradient' => __( 'Overlay - Gradient (blue)', 'zaso' ),
		);

		return apply_filters( 'zaso_hover_card_designs', $zaso_hover_card_free_designs );
	}
endif;

if ( ! function_exists( 'zaso_hover_card_design_description' ) ) :
	/**
	 * Help text for the "Pre-made Design" field.
	 *
	 * On a white-labelled Pro site the agency's client must never see the real
	 * product name or an upsell (they already have the full library), so the brand
	 * + "unlocks twenty-four more" sentence is dropped. Everywhere else (free, or
	 * licensed-but-not-white-labelled) the upsell line is kept.
	 *
	 * @since 1.10.10
	 *
	 * @return string Field description.
	 */
	function zaso_hover_card_design_description() {
		$white_label = class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label();

		if ( $white_label ) {
			return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. Leave on "Default (classic hover card)" to build your own look with the Layout and Design colour settings instead.', 'zaso' );
		}

		return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. The free core ships six; Zen Addons Pro unlocks twenty-four more (license required). Leave on "Default (classic hover card)" to build your own look with the Layout and Design colour settings instead.', 'zaso' );
	}
endif;

if( ! class_exists( 'Zen_Addons_SiteOrigin_Hover_Card_Widget' ) ) :


class Zen_Addons_SiteOrigin_Hover_Card_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO hover card field array.
		$zaso_hover_card_field_array = array(
			/**
			 * Pre-made design (visual picker). Empty ('') is the classic look and
			 * adds NO class, so existing instances (which have no design_variant
			 * key) render byte-identical. A picked design applies a self-contained
			 * skin that overrides the manual Design colours below. The Browse
			 * designs modal (core/design-picker.php) enhances this select.
			 */
			'design_variant' => array(
				'type'        => 'select',
				'label'       => __( 'Pre-made Design', 'zaso' ),
				'default'     => '',
				'description' => zaso_hover_card_design_description(),
				'options'     => zaso_hover_card_design_options(),
			),
			'hover_card_title' => array(
				'type'  => 'text',
				'label' => __( 'Title Caption' , 'zaso' )
			),
            'hover_card_text_content' => array(
				'type'  => 'tinymce',
				'label' => __( 'Text Content' , 'zaso' )
			),
			'hover_card_image' => array(
				'type'  => 'media',
				'label' => __( 'Featured Image', 'zaso' ),
				'library' => 'image',
				'fallback' => true
			),
			'hover_card_action_text' => array(
				'type'  => 'text',
				'label' => __( 'Action Text', 'zaso' ),
				'default' => __( 'Learn More', 'zaso' )
			),
			'hover_card_action_url' => array(
				'type'  => 'link',
				'label' => __( 'Action URL', 'zaso' ),
				'default' => '#'
			),
			'hover_card_animation' => array(
				'type'    => 'select',
				'label'   => __( 'Hover Animation', 'zaso' ),
				'default' => 'fadein',
				'options' => array(
					'fadein'  => __( 'Fade In', 'zaso' )
				)
			),
			'layout' => array(
				'type'        => 'select',
				'label'       => __( 'Layout', 'zaso' ),
				'default'     => 'default',
				'description' => __( 'Structural layout of the card. The Style skin below still controls colours; Layout controls how the image and caption are arranged and how the card reveals on hover.', 'zaso' ),
				'options'     => array(
					'default'       => __( 'Default (caption overlay)', 'zaso' ),
					'caption-below' => __( 'Caption Below (solid panel under image)', 'zaso' ),
					'slide-up'      => __( 'Slide Up (panel slides up on hover)', 'zaso' ),
					'zoom'          => __( 'Zoom (image zooms, caption fixed)', 'zaso' ),
				),
			),
			'design' => array(
				'type' =>  'section',
				'label' => __( 'Design', 'zaso' ),
				'hide' => true,
				'fields' => array(
					'hover_box' => array(
						'type' => 'section',
						'label' => __( 'Hover Card', 'zaso' ),
						'hide' => true,
						'fields' => array(
							'caption_background_color' => array(
								'type' => 'color',
								'label' => __( 'Caption Background Color',  'zaso' ),
								'default' => '#000000',
							),
							'caption_background_opacity' => array(
								'type'    => 'select',
								'label'   => __( 'Caption Background Opacity', 'zaso' ),
								'default' => '100',
								'options' => array(
									'100'  => '100%',
									'90'  => '90%',
									'80'  => '80%',
									'70'  => '70%',
									'60'  => '60%',
									'50'  => '50%',
									'40'  => '40%',
									'30'  => '30%',
									'20'  => '20%',
									'10'  => '10%'
								)
							),
							'caption_font_color' => array(
								'type'    => 'color',
								'label'   => __( 'Caption Font Color', 'zaso' ),
								'default' => '#ffffff',
							),
							'caption_font_size' => array(
								'type'    => 'measurement',
								'label'   => __( 'Caption Font Size', 'zaso' ),
								'default' => '26px',
							),
							'caption_font_weight' => array(
								'type'    => 'select',
								'label'   => __( 'Caption Font Weight', 'zaso' ),
								'default' => '400',
								'options' => array(
									'100'  => 100,
									'200'  => 200,
									'300'  => 300,
									'400'  => 400,
									'500'  => 500,
									'600'  => 600,
									'700'  => 700,
									'800'  => 800,
									'900'  => 900
								)
							),
							'caption_font_alignment' => array(
								'type'    => 'select',
								'label'   => __( 'Caption Text Alignment', 'zaso' ),
								'default' => 'center',
								'options' => array(
									'left'  => __( 'Left', 'zaso' ),
									'right'  => __( 'Right', 'zaso' ),
									'center'  => __( 'Center', 'zaso' ),
									'justify'  => __( 'Justify', 'zaso' ),
									'initial'  => __( 'Initial', 'zaso' ),
									'inherit'  => __( 'Inherit', 'zaso' )
								)
							),
							'caption_font_transform' => array(
								'type'    => 'select',
								'label'   => __( 'Caption Text Transform', 'zaso' ),
								'default' => 'none',
								'options' => array(
									'none'  => __( 'None', 'zaso' ),
									'capitalize'  => __( 'Capitalize', 'zaso' ),
									'uppercase'  => __( 'Uppercase', 'zaso' ),
									'lowercase'  => __( 'Lowecase', 'zaso' ),
									'initial'  => __( 'Initial', 'zaso' ),
									'inherit'  => __( 'Inherit', 'zaso' )
								)
							),
							'caption_margin' => array(
								'type' => 'section',
								'label' => __( 'Caption Margin', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zaso' ),
										'default' => '0px'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zaso' ),
										'default' => '0px'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zaso' ),
										'default' => '0px'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zaso' ),
										'default' => '0px'
									),
								),
							),
							'caption_padding' => array(
								'type' => 'section',
								'label' => __( 'Caption Padding', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zaso' ),
										'default' => '10px'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zaso' ),
										'default' => '10px'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zaso' ),
										'default' => '10px'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zaso' ),
										'default' => '10px'
									),
								),
							),
							'card_box_shadow' => array(
								'type' => 'section',
								'label' => __( 'Card Box Shadow', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'horizontal_offset' => array(
										'type' => 'measurement',
										'label' => __( 'Horizontal Offset', 'zaso' ),
										'default' => '4px'
									),
									'vertical_offset' => array(
										'type' => 'measurement',
										'label' => __( 'Vertical Offset', 'zaso' ),
										'default' => '4px'
									),
									'blur' => array(
										'type' => 'measurement',
										'label' => __( 'Blur', 'zaso' ),
										'default' => '6px'
									),
									'spread' => array(
										'type' => 'measurement',
										'label' => __( 'Spread', 'zaso' ),
										'default' => '0px'
									),
									'shadow_color' => array(
										'type' => 'color',
										'label' => __( 'Shadow Color', 'zaso' ),
										'default' => '#000000'
									),
									'shadow_color_opacity' => array(
										'type'    => 'select',
										'label'   => __( 'Shadow Color Opacity', 'zaso' ),
										'default' => '20',
										'options' => array(
											'100'  => '100%',
											'90'  => '90%',
											'80'  => '80%',
											'70'  => '70%',
											'60'  => '60%',
											'50'  => '50%',
											'40'  => '40%',
											'30'  => '30%',
											'20'  => '20%',
											'10'  => '10%',
											'0'	  => '0% ' . __( '(transparent)', 'zaso' )
										)
									),
								),
							)
						),
					),
					'modal_button' => array(
						'type' => 'section',
						'label' => __( 'Modal Button', 'zaso' ),
						'hide' => true,
						'fields' => array(
							'button_background_color' => array(
								'type' => 'color',
								'label' => __( 'Button Background Color',  'zaso' ),
								'default' => '#000000',
							),
							'button_background_color_opacity' => array(
								'type'    => 'select',
								'label'   => __( 'Button Background Opacity', 'zaso' ),
								'default' => '100',
								'options' => array(
									'100'  => '100%',
									'90'  => '90%',
									'80'  => '80%',
									'70'  => '70%',
									'60'  => '60%',
									'50'  => '50%',
									'40'  => '40%',
									'30'  => '30%',
									'20'  => '20%',
									'10'  => '10%',
									'0'	  => '0% ' . __( '(transparent)', 'zaso' )
								)
							),
							'button_background_color_hover' => array(
								'type' => 'color',
								'label' => __( 'Button Background Color (Hover)',  'zaso' ),
								'default' => '#e4e4e4',
							),
							'button_background_color_opacity_hover' => array(
								'type'    => 'select',
								'label'   => __( 'Button Background Opacity (Hover)', 'zaso' ),
								'default' => '100',
								'options' => array(
									'100'  => '100%',
									'90'  => '90%',
									'80'  => '80%',
									'70'  => '70%',
									'60'  => '60%',
									'50'  => '50%',
									'40'  => '40%',
									'30'  => '30%',
									'20'  => '20%',
									'10'  => '10%',
									'0'	  => '0% ' . __( '(transparent)', 'zaso' )
								)
							),
							'button_border_color' => array(
								'type'    => 'color',
								'label'   => __( 'Button Border Color', 'zaso' ),
								'default' => '#ffffff',
							),
							'button_border_color_hover' => array(
								'type'    => 'color',
								'label'   => __( 'Button Border Color (hover)', 'zaso' ),
								'default' => '#e4e4e4',
							),
							'button_font_color' => array(
								'type'    => 'color',
								'label'   => __( 'Button Font Color', 'zaso' ),
								'default' => '#ffffff',
							),
							'button_font_color_hover' => array(
								'type'    => 'color',
								'label'   => __( 'Button Font Color (Hover)', 'zaso' ),
								'default' => '#000000',
							),
							'button_font_size' => array(
								'type'    => 'measurement',
								'label'   => __( 'Button Font Size', 'zaso' ),
								'default' => '18px',
							),
							'button_font_weight' => array(
								'type'    => 'select',
								'label'   => __( 'Button Font Weight', 'zaso' ),
								'default' => '400',
								'options' => array(
									'100'  => 100,
									'200'  => 200,
									'300'  => 300,
									'400'  => 400,
									'500'  => 500,
									'600'  => 600,
									'700'  => 700,
									'800'  => 800,
									'900'  => 900
								)
							),
							'button_font_transform' => array(
								'type'    => 'select',
								'label'   => __( 'Button Text Transform', 'zaso' ),
								'default' => 'none',
								'options' => array(
									'none'  => __( 'None', 'zaso' ),
									'capitalize'  => __( 'Capitalize', 'zaso' ),
									'uppercase'  => __( 'Uppercase', 'zaso' ),
									'lowercase'  => __( 'Lowecase', 'zaso' ),
									'initial'  => __( 'Initial', 'zaso' ),
									'inherit'  => __( 'Inherit', 'zaso' )
								)
							),
							'button_padding' => array(
								'type' => 'section',
								'label' => __( 'Button Padding', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zaso' ),
										'default' => '11px'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zaso' ),
										'default' => '21px'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zaso' ),
										'default' => '11px'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zaso' ),
										'default' => '21px'
									),
								),
							),
						),
					)
				),
			),
			'extra_id' => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
				'description'	=> __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			)
		);

		// Add filter.
		$zaso_hover_card_fields = apply_filters( 'zaso_hover_card_fields', $zaso_hover_card_field_array );

		parent::__construct(
			'zen-addons-siteorigin-hover-card',
			__( 'Zen Addons - Hover Card', 'zaso' ),
			array(
				'description'   => __( 'Display image box, title caption and learn more button with hover transition', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_hover_card_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// Defensive: a design_style preset may fill only some of the design
		// sub-fields (the colour skins set the caption/button colours but omit
		// caption_margin, caption_padding, card_box_shadow, button_padding and
		// several scalar typography fields). Default every missing piece so a
		// partially-filled preset can never crash the LESS output or emit a
		// notice. A fully-saved Default instance already carries every key, so
		// these fallbacks only apply to keys a preset omitted and its output is
		// byte-identical. Fallback values equal the widget's own field defaults.
		$design       = isset( $instance['design'] ) && is_array( $instance['design'] ) ? $instance['design'] : array();
		$hover_box    = isset( $design['hover_box'] ) && is_array( $design['hover_box'] ) ? $design['hover_box'] : array();
		$modal_button = isset( $design['modal_button'] ) && is_array( $design['modal_button'] ) ? $design['modal_button'] : array();

		$hover_box_margin = wp_parse_args(
			( isset( $hover_box['caption_margin'] ) && is_array( $hover_box['caption_margin'] ) ) ? $hover_box['caption_margin'] : array(),
			array( 'top' => '0px', 'right' => '0px', 'bottom' => '0px', 'left' => '0px' )
		);
		$hover_box_padding = wp_parse_args(
			( isset( $hover_box['caption_padding'] ) && is_array( $hover_box['caption_padding'] ) ) ? $hover_box['caption_padding'] : array(),
			array( 'top' => '10px', 'right' => '10px', 'bottom' => '10px', 'left' => '10px' )
		);
		$hover_card_box_shadow = wp_parse_args(
			( isset( $hover_box['card_box_shadow'] ) && is_array( $hover_box['card_box_shadow'] ) ) ? $hover_box['card_box_shadow'] : array(),
			array(
				'horizontal_offset'    => '4px',
				'vertical_offset'      => '4px',
				'blur'                 => '6px',
				'spread'               => '0px',
				'shadow_color'         => '#000000',
				'shadow_color_opacity' => '20',
			)
		);
		$modal_button_padding = wp_parse_args(
			( isset( $modal_button['button_padding'] ) && is_array( $modal_button['button_padding'] ) ) ? $modal_button['button_padding'] : array(),
			array( 'top' => '11px', 'right' => '21px', 'bottom' => '11px', 'left' => '21px' )
		);

		return apply_filters( 'zaso_hover_card_less_variables', array(
			// Hover Box.
			'caption_background_color' => isset( $hover_box['caption_background_color'] ) ? $hover_box['caption_background_color'] : '#000000',
			'caption_background_opacity' => isset( $hover_box['caption_background_opacity'] ) ? $hover_box['caption_background_opacity'] : '100',
			'caption_font_color' => isset( $hover_box['caption_font_color'] ) ? $hover_box['caption_font_color'] : '#ffffff',
			'caption_font_size' => isset( $hover_box['caption_font_size'] ) ? $hover_box['caption_font_size'] : '26px',
			'caption_font_alignment' => isset( $hover_box['caption_font_alignment'] ) ? $hover_box['caption_font_alignment'] : 'center',
			'caption_font_weight' => isset( $hover_box['caption_font_weight'] ) ? $hover_box['caption_font_weight'] : '400',
			'caption_font_transform' => isset( $hover_box['caption_font_transform'] ) ? $hover_box['caption_font_transform'] : 'none',
			'caption_margin' =>
				sprintf( '%1$s %2$s %3$s %4$s',
					$hover_box_margin['top'],
					$hover_box_margin['right'],
					$hover_box_margin['bottom'],
					$hover_box_margin['left']
				),
			'caption_padding' =>
				sprintf( '%1$s %2$s %3$s %4$s',
					$hover_box_padding['top'],
					$hover_box_padding['right'],
					$hover_box_padding['bottom'],
					$hover_box_padding['left']
				),
			'hover_card_box_shadow' =>
				sprintf( '%1$s %2$s %3$s %4$s',
					$hover_card_box_shadow['horizontal_offset'],
					$hover_card_box_shadow['vertical_offset'],
					$hover_card_box_shadow['blur'],
					$hover_card_box_shadow['spread']
				),
			'hover_card_box_shadow_color' => $hover_card_box_shadow['shadow_color'],
			'hover_card_box_shadow_opacity' => $hover_card_box_shadow['shadow_color_opacity'],
			// Modal Button.
			'modal_background_color' => isset( $modal_button['button_background_color'] ) ? $modal_button['button_background_color'] : '#000000',
			'modal_background_color_opacity' => isset( $modal_button['button_background_color_opacity'] ) ? $modal_button['button_background_color_opacity'] : '100',
			'modal_background_color_hover' => isset( $modal_button['button_background_color_hover'] ) ? $modal_button['button_background_color_hover'] : '#e4e4e4',
			'modal_background_color_opacity_hover' => isset( $modal_button['button_background_color_opacity_hover'] ) ? $modal_button['button_background_color_opacity_hover'] : '100',
			'modal_button_font_color' => isset( $modal_button['button_font_color'] ) ? $modal_button['button_font_color'] : '#ffffff',
			'modal_button_font_color_hover' => isset( $modal_button['button_font_color_hover'] ) ? $modal_button['button_font_color_hover'] : '#000000',
			'modal_button_font_size' => isset( $modal_button['button_font_size'] ) ? $modal_button['button_font_size'] : '18px',
			'modal_button_font_weight' => isset( $modal_button['button_font_weight'] ) ? $modal_button['button_font_weight'] : '400',
			'modal_button_font_transform' => isset( $modal_button['button_font_transform'] ) ? $modal_button['button_font_transform'] : 'none',
			'modal_button_padding' =>
				sprintf( '%1$s %2$s %3$s %4$s',
					$modal_button_padding['top'],
					$modal_button_padding['right'],
					$modal_button_padding['bottom'],
					$modal_button_padding['left']
				),
			'modal_button_border_color' => isset( $modal_button['button_border_color'] ) ? $modal_button['button_border_color'] : '#ffffff',
			'modal_button_border_color_hover' => isset( $modal_button['button_border_color_hover'] ) ? $modal_button['button_border_color_hover'] : '#e4e4e4',
		) );

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-hover-card',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array( 'jquery' ),
					ZASO_VERSION,
					true,
				)
			)
		);

		// Self-hosted Material Symbols Rounded @font-face. Certain design variants
		// render a per-design glyph through this font via a ::before / ::after
		// pseudo-element (the free Slide Up arrows, plus the Pro designs), so it
		// must load whenever a hover card renders. SiteOrigin enqueues this only on
		// pages where the widget is present, and skips it if already enqueued.
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
siteorigin_widget_register( 'zen-addons-siteorigin-hover-card', __FILE__, 'Zen_Addons_SiteOrigin_Hover_Card_Widget' );


endif;