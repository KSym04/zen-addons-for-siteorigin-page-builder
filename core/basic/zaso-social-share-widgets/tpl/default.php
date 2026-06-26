<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Social Share Bar Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.9.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $items       Enabled networks (key, label, color, icon SVG path, href).
 * @var string $share_url   Raw URL shared by the copy-link button.
 * @var bool   $show_labels Whether to print the network label next to each icon.
 * @var string $color_mode  'brand' or 'mono'.
 * @var string $classes     Root element class string.
 *
 * Also available directly:
 * @var array  $instance    Full widget instance.
 * @var array  $args        Widget sidebar args.
 */

if ( empty( $items ) ) {
	return;
}

// Per-network accessible labels. Falls back to "Share on %s".
$zaso_share_aria = array(
	'email' => __( 'Share by email', 'zaso' ),
	'copy'  => __( 'Copy link to clipboard', 'zaso' ),
);
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
<nav <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $classes ); ?>" aria-label="<?php esc_attr_e( 'Share this page', 'zaso' ); ?>">
	<?php
	foreach ( $items as $item ) :
		$aria = isset( $zaso_share_aria[ $item['key'] ] )
			? $zaso_share_aria[ $item['key'] ]
			/* translators: %s: social network name. */
			: sprintf( __( 'Share on %s', 'zaso' ), $item['label'] );

		// Brand mode paints each button its network colour inline; mono mode is handled in CSS.
		$style = ( 'brand' === $color_mode ) ? ' style="background-color:' . esc_attr( $item['color'] ) . ';"' : '';

		// Static, hard-coded glyph from the network registry (no user input).
		$svg = '<svg class="zaso-social-share__icon" viewBox="0 0 24 24" width="1em" height="1em" fill="currentColor" focusable="false" aria-hidden="true">' . $item['icon'] . '</svg>';
		?>

		<?php if ( 'copy' === $item['key'] ) : ?>
			<button type="button"
				class="zaso-social-share__btn zaso-social-share__btn--copy"
				data-zaso-share-url="<?php echo esc_url( $share_url ); ?>"
				aria-label="<?php echo esc_attr( $aria ); ?>">
				<?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $svg is built from a static plugin-defined icon registry, not user input. ?>
				<?php if ( $show_labels ) : ?>
					<span class="zaso-social-share__label"><?php echo esc_html( $item['label'] ); ?></span>
				<?php endif; ?>
			</button>
		<?php else : ?>
			<a class="zaso-social-share__btn zaso-social-share__btn--<?php echo esc_attr( $item['key'] ); ?>"
				href="<?php echo esc_url( $item['href'] ); ?>"
				<?php echo ( 0 === strpos( $item['href'], 'http' ) ) ? 'target="_blank" rel="noopener noreferrer nofollow"' : ''; ?>
				<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $style is a pre-escaped inline background-color built with esc_attr(). ?>
				aria-label="<?php echo esc_attr( $aria ); ?>">
				<?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $svg is built from a static plugin-defined icon registry, not user input. ?>
				<?php if ( $show_labels ) : ?>
					<span class="zaso-social-share__label"><?php echo esc_html( $item['label'] ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>

	<?php endforeach; ?>

	<span class="zaso-social-share__status" aria-live="polite"></span>
</nav>
