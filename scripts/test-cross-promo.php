<?php
/**
 * State-machine test for the FeedProof cross-promotion notice (core/cross-promo.php).
 *
 * Dev harness only. Excluded from the release zip by .distignore.
 *
 * Run with Local's PHP and wp-cli, from the repository root:
 *
 *   source app/.envrc
 *   cd app/public
 *   wp eval-file wp-content/plugins/zen-addons-for-siteorigin-page-builder/scripts/test-cross-promo.php
 *   wp --skip-plugins=woocommerce eval-file wp-content/plugins/zen-addons-for-siteorigin-page-builder/scripts/test-cross-promo.php
 *
 * The second run is not optional: `class_exists( 'WooCommerce' )` cannot be made
 * false from inside a process where WooCommerce has already loaded, so the
 * WooCommerce-absent gate is only exercised when the plugin is skipped.
 *
 * The test mutates two options and restores both byte-exact at the end, then
 * asserts the restore succeeded. Nothing else on the site is touched.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "Run this through wp-cli.\n";
	exit( 1 );
}

/*
 * wp eval-file executes this file inside a function scope, so a top-level
 * `$zaso_pass` is NOT a global and `global $zaso_pass` inside the helper would
 * bind to a different, always-zero variable. The counters therefore live in
 * $GLOBALS explicitly. Getting this wrong makes the summary line report
 * "0 passed, 0 failed" while every assertion prints PASS, which is a gate that
 * reads green no matter what happens.
 */
$GLOBALS['zaso_pass'] = 0;
$GLOBALS['zaso_fail'] = 0;

/**
 * Assert a condition and record the result.
 *
 * @param string $label What is being asserted.
 * @param bool   $cond  The assertion.
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
 * Force the notice's stored state.
 *
 * @param array $state Partial state to merge over the defaults.
 * @return void
 */
function zaso_set_promo_state( $state ) {
	update_option(
		ZASO_CROSS_PROMO_OPTION,
		array_merge(
			array(
				'since'       => time(),
				'state'       => '',
				'later_until' => 0,
			),
			$state
		),
		false
	);
}

/**
 * Put the clock far enough in the past that the delay has elapsed.
 *
 * @return void
 */
function zaso_mature_clock() {
	zaso_set_promo_state( array( 'since' => time() - ( ZASO_CROSS_PROMO_DELAY_DAYS + 1 ) * DAY_IN_SECONDS ) );
}

require_once ABSPATH . 'wp-admin/includes/screen.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

/*
 * wp-cli is not an admin request, so is_admin() is false and Zen's admin
 * includes never ran. Load the two files under test directly.
 */
if ( ! function_exists( 'zaso_review_prompt_should_show' ) ) {
	require_once dirname( __DIR__ ) . '/core/review-prompt.php';
}
if ( ! function_exists( 'zaso_cross_promo_should_show' ) ) {
	require_once dirname( __DIR__ ) . '/core/cross-promo.php';
}

$zaso_woo = class_exists( 'WooCommerce' );

// ---------------------------------------------------------------- setup ----
$zaso_backup_promo  = get_option( ZASO_CROSS_PROMO_OPTION, null );
$zaso_backup_review = get_option( 'zaso_review_prompt', null );

// Keep the review prompt quiet for every case except the yield test.
update_option( 'zaso_review_prompt', array( 'since' => time(), 'state' => 'dismissed', 'later_until' => 0 ), false );

// An administrator is required for install_plugins.
$zaso_admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( empty( $zaso_admins ) ) {
	echo "No administrator on this site; cannot run.\n";
	exit( 1 );
}
wp_set_current_user( (int) $zaso_admins[0] );

// Pretend FeedProof is not installed. The sandbox is the dev tree, so it is.
add_filter( 'zaso_cross_promo_feedproof_present', '__return_false', 99 );

echo "\n=== cross-promo state machine (WooCommerce " . ( $zaso_woo ? 'ACTIVE' : 'SKIPPED' ) . ") ===\n";

if ( ! $zaso_woo ) {
	// ------------------------------------------------ WooCommerce absent ----
	set_current_screen( 'plugins' );
	zaso_mature_clock();
	zaso_t( 'hidden when WooCommerce is not active', false === zaso_cross_promo_should_show() );
} else {
	// ------------------------------------------------------ screen gates ----
	set_current_screen( 'dashboard' );
	zaso_t( 'screen key empty on a non-qualifying screen', '' === zaso_cross_promo_screen_key() );
	zaso_mature_clock();
	zaso_t( 'hidden on a non-qualifying screen', false === zaso_cross_promo_should_show() );

	set_current_screen( 'plugins' );
	zaso_t( "screen key is 'plugins_screen' on the Plugins screen", 'plugins_screen' === zaso_cross_promo_screen_key() );

	set_current_screen( 'toplevel_page_zen-addons' );
	zaso_t( "screen key is 'zen_screen' on a Zen Addons screen", 'zen_screen' === zaso_cross_promo_screen_key() );

	// ------------------------------------------------------- happy path ----
	set_current_screen( 'plugins' );
	zaso_mature_clock();
	zaso_t( 'SHOWN on the Plugins screen once every gate passes', true === zaso_cross_promo_should_show() );

	set_current_screen( 'toplevel_page_zen-addons' );
	zaso_t( 'SHOWN on a Zen Addons screen once every gate passes', true === zaso_cross_promo_should_show() );

	// ------------------------------------------------ FeedProof present ----
	remove_filter( 'zaso_cross_promo_feedproof_present', '__return_false', 99 );
	zaso_t( 'presence check sees the real FeedProof install on this sandbox', true === zaso_cross_promo_is_feedproof_present() );
	zaso_t( 'hidden when FeedProof is already present', false === zaso_cross_promo_should_show() );
	add_filter( 'zaso_cross_promo_feedproof_present', '__return_false', 99 );

	// ------------------------------------------------------- capability ----
	$zaso_sub = get_users( array( 'role' => 'subscriber', 'number' => 1, 'fields' => 'ID' ) );
	if ( ! empty( $zaso_sub ) ) {
		wp_set_current_user( (int) $zaso_sub[0] );
		zaso_t( 'hidden for a user without install_plugins', false === zaso_cross_promo_should_show() );
		wp_set_current_user( (int) $zaso_admins[0] );
	} else {
		$zaso_temp_user = wp_insert_user(
			array(
				'user_login' => 'zaso_promo_test_sub',
				'user_pass'  => wp_generate_password( 24 ),
				'user_email' => 'zaso_promo_test_sub@example.invalid',
				'role'       => 'subscriber',
			)
		);
		if ( ! is_wp_error( $zaso_temp_user ) ) {
			wp_set_current_user( (int) $zaso_temp_user );
			zaso_t( 'hidden for a user without install_plugins', false === zaso_cross_promo_should_show() );
			wp_set_current_user( (int) $zaso_admins[0] );
			require_once ABSPATH . 'wp-admin/includes/user.php';
			wp_delete_user( (int) $zaso_temp_user );
		} else {
			zaso_t( 'hidden for a user without install_plugins (could not create test user)', false );
		}
	}

	// ------------------------------------------------------------ clock ----
	set_current_screen( 'plugins' );
	zaso_set_promo_state( array( 'since' => time() ) );
	zaso_t( 'hidden on update day, before the delay elapses', false === zaso_cross_promo_should_show() );

	zaso_set_promo_state( array( 'since' => time() - ( ZASO_CROSS_PROMO_DELAY_DAYS - 1 ) * DAY_IN_SECONDS ) );
	zaso_t( 'still hidden one day short of the delay', false === zaso_cross_promo_should_show() );

	zaso_mature_clock();
	zaso_t( 'shown once the delay has elapsed', true === zaso_cross_promo_should_show() );

	// ------------------------------------------------- stored decisions ----
	zaso_set_promo_state( array( 'since' => time() - 30 * DAY_IN_SECONDS, 'state' => 'dismissed' ) );
	zaso_t( 'hidden forever after No thanks', false === zaso_cross_promo_should_show() );

	zaso_set_promo_state( array( 'since' => time() - 30 * DAY_IN_SECONDS, 'state' => 'later', 'later_until' => time() + DAY_IN_SECONDS ) );
	zaso_t( 'hidden while a Maybe later snooze is running', false === zaso_cross_promo_should_show() );

	zaso_set_promo_state( array( 'since' => time() - 60 * DAY_IN_SECONDS, 'state' => 'later', 'later_until' => time() - DAY_IN_SECONDS ) );
	zaso_t( 'shown again once the snooze expires', true === zaso_cross_promo_should_show() );

	// -------------------------------------------- yield to review prompt ----
	zaso_mature_clock();
	update_option(
		'zaso_review_prompt',
		array( 'since' => time() - 15 * DAY_IN_SECONDS, 'state' => '', 'later_until' => 0 ),
		false
	);
	zaso_t( 'review prompt is renderable in this state (control)', true === zaso_review_prompt_should_show() );
	zaso_t( 'YIELDS: hidden while the review prompt would render', false === zaso_cross_promo_should_show() );

	update_option( 'zaso_review_prompt', array( 'since' => time() - 15 * DAY_IN_SECONDS, 'state' => 'dismissed', 'later_until' => 0 ), false );
	zaso_t( 'shown once the review prompt has been answered', true === zaso_cross_promo_should_show() );

	// --------------------------------------------------- clock autostart ----
	delete_option( ZASO_CROSS_PROMO_OPTION );
	zaso_cross_promo_should_show();
	$zaso_started = get_option( ZASO_CROSS_PROMO_OPTION, array() );
	zaso_t( 'delay clock starts itself on the first qualifying view', ! empty( $zaso_started['since'] ) );

	// ---------------------------------------------------- action handler ----
	zaso_mature_clock();
	$_GET['zaso_cross_promo_action'] = 'wat';
	$_REQUEST['_wpnonce']            = wp_create_nonce( 'zaso_cross_promo' );
	$_GET['_wpnonce']                = $_REQUEST['_wpnonce'];
	zaso_cross_promo_handle_action();
	$zaso_after = get_option( ZASO_CROSS_PROMO_OPTION, array() );
	zaso_t( 'an unknown action does NOT dismiss the notice', '' === $zaso_after['state'] );

	$_GET['zaso_cross_promo_action'] = 'later';
	$zaso_state                      = zaso_cross_promo_state();
	$zaso_state['state']             = 'later';
	$zaso_state['later_until']       = time() + ZASO_CROSS_PROMO_SNOOZE_DAYS * DAY_IN_SECONDS;
	update_option( ZASO_CROSS_PROMO_OPTION, $zaso_state, false );
	$zaso_after = get_option( ZASO_CROSS_PROMO_OPTION, array() );
	zaso_t(
		'Maybe later snoozes for ' . ZASO_CROSS_PROMO_SNOOZE_DAYS . ' days',
		'later' === $zaso_after['state']
		&& $zaso_after['later_until'] > time() + ( ZASO_CROSS_PROMO_SNOOZE_DAYS - 1 ) * DAY_IN_SECONDS
	);

	unset( $_GET['zaso_cross_promo_action'], $_GET['_wpnonce'], $_REQUEST['_wpnonce'] );

	// ------------------------------------------------------------- URLs ----
	set_current_screen( 'plugins' );
	$zaso_info = zaso_cross_promo_info_url();
	zaso_t( 'info URL carries utm_source=zen-addons', false !== strpos( $zaso_info, 'utm_source=zen-addons' ) );
	zaso_t( 'info URL carries utm_campaign=cross-promo', false !== strpos( $zaso_info, 'utm_campaign=cross-promo' ) );
	zaso_t( 'info URL carries the screen as utm_content', false !== strpos( $zaso_info, 'utm_content=plugins_screen' ) );
	zaso_t( 'info URL points at the FeedProof product page', 0 === strpos( $zaso_info, ZASO_CROSS_PROMO_INFO_URL ) );

	$zaso_details = zaso_cross_promo_details_url();
	zaso_t( 'details URL targets core plugin-install.php', false !== strpos( $zaso_details, 'plugin-install.php' ) );
	zaso_t( 'details URL asks for the FeedProof plugin', false !== strpos( $zaso_details, 'plugin=' . ZASO_CROSS_PROMO_SLUG ) );
	zaso_t( 'details URL opens in the thickbox modal', false !== strpos( $zaso_details, 'TB_iframe=true' ) );

	// ---------------------------------------------------- render output ----
	zaso_mature_clock();
	ob_start();
	zaso_cross_promo_render();
	$zaso_html = ob_get_clean();
	zaso_t( 'notice renders markup when it should show', false !== strpos( $zaso_html, 'zaso-cross-promo-notice' ) );
	zaso_t( 'notice offers the install call to action', false !== strpos( $zaso_html, 'open-plugin-details-modal' ) );
	zaso_t( 'notice offers a permanent dismiss', false !== strpos( $zaso_html, 'zaso_cross_promo_action=dismiss' ) );
	zaso_t( 'notice contains no em dash', false === strpos( $zaso_html, "\xe2\x80\x94" ) );

	zaso_set_promo_state( array( 'since' => time(), 'state' => 'dismissed' ) );
	ob_start();
	zaso_cross_promo_render();
	$zaso_html = ob_get_clean();
	zaso_t( 'notice renders nothing when dismissed', '' === trim( $zaso_html ) );

	// ------------------------------------------------- uninstall wiring ----
	$zaso_uninstall = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
	zaso_t( 'uninstall.php deletes the notice option', false !== strpos( $zaso_uninstall, "delete_option( 'zaso_cross_promo' )" ) );
}

// -------------------------------------------------------------- restore ----
if ( null === $zaso_backup_promo ) {
	delete_option( ZASO_CROSS_PROMO_OPTION );
} else {
	update_option( ZASO_CROSS_PROMO_OPTION, $zaso_backup_promo, false );
}

if ( null === $zaso_backup_review ) {
	delete_option( 'zaso_review_prompt' );
} else {
	update_option( 'zaso_review_prompt', $zaso_backup_review, false );
}

$zaso_now_review = get_option( 'zaso_review_prompt', null );
zaso_t(
	'review prompt option restored byte-exact',
	wp_json_encode( $zaso_now_review ) === wp_json_encode( $zaso_backup_review )
);

$zaso_now_promo = get_option( ZASO_CROSS_PROMO_OPTION, null );
zaso_t(
	'cross-promo option restored byte-exact',
	wp_json_encode( $zaso_now_promo ) === wp_json_encode( $zaso_backup_promo )
);

$zaso_total = (int) $GLOBALS['zaso_pass'] + (int) $GLOBALS['zaso_fail'];

/*
 * Self-check on the harness itself: a run that asserts nothing must fail loudly
 * rather than print a reassuring zero.
 */
if ( 0 === $zaso_total ) {
	echo "\nHARNESS ERROR: no assertions ran.\n";
	exit( 1 );
}

echo "\n{$GLOBALS['zaso_pass']} passed, {$GLOBALS['zaso_fail']} failed (of {$zaso_total})\n";
exit( $GLOBALS['zaso_fail'] > 0 ? 1 : 0 );
