<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Testimonial Slider
 * Widget ID: zen-addons-siteorigin-testimonial-slider
 * Description: A sliding testimonial carousel with auto-play, swipe, and keyboard support.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget' ) ) :


class Zen_Addons_SiteOrigin_Testimonial_Slider_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_testimonial_slider_field_array = array(
			'testimonials' => array(
				'type'       => 'repeater',
				'label'      => __( 'Testimonials', 'zaso' ),
				'item_name'  => __( 'Testimonial', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[author_name]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'quote' => array(
						'type'  => 'textarea',
						'label' => __( 'Quote', 'zaso' ),
					),
					'author_name' => array(
						'type'  => 'text',
						'label' => __( 'Author Name', 'zaso' ),
					),
					'author_title' => array(
						'type'        => 'text',
						'label'       => __( 'Role / Company', 'zaso' ),
						'description' => __( 'e.g. CEO at Acme Corp', 'zaso' ),
					),
					'author_photo' => array(
						'type'     => 'media',
						'label'    => __( 'Author Photo', 'zaso' ),
						'library'  => 'image',
						'fallback' => true,
					),
					'rating' => array(
						'type'    => 'select',
						'label'   => __( 'Star Rating', 'zaso' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'None', 'zaso' ),
							'1' => '★',
							'2' => '★★',
							'3' => '★★★',
							'4' => '★★★★',
							'5' => '★★★★★',
						),
					),
				),
			),
			'autoplay'          => array(
				'type'    => 'checkbox',
				'label'   => __( 'Auto-play', 'zaso' ),
				'default' => true,
			),
			'autoplay_duration' => array(
				'type'        => 'number',
				'label'       => __( 'Auto-play Duration (ms)', 'zaso' ),
				'default'     => 5000,
				'description' => __( 'Time each slide is shown, in milliseconds.', 'zaso' ),
			),
			'show_arrows'       => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Navigation Arrows', 'zaso' ),
				'default' => true,
			),
			'show_dots'         => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Dot Pagination', 'zaso' ),
				'default' => true,
			),
			'design'            => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'quote_font_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Quote Font Size', 'zaso' ),
						'default' => '18px',
					),
					'quote_color' => array(
						'type'    => 'color',
						'label'   => __( 'Quote Color', 'zaso' ),
						'default' => '#333333',
					),
					'quote_italic' => array(
						'type'    => 'select',
						'label'   => __( 'Quote Italic', 'zaso' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'zaso' ),
							'no'  => __( 'No', 'zaso' ),
						),
					),
					'author_name_color' => array(
						'type'    => 'color',
						'label'   => __( 'Author Name Color', 'zaso' ),
						'default' => '#111111',
					),
					'author_title_color' => array(
						'type'    => 'color',
						'label'   => __( 'Author Title Color', 'zaso' ),
						'default' => '#888888',
					),
					'star_color' => array(
						'type'    => 'color',
						'label'   => __( 'Star Color', 'zaso' ),
						'default' => '#f5a623',
					),
					'card_background' => array(
						'type'    => 'color',
						'label'   => __( 'Card Background', 'zaso' ),
						'default' => '#ffffff',
					),
					'card_padding' => array(
						'type'   => 'section',
						'label'  => __( 'Card Padding', 'zaso' ),
						'hide'   => true,
						'fields' => array(
							'top' => array(
								'type'    => 'measurement',
								'label'   => __( 'Top', 'zaso' ),
								'default' => '32px',
							),
							'right' => array(
								'type'    => 'measurement',
								'label'   => __( 'Right', 'zaso' ),
								'default' => '32px',
							),
							'bottom' => array(
								'type'    => 'measurement',
								'label'   => __( 'Bottom', 'zaso' ),
								'default' => '32px',
							),
							'left' => array(
								'type'    => 'measurement',
								'label'   => __( 'Left', 'zaso' ),
								'default' => '32px',
							),
						),
					),
					'card_border_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Border Radius', 'zaso' ),
						'default' => '8px',
					),
					'arrow_color' => array(
						'type'    => 'color',
						'label'   => __( 'Arrow Color', 'zaso' ),
						'default' => '#111111',
					),
					'dot_color' => array(
						'type'    => 'color',
						'label'   => __( 'Dot Color', 'zaso' ),
						'default' => '#cccccc',
					),
					'dot_active_color' => array(
						'type'    => 'color',
						'label'   => __( 'Active Dot Color', 'zaso' ),
						'default' => '#111111',
					),
				),
			),
			'extra_id'   => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
			),
			'extra_class' => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
			),
		);

		$zaso_testimonial_slider_fields = apply_filters( 'zaso_testimonial_slider_fields', $zaso_testimonial_slider_field_array );

		parent::__construct(
			'zen-addons-siteorigin-testimonial-slider',
			__( 'Zen Addons - Testimonial Slider', 'zaso' ),
			array(
				'description'   => __( 'A sliding testimonial carousel with auto-play, swipe, and keyboard support.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_testimonial_slider_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	function get_template_variables( $instance, $args ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$testimonials = array();
		if ( ! empty( $instance['testimonials'] ) && is_array( $instance['testimonials'] ) ) {
			foreach ( $instance['testimonials'] as $raw ) {
				$author_name = isset( $raw['author_name'] ) ? $raw['author_name'] : '';

				$photo_src = '';
				$photo_alt = $author_name;
				if ( ! empty( $raw['author_photo'] ) ) {
					$img = siteorigin_widgets_get_attachment_image_src( $raw['author_photo'], 'thumbnail' );
					if ( ! empty( $img[0] ) ) {
						$photo_src = $img[0];
					}
					$meta_alt = get_post_meta( $raw['author_photo'], '_wp_attachment_image_alt', true );
					if ( ! empty( $meta_alt ) ) {
						$photo_alt = $meta_alt;
					}
				}

				// Clamp to 0-5 so the star markup (str_repeat) can never receive a
				// negative count, which would fatal under PHP 8 on a corrupted instance.
				$rating = min( 5, max( 0, absint( isset( $raw['rating'] ) ? $raw['rating'] : 0 ) ) );

				$testimonials[] = array(
					'quote'        => isset( $raw['quote'] ) ? $raw['quote'] : '',
					'author_name'  => $author_name,
					'author_title' => isset( $raw['author_title'] ) ? $raw['author_title'] : '',
					'photo_src'    => $photo_src,
					'photo_alt'    => $photo_alt,
					'rating'       => $rating,
					/* translators: 1: number of stars, 2: max stars */
					'rating_label' => $rating > 0 ? sprintf( __( '%1$d out of %2$d stars', 'zaso' ), $rating, 5 ) : '',
				);
			}
		}

		$count             = count( $testimonials );
		$autoplay          = ! empty( $instance['autoplay'] );
		$autoplay_duration = absint( isset( $instance['autoplay_duration'] ) ? $instance['autoplay_duration'] : 5000 );
		$show_arrows       = ! empty( $instance['show_arrows'] );
		$show_dots         = ! empty( $instance['show_dots'] );

		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';
		$classes     = trim( 'zaso-testimonial-slider ' . $extra_class );

		return apply_filters( 'zaso_testimonial_slider_template_variables', array(
			'testimonials'      => $testimonials,
			'count'             => $count,
			'autoplay'          => $autoplay,
			'autoplay_duration' => $autoplay_duration,
			'show_arrows'       => $show_arrows,
			'show_dots'         => $show_dots,
			'classes'           => $classes,
		) );
	}

	function get_less_variables( $instance ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$pad_top    = isset( $padding['top'] )    ? $padding['top']    : '32px';
		$pad_right  = isset( $padding['right'] )  ? $padding['right']  : '32px';
		$pad_bottom = isset( $padding['bottom'] ) ? $padding['bottom'] : '32px';
		$pad_left   = isset( $padding['left'] )   ? $padding['left']   : '32px';

		$quote_italic = ( isset( $design['quote_italic'] ) && 'no' === $design['quote_italic'] ) ? 'normal' : 'italic';

		return apply_filters( 'zaso_testimonial_slider_less_variables', array(
			'quote_font_size'    => isset( $design['quote_font_size'] )    ? $design['quote_font_size']    : '18px',
			'quote_color'        => isset( $design['quote_color'] )        ? $design['quote_color']        : '#333333',
			'quote_italic'       => $quote_italic,
			'author_name_color'  => isset( $design['author_name_color'] )  ? $design['author_name_color']  : '#111111',
			'author_title_color' => isset( $design['author_title_color'] ) ? $design['author_title_color'] : '#888888',
			'star_color'         => isset( $design['star_color'] )         ? $design['star_color']         : '#f5a623',
			'card_background'    => isset( $design['card_background'] )    ? $design['card_background']    : '#ffffff',
			'card_padding'       => sprintf( '%s %s %s %s', $pad_top, $pad_right, $pad_bottom, $pad_left ),
			'card_border_radius' => isset( $design['card_border_radius'] ) ? $design['card_border_radius'] : '8px',
			'arrow_color'        => isset( $design['arrow_color'] )        ? $design['arrow_color']        : '#111111',
			'dot_color'          => isset( $design['dot_color'] )          ? $design['dot_color']          : '#cccccc',
			'dot_active_color'   => isset( $design['dot_active_color'] )   ? $design['dot_active_color']   : '#111111',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-testimonial-slider',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-testimonial-slider', __FILE__, 'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget' );


endif;
