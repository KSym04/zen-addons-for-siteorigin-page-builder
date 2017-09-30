<?php
/*
Plugin Name: Zen Addons for SiteOrigin Page Builder
Description: Ultimate collection of functional, professional and intuitive widgets extension for SiteOrigin.
Version: 1.0.0
Text Domain: zaso
Domain Path: /lang
Author: DopeThemes
Copyright: DopeThemes
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://wordpress.dopethemes.com
Plugin URI: http://wordpress.dopethemes.com/zen-addons-siteorigin/
Domain Path: /lang
*/

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('zen_addons_siteorigin') ) :


class zen_addons_siteorigin {

	// vars
	var $version = '1.0.0';

	/*
	*  __construct
	*
	*  A dummy constructor to ensure Zen Addons for SiteOrigin is only initialized once
	*
	*  @type	function
	*  @date	09/24/17
	*  @since	1.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct() {

		/* Do nothing here */

	}

	/*
	*  initialize
	*
	*  The real constructor to initialize Zen Addons for SiteOrigin
	*
	*  @type	function
	*  @date	09/24/17
	*  @since	1.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function initialize() {

		// vars
		$this->settings = array(

			// info
			'name' 		=> __( 'Zen Addons for SiteOrigin', 'zaso' ),
			'version' 	=> $this->version,

			// path
			'file' 		=> __FILE__,
			'basename' 	=> plugin_basename( __FILE__ ),
			'path' 		=> plugin_dir_path( __FILE__ ),
			'dir' 		=> plugin_dir_url( __FILE__ )

		);

		// defines
		define( 'ZASO_VERSION', $this->version );

		define( 'ZASO_BASE_DIR', $this->settings['dir'] );
		define( 'ZASO_CORE_DIR', $this->settings['dir'] . 'core/' );
		define( 'ZASO_LIBRARY_DIR', $this->settings['dir'] . 'core/lib/' );
		define( 'ZASO_WIDGET_BASIC_DIR', $this->settings['dir'] . 'core/basic/' );

		define( 'ZASO_BASE_PATH', $this->settings['path'] );
		define( 'ZASO_CORE_PATH', $this->settings['path'] . 'core/' );
		define( 'ZASO_LIBRARY_PATH', $this->settings['path'] . 'core/lib/' );
		define( 'ZASO_WIDGET_BASIC_PATH', $this->settings['path'] . 'core/basic/' );

		// set text domain
		load_textdomain( 'zaso', ZASO_BASE_PATH . 'lang/zaso-' . get_locale() . '.mo' );

		// scripts and styles
		add_action( 'init',	array($this, 'register_styles') );
		add_action( 'init',	array($this, 'register_scripts') );

		// includes
		include( 'core/helpers.php' );
		include( 'core/widgets.php' );
		include( 'core/shortcodes.php' );

	}

	/*
	*  register_styles
	*
	*  @type	function
	*  @date	09/24/17
	*  @since	1.0.0
	*/

	function register_styles() {

		// register
		wp_register_style( 'zen-addons-base', ZASO_BASE_DIR . 'assets/css/main.min.css', array(), ZASO_VERSION );

		// init
		wp_enqueue_style( 'zen-addons-base' );

	}

	/*
	*  register_scripts
	*
	*  @type	function
	*  @date	09/24/17
	*  @since	1.0.0
	*/

	function register_scripts() {

		// register
		wp_register_script( 'zen-addons-base', ZASO_BASE_DIR . 'assets/js/main.min.js', array('jquery'), ZASO_VERSION );

		// init
		wp_enqueue_script( 'zen-addons-base' );

	}

}

/*
*  zen_addons_siteorigin
*
*  The main function responsible for returning the one true zen_addons_siteorigin Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php $zen_addons_siteorigin = zen_addons_siteorigin(); ?>
*
*  @type	function
*  @date	09/24/17
*  @since	1.0.0
*
*  @param	N/A
*  @return	(object)
*/

function zen_addons_siteorigin() {

	global $zen_addons_siteorigin;

	if( !isset($zen_addons_siteorigin) ) {

		$zen_addons_siteorigin = new zen_addons_siteorigin();

		$zen_addons_siteorigin->initialize();

	}

	return $zen_addons_siteorigin;

}

// initialize
zen_addons_siteorigin();


endif; // class_exists check