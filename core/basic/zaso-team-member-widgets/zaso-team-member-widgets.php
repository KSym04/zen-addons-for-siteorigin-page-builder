<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Team Member
 * Widget ID: zen-addons-siteorigin-team-member
 * Description: Display a grid of team members with photos, roles, bios, and social links.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Team_Member_Widget' ) ) :


class Zen_Addons_SiteOrigin_Team_Member_Widget extends SiteOrigin_Widget {

	function __construct() {

		$zaso_team_member_field_array = array(
			'members' => array(
				'type'       => 'repeater',
				'label'      => __( 'Team Members', 'zaso' ),
				'item_name'  => __( 'Team Member', 'zaso' ),
				'item_label' => array(
					'selector'     => "[name*='[name]']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'photo' => array(
						'type'    => 'media',
						'label'   => __( 'Photo', 'zaso' ),
						'library' => 'image',
						'fallback' => true,
					),
					'name' => array(
						'type'  => 'text',
						'label' => __( 'Name', 'zaso' ),
					),
					'role' => array(
						'type'  => 'text',
						'label' => __( 'Role / Title', 'zaso' ),
					),
					'bio' => array(
						'type'  => 'textarea',
						'label' => __( 'Bio', 'zaso' ),
					),
					'social_twitter' => array(
						'type'  => 'link',
						'label' => __( 'Twitter / X URL', 'zaso' ),
					),
					'social_linkedin' => array(
						'type'  => 'link',
						'label' => __( 'LinkedIn URL', 'zaso' ),
					),
					'social_facebook' => array(
						'type'  => 'link',
						'label' => __( 'Facebook URL', 'zaso' ),
					),
					'social_instagram' => array(
						'type'  => 'link',
						'label' => __( 'Instagram URL', 'zaso' ),
					),
					'social_website' => array(
						'type'  => 'link',
						'label' => __( 'Website URL', 'zaso' ),
					),
				),
			),
			'columns'    => array(
				'type'    => 'select',
				'label'   => __( 'Columns', 'zaso' ),
				'default' => '3',
				'options' => array(
					'2' => __( '2 Columns', 'zaso' ),
					'3' => __( '3 Columns', 'zaso' ),
					'4' => __( '4 Columns', 'zaso' ),
				),
			),
			'card_style' => array(
				'type'    => 'select',
				'label'   => __( 'Card Style', 'zaso' ),
				'default' => 'minimal',
				'options' => array(
					'minimal' => __( 'Minimal', 'zaso' ),
					'framed'  => __( 'Framed', 'zaso' ),
				),
			),
			'alignment'  => array(
				'type'    => 'select',
				'label'   => __( 'Alignment', 'zaso' ),
				'default' => 'center',
				'options' => array(
					'left'   => __( 'Left', 'zaso' ),
					'center' => __( 'Center', 'zaso' ),
				),
			),
			'design'     => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'photo_shape' => array(
						'type'    => 'select',
						'label'   => __( 'Photo Shape', 'zaso' ),
						'default' => 'circle',
						'options' => array(
							'circle' => __( 'Circle', 'zaso' ),
							'square' => __( 'Square', 'zaso' ),
						),
					),
					'photo_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Photo Size', 'zaso' ),
						'default' => '100px',
					),
					'name_color' => array(
						'type'    => 'color',
						'label'   => __( 'Name Color', 'zaso' ),
						'default' => '#111111',
					),
					'role_color' => array(
						'type'    => 'color',
						'label'   => __( 'Role Color', 'zaso' ),
						'default' => '#6b6b6b',
					),
					'bio_color' => array(
						'type'    => 'color',
						'label'   => __( 'Bio Color', 'zaso' ),
						'default' => '#444444',
					),
					'social_color' => array(
						'type'    => 'color',
						'label'   => __( 'Social Icon Color', 'zaso' ),
						'default' => '#6b6b6b',
					),
					'social_color_hover' => array(
						'type'    => 'color',
						'label'   => __( 'Social Icon Hover Color', 'zaso' ),
						'default' => '#111111',
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
								'default' => '24px',
							),
							'right' => array(
								'type'    => 'measurement',
								'label'   => __( 'Right', 'zaso' ),
								'default' => '24px',
							),
							'bottom' => array(
								'type'    => 'measurement',
								'label'   => __( 'Bottom', 'zaso' ),
								'default' => '24px',
							),
							'left' => array(
								'type'    => 'measurement',
								'label'   => __( 'Left', 'zaso' ),
								'default' => '24px',
							),
						),
					),
					'card_border_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Card Border Radius', 'zaso' ),
						'default' => '8px',
					),
					'gap' => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Cards', 'zaso' ),
						'default' => '24px',
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

		$zaso_team_member_fields = apply_filters( 'zaso_team_member_fields', $zaso_team_member_field_array );

		parent::__construct(
			'zen-addons-siteorigin-team-member',
			__( 'Zen Addons - Team Member', 'zaso' ),
			array(
				'description'   => __( 'Display a grid of team members with photos, roles, bios, and social links.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_team_member_fields,
			ZASO_WIDGET_BASIC_DIR
		);
	}

	/**
	 * Resolve attachment image src for a repeater media field.
	 *
	 * @param mixed  $attachment_id Attachment ID from the media field.
	 * @param string $size          Image size slug.
	 * @param string $member_name   Member name used as alt fallback.
	 * @return array { src: string, alt: string }
	 */
	private function zaso_resolve_member_photo( $attachment_id, $member_name ) {
		$src = '';
		if ( ! empty( $attachment_id ) ) {
			$img = siteorigin_widgets_get_attachment_image_src( $attachment_id, 'medium' );
			if ( ! empty( $img[0] ) ) {
				$src = $img[0];
			}
		}
		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		if ( empty( $alt ) ) {
			$alt = $member_name;
		}
		return array(
			'src' => $src,
			'alt' => $alt,
		);
	}

	function get_template_variables( $instance, $args ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$members = array();
		if ( ! empty( $instance['members'] ) && is_array( $instance['members'] ) ) {
			foreach ( $instance['members'] as $raw ) {
				$name  = isset( $raw['name'] ) ? $raw['name'] : '';
				$photo = $this->zaso_resolve_member_photo(
					isset( $raw['photo'] ) ? $raw['photo'] : '',
					$name
				);

				$social_links = array();
				$platforms = array(
					'twitter'   => __( 'Twitter / X', 'zaso' ),
					'linkedin'  => __( 'LinkedIn', 'zaso' ),
					'facebook'  => __( 'Facebook', 'zaso' ),
					'instagram' => __( 'Instagram', 'zaso' ),
					'website'   => __( 'Website', 'zaso' ),
				);
				foreach ( $platforms as $key => $label ) {
					$field = 'social_' . $key;
					if ( ! empty( $raw[ $field ] ) ) {
						$social_links[ $key ] = array(
							'url'   => $raw[ $field ],
							'label' => sprintf(
								/* translators: 1: person name, 2: platform name */
								__( '%1$s on %2$s', 'zaso' ),
								$name,
								$label
							),
						);
					}
				}

				$members[] = array(
					'photo'        => $photo,
					'name'         => $name,
					'role'         => isset( $raw['role'] ) ? $raw['role'] : '',
					'bio'          => isset( $raw['bio'] ) ? $raw['bio'] : '',
					'social_links' => $social_links,
				);
			}
		}

		$columns    = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$card_style = isset( $instance['card_style'] ) ? $instance['card_style'] : 'minimal';
		$alignment  = isset( $instance['alignment'] ) ? $instance['alignment'] : 'center';

		$container_classes = array(
			'zaso-team-member',
			'zaso-team-member--cols-' . $columns,
			'zaso-team-member--style-' . sanitize_html_class( $card_style ),
			'zaso-team-member--align-' . sanitize_html_class( $alignment ),
		);
		if ( ! empty( $instance['extra_class'] ) ) {
			$container_classes[] = sanitize_html_class( $instance['extra_class'] );
		}

		return apply_filters( 'zaso_team_member_template_variables', array(
			'members'           => $members,
			'container_classes' => implode( ' ', $container_classes ),
		) );
	}

	function get_less_variables( $instance ) {
		$design  = isset( $instance['design'] ) ? $instance['design'] : array();
		$padding = isset( $design['card_padding'] ) ? $design['card_padding'] : array();

		$pad_top    = isset( $padding['top'] )    ? $padding['top']    : '24px';
		$pad_right  = isset( $padding['right'] )  ? $padding['right']  : '24px';
		$pad_bottom = isset( $padding['bottom'] ) ? $padding['bottom'] : '24px';
		$pad_left   = isset( $padding['left'] )   ? $padding['left']   : '24px';

		$photo_shape = ( isset( $design['photo_shape'] ) && 'square' === $design['photo_shape'] ) ? '0' : '50%';

		return apply_filters( 'zaso_team_member_less_variables', array(
			'photo_size'          => isset( $design['photo_size'] )          ? $design['photo_size']          : '100px',
			'photo_shape'         => $photo_shape,
			'name_color'          => isset( $design['name_color'] )          ? $design['name_color']          : '#111111',
			'role_color'          => isset( $design['role_color'] )          ? $design['role_color']          : '#6b6b6b',
			'bio_color'           => isset( $design['bio_color'] )           ? $design['bio_color']           : '#444444',
			'social_color'        => isset( $design['social_color'] )        ? $design['social_color']        : '#6b6b6b',
			'social_color_hover'  => isset( $design['social_color_hover'] )  ? $design['social_color_hover']  : '#111111',
			'card_background'     => isset( $design['card_background'] )     ? $design['card_background']     : '#ffffff',
			'card_padding'        => sprintf( '%s %s %s %s', $pad_top, $pad_right, $pad_bottom, $pad_left ),
			'card_border_radius'  => isset( $design['card_border_radius'] )  ? $design['card_border_radius']  : '8px',
			'gap'                 => isset( $design['gap'] )                 ? $design['gap']                 : '24px',
		) );
	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-team-member', __FILE__, 'Zen_Addons_SiteOrigin_Team_Member_Widget' );


endif;
