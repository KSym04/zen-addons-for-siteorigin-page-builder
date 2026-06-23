<?php
/*
 * Plugin Name: Zen Addons for SiteOrigin Page Builder
 * Description: Zen Addons is a collection of helpful widget extensions for SiteOrigin Page Builder. It's simple, flexible, and useful.
 * Version: 1.0.18
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 * Plugin URI: https://www.dopethemes.com/downloads/zen-addons-siteorigin/
 * Copyright: DopeThemes
 * Text Domain: zaso
 * Domain Path: /lang
 * License: GPLv3
 * License URI: https://www.dopethemes.com/gplv3/
 */

/*
    Copyright DopeThemes

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1335, USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'zen_addons_siteorigin' ) ) :

class zen_addons_siteorigin {

	// vars
	var $version = '1.0.18';

	/**
	 * A dummy constructor to ensure Zen Addons for SiteOrigin is only initialized once
	 *
	 * @type  function
	 * @date  09/24/2017
	 * @since 1.0.0
	 */
	function __construct() {
		// Do nothing here.
	}

	/**
	 * The real constructor to initialize Zen Addons for SiteOrigin
	 *
	 * @type  function
	 * @date  09/24/2017
	 * @since 1.0.0
	 */
	function initialize() {
		// Vars.
		$this->settings = array(
			'name'     => esc_html__( 'Zen Addons for SiteOrigin', 'zaso' ),
			'version'  => $this->version,
			'file'     => __FILE__,
			'basename' => plugin_basename( __FILE__ ),
			'path'     => plugin_dir_path( __FILE__ ),
			'dir'      => plugin_dir_url( __FILE__ )
		);

		// Defines.
		define( 'ZASO_VERSION',           $this->version );
		define( 'ZASO_BASE_DIR',          $this->settings['dir'] );
		define( 'ZASO_CORE_DIR',          $this->settings['dir'] . 'core/' );
		define( 'ZASO_LIBRARY_DIR',       $this->settings['dir'] . 'core/lib/' );
		define( 'ZASO_WIDGET_BASIC_DIR',  $this->settings['dir'] . 'core/basic/' );
		define( 'ZASO_BASE_PATH',         $this->settings['path'] );
		define( 'ZASO_CORE_PATH',         $this->settings['path'] . 'core/' );
		define( 'ZASO_LIBRARY_PATH',      $this->settings['path'] . 'core/lib/' );
		define( 'ZASO_WIDGET_BASIC_PATH', $this->settings['path'] . 'core/basic/' );

		// Set text domain.
		load_textdomain( 'zaso', ZASO_BASE_PATH . 'lang/zaso-' . get_locale() . '.mo' );

		// Includes core.
		include( 'core/helpers.php' );
		include( 'core/widgets.php' );
		include( 'core/shortcodes.php' );

		// Includes vendor.
		include( 'core/vendor/dopethemes-dashboard.php' );

		// Plugin action links.
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Added action plugin links
	 *
	 * @type  function
	 * @date  08/26/2020
	 * @since 1.0.7
	 * @param array $links
	 * @return array
	 */
	function plugin_action_links( $links ) {
		if ( isset( $links['edit'] ) ) {
			unset( $links['edit'] );
		}

		$links['learn-more'] = '<a href="https://www.dopethemes.com/downloads/zen-addons-siteorigin/" target="_blank" rel="noopener noreferrer">' . __( 'Learn More', 'zaso' ) . '</a>';

		return $links;
	}

}

/**
 * The main function responsible for returning the one true zen_addons_siteorigin Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * @type  function
 * @date  09/24/2017
 * @since 1.0.0
 * @return object
 */
function zen_addons_siteorigin() {
	global $zen_addons_siteorigin;

	if ( ! isset( $zen_addons_siteorigin ) ) {
		$zen_addons_siteorigin = new zen_addons_siteorigin();
		$zen_addons_siteorigin->initialize();
	}

	return $zen_addons_siteorigin;
}

// initialize
zen_addons_siteorigin();

endif; // class_exists check
