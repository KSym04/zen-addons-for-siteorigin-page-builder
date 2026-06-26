<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: ZASO - Social Share Bar
 * Widget ID: zen-addons-siteorigin-social-share
 * Description: A row of share buttons (Facebook, X, LinkedIn, Pinterest, Reddit, WhatsApp, Telegram, Email) plus a copy-link button. Server-built share URLs, no third-party scripts.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Social_Share_Widget' ) ) :


class Zen_Addons_SiteOrigin_Social_Share_Widget extends SiteOrigin_Widget {

	/**
	 * Static registry of supported networks.
	 *
	 * Each entry: label, brand colour, inline SVG glyph (24x24, single path using
	 * currentColor) and a share URL template using the __URL__ / __TITLE__ tokens.
	 * The 'copy' network is special-cased in the template (it is a button, not a link).
	 *
	 * @since 1.9.0
	 * @return array
	 */
	public static function networks() {
		return array(
			'facebook' => array(
				'label' => __( 'Facebook', 'zaso' ),
				'color' => '#1877f2',
				'url'   => 'https://www.facebook.com/sharer/sharer.php?u=__URL__',
				'icon'  => '<path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 6.02 4.39 11.01 10.13 11.93v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.69.24 2.69.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.08 24 18.09 24 12.07z"/>',
			),
			'x'        => array(
				'label' => __( 'X', 'zaso' ),
				'color' => '#000000',
				'url'   => 'https://twitter.com/intent/tweet?url=__URL__&text=__TITLE__',
				'icon'  => '<path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.41l-5.8-7.58-6.64 7.58H.46l8.6-9.83L0 1.15h7.59l5.24 6.93zm-1.29 19.5h2.04L6.49 3.24H4.3z"/>',
			),
			'linkedin' => array(
				'label' => __( 'LinkedIn', 'zaso' ),
				'color' => '#0a66c2',
				'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=__URL__',
				'icon'  => '<path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.13 1.45-2.13 2.94v5.67H9.35V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zM7.12 20.45H3.55V9h3.57zM22.22 0H1.77C.8 0 0 .78 0 1.74v20.52C0 23.22.8 24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.74V1.74C24 .78 23.2 0 22.22 0z"/>',
			),
			'pinterest' => array(
				'label' => __( 'Pinterest', 'zaso' ),
				'color' => '#e60023',
				'url'   => 'https://pinterest.com/pin/create/button/?url=__URL__&description=__TITLE__',
				'icon'  => '<path d="M12 0C5.37 0 0 5.37 0 12c0 5.08 3.16 9.43 7.62 11.18-.11-.95-.2-2.41.04-3.45.22-.93 1.4-5.96 1.4-5.96s-.36-.72-.36-1.78c0-1.67.97-2.92 2.18-2.92 1.03 0 1.52.77 1.52 1.7 0 1.03-.66 2.58-1 4.01-.28 1.2.6 2.18 1.79 2.18 2.15 0 3.8-2.27 3.8-5.54 0-2.9-2.08-4.92-5.06-4.92-3.45 0-5.47 2.58-5.47 5.25 0 1.04.4 2.16.9 2.76.1.12.11.23.08.35l-.33 1.36c-.05.22-.18.27-.4.16-1.5-.7-2.43-2.88-2.43-4.64 0-3.78 2.75-7.25 7.92-7.25 4.16 0 7.39 2.96 7.39 6.92 0 4.13-2.6 7.45-6.22 7.45-1.21 0-2.35-.63-2.74-1.38l-.75 2.84c-.27 1.04-1 2.35-1.49 3.15A12 12 0 1 0 12 0z"/>',
			),
			'reddit'   => array(
				'label' => __( 'Reddit', 'zaso' ),
				'color' => '#e03d00',
				'url'   => 'https://www.reddit.com/submit?url=__URL__&title=__TITLE__',
				'icon'  => '<path d="M24 11.78c0-1.45-1.18-2.63-2.63-2.63-.7 0-1.34.28-1.81.73-1.78-1.28-4.24-2.1-6.97-2.2l1.19-5.6 3.89.82a1.88 1.88 0 1 0 .19-1.1l-4.34-.92a.55.55 0 0 0-.65.42l-1.32 6.23c-2.77.08-5.27.9-7.07 2.2a2.62 2.62 0 0 0-1.81-.73A2.63 2.63 0 0 0 0 11.78c0 1.06.63 1.96 1.53 2.38a4.7 4.7 0 0 0-.06.75c0 3.79 4.41 6.86 9.85 6.86s9.85-3.07 9.85-6.86c0-.25-.02-.5-.06-.74A2.63 2.63 0 0 0 24 11.78zm-16.5 1.87a1.88 1.88 0 1 1 3.75 0 1.88 1.88 0 0 1-3.75 0zm9.96 4.68c-1.15 1.15-3.35 1.24-3.99 1.24s-2.84-.09-3.99-1.24a.44.44 0 0 1 .62-.62c.73.73 2.27.98 3.37.98s2.65-.25 3.37-.98a.44.44 0 0 1 .62.62zm-.34-2.8a1.88 1.88 0 1 1 0-3.76 1.88 1.88 0 0 1 0 3.76z"/>',
			),
			'whatsapp' => array(
				'label' => __( 'WhatsApp', 'zaso' ),
				'color' => '#1a8a4d',
				'url'   => 'https://api.whatsapp.com/send?text=__TITLE__%20__URL__',
				'icon'  => '<path d="M.06 24l1.68-6.15a11.87 11.87 0 0 1-1.6-5.95C.15 5.32 5.5 0 12.06 0a11.82 11.82 0 0 1 8.42 3.49 11.74 11.74 0 0 1 3.48 8.41c0 6.57-5.35 11.9-11.92 11.9a12 12 0 0 1-5.7-1.45zm6.6-3.8c1.67.99 3.28 1.58 5.4 1.58 5.46 0 9.9-4.42 9.9-9.88a9.8 9.8 0 0 0-2.89-6.98 9.83 9.83 0 0 0-6.99-2.9c-5.46 0-9.9 4.43-9.9 9.88 0 2.23.65 3.9 1.74 5.65l-.99 3.62zm11.4-5.55c-.07-.12-.27-.2-.56-.34-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.66.15-.2.3-.76.96-.93 1.16-.17.2-.34.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.6.13-.14.3-.34.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.66-1.6-.9-2.19-.24-.57-.48-.5-.66-.5l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.03 1-1.03 2.46s1.06 2.85 1.21 3.05c.15.2 2.08 3.18 5.05 4.46.7.3 1.25.48 1.68.62.71.22 1.35.2 1.86.12.57-.08 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41z"/>',
			),
			'telegram' => array(
				'label' => __( 'Telegram', 'zaso' ),
				'color' => '#1c8cc2',
				'url'   => 'https://t.me/share/url?url=__URL__&text=__TITLE__',
				'icon'  => '<path d="M23.91 3.79 20.3 20.84c-.25 1.21-.98 1.5-2 .94l-5.5-4.07-2.66 2.57c-.3.3-.55.56-1.1.56l.38-5.56 10.1-9.13c.44-.39-.1-.61-.68-.22L6.27 13.5l-5.45-1.7c-1.18-.37-1.2-1.18.26-1.75l21.26-8.2c.99-.36 1.85.22 1.57 1.94z"/>',
			),
			'email'    => array(
				'label' => __( 'Email', 'zaso' ),
				'color' => '#475569',
				'url'   => 'mailto:?subject=__TITLE__&body=__URL__',
				'icon'  => '<path d="M1.5 4h21A1.5 1.5 0 0 1 24 5.5v13a1.5 1.5 0 0 1-1.5 1.5h-21A1.5 1.5 0 0 1 0 18.5v-13A1.5 1.5 0 0 1 1.5 4zm10.5 8.64 9.43-6.14H2.57zm-.54 2.05L2 6.96V17.5h20V6.96l-9.46 7.73a1 1 0 0 1-1.08 0z"/>',
			),
			'copy'     => array(
				'label' => __( 'Copy Link', 'zaso' ),
				'color' => '#334155',
				'url'   => '',
				'icon'  => '<path d="M16.5 1h-9A2.5 2.5 0 0 0 5 3.5V5h2V3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H15v2h1.5A2.5 2.5 0 0 0 19 15.5v-12A2.5 2.5 0 0 0 16.5 1zm-4 6h-9A2.5 2.5 0 0 0 1 9.5v11A2.5 2.5 0 0 0 3.5 23h9a2.5 2.5 0 0 0 2.5-2.5v-11A2.5 2.5 0 0 0 12.5 7zm.5 13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5z"/>',
			),
		);
	}

	function __construct() {

		$network_fields = array();
		$defaults_on    = array( 'facebook', 'x', 'linkedin', 'email', 'copy' );

		foreach ( self::networks() as $key => $data ) {
			$network_fields[ $key ] = array(
				'type'    => 'checkbox',
				'label'   => $data['label'],
				'default' => in_array( $key, $defaults_on, true ),
			);
		}

		$zaso_social_share_field_array = array(
			'networks'     => array(
				'type'   => 'section',
				'label'  => __( 'Networks', 'zaso' ),
				'hide'   => false,
				'fields' => $network_fields,
			),
			'share_source' => array(
				'type'    => 'select',
				'label'   => __( 'Share Target', 'zaso' ),
				'default' => 'current',
				'options' => array(
					'current' => __( 'Current Page', 'zaso' ),
					'custom'  => __( 'Custom URL', 'zaso' ),
				),
			),
			'custom_url'   => array(
				'type'        => 'link',
				'label'       => __( 'Custom URL', 'zaso' ),
				'description' => __( 'Used only when Share Target is set to Custom URL.', 'zaso' ),
			),
			'custom_title' => array(
				'type'        => 'text',
				'label'       => __( 'Custom Title', 'zaso' ),
				'description' => __( 'Optional. Overrides the auto-detected page title in the share text.', 'zaso' ),
			),
			'show_labels'  => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Network Labels', 'zaso' ),
				'default' => false,
			),
			'design'       => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'shape'          => array(
						'type'    => 'select',
						'label'   => __( 'Button Shape', 'zaso' ),
						'default' => 'rounded',
						'options' => array(
							'square'  => __( 'Square', 'zaso' ),
							'rounded' => __( 'Rounded', 'zaso' ),
							'circle'  => __( 'Circle', 'zaso' ),
						),
					),
					'color_mode'     => array(
						'type'    => 'select',
						'label'   => __( 'Color Mode', 'zaso' ),
						'default' => 'brand',
						'options' => array(
							'brand' => __( 'Brand Colors', 'zaso' ),
							'mono'  => __( 'Single Color', 'zaso' ),
						),
					),
					'mono_bg_color'  => array(
						'type'        => 'color',
						'label'       => __( 'Single Color: Button', 'zaso' ),
						'default'     => '#01949a',
						'description' => __( 'Used only when Color Mode is Single Color.', 'zaso' ),
					),
					'mono_icon_color' => array(
						'type'    => 'color',
						'label'   => __( 'Single Color: Icon', 'zaso' ),
						'default' => '#ffffff',
					),
					'alignment'      => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'zaso' ),
						'default' => 'flex-start',
						'options' => array(
							'flex-start' => __( 'Left', 'zaso' ),
							'center'     => __( 'Center', 'zaso' ),
							'flex-end'   => __( 'Right', 'zaso' ),
						),
					),
					'icon_size'      => array(
						'type'    => 'measurement',
						'label'   => __( 'Icon Size', 'zaso' ),
						'default' => '20px',
					),
					'button_padding' => array(
						'type'    => 'measurement',
						'label'   => __( 'Button Padding', 'zaso' ),
						'default' => '12px',
					),
					'gap'            => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Buttons', 'zaso' ),
						'default' => '8px',
					),
				),
			),
			'extra_id'     => array(
				'type'  => 'text',
				'label' => __( 'Extra ID', 'zaso' ),
			),
			'extra_class'  => array(
				'type'  => 'text',
				'label' => __( 'Extra Class', 'zaso' ),
			),
		);

		$zaso_social_share_fields = apply_filters( 'zaso_social_share_fields', $zaso_social_share_field_array );

		parent::__construct(
			'zen-addons-siteorigin-social-share',
			__( 'ZASO - Social Share Bar', 'zaso' ),
			array(
				'description'   => __( 'A row of share buttons plus a copy-link button. Server-built share URLs, no third-party scripts.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_social_share_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Resolve the URL that should be shared.
	 *
	 * @since 1.9.0
	 * @param array $instance Widget instance.
	 * @return string Raw (unencoded) share URL.
	 */
	protected function resolve_share_url( $instance ) {
		$source = isset( $instance['share_source'] ) ? $instance['share_source'] : 'current';

		if ( 'custom' === $source && ! empty( $instance['custom_url'] ) ) {
			return esc_url_raw( $instance['custom_url'] );
		}

		if ( is_singular() ) {
			$permalink = get_permalink();
			if ( $permalink ) {
				return $permalink;
			}
		}

		global $wp;
		$request = isset( $wp->request ) ? $wp->request : '';

		return home_url( add_query_arg( array(), $request ) );
	}

	/**
	 * Resolve the title used in share text.
	 *
	 * @since 1.9.0
	 * @param array $instance Widget instance.
	 * @return string Raw (unencoded) title.
	 */
	protected function resolve_share_title( $instance ) {
		if ( ! empty( $instance['custom_title'] ) ) {
			return sanitize_text_field( $instance['custom_title'] );
		}

		if ( is_singular() ) {
			return wp_strip_all_tags( get_the_title() );
		}

		return wp_strip_all_tags( wp_get_document_title() );
	}

	function get_template_variables( $instance, $args ) {
		$networks      = self::networks();
		$selected      = isset( $instance['networks'] ) ? $instance['networks'] : array();
		$share_url     = $this->resolve_share_url( $instance );
		$share_title   = $this->resolve_share_title( $instance );
		$encoded_url   = rawurlencode( $share_url );
		$encoded_title = rawurlencode( $share_title );

		$items = array();
		foreach ( $networks as $key => $data ) {
			if ( empty( $selected[ $key ] ) ) {
				continue;
			}

			$href = '';
			if ( 'copy' !== $key && ! empty( $data['url'] ) ) {
				$href = str_replace(
					array( '__URL__', '__TITLE__' ),
					array( $encoded_url, $encoded_title ),
					$data['url']
				);
			}

			$items[] = array(
				'key'   => $key,
				'label' => $data['label'],
				'color' => $data['color'],
				'icon'  => $data['icon'],
				'href'  => $href,
			);
		}

		$design      = isset( $instance['design'] ) ? $instance['design'] : array();
		$shape       = isset( $design['shape'] ) ? $design['shape'] : 'rounded';
		$color_mode  = isset( $design['color_mode'] ) ? $design['color_mode'] : 'brand';
		$extra_class = isset( $instance['extra_class'] ) ? sanitize_html_class( $instance['extra_class'] ) : '';

		$classes = trim(
			'zaso-social-share'
			. ' zaso-social-share--' . sanitize_html_class( $shape )
			. ' zaso-social-share--' . sanitize_html_class( $color_mode )
			. ' ' . $extra_class
		);

		return apply_filters( 'zaso_social_share_template_variables', array(
			'items'       => $items,
			'share_url'   => $share_url,
			'show_labels' => ! empty( $instance['show_labels'] ),
			'color_mode'  => $color_mode,
			'classes'     => $classes,
		) );
	}

	function get_less_variables( $instance ) {
		$design = isset( $instance['design'] ) ? $instance['design'] : array();

		$shape  = isset( $design['shape'] ) ? $design['shape'] : 'rounded';
		$radius = '8px';
		if ( 'square' === $shape ) {
			$radius = '0px';
		} elseif ( 'circle' === $shape ) {
			// Stadium radius: an icon-only button becomes a perfect circle, while a
			// labelled button becomes a clean pill. A literal 50% would distort wide
			// (labelled) buttons into ellipses.
			$radius = '999px';
		}

		return apply_filters( 'zaso_social_share_less_variables', array(
			'alignment'       => isset( $design['alignment'] ) ? $design['alignment'] : 'flex-start',
			'gap'             => isset( $design['gap'] ) ? $design['gap'] : '8px',
			'icon_size'       => isset( $design['icon_size'] ) ? $design['icon_size'] : '20px',
			'button_padding'  => isset( $design['button_padding'] ) ? $design['button_padding'] : '12px',
			'border_radius'   => $radius,
			'mono_bg_color'   => isset( $design['mono_bg_color'] ) ? $design['mono_bg_color'] : '#01949a',
			'mono_icon_color' => isset( $design['mono_icon_color'] ) ? $design['mono_icon_color'] : '#ffffff',
		) );
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-social-share',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-social-share', __FILE__, 'Zen_Addons_SiteOrigin_Social_Share_Widget' );


endif;
