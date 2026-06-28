<?php
/**
 * Widgets - Basic
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */

// Ensure that the code is only run from within WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Create widgets group tab.
 *
 * @since 1.0.0
 *
 * @param array $tabs Existing tabs.
 * @return array Modified tabs including the new tab group.
 */
function zen_addons_siteorigin_widget_tabs( $tabs ) {
	// Create a new tab group for ZASO widgets.
	$tabs[] = array(
		'title'  => esc_html__( 'Zen Addons', 'zaso' ),
		'filter' => array(
			'groups' => array( 'zaso-plugin-widgets' )
		)
	);

	// Return the modified tabs array.
	return $tabs;
}
add_filter( 'siteorigin_panels_widget_dialog_tabs', 'zen_addons_siteorigin_widget_tabs', 20 );

/**
 * Add our basic widgets by including the folder where they are located.
 *
 * @since 1.0.0
 *
 * @param array $folders Existing widget folders.
 * @return array Modified folders including the path to the basic widgets.
 */
function zen_addons_siteorigin_widgets_collection_basic( $folders ) {
	// Get widgets folder defined by ZASO_WIDGET_BASIC_PATH.
	$folders[] = ZASO_WIDGET_BASIC_PATH;

	// Return the modified folders list.
	return $folders;
}
add_filter( 'siteorigin_widgets_widget_folders', 'zen_addons_siteorigin_widgets_collection_basic' );

/**
 * Make every ZASO widget active by default.
 *
 * SiteOrigin merges this default map under the saved siteorigin_widgets_active
 * option with wp_parse_args( $saved, $defaults ), so a saved value always wins.
 * A widget the user has deliberately toggled off therefore stays off, while a
 * newly shipped ZASO widget (absent from the saved option) defaults to active
 * instead of SiteOrigin's inactive default. Glob keeps this correct for future
 * widgets with no per-release maintenance.
 *
 * @since 1.10.0
 *
 * @param array $defaults Default active map ( widget folder slug => bool ).
 * @return array Map including every ZASO widget folder slug set to true.
 */
function zen_addons_siteorigin_default_active_widgets( $defaults ) {
	if ( ! is_array( $defaults ) ) {
		$defaults = array();
	}

	foreach ( (array) glob( ZASO_WIDGET_BASIC_PATH . '*/', GLOB_ONLYDIR ) as $dir ) {
		$defaults[ basename( $dir ) ] = true;
	}

	return $defaults;
}
add_filter( 'siteorigin_widgets_default_active', 'zen_addons_siteorigin_default_active_widgets' );
