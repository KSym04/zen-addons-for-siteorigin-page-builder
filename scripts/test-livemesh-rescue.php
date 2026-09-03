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

// --- Back up everything we touch. ---
$zaso_backup_livemesh = get_option( 'zaso_livemesh_rescue', null );

// --- Self-healing: clean stale fixtures and orphaned meta from aborted previous runs. ---
global $wpdb;

// Delete any leftover fixture posts (shouldn't exist if cleanup was perfect).
$stale_ids = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s",
		'ZASO livemesh fixture'
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

echo "=== Task 1: detection ===\n";

// No orphans yet: the fixture post exists but has no panels_data.
delete_transient( ZASO_LIVEMESH_TRANSIENT );
zaso_t( 'no orphans before fixture panels_data is written', false === zaso_livemesh_has_orphans() );

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
zaso_t( 'near-miss class LSOWX is NOT treated as Livemesh (underscore escaped)', false === zaso_livemesh_has_orphans() );

// Put the real fixture back for the remaining tasks.
update_post_meta( $zaso_fixture_id, 'panels_data', $zaso_fixture_panels );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

zaso_t( 'livemesh reported inactive in this sandbox', false === zaso_livemesh_is_active() );

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

// No screen in a wp-cli context, so should_show must be false for that reason alone.
zaso_t( 'does not show without an admin screen', false === zaso_livemesh_should_show() );

// Dismissal is respected.
update_option( ZASO_LIVEMESH_OPTION, array( 'state' => 'dismissed' ), false );
zaso_t( 'does not show once dismissed', false === zaso_livemesh_should_show() );

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

// --- Restore. ---
// Explicitly delete all postmeta for the fixture to prevent orphaned rows.
$wpdb->delete( $wpdb->postmeta, array( 'post_id' => (int) $zaso_fixture_id ), array( '%d' ) );
// Then delete the post itself.
wp_delete_post( $zaso_fixture_id, true );
delete_transient( ZASO_LIVEMESH_TRANSIENT );

if ( null === $zaso_backup_livemesh ) {
	delete_option( 'zaso_livemesh_rescue' );
} else {
	update_option( 'zaso_livemesh_rescue', $zaso_backup_livemesh, false );
}

zaso_t( 'livemesh option restored byte-exact', get_option( 'zaso_livemesh_rescue', null ) == $zaso_backup_livemesh );
zaso_t( 'fixture post removed', null === get_post( $zaso_fixture_id ) );

echo "\n{$GLOBALS['zaso_pass']} passed, {$GLOBALS['zaso_fail']} failed (of " . ( $GLOBALS['zaso_pass'] + $GLOBALS['zaso_fail'] ) . ")\n";
