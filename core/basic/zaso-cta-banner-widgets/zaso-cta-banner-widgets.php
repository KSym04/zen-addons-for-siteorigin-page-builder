<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Call to Action
 * Widget ID: zen-addons-siteorigin-cta-banner
 * Description: A call to action banner with a heading, text, and button over a color, gradient, or image background.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! function_exists( 'zaso_cta_banner_design_options' ) ) :
	/**
	 * Curated "designs" for the Call to Action widget.
	 *
	 * The free core ships six ready-made designs inline; Zen Addons Pro appends
	 * its twenty-four additional designs via the shared `zaso_cta_designs` filter
	 * (the Pro controller self-gates on a valid license, so an unlicensed or lapsed
	 * site only ever sees the six free entries). The empty-string key is the classic
	 * "Default" banner and adds no class, keeping every existing instance
	 * byte-identical.
	 *
	 * @since 1.10.7
	 *
	 * @return array Map of design id => human label.
	 */
	function zaso_cta_banner_design_options() {
		$zaso_cta_banner_free_designs = array(
			''                  => __( 'Default (classic banner)', 'zaso' ),
			'solid-centered'    => __( 'Solid Centered (indigo)', 'zaso' ),
			'horizontal-split'  => __( 'Horizontal Split (teal)', 'zaso' ),
			'soft-tint'         => __( 'Soft Tint (blue)', 'zaso' ),
			'gradient-centered' => __( 'Gradient Centered (violet)', 'zaso' ),
			'outlined'          => __( 'Outlined (minimal)', 'zaso' ),
			'dark'              => __( 'Dark', 'zaso' ),
		);

		return apply_filters( 'zaso_cta_designs', $zaso_cta_banner_free_designs );
	}
endif;

if ( ! function_exists( 'zaso_cta_banner_design_description' ) ) :
	/**
	 * Help text for the "Pre-made Design" field.
	 *
	 * On a white-labelled Pro site the agency's client must never see the real
	 * product name or an upsell (they already have the full library), so the brand
	 * + "unlocks twenty-four more" sentence is dropped. Everywhere else (free, or
	 * licensed-but-not-white-labelled) the upsell line is kept.
	 *
	 * @since 1.10.7
	 *
	 * @return string Field description.
	 */
	function zaso_cta_banner_design_description() {
		$white_label = class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label();

		if ( $white_label ) {
			return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. Leave on "Default (classic banner)" to build your own look with the Layout Structure and Design colour settings instead.', 'zaso' );
		}

		return __( 'One-click, fully styled looks. Click "Browse designs" to preview every design and pick one visually. The free core ships six; Zen Addons Pro unlocks twenty-four more (license required). Leave on "Default (classic banner)" to build your own look with the Layout Structure and Design colour settings instead.', 'zaso' );
	}
endif;

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
				'description' => zaso_cta_banner_design_description(),
				'options'     => zaso_cta_banner_design_options(),
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

		// Defensive: a design_style preset may fill only some of the design
		// sub-fields (a solid skin omits the gradient + overlay keys, and every
		// skin omits heading_size / subheading_size). Default any missing piece
		// so a partially-filled preset can never crash or emit a notice. A
		// fully-saved Default instance already carries every key, so these
		// fallbacks only apply to keys a preset omitted and its output is
		// byte-identical. Fallback values equal the widget's own field defaults.
		$design     = isset( $instance['design'] ) && is_array( $instance['design'] ) ? $instance['design'] : array();
		$background  = wp_parse_args(
			( isset( $design['background'] ) && is_array( $design['background'] ) ) ? $design['background'] : array(),
			array(
				'bg_type'         => 'solid',
				'bg_color'        => '#1e293b',
				'gradient_start'  => '#4f46e5',
				'gradient_end'    => '#1e293b',
				'gradient_angle'  => 135,
				'overlay_color'   => '#0f172a',
				'overlay_opacity' => 60,
			)
		);
		$typography  = wp_parse_args(
			( isset( $design['typography'] ) && is_array( $design['typography'] ) ) ? $design['typography'] : array(),
			array(
				'heading_color'    => '#ffffff',
				'heading_size'     => '2rem',
				'subheading_color' => '#cbd5e1',
				'subheading_size'  => '1.125rem',
				'text_color'       => '#e2e8f0',
			)
		);
		$button      = wp_parse_args(
			( isset( $design['button'] ) && is_array( $design['button'] ) ) ? $design['button'] : array(),
			array(
				'button_bg'       => '#4f46e5',
				'button_bg_hover' => '#4338ca',
				'button_color'    => '#ffffff',
				'button_radius'   => '6px',
			)
		);
		$spacing     = wp_parse_args(
			( isset( $design['spacing'] ) && is_array( $design['spacing'] ) ) ? $design['spacing'] : array(),
			array(
				'padding_y'     => '3rem',
				'padding_x'     => '2rem',
				'border_radius' => '0px',
			)
		);

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

		// Defensive: mirror get_less_variables so a partially-filled preset cannot
		// crash the template build either. Defaults match the widget's own fields.
		$design     = isset( $instance['design'] ) && is_array( $instance['design'] ) ? $instance['design'] : array();
		$background  = isset( $design['background'] ) && is_array( $design['background'] ) ? $design['background'] : array();
		if ( ! isset( $background['bg_type'] ) ) {
			$background['bg_type'] = 'solid';
		}

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
