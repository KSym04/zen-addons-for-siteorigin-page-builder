<?php
/**
 * Widgets - Basic
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Create widgets group tab
 *
 * @return array
 */
function zen_addons_siteorigin_widget_tabs( $tabs ) {

  // create tab group
  $tabs[] = array(
   'title' => __( 'ZASO Widgets', 'zaso' ),
   'filter' => array(
       'groups' => array( 'zaso-plugin-widgets' )
   )
  );

  return $tabs;
}
add_filter( 'siteorigin_panels_widget_dialog_tabs', 'zen_addons_siteorigin_widget_tabs', 20 );

/**
 * Add our basic widgets.
 *
 * @return array
 */
function zen_addons_siteorigin_widgets_collection_basic( $folders ) {

  // Get widgets folder.
  $folders[] = ZASO_WIDGET_BASIC_PATH;

  // Return folders list.
  return $folders;
}
add_filter( 'siteorigin_widgets_widget_folders', 'zen_addons_siteorigin_widgets_collection_basic' );