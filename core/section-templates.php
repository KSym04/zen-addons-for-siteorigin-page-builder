<?php
/**
 * Zen Addons - Section Template Library (free base).
 *
 * Registers ready-to-insert SECTION templates into SiteOrigin Page Builder's
 * "Layouts" (prebuilt) browser via the `siteorigin_panels_prebuilt_layouts`
 * filter. Selecting one imports a fully designed section (built from Zen Addons
 * widgets using their Layout + Style options) into the page in one click.
 *
 * Each template lives in its own file under core/section-templates/<id>.php and
 * returns an array of: name, description, screenshot (url), and the SiteOrigin
 * panels_data keys (widgets / grids / grid_cells). The free plugin ships a
 * starter set; Zen Addons Pro appends the full library (license gated) through
 * the same filter.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.10.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ZASO_Section_Templates' ) ) :

	/**
	 * Registers the free starter section templates.
	 */
	class ZASO_Section_Templates {

		/**
		 * Prefix for every template id, so ours never collide with theme layouts.
		 *
		 * @var string
		 */
		const PREFIX = 'zaso-section-';

		public function __construct() {
			add_filter( 'siteorigin_panels_prebuilt_layouts', array( $this, 'register' ) );
		}

		/**
		 * Directory holding the free section definition files.
		 *
		 * @return string
		 */
		protected function dir() {
			return trailingslashit( dirname( __FILE__ ) ) . 'section-templates/';
		}

		/**
		 * Add the free starter sections to the prebuilt-layouts list.
		 *
		 * @param array $layouts Existing prebuilt layouts.
		 * @return array
		 */
		public function register( $layouts ) {
			foreach ( (array) glob( $this->dir() . '*.php' ) as $file ) {
				$section = include $file;
				if ( ! is_array( $section ) || empty( $section['name'] ) || empty( $section['widgets'] ) ) {
					continue;
				}
				$id = self::PREFIX . basename( $file, '.php' );
				$layouts[ $id ] = $section;
			}
			return $layouts;
		}
	}

	new ZASO_Section_Templates();

endif;
