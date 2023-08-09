<?php
/**
 * Namespace DopeThemes
 *
 * Contains all the functionalities related to DopeThemes, including handling the logic for
 * displaying, dismissing, and re-enabling DopeThemes posts in the WordPress dashboard.
 *
 * @package DopeThemes
 */
namespace DopeThemes;

if ( ! defined( 'DOPETHEMES_DASHBOARD_LOADED' ) ) {

    define( 'DOPETHEMES_DASHBOARD_LOADED', true );

    /**
     * Class DopeThemes_Dashboard
     * Handles the logic for displaying, dismissing, and re-enabling DopeThemes posts in the WordPress dashboard.
	 *
     * @package DopeThemes
     */
    class DopeThemes_Dashboard {

		/**
		 * Constructor for the DopeThemes_Dashboard class.
		 *
		 * Initializes the class and hooks the necessary methods into WordPress actions:
		 * - 'print_dashboard_script' method to the 'admin_footer' action to print the dashboard script.
		 * - 'register_dismiss_action' method to the 'admin_init' action to register the dismiss action.
		 * - 'register_enable_action' method to the 'admin_init' action to register the enable action.
		 *
		 * @since 1.0.0
		 */
        public function __construct() {
            add_action( 'admin_footer', array( $this, 'print_dashboard_script' ) );
            add_action( 'admin_init', array( $this, 'register_dismiss_action' ) );
            add_action( 'admin_init', array( $this, 'register_enable_action' ) );
        }

        /**
         * Fetch and render posts from DopeThemes.
         *
         * @since 1.0.0
         *
         * @return array
         */
        public function get_dopethemes_posts() {
			// Make a request to the REST API.
			$response = wp_remote_get( 'https://www.dopethemes.com/wp-json/wp/v2/posts' );

			// Check for errors.
			if( is_wp_error( $response ) ) {
				return array();
			}

			// Parse the response.
			$posts = json_decode( wp_remote_retrieve_body( $response ), true );

			// Check if we have posts.
			if( empty( $posts ) ) {
				return array();
			}

			// Get the latest 3 posts.
			$posts = array_slice( $posts, 0, 3 );

			return $posts;
		}

        /**
        * Render posts for dashboard.
        *
        * @since 1.0.0
        *
        * @param array $posts Posts to be rendered.
        * @return void
        */
        public function render_posts( $posts ) {
			foreach ( $posts as $post ) {
				printf(
					'<li><a class="rsswidget" href="%s">%s</a></li>',
					esc_url( $post['link'] ),
					esc_html( $post['title']['rendered'] )
				);
			}
		}

        /**
         * Print a script in the WordPress dashboard that renders the DopeThemes posts
         * and listens for the dismiss event.
         *
         * @since 1.0.0
         *
         * @return void
         */
		public function print_dashboard_script() {
            $dismissed = get_option( 'dopethemes_dismissed', false );
            if ( $dismissed ) return;

            $posts = $this->get_dopethemes_posts();
            $ajax_url = esc_url_raw( admin_url( 'admin-ajax.php?action=dismiss_dopethemes&_wpnonce=' . wp_create_nonce( 'dismiss_dopethemes_nonce' ) ) );

			echo '<script>';
				echo 'document.addEventListener("DOMContentLoaded", function() {';
					echo '  var widget = document.querySelector("#dashboard_primary .inside .wordpress-news");';
					echo '  if (widget) {';
					echo '    var dopethemes = document.createElement("div");';
					echo '    dopethemes.className = "rss-widget";';
					echo '    var htmlContent = \'<ul>\';';

					foreach ( $posts as $post ) {
						$htmlContent = sprintf(
							'<li class="dopethemes_dashboard_news_item"><a href="%s" class="dashicons dashicons-no-alt" title="Dismiss all DopeThemes news" onclick="dismiss_dopethemes_news(event); return false;" style="float: right; box-shadow: none; margin-left: 5px; display: none;"></a><a class="rsswidget" href="%s">%s</a></li>',
							esc_url( admin_url() ),
							esc_url( $post['link'] ),
							esc_html( $post['title']['rendered'] )
						);
						echo '    htmlContent += \'' . $htmlContent . '\';';
					}

					echo '    htmlContent += \'</ul>\';';
					echo '    dopethemes.innerHTML = htmlContent;';
					echo '    widget.appendChild(dopethemes);';
					echo '  }';
				echo '});';

				echo 'function dismiss_dopethemes_news(event) {';
				echo '  event.preventDefault();';
				echo '  if (window.confirm("Are you sure you want to remove DopeThemes Tutorials forever?")) {';
				echo '    var item = event.target.parentElement;';
				echo '    item.style.display = "none";';
				echo '    fetch("' . $ajax_url . '", { method: "POST" });';
				echo '  }';
				echo '}';
			echo '</script>';
		}

        /**
		 * Register an AJAX action to dismiss DopeThemes posts.
         *
         * @since 1.0.0
         *
		 * @return void
		 */
		public function register_dismiss_action() {
			add_action( 'wp_ajax_dismiss_dopethemes', array( $this, 'dismiss_dopethemes_posts' ) );
		}

        /**
         * Update the option when the DopeThemes posts are dismissed.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function dismiss_dopethemes_posts() {
            // Check nonce for security
            check_ajax_referer( 'dismiss_dopethemes_nonce' );

            // Check user permissions
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to perform this action.' ) );
            }

            update_option( 'dopethemes_dismissed', true );
            wp_die();
		}

        /**
         * Register an AJAX action to enable DopeThemes posts.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function register_enable_action() {
            add_action( 'wp_ajax_enable_dopethemes', array( $this, 'enable_dopethemes_posts' ) );
        }

        /**
         * Update the option when the DopeThemes posts are enabled.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function enable_dopethemes_posts() {
			update_option( 'dopethemes_dismissed', false );
			wp_die(); // This is required to terminate immediately and return a proper response.
		}
    }

    $dopeThemesDashboard = new DopeThemes_Dashboard();
}
