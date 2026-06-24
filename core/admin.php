<?php
/**
 * Zen Addons admin management page.
 *
 * Provides a single "Zen Addons" screen to activate or deactivate the ZASO
 * widgets. It reuses the SiteOrigin Widgets Bundle's own activation API
 * (activate_widget / deactivate_widget) so state stays consistent with the
 * Plugins > SiteOrigin Widgets screen. It stores no options of its own.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ZASO_Admin' ) ) :

	/**
	 * Class ZASO_Admin
	 *
	 * Registers and renders the Zen Addons widget management page.
	 *
	 * @since 1.2.0
	 */
	class ZASO_Admin {

		/**
		 * Admin page slug.
		 *
		 * @since 1.2.0
		 * @var string
		 */
		const MENU_SLUG = 'zen-addons';

		/**
		 * Nonce action for the save form.
		 *
		 * @since 1.2.0
		 * @var string
		 */
		const NONCE_ACTION = 'zaso_save_widgets';

		/**
		 * Hook up the admin page and its form handler.
		 *
		 * @since 1.2.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_post_zaso_save_widgets', array( $this, 'handle_save' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * The widget catalog, grouped by category.
		 *
		 * Each widget id is the folder basename under core/basic/, which is also
		 * the key SiteOrigin uses in the siteorigin_widgets_active option.
		 *
		 * @since  1.2.0
		 * @return array Categories, each with a label and an array of widget ids.
		 */
		public function get_categories() {
			return array(
				'layout'       => array(
					'label'   => esc_html__( 'Layout & Content', 'zaso' ),
					'widgets' => array( 'zaso-spacer-widgets', 'zaso-alert-box-widgets', 'zaso-widgetized-widgets' ),
				),
				'interactive'  => array(
					'label'   => esc_html__( 'Interactive', 'zaso' ),
					'widgets' => array( 'zaso-simple-accordion-widgets', 'zaso-basic-tabs-widgets' ),
				),
				'media'        => array(
					'label'   => esc_html__( 'Media', 'zaso' ),
					'widgets' => array( 'zaso-video-widgets', 'zaso-youtube-lightbox-widgets', 'zaso-vimeo-lightbox-widgets' ),
				),
				'business'     => array(
					'label'   => esc_html__( 'Business & Marketing', 'zaso' ),
					'widgets' => array( 'zaso-info-box-widgets', 'zaso-hover-card-widgets', 'zaso-icon-widgets', 'zaso-image-icon-group-widgets' ),
				),
				'engagement'   => array(
					'label'   => esc_html__( 'Conversion & Engagement', 'zaso' ),
					'widgets' => array( 'zaso-cta-banner-widgets', 'zaso-counter-widgets', 'zaso-countdown-widgets', 'zaso-before-after-widgets' ),
				),
				'people'       => array(
					'label'   => esc_html__( 'People & Social Proof', 'zaso' ),
					'widgets' => array( 'zaso-team-member-widgets', 'zaso-testimonial-slider-widgets' ),
				),
				'community'    => array(
					'label'   => esc_html__( 'Community (bbPress)', 'zaso' ),
					'widgets' => array( 'zaso-bbpress-forum-index-widgets', 'zaso-bbpress-topic-index-widgets', 'zaso-bbpress-login-widgets', 'zaso-bbpress-registration-widgets', 'zaso-bbpress-lost-password-widgets' ),
				),
				'integrations' => array(
					'label'   => esc_html__( 'Integrations', 'zaso' ),
					'widgets' => array( 'zaso-contact-form-7-widgets' ),
				),
			);
		}

		/**
		 * Flat whitelist of every known ZASO widget id.
		 *
		 * @since  1.2.0
		 * @return array Widget ids.
		 */
		public function get_widget_ids() {
			$ids = array();
			foreach ( $this->get_categories() as $category ) {
				$ids = array_merge( $ids, $category['widgets'] );
			}

			return $ids;
		}

		/**
		 * Read a widget's name and description from its header.
		 *
		 * @since  1.2.0
		 *
		 * @param  string $id Widget id (folder basename).
		 * @return array|false Header data, or false if the file is missing.
		 */
		public function get_widget_meta( $id ) {
			$file = ZASO_WIDGET_BASIC_PATH . $id . '/' . $id . '.php';
			if ( ! file_exists( $file ) ) {
				return false;
			}

			return get_file_data( $file, array( 'name' => 'Widget Name', 'desc' => 'Description' ), 'siteorigin-widget' );
		}

		/**
		 * Get the SiteOrigin Widgets Bundle singleton, if available.
		 *
		 * @since  1.2.0
		 * @return SiteOrigin_Widgets_Bundle|null
		 */
		public function bundle() {
			return class_exists( 'SiteOrigin_Widgets_Bundle' ) ? SiteOrigin_Widgets_Bundle::single() : null;
		}

		/**
		 * Map of widget id => active (bool), from SiteOrigin.
		 *
		 * @since  1.2.0
		 * @return array
		 */
		public function get_active_map() {
			$bundle = $this->bundle();
			if ( ! $bundle ) {
				return array();
			}

			$active = $bundle->get_active_widgets();

			return is_array( $active ) ? $active : array();
		}

		/**
		 * Register the top-level Zen Addons admin menu.
		 *
		 * @since 1.2.0
		 */
		public function register_menu() {
			add_menu_page(
				esc_html__( 'Zen Addons', 'zaso' ),
				esc_html__( 'Zen Addons', 'zaso' ),
				'manage_options',
				self::MENU_SLUG,
				array( $this, 'render_page' ),
				'dashicons-layout',
				58
			);
		}

		/**
		 * Enqueue the admin page stylesheet on our page only.
		 *
		 * @since 1.2.0
		 *
		 * @param string $hook Current admin page hook suffix.
		 */
		public function enqueue_assets( $hook ) {
			if ( 'toplevel_page_' . self::MENU_SLUG !== $hook ) {
				return;
			}

			wp_enqueue_style( 'zaso-admin', ZASO_BASE_DIR . 'assets/css/admin.css', array(), ZASO_VERSION );
		}

		/**
		 * Handle the widget activate/deactivate form submission.
		 *
		 * @since 1.2.0
		 */
		public function handle_save() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to perform this action.', 'zaso' ) );
			}

			check_admin_referer( self::NONCE_ACTION );

			$bundle = $this->bundle();
			if ( $bundle ) {
				$all  = $this->get_widget_ids();
				$bulk = isset( $_POST['bulk'] ) ? sanitize_key( wp_unslash( $_POST['bulk'] ) ) : '';

				if ( 'enable_all' === $bulk ) {
					$enabled = $all;
				} elseif ( 'disable_all' === $bulk ) {
					$enabled = array();
				} else {
					$posted  = isset( $_POST['zaso_widgets'] ) ? (array) wp_unslash( $_POST['zaso_widgets'] ) : array();
					$posted  = array_map( 'sanitize_key', $posted );
					$enabled = array_values( array_intersect( $all, $posted ) ); // Whitelist to known widgets.
				}

				foreach ( $all as $id ) {
					if ( in_array( $id, $enabled, true ) ) {
						$bundle->activate_widget( $id, false );
					} else {
						$bundle->deactivate_widget( $id );
					}
				}
			}

			wp_safe_redirect( add_query_arg( array( 'page' => self::MENU_SLUG, 'updated' => 1 ), admin_url( 'admin.php' ) ) );
			exit;
		}

		/**
		 * Render the management page.
		 *
		 * @since 1.2.0
		 */
		public function render_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			include ZASO_CORE_PATH . 'admin/views/manage-page.php';
		}
	}

	new ZASO_Admin();

endif; // class_exists check.
