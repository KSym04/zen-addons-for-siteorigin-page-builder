<?php
/**
 * Widget Name: ZASO - Hover Card
 * Widget ID: zen-addons-siteorigin-hover-card
 * Description: Display image box, title caption and learn more button with hover transition.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Hover_Card_Widget' ) ) :


class Zen_Addons_SiteOrigin_Hover_Card_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO hover card field array.
		$zaso_hover_card_field_array = array(
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
			__( 'ZASO - Hover Card', 'zaso' ),
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

		// Variable pointers.
		$design = $instance['design'];
		$hover_box = $design['hover_box'];
		$hover_box_margin = $hover_box['caption_margin'];
		$hover_box_padding = $hover_box['caption_padding'];
		$hover_card_box_shadow = $hover_box['card_box_shadow'];

		$modal_button = $design['modal_button'];
		$modal_button_padding = $modal_button['button_padding'];

		return apply_filters( 'zaso_hover_card_less_variables', array(
			// Hover Box.
			'caption_background_color' => $hover_box['caption_background_color'],
			'caption_background_opacity' => $hover_box['caption_background_opacity'],
			'caption_font_color' => $hover_box['caption_font_color'],
			'caption_font_size' => $hover_box['caption_font_size'],
			'caption_font_alignment' => $hover_box['caption_font_alignment'],
			'caption_font_weight' => $hover_box['caption_font_weight'],
			'caption_font_transform' => $hover_box['caption_font_transform'],
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
			'modal_background_color' => $modal_button['button_background_color'],
			'modal_background_color_opacity' => $modal_button['button_background_color_opacity'],
			'modal_background_color_hover' => $modal_button['button_background_color_hover'],
			'modal_background_color_opacity_hover' => $modal_button['button_background_color_opacity_hover'],
			'modal_button_font_color' => $modal_button['button_font_color'],
			'modal_button_font_color_hover' => $modal_button['button_font_color_hover'],
			'modal_button_font_size' => $modal_button['button_font_size'],
			'modal_button_font_weight' => $modal_button['button_font_weight'],
			'modal_button_font_transform' => $modal_button['button_font_transform'],
			'modal_button_padding' => 
				sprintf( '%1$s %2$s %3$s %4$s',
					$modal_button_padding['top'],
					$modal_button_padding['right'],
					$modal_button_padding['bottom'],
					$modal_button_padding['left']
				),
			'modal_button_border_color' => $modal_button['button_border_color'],
			'modal_button_border_color_hover' => $modal_button['button_border_color_hover'],
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

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-hover-card', __FILE__, 'Zen_Addons_SiteOrigin_Hover_Card_Widget' );


endif;