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
$expected = 26; // Number of ZASO widgets shipped.
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
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'height' => '40px' ),
	),
	'cta-banner'  => array(
		'file'     => $base . 'zaso-cta-banner-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'layout' => 'stacked', 'alignment' => 'center', 'heading' => 'Hi <b>x</b>', 'subheading' => 'sub', 'content' => '<p>c</p><script>bad()</script>', 'button_text' => 'Go', 'button_url' => 'https://example.com', 'button_new_tab' => true, 'button_nofollow' => true ),
		'vars'     => array( 'bg_type' => 'solid', 'bg_image_url' => '' ),
	),
	'counter'     => array(
		'file'     => $base . 'zaso-counter-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'design' => array( 'alignment' => 'center' ) ),
		'vars'     => array( 'prefix' => '$', 'suffix' => '+', 'formatted_end' => '1,250', 'start' => 0.0, 'end' => 1250.0, 'duration' => 2000, 'decimals' => 0, 'separator' => ',', 'title' => 'Customers', 'icon' => '', 'image' => 0, 'image_attr' => array(), 'alignment' => 'center' ),
	),
	'countdown'   => array(
		'file'     => $base . 'zaso-countdown-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'on_expire' => 'message', 'expire_message' => '<p>Done</p><script>bad()</script>', 'design' => array( 'alignment' => 'center' ) ),
		'vars'     => array( 'deadline_ms' => 9999999999000, 'units' => array( 'days' => array( 'value' => 1, 'label' => 'Days' ), 'seconds' => array( 'value' => 5, 'label' => 'Seconds' ) ), 'on_expire' => 'message', 'alignment' => 'center', 'aria_label' => 'Countdown to later', 'is_expired' => false ),
	),
	'before-after'         => array(
		'file'     => $base . 'zaso-before-after-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'before_label' => 'Before', 'after_label' => 'After', 'show_labels' => true ),
		'vars'     => array( 'before' => array( 'src' => 'https://example.com/b.jpg', 'width' => 800, 'height' => 600, 'alt' => 'b' ), 'after' => array( 'src' => 'https://example.com/a.jpg', 'width' => 800, 'height' => 600, 'alt' => 'a' ), 'orientation' => 'horizontal', 'position' => 50 ),
	),
	'team-member'          => array(
		'file'     => $base . 'zaso-team-member-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil, 'members' => array(), 'columns' => '3', 'card_style' => 'minimal', 'alignment' => 'center' ),
		'vars'     => array(
			'members'           => array(
				array(
					'photo'        => array( 'src' => '', 'alt' => 'Jane Doe' ),
					'name'         => 'Jane <b>Doe</b>' . $evil,
					'role'         => 'CEO & Co-founder' . $evil,
					'bio'          => 'Leading our team with vision.<script>bad()</script>',
					'social_links' => array(
						'linkedin' => array( 'url' => 'https://linkedin.com', 'label' => 'Jane Doe on LinkedIn' ),
					),
				),
			),
			'container_classes' => 'zaso-team-member zaso-team-member--cols-3 zaso-team-member--style-minimal zaso-team-member--align-center',
		),
	),
	'testimonial-slider'   => array(
		'file'     => $base . 'zaso-testimonial-slider-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil ),
		'vars'     => array(
			'testimonials'      => array(
				array(
					'quote'        => 'This product changed our business.<script>bad()</script>',
					'author_name'  => 'John Smith' . $evil,
					'author_title' => 'Director' . $evil,
					'photo_src'    => '',
					'photo_alt'    => 'John Smith',
					'rating'       => 5,
					'rating_label' => '5 out of 5 stars',
				),
				// Out-of-range rating must NOT fatal (str_repeat negative-count guard).
				array(
					'quote'        => 'Second voice.',
					'author_name'  => 'Jane Roe',
					'author_title' => 'CTO',
					'photo_src'    => '',
					'photo_alt'    => 'Jane Roe',
					'rating'       => 6,
					'rating_label' => '5 out of 5 stars',
				),
			),
			'count'             => 2,
			'autoplay'          => false,
			'autoplay_duration' => 5000,
			'show_arrows'       => true,
			'show_dots'         => true,
			'classes'           => 'zaso-testimonial-slider',
		),
	),
	'services-grid'        => array(
		'file'     => $base . 'zaso-services-grid-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil ),
		'vars'     => array(
			'services'          => array(
				array(
					'icon'         => '',
					'image_attr'   => array(),
					'title'        => 'Fast Delivery' . $evil,
					'description'  => 'Ships in 24 hours.<script>bad()</script>',
					'link_url'     => 'https://example.com',
					'link_text'    => 'Learn more' . $evil,
					'link_new_tab' => true,
					'has_link'     => true,
				),
			),
			'container_classes' => 'zaso-services-grid zaso-services-grid--cols-3 zaso-services-grid--style-framed zaso-services-grid--align-center',
		),
	),
	'progress-bars'        => array(
		'file'     => $base . 'zaso-progress-bars-widgets/tpl/default.php',
		'instance' => array( 'extra_id' => $evil, 'extra_class' => $evil ),
		'vars'     => array(
			'bars'               => array(
				array( 'label' => 'Design' . $evil, 'percentage' => 90, 'bar_color' => '#4f46e5' ),
				// Out-of-range percentage must already be clamped to 0-100.
				array( 'label' => 'Strategy<script>bad()</script>', 'percentage' => 100, 'bar_color' => '' ),
			),
			'show_percentage'    => true,
			'animate'            => true,
			'animation_duration' => 1200,
			'classes'            => 'zaso-progress-bars',
		),
	),
);

foreach ( $cases as $name => $case ) {
	if ( ! file_exists( $case['file'] ) ) {
		$check( "render $name", false, 'template missing' );
		continue;
	}
	$instance = $case['instance'];
	if ( ! empty( $case['vars'] ) && is_array( $case['vars'] ) ) {
		extract( $case['vars'], EXTR_OVERWRITE ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- QA harness only; injects get_template_variables() output for the render test.
	}
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
