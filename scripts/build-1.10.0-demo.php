<?php
/**
 * Build a demo page for Zen Addons 1.10.0 (Section Divider + Icon List).
 *
 * Run: wp eval-file scripts/build-1.10.0-demo.php --path=app/public
 *
 * Creates (or refreshes) a single published page that showcases both new
 * widgets via SiteOrigin panels_data. Safe to re-run (updates the same page
 * by slug).
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading_style = 'margin:48px 0 14px;font-weight:600;color:#475569;font-size:1rem;';
$sub_style     = 'margin:0 0 8px;color:#94a3b8;font-size:0.85rem;';

// --- Widget instances -------------------------------------------------------

$divider_waves = array(
	'style'           => 'waves',
	'color'           => '#4f46e5',
	'height'          => '110px',
	'width'           => '100%',
	'flip_horizontal' => false,
	'flip_vertical'   => false,
	'extra_id'        => '',
	'extra_class'     => '',
);

$divider_tilt = array(
	'style'           => 'tilt',
	'color'           => '#0ea5e9',
	'height'          => '90px',
	'width'           => '100%',
	'flip_horizontal' => true,
	'flip_vertical'   => false,
	'extra_id'        => '',
	'extra_class'     => '',
);

$divider_triangle = array(
	'style'           => 'triangle',
	'color'           => '#10b981',
	'height'          => '80px',
	'width'           => '100%',
	'flip_horizontal' => false,
	'flip_vertical'   => false,
	'extra_id'        => '',
	'extra_class'     => '',
);

$icon_list_vertical = array(
	'items'        => array(
		array( 'text' => 'Plugin Check clean and built to standards', 'icon' => 'fontawesome-check', 'link' => '' ),
		array( 'text' => 'Fully accessible, WCAG 2.1 AA across the pack', 'icon' => 'fontawesome-check', 'link' => '' ),
		array( 'text' => 'No external CDN, assets load only when used', 'icon' => 'fontawesome-check', 'link' => '' ),
		array( 'text' => '36 widgets and growing on a regular schedule', 'icon' => 'fontawesome-check', 'link' => '' ),
	),
	'default_icon' => 'fontawesome-check',
	'layout'       => 'vertical',
	'design'       => array(
		'icon_color' => '#10b981',
		'icon_size'  => '1.2rem',
		'text_color' => '#1f2937',
		'text_size'  => '1.05rem',
		'gap'        => '0.85rem',
	),
	'extra_id'     => '',
	'extra_class'  => '',
);

$icon_list_horizontal = array(
	'items'        => array(
		array( 'text' => 'Fast', 'icon' => 'fontawesome-bolt', 'link' => '' ),
		array( 'text' => 'Secure', 'icon' => 'fontawesome-shield', 'link' => '' ),
		array( 'text' => 'Loved', 'icon' => 'fontawesome-heart', 'link' => '' ),
		array( 'text' => 'Rated', 'icon' => 'fontawesome-star', 'link' => '' ),
	),
	'default_icon' => 'fontawesome-circle',
	'layout'       => 'horizontal',
	'design'       => array(
		'icon_color' => '#4f46e5',
		'icon_size'  => '1.3rem',
		'text_color' => '#334155',
		'text_size'  => '1.05rem',
		'gap'        => '2rem',
	),
	'extra_id'     => '',
	'extra_class'  => '',
);

// --- Assemble panels_data (single-column stack) -----------------------------

$widgets = array();
$row     = 0;
$id      = 0;

/**
 * Push a Custom HTML heading block.
 */
$push_html = function ( $html ) use ( &$widgets, &$id, $row ) {
	$widgets[] = array(
		'title'       => '',
		'content'     => $html,
		'panels_info' => array( 'class' => 'WP_Widget_Custom_HTML', 'raw' => false, 'grid' => $row, 'cell' => 0, 'id' => $id ),
	);
	$id++;
};

/**
 * Push a ZASO widget block.
 */
$push_widget = function ( $class, $data ) use ( &$widgets, &$id, $row ) {
	$data['panels_info'] = array( 'class' => $class, 'raw' => false, 'grid' => $row, 'cell' => 0, 'id' => $id );
	$widgets[]           = $data;
	$id++;
};

$push_html( '<h2 style="margin:0 0 6px;color:#0f172a;">Zen Addons 1.10.0</h2><p style="' . $sub_style . '">New widgets: Section Divider and Icon List</p>' );

$push_html( '<p style="' . $heading_style . '">Section Divider &mdash; Waves</p>' );
$push_widget( 'Zen_Addons_SiteOrigin_Section_Divider_Widget', $divider_waves );

$push_html( '<p style="' . $heading_style . '">Section Divider &mdash; Tilt (flipped)</p>' );
$push_widget( 'Zen_Addons_SiteOrigin_Section_Divider_Widget', $divider_tilt );

$push_html( '<p style="' . $heading_style . '">Section Divider &mdash; Triangle</p>' );
$push_widget( 'Zen_Addons_SiteOrigin_Section_Divider_Widget', $divider_triangle );

$push_html( '<p style="' . $heading_style . '">Icon List &mdash; Vertical</p>' );
$push_widget( 'Zen_Addons_SiteOrigin_Icon_List_Widget', $icon_list_vertical );

$push_html( '<p style="' . $heading_style . '">Icon List &mdash; Horizontal</p>' );
$push_widget( 'Zen_Addons_SiteOrigin_Icon_List_Widget', $icon_list_horizontal );

$panels_data = array(
	'widgets'    => $widgets,
	'grids'      => array( array( 'cells' => 1, 'style' => array() ) ),
	'grid_cells' => array( array( 'grid' => 0, 'weight' => 1 ) ),
);

// --- Create or update the demo page -----------------------------------------

$slug     = 'zen-1-10-0-demo';
$existing = get_page_by_path( $slug );
$postarr  = array(
	'post_title'   => 'Zen Addons 1.10.0 Demo',
	'post_name'    => $slug,
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_content' => '', // SiteOrigin rebuilds from panels_data.
);

if ( $existing ) {
	$postarr['ID'] = $existing->ID;
	$page_id       = wp_update_post( $postarr );
	printf( "[OK] Updated demo page (ID %d)\n", $page_id );
} else {
	$page_id = wp_insert_post( $postarr );
	printf( "[OK] Created demo page (ID %d)\n", $page_id );
}

update_post_meta( $page_id, 'panels_data', $panels_data );

echo "URL: http://dopeplugins.local/?page_id=" . (int) $page_id . "\n";
echo "PRETTY: http://dopeplugins.local/" . $slug . "/\n";
