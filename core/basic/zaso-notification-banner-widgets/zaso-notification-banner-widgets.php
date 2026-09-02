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
				'label'       => __( 'Message', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'The announcement itself. Keep it to one short line for a sticky bar.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'banner_link_text' => array(
				'type'  => 'text',
				'label' => __( 'Button Text', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'banner_link_url' => array(
				'type'        => 'link',
				'label'       => __( 'Button Link', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Leave the text or the link empty to show no button.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'banner_link_new_tab' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Open Button Link In A New Tab', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => false,
			),
			'banner_position' => array(
				'type'    => 'select',
				'label'   => __( 'Position', 'zen-addons-for-siteorigin-page-builder' ),
				'default' => 'inline',
				'options' => array(
					'inline' => __( 'Inline (in the page flow)', 'zen-addons-for-siteorigin-page-builder' ),
					'top'    => __( 'Stuck to the top of the screen', 'zen-addons-for-siteorigin-page-builder' ),
					'bottom' => __( 'Stuck to the bottom of the screen', 'zen-addons-for-siteorigin-page-builder' ),
				),
			),
			'banner_dismissible' => array(
				'type'        => 'checkbox',
				'label'       => __( 'Show A Dismiss Button', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => true,
				'description' => __( 'Always leave this on for a sticky banner, or visitors cannot get rid of it.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'banner_remember' => array(
				'type'        => 'select',
				'label'       => __( 'Remember The Dismissal', 'zen-addons-for-siteorigin-page-builder' ),
				'default'     => 'forever',
				'options'     => array(
					'none'    => __( 'Not at all (shows again on the next page)', 'zen-addons-for-siteorigin-page-builder' ),
					'session' => __( 'Until the browser is closed', 'zen-addons-for-siteorigin-page-builder' ),
					'forever' => __( 'Until the message is edited', 'zen-addons-for-siteorigin-page-builder' ),
				),
				'description' => __( 'Stored in the visitor\'s own browser, so no cookie notice is needed. Editing the message shows it again to everyone.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zen-addons-for-siteorigin-page-builder' ),
				'hide'   => true,
				'fields' => array(
					'background_color' => array(
						'type'    => 'color',
						'label'   => __( 'Background Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#1e293b',
					),
					'font_color' => array(
						'type'    => 'color',
						'label'   => __( 'Text Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#ffffff',
					),
					'link_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#2563eb',
					),
					'link_font_color' => array(
						'type'    => 'color',
						'label'   => __( 'Button Text Color', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => '#ffffff',
					),
					'align' => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'zen-addons-for-siteorigin-page-builder' ),
						'default' => 'center',
						'options' => array(
							'left'   => __( 'Left', 'zen-addons-for-siteorigin-page-builder' ),
							'center' => __( 'Center', 'zen-addons-for-siteorigin-page-builder' ),
						),
					),
				),
			),
			'extra_id' => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra ID.', 'zen-addons-for-siteorigin-page-builder' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zen-addons-for-siteorigin-page-builder' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zen-addons-for-siteorigin-page-builder' ),
			),
		);

		// Add filter.
		$zaso_notification_banner_fields = apply_filters( 'zaso_notification_banner_fields', $zaso_notification_banner_field_array );

		parent::__construct(
			'zen-addons-siteorigin-notification-banner',
			__( 'Zen Addons - Notification Banner', 'zen-addons-for-siteorigin-page-builder' ),
			array(
				'description'   => __( 'Announcement bar with an optional sticky position and a remembered dismissal.', 'zen-addons-for-siteorigin-page-builder' ),
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
