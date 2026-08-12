<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Notification Banner
 * Widget ID: zen-addons-siteorigin-notification-banner
 * Description: Announce something once, with an optional sticky bar and a dismissal the visitor's browser remembers.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Notification_Banner_Widget' ) ) :

/**
 * Notification Banner widget.
 *
 * Distinct from the Alert Box widget on purpose: Alert Box is an inline message
 * block whose close button only hides it for the current page view. This widget
 * is an announcement bar that can stick to the top or bottom of the viewport and
 * whose dismissal is REMEMBERED, so a visitor who closes it is not asked again.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.11.0
 */
class Zen_Addons_SiteOrigin_Notification_Banner_Widget extends SiteOrigin_Widget {

	/**
	 * Register the widget and its form fields.
	 *
	 * @since 1.11.0
	 */
	function __construct() {

		// ZASO field array.
		$zaso_notification_banner_field_array = array(
			'banner_message' => array(
				'type'        => 'tinymce',
				'label'       => __( 'Message', 'zaso' ),
				'description' => __( 'The announcement itself. Keep it to one short line for a sticky bar.', 'zaso' ),
			),
			'banner_link_text' => array(
				'type'  => 'text',
				'label' => __( 'Button Text', 'zaso' ),
			),
			'banner_link_url' => array(
				'type'        => 'link',
				'label'       => __( 'Button Link', 'zaso' ),
				'description' => __( 'Leave the text or the link empty to show no button.', 'zaso' ),
			),
			'banner_link_new_tab' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Open Button Link In A New Tab', 'zaso' ),
				'default' => false,
			),
			'banner_position' => array(
				'type'    => 'select',
				'label'   => __( 'Position', 'zaso' ),
				'default' => 'inline',
				'options' => array(
					'inline' => __( 'Inline (in the page flow)', 'zaso' ),
					'top'    => __( 'Stuck to the top of the screen', 'zaso' ),
					'bottom' => __( 'Stuck to the bottom of the screen', 'zaso' ),
				),
			),
			'banner_dismissible' => array(
				'type'        => 'checkbox',
				'label'       => __( 'Show A Dismiss Button', 'zaso' ),
				'default'     => true,
				'description' => __( 'Always leave this on for a sticky banner, or visitors cannot get rid of it.', 'zaso' ),
			),
			'banner_remember' => array(
				'type'        => 'select',
				'label'       => __( 'Remember The Dismissal', 'zaso' ),
				'default'     => 'forever',
				'options'     => array(
					'none'    => __( 'Not at all (shows again on the next page)', 'zaso' ),
					'session' => __( 'Until the browser is closed', 'zaso' ),
					'forever' => __( 'Until the message is edited', 'zaso' ),
				),
				'description' => __( 'Stored in the visitor\'s own browser, so no cookie notice is needed. Editing the message shows it again to everyone.', 'zaso' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'background_color' => array(
						'type'    => 'color',
						'label'   => __( 'Background Color', 'zaso' ),
						'default' => '#1e293b',
					),
					'font_color' => array(
						'type'    => 'color',
						'label'   => __( 'Text Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'link_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Color', 'zaso' ),
						'default' => '#2563eb',
					),
					'link_font_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Text Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'align' => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'zaso' ),
						'default' => 'center',
						'options' => array(
							'left'   => __( 'Left', 'zaso' ),
							'center' => __( 'Center', 'zaso' ),
						),
					),
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
		);

		// Add filter.
		$zaso_notification_banner_fields = apply_filters( 'zaso_notification_banner_fields', $zaso_notification_banner_field_array );

		parent::__construct(
			'zen-addons-siteorigin-notification-banner',
			__( 'Zen Addons - Notification Banner', 'zaso' ),
			array(
				'description'   => __( 'Announcement bar with an optional sticky position and a remembered dismissal.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_notification_banner_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	/**
	 * Register the front-end assets.
	 *
	 * SiteOrigin only enqueues these on pages where the widget actually renders.
	 *
	 * @since 1.11.0
	 */
	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-notification-banner',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);

	}

	/**
	 * Build the LESS variables from the Design section.
	 *
	 * @since 1.11.0
	 *
	 * @param array $instance The widget instance values.
	 * @return array LESS variables.
	 */
	function get_less_variables( $instance ) {

		// Never return an empty array here. Every variable below is referenced by
		// styles/default.less, and an undefined LESS variable fails compilation,
		// which would leave the banner completely unstyled.
		$design = ( ! empty( $instance ) && ! empty( $instance['design'] ) ) ? $instance['design'] : array();

		return array(
			'background_color' => ! empty( $design['background_color'] ) ? $design['background_color'] : '#1e293b',
			'font_color'       => ! empty( $design['font_color'] ) ? $design['font_color'] : '#ffffff',
			'link_color'       => ! empty( $design['link_color'] ) ? $design['link_color'] : '#2563eb',
			'link_font_color'  => ! empty( $design['link_font_color'] ) ? $design['link_font_color'] : '#ffffff',
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-notification-banner', __FILE__, 'Zen_Addons_SiteOrigin_Notification_Banner_Widget' );

endif;
