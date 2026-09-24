<?php
/**
 * Deterministic state machine for the Livemesh rescue notice.
 *
 * Run: source app/.envrc && wp --path=app/public eval-file \
 *   app/public/wp-content/plugins/zen-addons-for-siteorigin-page-builder/scripts/test-livemesh-rescue.php
 *
 * Mutates one option, one transient and one scratch post, and restores all of
 * them, then asserts the restore succeeded. Self-heals by removing orphaned
 * postmeta from aborted previous runs, scoped strictly to this test's fixture.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

defined( 'ABSPATH' ) || exit;

/*
 * wp-cli is not an admin request, so is_admin() is false and Zen's admin
 * includes never ran. Load the file under test directly.
 */
if ( ! function_exists( 'zaso_livemesh_has_orphans' ) ) {
	require_once dirname( __DIR__ ) . '/core/livemesh-rescue.php';
}

$GLOBALS['zaso_pass'] = 0;
$GLOBALS['zaso_fail'] = 0;
$GLOBALS['zaso_skip'] = 0;

/**
 * Assert one condition.
 *
 * @param string $label Human-readable assertion name.
 * @param bool   $cond  Condition under test.
 * @return void
 */
function zaso_t( $label, $cond ) {
	if ( $cond ) {
		$GLOBALS['zaso_pass']++;
		echo "  PASS  {$label}\n";
	} else {
		$GLOBALS['zaso_fail']++;
		echo "  FAIL  {$label}\n";
	}
}

/**
 * Skip one assertion.
 *
 * @param string $label  Assertion name.
 * @param string $reason Why it is skipped.
 * @return void
 */
function zaso_skip( $label, $reason ) {
	$GLOBALS['zaso_skip']++;
	echo "  SKIP  {$label} ({$reason})\n";
}

// --- Back up everything we touch. ---
$zaso_backup_livemesh = get_option( 'zaso_livemesh_rescue', null );

// The orphan-scan transient is the site's own cached answer, and nearly every
// detection assertion below clears it. Keep its value and its expiry so the
// Restore section can put the entry back exactly as it was found, instead of
// leaving the site with no cached answer. get_transient() returns false for a
// missing or already expired entry, and both mean "nothing to restore".
$zaso_backup_transient         = get_transient( ZASO_LIVEMESH_TRANSIENT );
$zaso_backup_transient_timeout = (int) get_option( '_transient_timeout_' . ZASO_LIVEMESH_TRANSIENT, 0 );

// --- Self-healing: clean stale fixtures and orphaned meta from aborted previous runs. ---
global $wpdb;

// Delete any leftover fixture posts (shouldn't exist if cleanup was perfect).
// Covers all six fixture titles this file creates: the main live fixture, the
// synthetic revision used to prove revisions are excluded, the ghost fixture
// used to prove a deleted post's leftover meta is excluded, and the three
// storage-mode fixtures (layout block, prose, trashed). A leftover published
// layout-block or trashed-fixture post from an aborted run would otherwise be
// detected as a live orphan and fail every "not detected" assertion below.
// Match exact titles only (BINARY defeats the case-insensitive collation) and only
// the post types the fixtures are created as, so a real post can never match.
$stale_ids = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( 'page', 'post', 'revision' ) AND BINARY post_title IN ( %s, %s, %s, %s, %s, %s )",
		'ZASO livemesh fixture',
		'ZASO livemesh fixture revision',
		'ZASO livemesh ghost fixture',
		'ZASO livemesh layout-block fixture',
		'ZASO livemesh prose fixture',
		'ZASO livemesh trashed fixture'
	)
);
foreach ( $stale_ids as $post_id ) {
	wp_delete_post( (int) $post_id, true );
}

// Delete orphaned postmeta rows from incomplete previous runs (meta exists but post is gone).
// Scope strictly: only meta containing this test's fixture marker 'Fixture accordion'.
// Never match on LSOW_ alone — that would delete real user data or Task 4's pages.
$needle = '%' . $wpdb->esc_like( 'Fixture accordion' ) . '%';
$orphaned = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT pm.meta_id FROM {$wpdb->postmeta} pm
		LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
		WHERE p.ID IS NULL AND pm.meta_key = %s AND pm.meta_value LIKE %s",
		'panels_data',
		$needle
	)
);
foreach ( $orphaned as $meta_id ) {
	delete_metadata_by_mid( 'post', (int) $meta_id );
}

// A leftover fixture from an aborted run may have made the orphan scan cache "yes".
// Restoring that backup would bring back a notice with nothing behind it, so when
// stale fixtures were removed, drop the backup and let the site recompute the answer.
if ( ! empty( $stale_ids ) || ! empty( $orphaned ) ) {
	$zaso_backup_transient         = false;
	$zaso_backup_transient_timeout = 0;
}

// --- Identity: an administrator is required for current_user_can( 'manage_options' ),
// the FIRST gate zaso_livemesh_should_show() checks. Without this, every
// assertion below that calls should_show() (directly or via render()) would
// pass at that gate alone, without ever reaching the behaviour it is named
// for, including the notice's only coverage of the dismissal gate.
require_once ABSPATH . 'wp-admin/includes/screen.php';

$zaso_admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( empty( $zaso_admins ) ) {
	echo "No administrator on this site; cannot run.\n";
	exit( 1 );
}
wp_set_current_user( (int) $zaso_admins[0] );

// --- Scratch post carrying fake orphaned Livemesh panels_data. ---
// Hand-built fixture. NEVER copied from a real customer site.
$zaso_fixture_id = wp_insert_post(
	array(
		'post_title'  => 'ZASO livemesh fixture',
		'post_status' => 'draft',
		'post_type'   => 'page',
	)
);

$zaso_fixture_panels = array(
	'widgets' => array(
		array(
			'title'       => 'Fixture accordion',
			'panels_info' => array( 'class' => 'LSOW_Accordion_Widget', 'grid' => 0, 'cell' => 0 ),
		),
	),
);

// Detect whether foreign Livemesh data exists (Task 4 page or real user data).
// If foreign data is present, the "no orphans" assertions will fail correctly,
// so we skip them to avoid false negatives when Task 4 runs its setup.
$zaso_has_foreign_lsow = false;
// esc_like() is essential here too: an unescaped '%LSOW_%' also matches the
// LSOWX near-miss, which would falsely register as foreign Livemesh data.
$zaso_foreign_needle  = '%' . $wpdb->esc_like( 'LSOW_' ) . '%';
$zaso_foreign_exclude = '%' . $wpdb->esc_like( 'Fixture accordion' ) . '%';
$zaso_foreign_check   = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT meta_id FROM {$wpdb->postmeta} pm
		WHERE pm.meta_value LIKE %s AND pm.meta_value NOT LIKE %s
		LIMIT 1",
		$zaso_foreign_needle,
		$zaso_foreign_exclude
	)
);
if ( ! empty( $zaso_foreign_check ) ) {
	$zaso_has_foreign_lsow = true;
}

echo "=== Task 1: detection ===\n";

// No orphans yet: the fixture post exists but has no panels_data.
// Skip this assertion if foreign Livemesh data is present, since it would correctly fail.
delete_transient( ZASO_LIVEMESH_TRANSIENT );
if ( ! $zaso_has_foreign_lsow ) {
	zaso_t( 'no orphans before fixture panels_data is written', false === zaso_livemesh_has_orphans() );
} else {
	zaso_skip( 'no orphans before fixture panels_data is written', 'foreign Livemesh data present' );
}

update_post_meta( $zaso_fixture_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );
zaso_t( 'orphans detected once LSOW_ data exists', true === zaso_livemesh_has_orphans() );

zaso_t( 'result is cached in a transient', false !== get_transient( ZASO_LIVEMESH_TRANSIENT ) );

// Guard the SQL wildcard bug: '_' is a single-char wildcard in LIKE, so an
// unescaped '%LSOW_%' would also match this near-miss. It must NOT be detected.
update_post_meta(
	$zaso_fixture_id,
	'panels_data',
	array( 'widgets' => array( array( 'panels_info' => array( 'class' => 'LSOWX_Not_Livemesh' ) ) ) )
);
delete_transient( ZASO_LIVEMESH_TRANSIENT );
// Skip if foreign Livemesh data is present, since it would correctly fail.
if ( ! $zaso_has_foreign_lsow ) {
	zaso_t( 'near-miss class LSOWX is NOT treated as Livemesh (underscore escaped)', false === zaso_livemesh_has_orphans() );
} else {
	zaso_skip( 'near-miss class LSOWX is NOT treated as Livemesh (underscore escaped)', 'foreign Livemesh data present' );
}

// Put the real fixture back for the remaining tasks.
update_post_meta( $zaso_fixture_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

zaso_t( 'livemesh reported inactive in this sandbox', false === zaso_livemesh_is_active() );

// --- Revisions must NOT trigger detection: this is the actual bug being fixed. ---
// SiteOrigin copies panels_data onto every revision (including autosaves), so
// a site that removed every live Livemesh widget could still carry the trail
// in its revision history forever under the old bare meta scan. Simulate a
// revision-only orphan by clearing the fixture's own live panels_data and
// attaching the same panels_data to a synthetic revision row instead.
delete_post_meta( $zaso_fixture_id, 'panels_data' );
$zaso_revision_id = wp_insert_post(
	array(
		'post_title'  => 'ZASO livemesh fixture revision',
		'post_type'   => 'revision',
		'post_status' => 'inherit',
		'post_parent' => $zaso_fixture_id,
	)
);
// update_post_meta() would silently redirect this write onto the PARENT
// post: wp_is_post_revision() checks inside update_post_meta()/
// add_post_meta()/delete_post_meta() exist specifically to "make sure meta
// is updated for the post, not for a revision" (wp-includes/post.php). Use
// the low-level meta API directly instead, exactly the way SiteOrigin's own
// inc/revisions.php does it, so the meta genuinely lands on the revision's
// own post ID and this fixture matches what a real site's revision table
// looks like.
add_metadata( 'post', $zaso_revision_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );
if ( ! $zaso_has_foreign_lsow ) {
	zaso_t( 'LSOW_ data stored on a revision is NOT treated as a live orphan', false === zaso_livemesh_has_orphans() );
} else {
	zaso_skip( 'LSOW_ data stored on a revision is NOT treated as a live orphan', 'foreign Livemesh data present' );
}
wp_delete_post( $zaso_revision_id, true );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// --- Orphaned meta whose post no longer exists must ALSO not trigger detection. ---
// Deliberate consequence of the fix: if the post is gone there is nothing left
// for the user to fix. Delete the post row directly (bypassing wp_delete_post,
// which would cascade-delete its postmeta too) so the meta survives as a true
// orphan with no matching post row, the way a hard-deleted page leaves things.
$zaso_ghost_id = wp_insert_post(
	array(
		'post_title'  => 'ZASO livemesh ghost fixture',
		'post_status' => 'draft',
		'post_type'   => 'page',
	)
);
update_post_meta( $zaso_ghost_id, 'panels_data', $zaso_fixture_panels );
$wpdb->delete( $wpdb->posts, array( 'ID' => (int) $zaso_ghost_id ), array( '%d' ) );
delete_transient( ZASO_LIVEMESH_TRANSIENT );
if ( ! $zaso_has_foreign_lsow ) {
	zaso_t( 'orphaned meta whose post no longer exists is NOT detected (nothing left to fix)', false === zaso_livemesh_has_orphans() );
} else {
	zaso_skip( 'orphaned meta whose post no longer exists is NOT detected (nothing left to fix)', 'foreign Livemesh data present' );
}
// No post row exists to cascade this; delete the true-orphan meta directly.
$wpdb->delete( $wpdb->postmeta, array( 'post_id' => (int) $zaso_ghost_id, 'meta_key' => 'panels_data' ) );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// --- Block editor storage mode MUST be detected. ---
// SiteOrigin has two separate stores. A layout built in the block editor lives
// entirely in the panelsData attribute of a siteorigin-panels/layout-block in
// post_content and writes NO panels_data postmeta, so a postmeta-only scan is
// blind to every block-editor site. This fixture carries no postmeta at all,
// which is exactly what makes it discriminating: it can only pass if the
// post_content branch runs.
//
// The layout is structurally complete (one row with one cell, and the widget
// placed in it) because SiteOrigin validates every layout block on save. A
// widget with no grid/cell and a layout with no grids makes that validation log
// three PHP warnings on every run, which would bury real warnings in the log.
$zaso_block_content = '<!-- wp:siteorigin-panels/layout-block {"panelsData":{"grids":[{"cells":1}],"grid_cells":[{"grid":0,"weight":1}],"widgets":[{"panels_info":{"class":"LSOW_Accordion_Widget","grid":0,"cell":0,"raw":false}}]}} -->' . "\n"
	. '<div class="so-panel widget"></div>' . "\n"
	. '<!-- /wp:siteorigin-panels/layout-block -->';
$zaso_block_id = wp_insert_post(
	array(
		'post_title'   => 'ZASO livemesh layout-block fixture',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => $zaso_block_content,
	)
);
zaso_t(
	'layout-block fixture genuinely has NO panels_data meta (so this test can only pass via post_content)',
	'' === (string) get_post_meta( $zaso_block_id, 'panels_data', true )
);
delete_transient( ZASO_LIVEMESH_TRANSIENT );
zaso_t( 'LSOW_ widget inside a block-editor layout block IS detected', true === zaso_livemesh_has_orphans() );
wp_delete_post( $zaso_block_id, true );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// --- Prose mentioning LSOW_ must NOT trigger the notice. ---
// The post_content branch requires the literal block name alongside the class
// prefix precisely so that an article about Livemesh cannot fire the notice.
$zaso_prose_id = wp_insert_post(
	array(
		'post_title'   => 'ZASO livemesh prose fixture',
		'post_status'  => 'publish',
		'post_type'    => 'post',
		'post_content' => 'A note about migrating away from LSOW_Accordion_Widget and friends.',
	)
);
delete_transient( ZASO_LIVEMESH_TRANSIENT );
if ( ! $zaso_has_foreign_lsow ) {
	zaso_t( 'prose containing LSOW_ but no layout block is NOT detected', false === zaso_livemesh_has_orphans() );
} else {
	zaso_skip( 'prose containing LSOW_ but no layout block is NOT detected', 'foreign Livemesh data present' );
}
wp_delete_post( $zaso_prose_id, true );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// --- Trashed posts must NOT trigger the notice. ---
// Same class of defect as the revision bug: the user already deleted this
// page, so there is nothing left for them to fix and the notice would be
// unactionable and permanent.
$zaso_trash_id = wp_insert_post(
	array(
		'post_title'  => 'ZASO livemesh trashed fixture',
		'post_status' => 'publish',
		'post_type'   => 'page',
	)
);
update_post_meta( $zaso_trash_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );
zaso_t( 'control: the same fixture IS detected while published', true === zaso_livemesh_has_orphans() );
wp_trash_post( $zaso_trash_id );
delete_transient( ZASO_LIVEMESH_TRANSIENT );
if ( ! $zaso_has_foreign_lsow ) {
	zaso_t( 'LSOW_ data on a TRASHED post is NOT treated as a live orphan', false === zaso_livemesh_has_orphans() );
} else {
	zaso_skip( 'LSOW_ data on a TRASHED post is NOT treated as a live orphan', 'foreign Livemesh data present' );
}
wp_delete_post( $zaso_trash_id, true );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// Put the real fixture's own panels_data back before Task 2 needs it.
update_post_meta( $zaso_fixture_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// --- Task 1 cleanup (not fixture deletion, since Task 2 needs it). ---
delete_transient( ZASO_LIVEMESH_TRANSIENT );
if ( null === $zaso_backup_livemesh ) {
	delete_option( 'zaso_livemesh_rescue' );
} else {
	update_option( 'zaso_livemesh_rescue', $zaso_backup_livemesh, false );
}

echo "\n=== Task 2: gating and yield chain ===\n";

$zaso_backup_review = get_option( 'zaso_review_prompt', null );
$zaso_backup_promo  = get_option( 'zaso_cross_promo', null );

// Silence both existing notices so they cannot mask our assertions.
update_option( 'zaso_review_prompt', array( 'since' => time(), 'state' => 'dismissed', 'later_until' => 0 ), false );
update_option( 'zaso_cross_promo', array( 'since' => time(), 'state' => 'dismissed', 'later_until' => 0 ), false );

update_post_meta( $zaso_fixture_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );
delete_option( ZASO_LIVEMESH_OPTION );

zaso_t( 'state defaults to empty', '' === zaso_livemesh_state()['state'] );

// No screen has been set yet in this wp-cli context (an administrator is
// current, so this genuinely exercises the screen gate rather than being
// masked by the capability gate).
zaso_t( 'does not show without an admin screen', false === zaso_livemesh_should_show() );

// Dismissal is respected. This needs a qualifying screen to actually reach
// the dismissal check rather than being satisfied earlier at the screen gate,
// so set one just for this assertion, then revert immediately: later
// assertions in this file (the render-output checks in Task 3) rely on "no
// qualifying screen" meaning what it says.
set_current_screen( 'plugins' );
update_option( ZASO_LIVEMESH_OPTION, array( 'state' => 'dismissed' ), false );
zaso_t( 'does not show once dismissed', false === zaso_livemesh_should_show() );
set_current_screen( 'dashboard' );

// Yield chain. NOTE: a runtime assertion here would be dead — under wp-cli there is
// no screen, so should_show() already returns false at the screen gate and every branch
// of such a test is satisfiable. Assert the wiring statically instead, and scope the
// search to the should_show() body so the function DEFINITION of has_orphans() is not
// mistaken for its call site. Behavioural proof lives in Task 4 Step 5's browser check.
$zaso_src = file_get_contents( dirname( __DIR__ ) . '/core/livemesh-rescue.php' );
$zaso_fn  = strstr( $zaso_src, 'function zaso_livemesh_should_show()' );
$zaso_fn  = ( false !== $zaso_fn ) ? substr( $zaso_fn, 0, strpos( $zaso_fn, "\n\t}" ) ) : '';

$zaso_p_orphans = strpos( $zaso_fn, 'zaso_livemesh_has_orphans()' );
$zaso_p_review  = strpos( $zaso_fn, 'zaso_review_prompt_should_show()' );
$zaso_p_promo   = strpos( $zaso_fn, 'zaso_cross_promo_should_show()' );

zaso_t( 'should_show() body was located', '' !== $zaso_fn );
zaso_t( 'yields to the review prompt', false !== $zaso_p_review );
zaso_t( 'yields to the cross-promo notice', false !== $zaso_p_promo );
zaso_t( 'both yields run AFTER the orphan gate, so the neighbouring clocks keep advancing',
	false !== $zaso_p_orphans && false !== $zaso_p_review && false !== $zaso_p_promo
	&& $zaso_p_review > $zaso_p_orphans && $zaso_p_promo > $zaso_p_orphans );

// Restore the two neighbouring notices byte-exact.
if ( null === $zaso_backup_review ) { delete_option( 'zaso_review_prompt' ); } else { update_option( 'zaso_review_prompt', $zaso_backup_review, false ); }
if ( null === $zaso_backup_promo )  { delete_option( 'zaso_cross_promo' );  } else { update_option( 'zaso_cross_promo', $zaso_backup_promo, false ); }

zaso_t( 'review prompt option restored byte-exact', get_option( 'zaso_review_prompt', null ) == $zaso_backup_review );
zaso_t( 'cross-promo option restored byte-exact', get_option( 'zaso_cross_promo', null ) == $zaso_backup_promo );

echo "\n=== Task 3: link and render ===\n";

$zaso_guide = zaso_livemesh_guide_url();
zaso_t( 'guide URL carries utm_source=zen-addons', false !== strpos( $zaso_guide, 'utm_source=zen-addons' ) );
zaso_t( 'guide URL carries utm_medium=plugin', false !== strpos( $zaso_guide, 'utm_medium=plugin' ) );
zaso_t( 'guide URL carries utm_campaign=livemesh-rescue', false !== strpos( $zaso_guide, 'utm_campaign=livemesh-rescue' ) );
zaso_t( 'guide URL does NOT reuse the cross-promo campaign', false === strpos( $zaso_guide, 'cross-promo' ) );
zaso_t( 'guide URL does NOT reuse the pro-upsell campaign', false === strpos( $zaso_guide, 'pro-upsell' ) );
zaso_t( 'action URL is nonce protected', false !== strpos( zaso_livemesh_action_url( 'dismiss' ), '_wpnonce' ) );

ob_start();
zaso_livemesh_render();
$zaso_out = ob_get_clean();
zaso_t( 'renders nothing when should_show is false', '' === trim( $zaso_out ) );

// render() still short-circuits here (no qualifying screen is set at this
// point in the run), so $zaso_out is always empty and checking it for an em
// dash would be vacuously true no matter what the notice actually says.
// Assert on the notice's own SOURCE instead, using the same function-body
// extraction technique as the yield-chain check above, so this can actually
// fail if a future edit reintroduces one.
$zaso_render_fn = strstr( $zaso_src, 'function zaso_livemesh_render()' );
$zaso_render_fn = ( false !== $zaso_render_fn ) ? substr( $zaso_render_fn, 0, strpos( $zaso_render_fn, "\n\t}" ) ) : '';
zaso_t( 'notice source was located', '' !== $zaso_render_fn );
zaso_t( 'notice markup contains no em dash', '' !== $zaso_render_fn && false === strpos( $zaso_render_fn, "\xe2\x80\x94" ) );

echo "\n=== Task 4: uninstall cleanup ===\n";

$zaso_uninstall = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
zaso_t( 'uninstall.php deletes the livemesh option', false !== strpos( $zaso_uninstall, 'zaso_livemesh_rescue' ) );
zaso_t( 'uninstall.php deletes the livemesh transient', false !== strpos( $zaso_uninstall, 'zaso_livemesh_orphans' ) );

// --- Restore. ---
// Explicitly delete all postmeta for the fixture to prevent orphaned rows.
$wpdb->delete( $wpdb->postmeta, array( 'post_id' => (int) $zaso_fixture_id ), array( '%d' ) );
// Then delete the post itself.
wp_delete_post( $zaso_fixture_id, true );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

// Put the site's cached orphan-scan answer back as it was before the run.
if ( false === $zaso_backup_transient ) {
	delete_transient( ZASO_LIVEMESH_TRANSIENT );
} else {
	// Remaining lifetime of the original entry. With no stored expiry (an
	// external object cache keeps none in the options table) fall back to the
	// plugin's own cache lifetime.
	$zaso_transient_ttl = ( $zaso_backup_transient_timeout > time() )
		? $zaso_backup_transient_timeout - time()
		: ZASO_LIVEMESH_CACHE_DAYS * DAY_IN_SECONDS;
	set_transient( ZASO_LIVEMESH_TRANSIENT, $zaso_backup_transient, $zaso_transient_ttl );

	// set_transient() recomputes the expiry from the current second, so write
	// the original expiry back to keep the entry identical to what was there.
	if ( $zaso_backup_transient_timeout > 0 && ! wp_using_ext_object_cache() ) {
		update_option( '_transient_timeout_' . ZASO_LIVEMESH_TRANSIENT, $zaso_backup_transient_timeout, false );
	}
}

if ( null === $zaso_backup_livemesh ) {
	delete_option( 'zaso_livemesh_rescue' );
} else {
	update_option( 'zaso_livemesh_rescue', $zaso_backup_livemesh, false );
}

zaso_t( 'livemesh option restored byte-exact', get_option( 'zaso_livemesh_rescue', null ) == $zaso_backup_livemesh );
zaso_t( 'fixture post removed', null === get_post( $zaso_fixture_id ) );

echo "\n=== Task 5: L3 invariant, Livemesh active means completely silent ===\n";

/*
 * Positive proof, deliberately last. Livemesh is not installed in this
 * sandbox, so this is the only way to exercise the "Livemesh is active"
 * branch: define its own marker constant ourselves. define() cannot be
 * undone within a PHP request, so once LSOW_PLUGIN_HELP_URL exists,
 * zaso_livemesh_is_active() reports true for the rest of this process and no
 * assertion above this point could be trusted afterward. Everything else in
 * this file, including all cleanup and restoration, MUST run before this.
 *
 * A qualifying screen is set so should_show() actually reaches the
 * is_active() gate rather than being satisfied earlier at the screen gate,
 * which would make the second assertion pass for the wrong reason.
 */
set_current_screen( 'plugins' );
define( 'LSOW_PLUGIN_HELP_URL', 'x' );
zaso_t( 'is_active() reports true once a Livemesh marker constant is defined', true === zaso_livemesh_is_active() );
zaso_t( 'should_show() is silent once Livemesh is active (L3 invariant)', false === zaso_livemesh_should_show() );

echo "\n{$GLOBALS['zaso_pass']} passed, {$GLOBALS['zaso_fail']} failed, {$GLOBALS['zaso_skip']} skipped (of " . ( $GLOBALS['zaso_pass'] + $GLOBALS['zaso_fail'] + $GLOBALS['zaso_skip'] ) . ")\n";
