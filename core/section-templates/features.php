<?php
/**
 * Section template: Features / Services grid.
 *
 * A three-up services grid built from the Zen Addons Services Grid widget
 * (boxed layout, light "icon accent" skin). Returned to SiteOrigin Page
 * Builder as a prebuilt layout (name + description + screenshot + panels_data)
 * so it can be inserted into a page in one click.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'name'        => __( 'Features: Services Grid', 'zaso' ),
	'description' => __( 'A clean three-column grid of features with icons, titles, and short descriptions.', 'zaso' ),
	'screenshot'  => plugins_url(
		'assets/img/sections/features.png',
		dirname( __FILE__, 3 ) . '/zen-addons-for-siteorigin-page-builder.php'
	),
	'widgets'     => array(
		array(
			'services'     => array(
				array(
					'icon'         => 'fontawesome-bolt',
					'image'        => '',
					'title'        => __( 'Fast Setup', 'zaso' ),
					'description'  => __( 'Install, drop in a section, and publish in minutes. No build tools and no code required.', 'zaso' ),
					'link'         => '',
					'link_text'    => '',
					'link_new_tab' => false,
				),
				array(
					'icon'         => 'fontawesome-shield',
					'image'        => '',
					'title'        => __( 'Secure by Default', 'zaso' ),
					'description'  => __( 'Sanitized input and escaped output on every widget keep your pages safe out of the box.', 'zaso' ),
					'link'         => '',
					'link_text'    => '',
					'link_new_tab' => false,
				),
				array(
					'icon'         => 'fontawesome-life-ring',
					'image'        => '',
					'title'        => __( 'Expert Support', 'zaso' ),
					'description'  => __( 'Friendly help and clear documentation whenever you need a hand getting things just right.', 'zaso' ),
					'link'         => '',
					'link_text'    => '',
					'link_new_tab' => false,
				),
			),
			'columns'      => '3',
			'card_style'   => 'framed',
			'alignment'    => 'center',
			'layout'       => 'boxed',
			'design'       => array(
				'icon_color'         => '#ffffff',
				'icon_size'          => '2.5rem',
				'icon_bg'            => '#4f46e5',
				'title_color'        => '#0f172a',
				'description_color'  => '#475569',
				'link_color'         => '#4f46e5',
				'card_background'    => '#ffffff',
				'card_padding'       => array(
					'top'    => '28px',
					'right'  => '28px',
					'bottom' => '28px',
					'left'   => '28px',
				),
				'card_border_radius' => '12px',
				'gap'                => '24px',
			),
			'extra_id'     => '',
			'extra_class'  => '',
			'panels_info'  => array(
				'class' => 'Zen_Addons_SiteOrigin_Services_Grid_Widget',
				'raw'   => false,
				'grid'  => 0,
				'cell'  => 0,
				'id'    => 0,
			),
		),
	),
	'grids'       => array(
		array( 'cells' => 1, 'style' => array() ),
	),
	'grid_cells'  => array(
		array( 'grid' => 0, 'weight' => 1 ),
	),
);
