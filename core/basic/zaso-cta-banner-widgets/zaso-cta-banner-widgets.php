<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Call to Action
 * Widget ID: zen-addons-siteorigin-cta-banner
 * Description: A call to action banner with a heading, text, and button over a color, gradient, or image background.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Cta_Banner_Widget' ) ) :


class Zen_Addons_SiteOrigin_Cta_Banner_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_cta_banner_field_array = array(
			'heading' => array(
				'type'  => 'text',
				'label' => __( 'Heading', 'zaso' ),
			),
			'subheading' => array(
				'type'  => 'text',
				'label' => __( 'Subheading', 'zaso' ),
			),
			'content' => array(
				'type'  => 'tinymce',
				'label' => __( 'Content', 'zaso' ),
			),
			'button_text' => array(
				'type'    => 'text',
				'label'   => __( 'Button Text', 'zaso' ),
				'default' => __( 'Learn More', 'zaso' ),
			),
			'button_url' => array(
				'type'  => 'link',
				'label' => __( 'Button Destination URL', 'zaso' ),
			),
			'button_new_tab' => array(
				'type'    => 'checkbox',
				'default' => false,
				'label'   => __( 'Open button link in a new tab', 'zaso' ),
			),
			'button_nofollow' => array(
				'type'    => 'checkbox',
				'default' => false,
				'label'   => __( 'Add rel="nofollow" to the button link', 'zaso' ),
			),
			'layout' => array(
				'type'    => 'select',
				'label'   => __( 'Button Placement', 'zaso' ),
				'default' => 'stacked',
				'options' => array(
					'stacked' => __( 'Stacked (button below text)', 'zaso' ),
					'inline'  => __( 'Inline (button beside text)', 'zaso' ),
				),
			),
			'alignment' => array(
				'type'    => 'select',
				'label'   => __( 'Text Alignment', 'zaso' ),
				'default' => 'center',
				'options' => array(
					'left'   => __( 'Left', 'zaso' ),
					'center' => __( 'Center', 'zaso' ),
					'right'  => __( 'Right', 'zaso' ),
				),
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
			/**
			 * Structural layout dimension (orthogonal to the colour skin). The
			 * Style preset below still drives all colours; this only restructures
			 * the banner shape (card elevation, split divider, centered measure).
			 * Note: the key is `block_layout`, not `layout`, because `layout`
			 * already ships as the button-placement (stacked / inline) control
			 * above and must not be repurposed. The default value adds no class,
			 * so existing instances render byte-identical.
			 */
			'block_layout' => array(
				'type'        => 'select',
				'label'       => __( 'Layout Structure', 'zaso' ),
				'default'     => 'default',
				'description' => __( 'Structural shape of the banner. The Style skin below still controls colours; this controls the layout (card elevation, split divider, centered measure). Independent of the button placement and text alignment options above.', 'zaso' ),
				'options'     => array(
					'default'  => __( 'Default (full-width band)', 'zaso' ),
					'card'     => __( 'Card (elevated, rounded, shadow)', 'zaso' ),
					'split'    => __( 'Split (content / action divided)', 'zaso' ),
					'centered' => __( 'Centered (constrained spotlight)', 'zaso' ),
				),
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
					// Free subset of the DopeThemes premium palette (4 schemes,
					// one per aesthetic family). Pro appends the full library.
					'saas_indigo'   => array(
						'label'  => __( 'Indigo', 'zaso' ),
						'values' => array(
							'design' => array(
								'background' => array(
									'bg_type' => 'gradient',
									'bg_color' => '#ffffff',
									'gradient_start' => '#ffffff',
									'gradient_end' => '#eef2ff',
									'gradient_angle' => 135,
								),
								'typography' => array(
									'heading_color' => '#0f172a',
									'subheading_color' => '#475569',
									'text_color' => '#475569',
								),
								'button' => array(
									'button_bg' => '#4f46e5',
									'button_bg_hover' => '#4338ca',
									'button_color' => '#ffffff',
									'button_radius' => '8px',
								),
								'spacing' => array(
									'padding_y' => '2.75rem',
									'padding_x' => '2rem',
									'border_radius' => '10px',
								),
							),
						),
					),
					'dark_midnight'   => array(
						'label'  => __( 'Midnight', 'zaso' ),
						'values' => array(
							'design' => array(
								'background' => array(
									'bg_type' => 'solid',
									'bg_color' => '#0f172a',
								),
								'typography' => array(
									'heading_color' => '#e2e8f0',
									'subheading_color' => '#94a3b8',
									'text_color' => '#94a3b8',
								),
								'button' => array(
									'button_bg' => '#4f46e5',
									'button_bg_hover' => '#4338ca',
									'button_color' => '#ffffff',
									'button_radius' => '10px',
								),
								'spacing' => array(
									'padding_y' => '3rem',
									'padding_x' => '2rem',
									'border_radius' => '12px',
								),
							),
						),
					),
					'min_mono'   => array(
						'label'  => __( 'Mono', 'zaso' ),
						'values' => array(
							'design' => array(
								'background' => array(
									'bg_type' => 'solid',
									'bg_color' => '#ffffff',
								),
								'typography' => array(
									'heading_color' => '#111111',
									'subheading_color' => '#6b7280',
									'text_color' => '#6b7280',
								),
								'button' => array(
									'button_bg' => '#111111',
									'button_bg_hover' => '#000000',
									'button_color' => '#ffffff',
									'button_radius' => '6px',
								),
								'spacing' => array(
									'padding_y' => '2.5rem',
									'padding_x' => '2rem',
									'border_radius' => '8px',
								),
							),
						),
					),
					'bold_sunset'   => array(
						'label'  => __( 'Sunset', 'zaso' ),
						'values' => array(
							'design' => array(
								'background' => array(
									'bg_type' => 'gradient',
									'bg_color' => '#fff7ed',
									'gradient_start' => '#fff7ed',
									'gradient_end' => '#ffedd5',
									'gradient_angle' => 135,
								),
								'typography' => array(
									'heading_color' => '#7c2d12',
									'subheading_color' => '#9a3412',
									'text_color' => '#9a3412',
								),
								'button' => array(
									'button_bg' => '#c2410c',
									'button_bg_hover' => '#9a3412',
									'button_color' => '#ffffff',
									'button_radius' => '8px',
								),
								'spacing' => array(
									'padding_y' => '2.75rem',
									'padding_x' => '2rem',
									'border_radius' => '12px',
								),
							),
						),
					),
				), 'cta-banner' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'background' => array(
						'type'   => 'section',
						'label'  => __( 'Background', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'bg_type' => array(
								'type'    => 'select',
								'label'   => __( 'Background Type', 'zaso' ),
								'default' => 'solid',
								'options' => array(
									'solid'    => __( 'Solid Color', 'zaso' ),
									'gradient' => __( 'Gradient', 'zaso' ),
									'image'    => __( 'Image', 'zaso' ),
								),
							),
							'bg_color' => array(
								'type'    => 'color',
								'label'   => __( 'Background Color', 'zaso' ),
								'default' => '#1e293b',
							),
							'gradient_start' => array(
								'type'    => 'color',
								'label'   => __( 'Gradient Start Color', 'zaso' ),
								'default' => '#4f46e5',
							),
							'gradient_end' => array(
								'type'    => 'color',
								'label'   => __( 'Gradient End Color', 'zaso' ),
								'default' => '#1e293b',
							),
							'gradient_angle' => array(
								'type'    => 'number',
								'label'   => __( 'Gradient Angle (degrees)', 'zaso' ),
								'default' => 135,
							),
							'bg_image' => array(
								'type'    => 'media',
								'label'   => __( 'Background Image', 'zaso' ),
								'library' => 'image',
							),
							'overlay_color' => array(
								'type'    => 'color',
								'label'   => __( 'Image Overlay Color', 'zaso' ),
								'default' => '#0f172a',
							),
							'overlay_opacity' => array(
								'type'    => 'slider',
								'label'   => __( 'Image Overlay Opacity', 'zaso' ),
								'default' => 60,
								'min'     => 0,
								'max'     => 100,
							),
						),
					),
					'typography' => array(
						'type'   => 'section',
						'label'  => __( 'Typography', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'heading_color' => array(
								'type'    => 'color',
								'label'   => __( 'Heading Color', 'zaso' ),
								'default' => '#ffffff',
							),
							'heading_size' => array(
								'type'    => 'measurement',
								'label'   => __( 'Heading Size', 'zaso' ),
								'default' => '2rem',
							),
							'subheading_color' => array(
								'type'    => 'color',
								'label'   => __( 'Subheading Color', 'zaso' ),
								'default' => '#cbd5e1',
							),
							'subheading_size' => array(
								'type'    => 'measurement',
								'label'   => __( 'Subheading Size', 'zaso' ),
								'default' => '1.125rem',
							),
							'text_color' => array(
								'type'    => 'color',
								'label'   => __( 'Content Text Color', 'zaso' ),
								'default' => '#e2e8f0',
							),
						),
					),
					'button' => array(
						'type'   => 'section',
						'label'  => __( 'Button', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'button_bg' => array(
								'type'    => 'color',
								'label'   => __( 'Button Background', 'zaso' ),
								'default' => '#4f46e5',
							),
							'button_bg_hover' => array(
								'type'    => 'color',
								'label'   => __( 'Button Background (Hover)', 'zaso' ),
								'default' => '#4338ca',
							),
							'button_color' => array(
								'type'    => 'color',
								'label'   => __( 'Button Text Color', 'zaso' ),
								'default' => '#ffffff',
							),
							'button_radius' => array(
								'type'    => 'measurement',
								'label'   => __( 'Button Border Radius', 'zaso' ),
								'default' => '6px',
							),
						),
					),
					'spacing' => array(
						'type'   => 'section',
						'label'  => __( 'Spacing', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'padding_y' => array(
								'type'    => 'measurement',
								'label'   => __( 'Vertical Padding', 'zaso' ),
								'default' => '3rem',
							),
							'padding_x' => array(
								'type'    => 'measurement',
								'label'   => __( 'Horizontal Padding', 'zaso' ),
								'default' => '2rem',
							),
							'border_radius' => array(
								'type'    => 'measurement',
								'label'   => __( 'Banner Border Radius', 'zaso' ),
								'default' => '0px',
							),
						),
					),
				),
			),
		);

		// Add filter.
		$zaso_cta_banner_fields = apply_filters( 'zaso_cta_banner_fields', $zaso_cta_banner_field_array );

		parent::__construct(
			'zen-addons-siteorigin-cta-banner',
			__( 'Zen Addons - Call to Action', 'zaso' ),
			array(
				'description'   => __( 'A call to action banner with a heading, text, and button.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_cta_banner_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		$design     = $instance['design'];
		$background  = $design['background'];
		$typography  = $design['typography'];
		$button      = $design['button'];
		$spacing     = $design['spacing'];

		// Compute the banner background from the chosen type. Image type is
		// rendered as an inline style in the template, so LESS stays transparent.
		if ( 'gradient' === $background['bg_type'] ) {
			$cta_background = sprintf(
				'linear-gradient(%1$ddeg, %2$s, %3$s)',
				(int) $background['gradient_angle'],
				$background['gradient_start'],
				$background['gradient_end']
			);
		} elseif ( 'image' === $background['bg_type'] ) {
			$cta_background = 'transparent';
		} else {
			$cta_background = $background['bg_color'];
		}

		// Overlay alpha as a 0-1 LESS value.
		$overlay_alpha = max( 0, min( 100, (int) $background['overlay_opacity'] ) ) / 100;

		return apply_filters( 'zaso_cta_banner_less_variables', array(
			'cta_background'    => $cta_background,
			'overlay_color'     => $background['overlay_color'],
			'overlay_alpha'     => $overlay_alpha,
			'heading_color'     => $typography['heading_color'],
			'heading_size'      => $typography['heading_size'],
			'subheading_color'  => $typography['subheading_color'],
			'subheading_size'   => $typography['subheading_size'],
			'text_color'        => $typography['text_color'],
			'button_bg'         => $button['button_bg'],
			'button_bg_hover'   => $button['button_bg_hover'],
			'button_color'      => $button['button_color'],
			'button_radius'     => $button['button_radius'],
			'padding_y'         => $spacing['padding_y'],
			'padding_x'         => $spacing['padding_x'],
			'border_radius'     => $spacing['border_radius'],
		) );

	}

	function get_template_variables( $instance, $args ) {

		$background = $instance['design']['background'];

		// Resolve the background image URL (used for the "image" background type).
		$bg_image_url = '';
		if ( 'image' === $background['bg_type'] && ! empty( $background['bg_image'] ) ) {
			$src = siteorigin_widgets_get_attachment_image_src( $background['bg_image'], 'full' );
			if ( ! empty( $src[0] ) ) {
				$bg_image_url = $src[0];
			}
		}

		return apply_filters( 'zaso_cta_banner_template_variables', array(
			'bg_type'      => $background['bg_type'],
			'bg_image_url' => $bg_image_url,
		) );

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-cta-banner', __FILE__, 'Zen_Addons_SiteOrigin_Cta_Banner_Widget' );


endif;
