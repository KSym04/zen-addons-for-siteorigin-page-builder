<?php
/**
 * Widget Name: ZASO - Alert Box
 * Widget ID: zen-addons-siteorigin-alert-box
 * Description: Create contextual feedback and flexible alert messages.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Alert_Box_Widget' ) ) :


class Zen_Addons_SiteOrigin_Alert_Box_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array
		$zaso_alert_box_field_array = array(
			'alert_message' => array(
				'type'  => 'tinymce',
				'label' => __( 'Messages' , 'zaso' )
			),
			'alert_closebtn' => array(
				'type'    => 'select',
				'label'   => __( 'Close Button', 'zaso' ),
				'default' => 'show',
				'options' => array(
					'show'  => __( 'Show', 'zaso' ),
					'hide' => __( 'Hide', 'zaso' ),
				)
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
			),
			'design' => array(
				'type' =>  'section',
				'label' => __( 'Design', 'zaso' ),
				'hide' => true,
				'fields' => array(
					'message_box' => array(
						'type' => 'section',
						'label' => __( 'Alert Box', 'zaso' ),
						'hide' => true,
						'fields' => array(
							'message_background_color' => array(
								'type' => 'color',
								'label' => __( 'Background Color',  'zaso' ),
								'default' => '#e7e8ea',
							),
							'message_font_color' => array(
								'type'    => 'color',
								'label'   => __( 'Font Color', 'zaso' ),
								'default' => '#464a4e',
							),
							'message_font_size' => array(
								'type'    => 'measurement',
								'label'   => __( 'Font Size', 'zaso' ),
								'default' => '1rem',
							),
							'message_margin' => array(
								'type' => 'section',
								'label' => __( 'Margin', 'zaso' ),
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
							'message_padding' => array(
								'type' => 'section',
								'label' => __( 'Padding', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'top' => array(
										'type' => 'measurement',
										'label' => __( 'Top', 'zaso' ),
										'default' => '1em'
									),
									'right' => array(
										'type' => 'measurement',
										'label' => __( 'Right', 'zaso' ),
										'default' => '2.3em'
									),
									'bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom', 'zaso' ),
										'default' => '1em'
									),
									'left' => array(
										'type' => 'measurement',
										'label' => __( 'Left', 'zaso' ),
										'default' => '1.2em'
									),
								),
							),
							'message_border' => array(
								'type' => 'section',
								'label' => __( 'Border Settings', 'zaso' ),
								'hide' => true,
								'fields' => array(
									'bw_top' => array(
										'type' => 'measurement',
										'label' => __( 'Top Border Width', 'zaso' ),
										'default' => '1px'
									),
									'bw_right' => array(
										'type' => 'measurement',
										'label' => __( 'Right Border Width', 'zaso' ),
										'default' => '1px'
									),
									'bw_bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom Border Width', 'zaso' ),
										'default' => '1px'
									),
									'bw_left' => array(
										'type' => 'measurement',
										'label' => __( 'Left Border Width', 'zaso' ),
										'default' => '1px'
									),
									'br_top' => array(
										'type' => 'measurement',
										'label' => __( 'Top Border Radius', 'zaso' ),
										'default' => '0px'
									),
									'br_right' => array(
										'type' => 'measurement',
										'label' => __( 'Right Border Radius', 'zaso' ),
										'default' => '0px'
									),
									'br_bottom' => array(
										'type' => 'measurement',
										'label' => __( 'Bottom Border Radius', 'zaso' ),
										'default' => '0px'
									),
									'br_left' => array(
										'type' => 'measurement',
										'label' => __( 'Left Border Radius', 'zaso' ),
										'default' => '0px'
									),
									'border_style' => array(
										'type'    => 'select',
										'label'   => __( 'Border Style', 'zaso' ),
										'default' => 'solid',
										'options' => array(
											'none'  => __( 'none', 'zaso' ),
											'hidden' => __( 'Hidden', 'zaso' ),
											'dotted' => __( 'Dotted', 'zaso' ),
											'dashed' => __( 'Dashed', 'zaso' ),
											'solid'  => __( 'Solid', 'zaso' ),
											'double' => __( 'Double', 'zaso' ),
											'groove' => __( 'Groove', 'zaso' ),
											'ridge'  => __( 'Ridge', 'zaso' ),
											'inset'  => __( 'Inset', 'zaso' ),
											'outset' => __( 'Outset', 'zaso' ),
										)
									),
									'border_color' => array(
										'type' => 'color',
										'label' => __( 'Border Color',  'zaso' ),
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
			__( 'ZASO - Alert Box', 'zaso' ),
			array(
				'description'   => __( 'Create contextual feedback and flexible alert messages.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_alert_box_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		// variable pointers
		$design = $instance['design'];
		$message_box = $design['message_box'];
		$message_margin = $message_box['message_margin'];
		$message_padding = $message_box['message_padding'];
		$message_border = $message_box['message_border'];

		return apply_filters( 'zaso_alert_box_less_variables', array(
			// basic tabs title vars
			'message_background_color' => $message_box['message_background_color'],
			'message_font_color' => $message_box['message_font_color'],
			'message_font_size' => $message_box['message_font_size'],
			'message_margin' => sprintf( '%1$s %2$s %3$s %4$s',
				$message_margin['top'],
				$message_margin['right'],
				$message_margin['bottom'],
				$message_margin['left'] ),
			'message_padding' => sprintf( '%1$s %2$s %3$s %4$s',
				$message_padding['top'],
				$message_padding['right'],
				$message_padding['bottom'],
				$message_padding['left'] ),
			'message_border_width' => sprintf( '%1$s %2$s %3$s %4$s',
				$message_border['bw_top'],
				$message_border['bw_right'],
				$message_border['bw_bottom'],
				$message_border['bw_left'] ),
			'message_border_radius' => sprintf( '%1$s %2$s %3$s %4$s',
				$message_border['br_top'],
				$message_border['br_right'],
				$message_border['br_bottom'],
				$message_border['br_left'] ),
			'message_border_style' => $message_border['border_style'],
			'message_border_color' => $message_border['border_color'],
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

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-alert-box', __FILE__, 'Zen_Addons_SiteOrigin_Alert_Box_Widget' );


endif;