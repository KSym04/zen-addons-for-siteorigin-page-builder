<?php
/**
 * Widget Name: ZASO - Spacer
 * Widget ID: zen-addons-siteorigin-spacer
 * Description: Create an empty space between elements.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if( ! class_exists( 'Zen_Addons_SiteOrigin_Spacer_Widget' ) ) :

/**
 * Class Zen_Addons_SiteOrigin_Spacer_Widget
 *
 * Widget to create an empty space between elements.
 *
 * @since 1.0.0
 */
class Zen_Addons_SiteOrigin_Spacer_Widget extends SiteOrigin_Widget {

	/**
	 * Zen_Addons_SiteOrigin_Spacer_Widget constructor.
	 *
	 * Initialize the widget with the required parameters.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// ZASO field array.
		$zaso_spacer_field_array = array(
			'height' => array(
				'type'        => 'measurement',
				'default'     => '20',
				'label'       => esc_html__( 'Height', 'zaso' ),
				'description' => esc_html__( 'Set empty space height.', 'zaso' ),
			),
			'extra_id' => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Extra ID', 'zaso' ),
				'description' => esc_html__( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Extra Class', 'zaso' ),
				'description' => esc_html__( 'Add an extra class for styling overrides.', 'zaso' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => esc_html__( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'background_color' => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Background Color', 'zaso' ),
						'default' => ''
					)
				)
			)
		);

		// Add filter.
		$zaso_spacer_fields = apply_filters( 'zaso_spacer_fields', $zaso_spacer_field_array );

		parent::__construct(
			'zen-addons-siteorigin-spacer',
			esc_html__( 'ZASO - Spacer', 'zaso' ),
			array(
				'description'   => esc_html__( 'Create an empty space between elements.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' )
			),
			array(),
			$zaso_spacer_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	/**
	 * Get LESS variables.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The widget instance's settings.
	 * @return array Filtered LESS variables.
	 */
	function get_less_variables( $instance ) {

		return apply_filters( 'zaso_spacer_less_variables', array(
			'background_color' => $instance['design']['background_color']
		));

	}

	/**
	 * Additional initialization logic can be placed here.
	 *
	 * @since 1.0.0
	 */
	function initialize() {

	}

}

siteorigin_widget_register( 'zen-addons-siteorigin-spacer', __FILE__, 'Zen_Addons_SiteOrigin_Spacer_Widget' );

endif;
