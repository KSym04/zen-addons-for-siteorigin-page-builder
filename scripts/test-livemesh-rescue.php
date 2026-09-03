<?php
/**
 * Deterministic state machine for the Livemesh rescue notice.
 *
 * Run: source app/.envrc && wp --path=app/public eval-file \
 *   app/public/wp-content/plugins/zen-addons-for-siteorigin-page-builder/scripts/test-livemesh-rescue.php
 *
 * Mutates one option, one transient and one scratch post, and restores all of
 * them, then asserts the restore succeeded. Nothing else on the site is touched.
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

// --- Restore. ---
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
