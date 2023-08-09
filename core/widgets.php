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
		'title'  => esc_html__( 'ZASO Widgets', 'zaso' ),
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
