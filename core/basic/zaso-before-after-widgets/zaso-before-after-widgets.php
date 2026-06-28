<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Before / After
 * Widget ID: zen-addons-siteorigin-before-after
 * Description: A draggable image comparison slider that reveals a before and after image.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Before_After_Widget' ) ) :


class Zen_Addons_SiteOrigin_Before_After_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_before_after_field_array = array(
			'before_image' => array(
				'type'    => 'media',
				'label'   => __( 'Before Image', 'zaso' ),
				'library' => 'image',
			),
			'before_alt' => array(
				'type'        => 'text',
				'label'       => __( 'Before Image Alt Text', 'zaso' ),
				'description' => __( 'Describes the before image for screen readers.', 'zaso' ),
			),
			'before_label' => array(
				'type'    => 'text',
				'label'   => __( 'Before Label', 'zaso' ),
				'default' => __( 'Before', 'zaso' ),
			),
			'after_image' => array(
				'type'    => 'media',
				'label'   => __( 'After Image', 'zaso' ),
				'library' => 'image',
			),
			'after_alt' => array(
				'type'        => 'text',
				'label'       => __( 'After Image Alt Text', 'zaso' ),
				'description' => __( 'Describes the after image for screen readers.', 'zaso' ),
			),
			'after_label' => array(
				'type'    => 'text',
				'label'   => __( 'After Label', 'zaso' ),
				'default' => __( 'After', 'zaso' ),
			),
			'start_position' => array(
				'type'    => 'slider',
				'label'   => __( 'Start Position (%)', 'zaso' ),
				'default' => 50,
				'min'     => 0,
				'max'     => 100,
			),
			'orientation' => array(
				'type'    => 'select',
				'label'   => __( 'Orientation', 'zaso' ),
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal (slide left / right)', 'zaso' ),
					'vertical'   => __( 'Vertical (slide up / down)', 'zaso' ),
				),
			),
			'show_labels' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Labels', 'zaso' ),
				'default' => true,
			),
			'extra_id' => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'max_width' => array(
						'type'    => 'measurement',
						'label'   => __( 'Maximum Width', 'zaso' ),
						'default' => '100%',
					),
					'border_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Border Radius', 'zaso' ),
						'default' => '8px',
					),
					'handle_color' => array(
						'type'    => 'color',
						'label'   => __( 'Handle Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'handle_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Handle Size', 'zaso' ),
						'default' => '40px',
					),
					'divider_color' => array(
						'type'    => 'color',
						'label'   => __( 'Divider Line Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'divider_width' => array(
						'type'    => 'measurement',
						'label'   => __( 'Divider Line Width', 'zaso' ),
						'default' => '3px',
					),
					'label_bg' => array(
						'type'    => 'color',
						'label'   => __( 'Label Background', 'zaso' ),
						'default' => '#0f172a',
					),
					'label_color' => array(
						'type'    => 'color',
						'label'   => __( 'Label Text Color', 'zaso' ),
						'default' => '#ffffff',
					),
				),
			),
		);

		// Add filter.
		$zaso_before_after_fields = apply_filters( 'zaso_before_after_fields', $zaso_before_after_field_array );

		parent::__construct(
			'zen-addons-siteorigin-before-after',
			__( 'Zen Addons - Before / After', 'zaso' ),
			array(
				'description'   => __( 'A draggable slider that compares a before and after image.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_before_after_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		$design = $instance['design'];

		return apply_filters( 'zaso_before_after_less_variables', array(
			'max_width'     => $design['max_width'],
			'border_radius' => $design['border_radius'],
			'handle_color'  => $design['handle_color'],
			'handle_size'   => $design['handle_size'],
			'divider_color' => $design['divider_color'],
			'divider_width' => $design['divider_width'],
			'label_bg'      => $design['label_bg'],
			'label_color'   => $design['label_color'],
		) );

	}

	/**
	 * Resolve an attachment into a src/width/height/alt array.
	 *
	 * @param int    $attachment_id The attachment ID.
	 * @param string $alt_override  Author-provided alt text.
	 * @return array Image attributes (may be empty).
	 */
	private function zaso_resolve_image( $attachment_id, $alt_override ) {
		$src = siteorigin_widgets_get_attachment_image_src( $attachment_id, 'full' );
		if ( empty( $src[0] ) ) {
			return array();
		}
		$alt = '' !== trim( (string) $alt_override )
			? $alt_override
			: (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

		return array(
			'src'    => $src[0],
			'width'  => ! empty( $src[1] ) ? $src[1] : '',
			'height' => ! empty( $src[2] ) ? $src[2] : '',
			'alt'    => $alt,
		);
	}

	function get_template_variables( $instance, $args ) {

		$position    = max( 0, min( 100, (int) $instance['start_position'] ) );
		$orientation = 'vertical' === $instance['orientation'] ? 'vertical' : 'horizontal';

		return apply_filters( 'zaso_before_after_template_variables', array(
			'before'      => $this->zaso_resolve_image( $instance['before_image'], $instance['before_alt'] ),
			'after'       => $this->zaso_resolve_image( $instance['after_image'], $instance['after_alt'] ),
			'position'    => $position,
			'orientation' => $orientation,
		) );

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-before-after',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-before-after', __FILE__, 'Zen_Addons_SiteOrigin_Before_After_Widget' );


endif;
