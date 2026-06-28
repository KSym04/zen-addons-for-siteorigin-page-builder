<?php
/**
 * Onboarding dependency notice.
 *
 * Zen Addons widgets are built on the SiteOrigin Widgets Bundle framework and are
 * placed into layouts by Page Builder by SiteOrigin. New users often install only
 * one of the three plugins and wonder why nothing works. This shows a friendly,
 * actionable notice with one-click install/activate links for whatever is missing.
 *
 * BACKWARD-COMPAT GUARANTEE: the notice renders ONLY when a dependency is missing
 * or inactive. A site that already has both SiteOrigin plugins active (every
 * existing Zen user) reaches the early `return` and sees nothing — zero change.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zaso_dependency_notice' ) ) :

	/**
	 * The SiteOrigin plugins Zen depends on, keyed by wordpress.org slug.
	 *
	 * @return array<string,array{name:string,file:string,why:string,required:bool}>
	 */
	function zaso_dependencies() {
		return array(
			'so-widgets-bundle' => array(
				'name'     => __( 'SiteOrigin Widgets Bundle', 'zaso' ),
				'file'     => 'so-widgets-bundle/so-widgets-bundle.php',
				'why'      => __( 'provides the framework every Zen widget is built on', 'zaso' ),
				'required' => true,
			),
			'siteorigin-panels' => array(
				'name'     => __( 'Page Builder by SiteOrigin', 'zaso' ),
				'file'     => 'siteorigin-panels/siteorigin-panels.php',
				'why'      => __( 'places Zen widgets into your page layouts', 'zaso' ),
				'required' => false,
			),
		);
	}

	/**
	 * Only show on screens where the guidance is relevant: the Plugins list and
	 * Zen Addons own admin pages. Avoids nagging across the whole dashboard.
	 *
	 * @return bool
	 */
	function zaso_dependency_notice_screen() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}
		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}
		if ( 'plugins' === $screen->id ) {
			return true;
		}
		// Any Zen Addons admin page (toplevel + submenus carry the slug).
		return ( false !== strpos( $screen->id, 'zen-addons' ) );
	}

	/**
	 * Build the action link (install or activate) for one missing dependency.
	 *
	 * @param string $slug Plugin slug.
	 * @param array  $dep  Dependency definition.
	 * @return string HTML anchor, or '' when the user lacks the capability.
	 */
	function zaso_dependency_action_link( $slug, $dep ) {
		$installed = array_key_exists( $dep['file'], get_plugins() );

		if ( $installed ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return '';
			}
			$url = wp_nonce_url(
				self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $dep['file'] ) ),
				'activate-plugin_' . $dep['file']
			);
			$label = __( 'Activate', 'zaso' );
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return '';
			}
			$url = wp_nonce_url(
				self_admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $slug ) ),
				'install-plugin_' . $slug
			);
			$label = __( 'Install', 'zaso' );
		}

		return '<a href="' . esc_url( $url ) . '" class="button button-primary" style="margin-left:8px;">'
			. esc_html( $label ) . ' ' . esc_html( $dep['name'] ) . '</a>';
	}

	/**
	 * Render the onboarding notice when a SiteOrigin dependency is missing.
	 */
	function zaso_dependency_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || ! zaso_dependency_notice_screen() ) {
			return;
		}

		if ( ! function_exists( 'is_plugin_active' ) || ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$missing = array();
		foreach ( zaso_dependencies() as $slug => $dep ) {
			if ( ! is_plugin_active( $dep['file'] ) ) {
				$missing[ $slug ] = $dep;
			}
		}

		// Every fully set-up site (all existing Zen users) returns here: no notice.
		if ( empty( $missing ) ) {
			return;
		}

		$any_required = false;
		foreach ( $missing as $dep ) {
			if ( $dep['required'] ) {
				$any_required = true;
				break;
			}
		}

		$class = $any_required ? 'notice notice-warning' : 'notice notice-info';
		echo '<div class="' . esc_attr( $class ) . '">';
		echo '<p><strong>' . esc_html__( 'Finish setting up Zen Addons', 'zaso' ) . '</strong></p>';
		echo '<p>' . esc_html__( 'Zen Addons needs these free SiteOrigin plugins to work:', 'zaso' ) . '</p>';
		echo '<ul style="list-style:disc;margin-left:20px;">';
		foreach ( $missing as $slug => $dep ) {
			$tag = $dep['required']
				? esc_html__( 'required', 'zaso' )
				: esc_html__( 'recommended', 'zaso' );
			echo '<li style="margin-bottom:8px;">';
			echo '<strong>' . esc_html( $dep['name'] ) . '</strong> (' . esc_html( $tag ) . ') &mdash; '
				. esc_html( $dep['why'] );
			echo wp_kses_post( zaso_dependency_action_link( $slug, $dep ) );
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	add_action( 'admin_notices', 'zaso_dependency_notice' );

endif;
