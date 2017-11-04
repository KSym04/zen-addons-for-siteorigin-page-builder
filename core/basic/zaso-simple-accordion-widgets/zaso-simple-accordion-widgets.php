<?php
/**
 * Widget Name: ZASO - Simple Accordion
 * Widget ID: zen-addons-siteorigin-simple-accordion
 * Description: Create a vertically stacked list of items.
 * Author: DopeThemes
 * Author URI: http://www.dopethemes.com/
 */

if( !class_exists( 'Zen_Addons_SiteOrigin_Simple_Accordion_Widget' ) ) :


class Zen_Addons_SiteOrigin_Simple_Accordion_Widget extends SiteOrigin_Widget {

	function __construct() {

		parent::__construct(
			'zen-addons-siteorigin-simple-accordion',
			__( 'ZASO - Simple Accordion', 'zaso' ),
			array(
				'description'   => __( 'Create a vertically stacked list of items.', 'zaso' ),
				'help'          => 'http://www.dopethemes.com/',
				'panels_groups' => array('zaso-plugin-widgets')
			),
			array(),
			array(
				'accordion' => array(
					'type' => 'repeater',
					'label' => __( 'Accordion List' , 'zaso' ),
					'item_name'  => __( 'Single Item', 'zaso' ),
					'item_label' => array(
						'selector'      => "[name*='accordion_field_title']",
						'update_event'  => 'change',
						'value_method'  => 'val'
					),
					'fields' => array(
						'accordion_field_title' => array(
							'type'  => 'text',
							'label' => __( 'Accordion Title' , 'zaso' )
						),
						'accordion_field_content' => array(
							'type'  => 'tinymce',
							'label' => __( 'Accordion Content' , 'zaso' ),
							'row'   => 20
						),
						'accordion_field_state' => array(
							'type'    => 'select',
							'label'   => __( 'Accordion Initial State' , 'zaso' ),
							'options' => array(
								'zaso-simple-accordion--close' => __( 'Close', 'zaso' ),
								'zaso-simple-accordion--open'  => __( 'Open', 'zaso' ),
							)
						),
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
						'heading' => array(
							'type' => 'section',
							'label' => __( 'Headings', 'zaso' ),
							'hide' => true,
							'fields' => array(
								'title_background_color' => array(
									'type'    => 'color',
									'label'   => __( 'Background Color', 'zaso' ),
									'default' => '#e5e5e5',
								),
								'title_background_color_hover' => array(
									'type'    => 'color',
									'label'   => __( 'Background Hover Color', 'zaso' ),
									'default' => '#d5d5d5',
								),
								'title_font_color' => array(
									'type'    => 'color',
									'label'   => __( 'Font Color', 'zaso' ),
									'default' => '#333333',
								),
								'title_font_weight' => array(
									'type'    => 'select',
									'label'   => __( 'Font Weight', 'zaso' ),
									'default' => '700',
									'options' => array(
										'100'  => __( '100', 'zaso' ),
										'200'  => __( '200', 'zaso' ),
										'300'  => __( '300', 'zaso' ),
										'400'  => __( '400 - Normal', 'zaso' ),
										'500'  => __( '500', 'zaso' ),
										'600'  => __( '600', 'zaso' ),
										'700'  => __( '700 - Bold', 'zaso' ),
										'800'  => __( '800', 'zaso' ),
										'900'  => __( '900', 'zaso' )
									)
								),
								'title_margin' => array(
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
								'title_padding' => array(
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
											'default' => '1.2em'
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
							),
						),
						'panels' => array(
							'type' => 'section',
							'label' => __( 'Panels', 'zaso' ),
							'hide' => true,
							'fields' => array(
								'content_background_color' => array(
									'type' => 'color',
									'label' => __( 'Background Color',  'zaso' ),
									'default' => '#f5f5f5',
								),
								'content_font_color' => array(
									'type'    => 'color',
									'label'   => __( 'Font Color', 'zaso' ),
									'default' => '#333333',
								),
								'content_font_size' => array(
									'type'    => 'measurement',
									'label'   => __( 'Font Size', 'zaso' ),
									'default' => '1rem',
								),
								'content_margin' => array(
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
								'content_padding' => array(
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
											'default' => '1.2em'
										),
										'bottom' => array(
											'type' => 'measurement',
											'label' => __( 'Bottom', 'zaso' ),
											'default' => '0.5em'
										),
										'left' => array(
											'type' => 'measurement',
											'label' => __( 'Left', 'zaso' ),
											'default' => '1.2em'
										),
									),
								),
							),
						),
					),
				),
			),
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		$design = $instance['design'];
		return array(
			// accordion title vars
			'title_background_color' => $design['heading']['title_background_color'],
			'title_background_color_hover' => $design['heading']['title_background_color_hover'],
			'title_font_color' => $design['heading']['title_font_color'],
			'title_font_weight' => $design['heading']['title_font_weight'],
			'title_margin' => sprintf( '%s %s %s %s',
				$design['heading']['title_margin']['top'],
				$design['heading']['title_margin']['right'],
				$design['heading']['title_margin']['bottom'],
				$design['heading']['title_margin']['left'] ),
			'title_padding' => sprintf( '%s %s %s %s',
				$design['heading']['title_padding']['top'],
				$design['heading']['title_padding']['right'],
				$design['heading']['title_padding']['bottom'],
				$design['heading']['title_padding']['left'] ),

			// accordion content vars
			'content_background_color' => $design['panels']['content_background_color'],
			'content_font_size' => $design['panels']['content_font_size'],
			'content_font_color' => $design['panels']['content_font_color'],
			'content_margin' => sprintf( '%s %s %s %s',
				$design['panels']['content_margin']['top'],
				$design['panels']['content_margin']['right'],
				$design['panels']['content_margin']['bottom'],
				$design['panels']['content_margin']['left'] ),
			'content_padding' => sprintf( '%s %s %s %s',
				$design['panels']['content_padding']['top'],
				$design['panels']['content_padding']['right'],
				$design['panels']['content_padding']['bottom'],
				$design['panels']['content_padding']['left'] ),
		);

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-simple-accordion',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array( 'jquery' ),
					ZASO_VERSION,
					true,
				)
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-simple-accordion', __FILE__, 'Zen_Addons_SiteOrigin_Simple_Accordion_Widget' );


endif;