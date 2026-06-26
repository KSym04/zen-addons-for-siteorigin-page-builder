<?php
/**
 * Build demo pages for Zen Addons 1.6.0 (Image Gallery + Logo Showcase).
 *
 * Run: wp eval-file scripts/build-1.6.0-demo.php --path=app/public
 *
 * Creates:
 *   - 6 logo PNG images in the media library for Logo Showcase
 *   - Appends Logo Showcase widget to the People page  (ID 806)
 *   - Appends Image Gallery widget to the Media page   (ID 592)
 *
 * Safe to re-run: widget rows are appended only if the widget class is not
 * already present on the target page.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// 1. Generate 6 PNG logo images via PHP GD
// ---------------------------------------------------------------------------

$upload_dir = wp_upload_dir();
$logo_specs = array(
	array( 'slug' => 'volta',  'name' => 'Volta',  'bg' => array( 26, 32, 44 ),   'fg' => array( 250, 204, 21 ),  'shape' => 'bolt',    'company' => 'Volta Energy' ),
	array( 'slug' => 'prism',  'name' => 'Prism',  'bg' => array( 15, 23, 42 ),   'fg' => array( 139, 92, 246 ),  'shape' => 'diamond', 'company' => 'Prism Design' ),
	array( 'slug' => 'harbor', 'name' => 'Harbor', 'bg' => array( 14, 116, 144 ), 'fg' => array( 255, 255, 255 ), 'shape' => 'anchor',  'company' => 'Harbor Labs' ),
	array( 'slug' => 'fern',   'name' => 'Fern',   'bg' => array( 20, 83, 45 ),   'fg' => array( 134, 239, 172 ), 'shape' => 'leaf',    'company' => 'Fern Co' ),
	array( 'slug' => 'orbit',  'name' => 'Orbit',  'bg' => array( 30, 27, 75 ),   'fg' => array( 99, 179, 237 ),  'shape' => 'circle',  'company' => 'Orbit SaaS' ),
	array( 'slug' => 'crest',  'name' => 'Crest',  'bg' => array( 120, 53, 15 ),  'fg' => array( 253, 186, 116 ), 'shape' => 'shield',  'company' => 'Crest Finance' ),
);

$logo_ids = array();

foreach ( $logo_specs as $spec ) {
	$filename = 'zaso-logo-' . $spec['slug'] . '.png';
	$filepath = $upload_dir['path'] . '/' . $filename;

	// Skip if a file with this name already has an attachment record.
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array( 'key' => '_wp_attached_file', 'value' => $filename, 'compare' => 'LIKE' ),
		),
	) );
	if ( ! empty( $existing ) ) {
		$logo_ids[ $spec['slug'] ] = $existing[0];
		printf( "[SKIP] Logo '%s' already exists (ID %d)\n", $spec['name'], $existing[0] );
		continue;
	}

	// Create 320x120 px logo PNG.
	$w  = 320;
	$h  = 120;
	$im = imagecreatetruecolor( $w, $h );

	$bg = imagecolorallocate( $im, $spec['bg'][0], $spec['bg'][1], $spec['bg'][2] );
	$fg = imagecolorallocate( $im, $spec['fg'][0], $spec['fg'][1], $spec['fg'][2] );
	imagefilledrectangle( $im, 0, 0, $w - 1, $h - 1, $bg );

	// Icon area (left square 120x120), text area (right 200x120).
	$icon_cx = 60;
	$icon_cy = 60;
	$icon_r  = 28;

	switch ( $spec['shape'] ) {
		case 'bolt':
			// Lightning bolt shape as a filled polygon.
			$pts = array(
				$icon_cx - 8, $icon_cy - $icon_r,
				$icon_cx + 4, $icon_cy - 4,
				$icon_cx + 12, $icon_cy - 4,
				$icon_cx, $icon_cy + $icon_r,
				$icon_cx - 4, $icon_cy + 4,
				$icon_cx - 14, $icon_cy + 4,
			);
			imagefilledpolygon( $im, $pts, 6, $fg );
			break;

		case 'diamond':
			$pts = array(
				$icon_cx, $icon_cy - $icon_r,
				$icon_cx + $icon_r, $icon_cy,
				$icon_cx, $icon_cy + $icon_r,
				$icon_cx - $icon_r, $icon_cy,
			);
			imagefilledpolygon( $im, $pts, 4, $fg );
			break;

		case 'anchor':
			// Circle top + vertical line + horizontal bar.
			imagefilledellipse( $im, $icon_cx, $icon_cy - 16, 22, 22, $fg );
			$hole = imagecolorallocate( $im, $spec['bg'][0], $spec['bg'][1], $spec['bg'][2] );
			imagefilledellipse( $im, $icon_cx, $icon_cy - 16, 10, 10, $hole );
			imagefilledrectangle( $im, $icon_cx - 3, $icon_cy - 5, $icon_cx + 3, $icon_cy + $icon_r - 4, $fg );
			imagefilledrectangle( $im, $icon_cx - 16, $icon_cy - 3, $icon_cx + 16, $icon_cy + 3, $fg );
			imagefilledellipse( $im, $icon_cx - 16, $icon_cy + $icon_r - 6, 10, 10, $fg );
			imagefilledellipse( $im, $icon_cx + 16, $icon_cy + $icon_r - 6, 10, 10, $fg );
			break;

		case 'leaf':
			// Filled ellipse rotated 45 degrees — approximate with rotated oval.
			imagefilledellipse( $im, $icon_cx, $icon_cy - 6, 24, 44, $fg );
			imagefilledellipse( $im, $icon_cx - 6, $icon_cy, 44, 24, $fg );
			// Stem.
			imageline( $im, $icon_cx, $icon_cy + 14, $icon_cx - 8, $icon_cy + $icon_r, $fg );
			imagesetthickness( $im, 2 );
			imageline( $im, $icon_cx, $icon_cy - 2, $icon_cx, $icon_cy + 14, $fg );
			imagesetthickness( $im, 1 );
			break;

		case 'circle':
			imagefilledellipse( $im, $icon_cx, $icon_cy, $icon_r * 2, $icon_r * 2, $fg );
			$inner = imagecolorallocate( $im, $spec['bg'][0], $spec['bg'][1], $spec['bg'][2] );
			imagefilledellipse( $im, $icon_cx, $icon_cy, $icon_r, $icon_r, $inner );
			// Orbit ring.
			imagearc( $im, $icon_cx, $icon_cy, $icon_r * 2 + 14, $icon_r * 2 + 14, 30, 210, $fg );
			imagesetthickness( $im, 2 );
			imagearc( $im, $icon_cx, $icon_cy, $icon_r * 2 + 14, $icon_r * 2 + 14, 30, 210, $fg );
			imagesetthickness( $im, 1 );
			break;

		case 'shield':
			$pts = array(
				$icon_cx, $icon_cy - $icon_r,
				$icon_cx + $icon_r, $icon_cy - $icon_r / 2,
				$icon_cx + $icon_r, $icon_cy + $icon_r / 3,
				$icon_cx, $icon_cy + $icon_r,
				$icon_cx - $icon_r, $icon_cy + $icon_r / 3,
				$icon_cx - $icon_r, $icon_cy - $icon_r / 2,
			);
			imagefilledpolygon( $im, $pts, 6, $fg );
			break;
	}

	// Company name — two lines: short name (large) + tagline (small).
	$text_x = 130;
	// Built-in GD font 5 is the largest (9×15px chars).
	$font    = 5;
	$char_w  = imagefontwidth( $font );
	$char_h  = imagefontheight( $font );

	// Short brand name.
	imagestring( $im, $font, $text_x, $h / 2 - $char_h - 3, strtoupper( $spec['name'] ), $fg );
	// Tagline in smaller font (font 3 = 7×13px).
	$sfont  = 3;
	$schar_h = imagefontheight( $sfont );
	$sub_color = imagecolorallocatealpha( $im, $spec['fg'][0], $spec['fg'][1], $spec['fg'][2], 40 );
	imagestring( $im, $sfont, $text_x + 2, $h / 2 + 5, $spec['company'], $sub_color );

	imagepng( $im, $filepath );
	imagedestroy( $im );

	// Register in WordPress media library.
	$attachment_id = wp_insert_attachment(
		array(
			'post_title'     => $spec['name'],
			'post_mime_type' => 'image/png',
			'post_status'    => 'inherit',
			'post_name'      => 'zaso-logo-' . $spec['slug'],
		),
		$filepath
	);

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$meta = wp_generate_attachment_metadata( $attachment_id, $filepath );
	wp_update_attachment_metadata( $attachment_id, $meta );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $spec['company'] . ' logo' );

	$logo_ids[ $spec['slug'] ] = $attachment_id;
	printf( "[OK]   Created logo '%s' (ID %d) → %s\n", $spec['name'], $attachment_id, $filename );
}

echo "\n";

// ---------------------------------------------------------------------------
// 2. Logo Showcase widget instance
// ---------------------------------------------------------------------------

$logo_order = array( 'volta', 'prism', 'harbor', 'fern', 'orbit', 'crest' );
$logos_data = array();
foreach ( $logo_order as $slug ) {
	if ( ! empty( $logo_ids[ $slug ] ) ) {
		$logos_data[] = array(
			'image'        => (string) $logo_ids[ $slug ],
			'alt'          => '',   // Falls back to media library alt.
			'link'         => '',
			'link_new_tab' => '',
		);
	}
}

$logo_showcase_instance = array(
	'logos'     => $logos_data,
	'columns'   => '6',
	'grayscale' => '1',
	'alignment' => 'center',
	'design'    => array(
		'logo_height' => '56px',
		'gap'         => '32px',
	),
	'extra_id'    => '',
	'extra_class' => '',
);

// ---------------------------------------------------------------------------
// 3. Image Gallery widget instance
// ---------------------------------------------------------------------------

// Use 6 best unique landscape images from the library.
$gallery_image_ids = array( 579, 580, 286, 287, 300, 301 );
$gallery_images    = array();
foreach ( $gallery_image_ids as $id ) {
	$gallery_images[] = array(
		'image'   => (string) $id,
		'caption' => '',
	);
}

$image_gallery_instance = array(
	'images'     => $gallery_images,
	'columns'    => '3',
	'lightbox'   => '1',
	'image_size' => 'large',
	'design'     => array(
		'gap'           => '8px',
		'border_radius' => '6px',
	),
	'extra_id'    => '',
	'extra_class' => '',
);

// ---------------------------------------------------------------------------
// 4. Helper: append a heading + widget to a page's panels_data
// ---------------------------------------------------------------------------

/**
 * Append a section heading (Custom HTML) and a ZASO widget to a page.
 *
 * @param int    $page_id       Page post ID.
 * @param string $heading_html  HTML content for the Custom HTML heading widget.
 * @param string $widget_class  PHP class name of the ZASO widget.
 * @param array  $widget_data   Widget instance data.
 */
function zaso_append_widget( $page_id, $heading_html, $widget_class, $widget_data ) {
	$panels = get_post_meta( $page_id, 'panels_data', true );
	if ( ! is_array( $panels ) || empty( $panels['widgets'] ) ) {
		printf( "[SKIP] No panels_data found for page %d\n", $page_id );
		return;
	}

	// Guard: skip if this widget class is already on the page.
	foreach ( $panels['widgets'] as $existing ) {
		if ( isset( $existing['panels_info']['class'] ) && $existing['panels_info']['class'] === $widget_class ) {
			printf( "[SKIP] %s already present on page %d\n", $widget_class, $page_id );
			return;
		}
	}

	$next_id = count( $panels['widgets'] );

	// Heading widget.
	$panels['widgets'][] = array(
		'title'        => '',
		'content'      => $heading_html,
		'panels_info'  => array(
			'class' => 'WP_Widget_Custom_HTML',
			'raw'   => false,
			'grid'  => 0,
			'cell'  => 0,
			'id'    => $next_id,
		),
	);

	// ZASO widget.
	$widget_data['panels_info'] = array(
		'class' => $widget_class,
		'raw'   => false,
		'grid'  => 0,
		'cell'  => 0,
		'id'    => $next_id + 1,
	);
	$panels['widgets'][] = $widget_data;

	update_post_meta( $page_id, 'panels_data', $panels );
	// Flush the post's cached content so SiteOrigin rebuilds it.
	wp_update_post( array( 'ID' => $page_id, 'post_content' => '' ) );

	printf( "[OK]   Appended %s to page %d\n", $widget_class, $page_id );
}

// ---------------------------------------------------------------------------
// 5. Append Logo Showcase to People page (806)
// ---------------------------------------------------------------------------

$heading_style = 'margin:52px 0 10px;font-weight:600;color:#475569;font-size:0.98rem;';
zaso_append_widget(
	806,
	'<p style="' . $heading_style . '">Logo Showcase</p>',
	'Zen_Addons_SiteOrigin_Logo_Showcase_Widget',
	$logo_showcase_instance
);

// ---------------------------------------------------------------------------
// 6. Append Image Gallery to Media page (592)
// ---------------------------------------------------------------------------

zaso_append_widget(
	592,
	'<p style="' . $heading_style . '">Image Gallery</p>',
	'Zen_Addons_SiteOrigin_Image_Gallery_Widget',
	$image_gallery_instance
);

echo "\nDone. Visit:\n";
echo "  People: http://dopeplugins.local/zaso-cat-8-people/\n";
echo "  Media:  http://dopeplugins.local/zaso-cat-3-media/\n";
