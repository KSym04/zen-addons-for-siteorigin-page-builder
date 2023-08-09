<?php
/**
 * Uninstall Plugin: Zen Addons for SiteOrigin Page Builder
 *
 * This file contains procedures to uninstall the Zen Addons for SiteOrigin Page Builder plugin.
 * When the plugin is deleted from WordPress, this uninstallation script will run automatically.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.2
 * @date 09/24/2017
 */

// If this file is called directly, exit.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// If WP_UNINSTALL_PLUGIN is not defined, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die; // Exit if uninstall script is not called by WordPress.
}
