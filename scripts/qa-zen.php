<?php
/**
 * Zen Addons QA harness (Layer 1, deterministic).
 *
 * Runs inside a loaded WordPress + SiteOrigin install via:
 *   wp eval-file scripts/qa-zen.php --path=app/public
 *
 * Checks:
 *   1. SiteOrigin Widgets Bundle framework is present.
 *   2. All expected ZASO widgets are discovered by SiteOrigin's folder filter.
 *   3. Representative widget templates render with malicious input without a
 *      fatal error and without leaking an unescaped XSS payload.
 *
 * Exit signal: prints "QA RESULT: PASS" or "QA RESULT: FAIL (n)".
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fails    = 0;
$expected = 18; // Number of ZASO widgets shipped.
$evil     = 'x" onmouseover="alert(1)';

/**
 * Print a single check line.
 */
$check = function ( $label, $ok, $detail = '' ) use ( &$fails ) {
	if ( ! $ok ) {
		$fails++;
	}
	printf( "[%s] %s%s\n", $ok ? 'PASS' : 'FAIL', $label, '' !== $detail ? ' - ' . $detail : '' );
};

// 1. Framework present.
$check( 'SiteOrigin_Widget framework available', class_exists( 'SiteOrigin_Widget' ) );

// 2. Widget discovery.
$folders   = apply_filters( 'siteorigin_widgets_widget_folders', array() );
$zaso_dirs = array_filter( $folders, function ( $p ) { return false !== strpos( $p, 'zen-addons' ); } );
$found     = 0;
foreach ( $zaso_dirs as $folder ) {
	foreach ( (array) glob( $folder . '*/', GLOB_ONLYDIR ) as $dir ) {
		foreach ( (array) glob( $dir . '*.php' ) as $file ) {
			$data = get_file_data( $file, array( 'name' => 'Widget Name' ) );
			if ( ! empty( $data['name'] ) ) {
				$found++;
			}
		}
	}
}
$check( "All $expected ZASO widgets discovered", $found === $expected, "found $found" );

// 3. Representative template render + escaping smoke test.
$base  = WP_PLUGIN_DIR . '/zen-addons-for-siteorigin-page-builder/core/basic/';
$cases = array(
	'alert-box'   => array(
		'file'     => $base . 'zaso-alert-box-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'alert_closebtn' => 'show', 'alert_message' => '<p>ok</p><script>bad()</script>' ),
	),
	'basic-tabs'  => array(
		'file'     => $base . 'zaso-basic-tabs-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => '', 'extra_class' => $evil, 'tab_main_title' => 'Main', 'tabs' => array( array( 'tab_field_title' => 'Tab <b>1</b>', 'tab_field_content' => '<p>c1</p>' ) ) ),
	),
	'spacer'      => array(
		'file'     => $base . 'zaso-spacer-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'spacer_height' => array( 'spacer_height_unit_value' => '40', 'spacer_height_unit' => 'px' ) ),
	),
);

foreach ( $cases as $name => $case ) {
	if ( ! file_exists( $case['file'] ) ) {
		$check( "render $name", false, 'template missing' );
		continue;
	}
	$instance = $case['instance'];
	ob_start();
	$err = '';
	try {
		include $case['file'];
	} catch ( \Throwable $e ) {
		$err = $e->getMessage();
	}
	$html = ob_get_clean();
	if ( '' !== $err ) {
		$check( "render $name", false, 'fatal: ' . $err );
		continue;
	}
	$leak = ( false !== strpos( $html, 'onmouseover="alert' ) );
	$check( "render $name (no fatal, no XSS leak)", ! $leak && strlen( $html ) > 0, $leak ? 'XSS LEAK' : strlen( $html ) . ' bytes' );
}

echo "\n";
echo 0 === $fails ? "QA RESULT: PASS\n" : "QA RESULT: FAIL ($fails)\n";
