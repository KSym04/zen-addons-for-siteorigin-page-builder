<?php
/**
 * Livemesh rescue notice.
 *
 * livemesh-siteorigin-widgets was closed on WordPress.org on 2026-05-18 for an
 * unpatched stored-XSS vulnerability, and its author is unresponsive. Sites that
 * remove it are left with pages whose SiteOrigin widgets no longer render,
 * because Page Builder stores widget identity as a PHP class name that no longer
 * exists. This file detects that orphaned data and offers a migration guide.
 *
 * BACKWARD-COMPAT GUARANTEE: this file writes exactly one option and one
 * transient. It never touches panels_data, post content, widgets, or any
 * existing option. It stays completely dormant unless orphaned Livemesh data is
 * present AND Livemesh itself is inactive, so an ordinary Zen install loads a
 * handful of function definitions and nothing more.
 *
 * We deliberately copy NO Livemesh code. Only their public widget class names
 * are used, and only to recognise data they left behind.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zaso_livemesh_has_orphans' ) ) :

	/**
	 * Option name holding the notice state.
	 *
	 * @since 1.10.22
	 * @var string
	 */
	define( 'ZASO_LIVEMESH_OPTION', 'zaso_livemesh_rescue' );

	/**
	 * Transient caching the detection result.
	 *
	 * @since 1.10.22
	 * @var string
	 */
	define( 'ZASO_LIVEMESH_TRANSIENT', 'zaso_livemesh_orphans' );

	/**
	 * How long a detection result stays cached.
	 *
	 * A site with no Livemesh data pays one cheap COUNT per week and nothing
	 * else, which keeps the cost invisible on large installs.
	 *
	 * @since 1.10.22
	 * @var int
	 */
	define( 'ZASO_LIVEMESH_CACHE_DAYS', 7 );

	/**
	 * Where the migration guide lives.
	 *
	 * @since 1.10.22
	 * @var string
	 */
	define( 'ZASO_LIVEMESH_GUIDE_URL', 'https://www.dopethemes.com/livemesh-siteorigin-widgets-migration/' );

	/**
	 * Whether Livemesh is still active on this site.
	 *
	 * When it is, its widgets render normally and there is nothing to rescue,
	 * so we stay silent. Checking a class rather than the active-plugins list
	 * means we also stay silent for a copy loaded by unusual means.
	 *
	 * @since 1.10.22
	 *
	 * @return bool True when Livemesh appears to be running.
	 */
	function zaso_livemesh_is_active() {
		return class_exists( 'LSOW_Accordion_Widget' ) || defined( 'LSOW_PLUGIN_HELP_URL' );
	}

	/**
	 * Whether this site has orphaned Livemesh widget data.
	 *
	 * SiteOrigin Page Builder records widget identity as a PHP class name inside
	 * panels_data, so a single LIKE over that meta key is enough to tell whether
	 * any Livemesh widget was ever placed. We only need existence, not a count.
	 *
	 * Any database problem is treated as "no orphans" so a failure can never
	 * surface a notice or break an admin screen.
	 *
	 * @since 1.10.22
	 *
	 * @return bool True when at least one LSOW_ widget is stored.
	 */
	function zaso_livemesh_has_orphans() {
		$cached = get_transient( ZASO_LIVEMESH_TRANSIENT );

		if ( false !== $cached ) {
			return ( 'yes' === $cached );
		}

		global $wpdb;

		// esc_like() is essential here: in SQL LIKE the underscore is a
		// single-character wildcard, so a raw '%LSOW_%' would also match LSOWX
		// or LSOW9. Escaping it keeps the match to the literal class prefix.
		$needle = '%' . $wpdb->esc_like( 'LSOW_' ) . '%';

		$found = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s LIMIT 1",
				'panels_data',
				$needle
			)
		);

		$has = ( ! empty( $found ) );

		set_transient(
			ZASO_LIVEMESH_TRANSIENT,
			$has ? 'yes' : 'no',
			ZASO_LIVEMESH_CACHE_DAYS * DAY_IN_SECONDS
		);

		return $has;
	}

	/**
	 * Read the stored notice state.
	 *
	 * @since 1.10.22
	 *
	 * @return array{state:string} Stored state, defaulted.
	 */
	function zaso_livemesh_state() {
		$defaults = array( 'state' => '' );

		$state = get_option( ZASO_LIVEMESH_OPTION, array() );

		return is_array( $state ) ? array_merge( $defaults, $state ) : $defaults;
	}

	/**
	 * Which qualifying admin screen we are on, if any.
	 *
	 * @since 1.10.22
	 *
	 * @return string 'plugins_screen', 'zen_screen', or ''.
	 */
	function zaso_livemesh_screen_key() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return '';
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return '';
		}

		if ( 'plugins' === $screen->id ) {
			return 'plugins_screen';
		}

		if ( false !== strpos( $screen->id, 'zen-addons' ) ) {
			return 'zen_screen';
		}

		return '';
	}

	/**
	 * Whether the notice should render for this user on this screen.
	 *
	 * Gate order matters. The cheap identity gates run first, and the two yields
	 * run LAST so the review prompt and cross-promo clocks keep advancing
	 * independently of this notice.
	 *
	 * @since 1.10.22
	 *
	 * @return bool
	 */
	function zaso_livemesh_should_show() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( '' === zaso_livemesh_screen_key() ) {
			return false;
		}

		if ( zaso_livemesh_is_active() ) {
			return false;
		}

		if ( 'dismissed' === zaso_livemesh_state()['state'] ) {
			return false;
		}

		if ( ! zaso_livemesh_has_orphans() ) {
			return false;
		}

		// One DopeThemes notice at a time. These run last so the other clocks advance.
		if ( function_exists( 'zaso_review_prompt_should_show' ) && zaso_review_prompt_should_show() ) {
			return false;
		}

		if ( function_exists( 'zaso_cross_promo_should_show' ) && zaso_cross_promo_should_show() ) {
			return false;
		}

		return true;
	}

endif;
