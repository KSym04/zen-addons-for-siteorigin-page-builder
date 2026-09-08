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
	 * so we stay silent. This is the invariant protecting every site still
	 * running Livemesh: a false positive here is unacceptable.
	 *
	 * Three independent signals are ORed together. class_exists() and the
	 * LSOW_PLUGIN_HELP_URL constant both depend on Livemesh's code having
	 * loaded, which in turn depends on which individual widgets the site
	 * owner enabled. The active_plugins option is timing-independent: it is
	 * populated the moment the plugin is activated, regardless of which
	 * widget classes it ever defines.
	 *
	 * @since 1.10.22
	 *
	 * @return bool True when Livemesh appears to be running.
	 */
	function zaso_livemesh_is_active() {
		if ( class_exists( 'LSOW_Accordion_Widget' ) || defined( 'LSOW_PLUGIN_HELP_URL' ) ) {
			return true;
		}

		// Defensive (array) cast: if the option is missing or somehow not an
		// array, treat it as an empty list rather than fatal on foreach.
		$active_plugins = (array) get_option( 'active_plugins', array() );

		foreach ( $active_plugins as $plugin ) {
			if ( 0 === strpos( (string) $plugin, 'livemesh-' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether this site has orphaned Livemesh widget data on a real, current post.
	 *
	 * SiteOrigin Page Builder records widget identity as a PHP class name inside
	 * panels_data, so a LIKE over that meta key tells us a Livemesh widget was
	 * placed somewhere. But SiteOrigin also copies panels_data onto every post
	 * revision (including autosaves), so a bare meta scan keeps matching long
	 * after the last Livemesh widget was removed from every live page: the
	 * trail just moves into revision history and the notice would never clear.
	 * We join to the posts table and require a real, current post: post_type
	 * 'revision' excludes ordinary revisions and autosaves (autosaves ARE type
	 * 'revision'), and post_status 'inherit' is the belt-and-suspenders second
	 * check, since 'inherit' is the status used by revisions and attachments
	 * rather than real content. The notice now fires only when a live page or
	 * post actually contains an LSOW_ widget today.
	 *
	 * Both SiteOrigin storage modes are checked, because they are separate
	 * stores and a site can use either: the classic metabox writes a
	 * 'panels_data' postmeta row, while the block editor keeps the layout in
	 * the panelsData attribute of a 'siteorigin-panels/layout-block' inside
	 * post_content and writes no postmeta at all.
	 *
	 * A side effect is deliberate: postmeta left behind by a deleted post can
	 * never match, because the INNER JOIN requires the post row to still
	 * exist. That is correct, since there is nothing left for the user to fix.
	 * For the same reason trashed and auto-draft posts are excluded, alongside
	 * revisions, which SiteOrigin also writes panels_data to.
	 *
	 * Any database problem is treated as "no orphans" so a failure can never
	 * surface a notice or break an admin screen.
	 *
	 * @since 1.10.22
	 *
	 * @return bool True when at least one LSOW_ widget is stored on a post the
	 *              user can still act on, in either storage mode.
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

		// Statuses that cannot represent content the user still needs to fix.
		// 'inherit' and the revision post_type both exclude revisions, which
		// SiteOrigin writes panels_data to; 'trash' and 'auto-draft' exclude
		// content the user has already deleted or never started. These are
		// passed as bound parameters rather than interpolated into the SQL, so
		// nothing but a placeholder ever reaches the query string.
		$status_inherit = 'inherit';
		$status_trash   = 'trash';
		$status_auto    = 'auto-draft';

		// Storage mode 1, the classic Page Builder metabox: the layout lives in
		// the 'panels_data' postmeta row.
		$found = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT pm.meta_id FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = %s
				AND pm.meta_value LIKE %s
				AND p.post_type != 'revision'
				AND p.post_status != %s
				AND p.post_status != %s
				AND p.post_status != %s
				LIMIT 1",
				'panels_data',
				$needle,
				$status_inherit,
				$status_trash,
				$status_auto
			)
		);

		// Storage mode 2, the block editor: a Page Builder layout inside a
		// 'siteorigin-panels/layout-block' keeps its widgets in the block's
		// panelsData attribute in post_content and writes NO postmeta at all,
		// so mode 1 alone is blind to every block-editor site. Both conditions
		// are required together: the bare LSOW_ prefix could appear in ordinary
		// prose, but not alongside the literal block name.
		if ( empty( $found ) ) {
			$found = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT p.ID FROM {$wpdb->posts} p
					WHERE p.post_content LIKE %s
					AND p.post_content LIKE %s
					AND p.post_type != 'revision'
					AND p.post_status != %s
					AND p.post_status != %s
					AND p.post_status != %s
					LIMIT 1",
					'%' . $wpdb->esc_like( 'siteorigin-panels/layout-block' ) . '%',
					$needle,
					$status_inherit,
					$status_trash,
					$status_auto
				)
			);
		}

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

	/**
	 * Migration guide URL, tagged so the click can be measured in the server log.
	 *
	 * The campaign is deliberately distinct from pro-upsell and cross-promo so
	 * the three funnels never merge in a log read.
	 *
	 * @since 1.10.22
	 *
	 * @return string
	 */
	function zaso_livemesh_guide_url() {
		return add_query_arg(
			array(
				'utm_source'   => 'zen-addons',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'livemesh-rescue',
				'utm_content'  => zaso_livemesh_screen_key(),
			),
			ZASO_LIVEMESH_GUIDE_URL
		);
	}

	/**
	 * Build a nonce-protected, self-returning dismiss URL.
	 *
	 * @since 1.10.22
	 *
	 * @param string $action Action key, currently only 'dismiss'.
	 * @return string
	 */
	function zaso_livemesh_action_url( $action ) {
		return wp_nonce_url(
			add_query_arg( 'zaso_livemesh_action', rawurlencode( $action ) ),
			'zaso_livemesh'
		);
	}

	/**
	 * Handle the dismiss link.
	 *
	 * Runs on admin_init so the redirect happens before any output. Requires the
	 * capability and a valid nonce; an unrecognised action is ignored rather
	 * than falling through to a dismiss.
	 *
	 * @since 1.10.22
	 *
	 * @return void
	 */
	function zaso_livemesh_handle_action() {
		if ( ! isset( $_GET['zaso_livemesh_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'zaso_livemesh' );

		$action = sanitize_key( wp_unslash( $_GET['zaso_livemesh_action'] ) );

		if ( 'dismiss' !== $action ) {
			return;
		}

		$state          = zaso_livemesh_state();
		$state['state'] = 'dismissed';

		update_option( ZASO_LIVEMESH_OPTION, $state, false );

		wp_safe_redirect( remove_query_arg( array( 'zaso_livemesh_action', '_wpnonce' ) ) );
		exit;
	}
	add_action( 'admin_init', 'zaso_livemesh_handle_action' );

	/**
	 * Render the notice.
	 *
	 * @since 1.10.22
	 *
	 * @return void
	 */
	function zaso_livemesh_render() {
		if ( ! zaso_livemesh_should_show() ) {
			return;
		}
		?>
		<div class="notice notice-warning zaso-livemesh-notice">
			<p>
				<strong><?php esc_html_e( 'This site uses widgets from a discontinued plugin', 'zen-addons-for-siteorigin-page-builder' ); ?></strong>
				<?php esc_html_e( 'This site has page content built with widgets from Livemesh SiteOrigin Widgets. That plugin was removed from WordPress.org in May 2026 and no longer receives updates. If it is no longer active, pages using those widgets can render empty. Zen Addons covers many of the same widget types, and our guide shows which is which.', 'zen-addons-for-siteorigin-page-builder' ); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( zaso_livemesh_guide_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read the migration guide', 'zen-addons-for-siteorigin-page-builder' ); ?></a>
				<a class="button" href="<?php echo esc_url( zaso_livemesh_action_url( 'dismiss' ) ); ?>"><?php esc_html_e( 'Dismiss', 'zen-addons-for-siteorigin-page-builder' ); ?></a>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'zaso_livemesh_render' );

endif;
