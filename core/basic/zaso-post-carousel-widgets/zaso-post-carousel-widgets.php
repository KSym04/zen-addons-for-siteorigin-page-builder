<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: ZASO - Post Carousel
 * Widget ID: zen-addons-siteorigin-post-carousel
 * Description: Display posts in a responsive, swipeable carousel with autoplay, arrows, and dots.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Post_Carousel_Widget' ) ) :


class Zen_Addons_SiteOrigin_Post_Carousel_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_post_carousel_field_array = array(
			'posts'          => array(
				'type'  => 'posts',
				'label' => __( 'Posts query', 'zaso' ),
			),
			'slides_to_show' => array(
				'type'    => 'select',
				'label'   => __( 'Slides Visible', 'zaso' ),
				'default' => '3',
				'options' => array(
					'1' => __( '1 Slide', 'zaso' ),
					'2' => __( '2 Slides', 'zaso' ),
					'3' => __( '3 Slides', 'zaso' ),
					'4' => __( '4 Slides', 'zaso' ),
				),
			),
			'autoplay'       => array(
				'type'    => 'checkbox',
				'label'   => __( 'Autoplay', 'zaso' ),
				'default' => true,
			),
			'autoplay_speed' => array(
				'type'        => 'number',
				'label'       => __( 'Autoplay Speed (ms)', 'zaso' ),
				'default'     => 4000,
				'description' => __( 'Time each slide stays before advancing, in milliseconds.', 'zaso' ),
			),
			'show_arrows'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Arrows', 'zaso' ),
				'default' => true,
			),
			'show_dots'      => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Dots', 'zaso' ),
				'default' => true,
			),
			'show_image'     => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Featured Image', 'zaso' ),
				'default' => true,
			),
			'image_size'     => array(
				'type'    => 'select',
				'label'   => __( 'Image Size', 'zaso' ),
				'default' => 'medium_large',
				'options' => array(
					'thumbnail'    => __( 'Thumbnail', 'zaso' ),
					'medium'       => __( 'Medium', 'zaso' ),
					'medium_large' => __( 'Medium Large', 'zaso' ),
					'large'        => __( 'Large', 'zaso' ),
				),
			),
			'show_date'      => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Date', 'zaso' ),
				'default' => true,
			),
			'show_author'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Author', 'zaso' ),
				'default' => false,
			),
			'show_excerpt'   => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Excerpt', 'zaso' ),
				'default' => true,
			),
			'excerpt_length' => array(
				'type'    => 'number',
				'label'   => __( 'Excerpt Word Count', 'zaso' ),
				'default' => 20,
			),
			'show_readmore'  => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Read More Link', 'zaso' ),
				'default' => true,
			),
			'readmore_text'  => array(
				'type'    => 'text',
				'label'   => __( 'Read More Text', 'zaso' ),
				'default' => __( 'Read More', 'zaso' ),
			),
			'design'         => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'gap'          => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Cards', 'zaso' ),
						'default' => '24px',
					),
					'card_bg'      => array(
						'type'    => 'color',
						'label'   => __( 'Card Background', 'zaso' ),
						'default' => '#ffffff',
					),
					'card_border'  => array(
						'type'    => 'color',
						'label'   => __( 'Card Border', 'zaso' ),
						'default' => '#e5e7eb',
					),
					'card_radius'  => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Corner Radius', 'zaso' ),
						'default' => '10px',
					),
					'title_color'  => array(
						'type'    => 'color',
						'label'   => __( 'Title Color', 'zaso' ),
						'default' => '#111111',
					),
					'meta_color'   => array(
						'type'    => 'color',
						'label'   => __( 'Meta Color', 'zaso' ),
						'default' => '#6b7280',
					),
					'accent_color' => array(
						'type'    => 'color',
						'label'   => __( 'Accent Color', 'zaso' ),
						'default' => '#4f46e5',
					),
					'image_height' => array(
						'type'    => 'measurement',
						'label'   => __( 'Image Height', 'zaso' ),
						'default' => '200px',
					),
					'arrow_color'  => array(
						'type'    => 'color',
						'label'   => __( 'Arrow Color', 'zaso' ),
						'default' => '#4f46e5',
					),
				),
			),
			'extra_id'       => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
			),
			'extra_class'    => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
			),
		);

		$zaso_post_carousel_fields = apply_filters( 'zaso_post_carousel_fields', $zaso_post_carousel_field_array );

		parent::__construct(
			'zen-addons-siteorigin-post-carousel',
			__( 'ZASO - Post Carousel', 'zaso' ),
			array(
				'description'   => __( 'Display posts in a responsive, swipeable carousel with autoplay, arrows, and dots.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_post_carousel_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Build a safe, capped WP_Query argument array from the posts field.
	 *
	 * @param  array $instance Widget instance.
	 * @return array WP_Query args.
	 */
	private function zaso_build_query_args( $instance ) {
		$query_args = array();
		if ( ! empty( $instance['posts'] ) && function_exists( 'siteorigin_widget_post_selector_process_query' ) ) {
			$query_args = siteorigin_widget_post_selector_process_query( $instance['posts'] );
		}
		if ( empty( $query_args ) || ! is_array( $query_args ) ) {
			$query_args = array( 'post_type' => 'post', 'posts_per_page' => 9 );
		}

		$per_page                          = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : 9;
		$query_args['posts_per_page']      = min( 24, max( 1, $per_page ) );
		$query_args['ignore_sticky_posts'] = true;
		$query_args['no_found_rows']       = true;

		return $query_args;
	}

	function get_template_variables( $instance, $args ) {
		$allowed_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );
		$image_size    = isset( $instance['image_size'] ) ? $instance['image_size'] : 'medium_large';
		if ( ! in_array( $image_size, $allowed_sizes, true ) ) {
			$image_size = 'medium_large';
		}

		$excerpt_length = isset( $instance['excerpt_length'] ) ? absint( $instance['excerpt_length'] ) : 20;
		$excerpt_length = min( 100, max( 5, $excerpt_length ) );

		$slides_to_show = isset( $instance['slides_to_show'] ) ? absint( $instance['slides_to_show'] ) : 3;
		$slides_to_show = min( 4, max( 1, $slides_to_show ) );

		$autoplay_speed = isset( $instance['autoplay_speed'] ) ? absint( $instance['autoplay_speed'] ) : 4000;
		$autoplay_speed = min( 20000, max( 1000, $autoplay_speed ) );

		$container_classes = array( 'zaso-post-carousel' );
		if ( ! empty( $instance['extra_class'] ) ) {
			$container_classes[] = sanitize_html_class( $instance['extra_class'] );
		}

		return apply_filters( 'zaso_post_carousel_template_variables', array(
			'query_args'        => $this->zaso_build_query_args( $instance ),
			'slides_to_show'    => $slides_to_show,
			'autoplay'          => ! empty( $instance['autoplay'] ),
			'autoplay_speed'    => $autoplay_speed,
			'show_arrows'       => ! empty( $instance['show_arrows'] ),
			'show_dots'         => ! empty( $instance['show_dots'] ),
			'show_image'        => ! empty( $instance['show_image'] ),
			'image_size'        => $image_size,
			'show_date'         => ! empty( $instance['show_date'] ),
			'show_author'       => ! empty( $instance['show_author'] ),
			'show_excerpt'      => ! empty( $instance['show_excerpt'] ),
			'excerpt_length'    => $excerpt_length,
			'show_readmore'     => ! empty( $instance['show_readmore'] ),
			'readmore_text'     => isset( $instance['readmore_text'] ) ? sanitize_text_field( $instance['readmore_text'] ) : __( 'Read More', 'zaso' ),
			'container_classes' => implode( ' ', $container_classes ),
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		return apply_filters( 'zaso_post_carousel_less_variables', array(
			'gap'          => isset( $design['gap'] )          ? $design['gap']          : '24px',
			'card_bg'      => isset( $design['card_bg'] )      ? $design['card_bg']      : '#ffffff',
			'card_border'  => isset( $design['card_border'] )  ? $design['card_border']  : '#e5e7eb',
			'card_radius'  => isset( $design['card_radius'] )  ? $design['card_radius']  : '10px',
			'title_color'  => isset( $design['title_color'] )  ? $design['title_color']  : '#111111',
			'meta_color'   => isset( $design['meta_color'] )   ? $design['meta_color']   : '#6b7280',
			'accent_color' => isset( $design['accent_color'] ) ? $design['accent_color'] : '#4f46e5',
			'image_height' => isset( $design['image_height'] ) ? $design['image_height'] : '200px',
			'arrow_color'  => isset( $design['arrow_color'] )  ? $design['arrow_color']  : '#4f46e5',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-post-carousel',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-post-carousel', __FILE__, 'Zen_Addons_SiteOrigin_Post_Carousel_Widget' );


endif;
