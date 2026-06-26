<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Section Divider Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.10.0
 */

// Static, plugin-authored SVG shapes. No user data is interpolated into the
// markup: the fill color and size come from LESS variables, and the flip comes
// from CSS classes. $style is whitelisted in get_template_variables().
$zaso_section_divider_shapes = array(
	'waves'    => '<path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"/>',
	'curve'    => '<path d="M0,0V7.23C0,65.52,268.63,112.77,600,112.77S1200,65.52,1200,7.23V0Z"/>',
	'tilt'     => '<path d="M1200,120L0,16.48V0H1200Z"/>',
	'triangle' => '<path d="M598.97,114.72,0,0V120H1200V0Z"/>',
);

$zaso_section_divider_classes = array( 'zaso-section-divider' );
if ( ! empty( $flip_horizontal ) ) {
	$zaso_section_divider_classes[] = 'zaso-section-divider--flip-x';
}
if ( ! empty( $flip_vertical ) ) {
	$zaso_section_divider_classes[] = 'zaso-section-divider--flip-y';
}
if ( ! empty( $instance['extra_class'] ) ) {
	$zaso_section_divider_classes[] = $instance['extra_class'];
}

$zaso_section_divider_svg = isset( $zaso_section_divider_shapes[ $style ] )
	? $zaso_section_divider_shapes[ $style ]
	: $zaso_section_divider_shapes['waves'];
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( implode( ' ', $zaso_section_divider_classes ) ); ?>" aria-hidden="true">
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" focusable="false" role="presentation">
		<?php echo $zaso_section_divider_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static plugin-authored SVG path markup, contains no user input. ?>
	</svg>
</div>
