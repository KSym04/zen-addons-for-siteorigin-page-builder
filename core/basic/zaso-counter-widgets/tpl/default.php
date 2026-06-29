<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Counter Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.3.0
 */

$alignment = in_array( $instance['design']['alignment'], array( 'left', 'center', 'right' ), true ) ? $instance['design']['alignment'] : 'center';

// Optional structural layout. Default ('default') is the original stacked layout
// and adds NO extra class, so existing instances render byte-identical. Only the
// alternate layouts (card / inline / circle) add a modifier class.
$zaso_layout         = ! empty( $instance['layout'] ) ? $instance['layout'] : 'default';
$zaso_layout_allowed = array( 'default', 'card', 'inline', 'circle' );
if ( ! in_array( $zaso_layout, $zaso_layout_allowed, true ) ) {
	$zaso_layout = 'default';
}
$zaso_layout_class = ( 'default' !== $zaso_layout ) ? ' zaso-counter--layout-' . $zaso_layout : '';

// Full accessible value, e.g. "$1,250+".
$aria_value = $prefix . $formatted_end . $suffix;
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-counter zaso-counter--align-<?php echo esc_attr( $alignment ); ?><?php echo esc_attr( $zaso_layout_class ); ?> <?php echo esc_attr( $instance['extra_class'] ); ?>">
	<div class="zaso-counter__inner">

		<?php if ( ! empty( $image ) && ! empty( $image_attr['src'] ) ) : ?>
			<div class="zaso-counter__icon">
				<img class="zaso-counter__image"<?php foreach ( $image_attr as $n => $v ) { if ( '' !== (string) $v ) { echo ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"'; } } ?> />
			</div>
		<?php elseif ( ! empty( $icon ) ) : ?>
			<div class="zaso-counter__icon">
				<?php echo siteorigin_widget_get_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- siteorigin_widget_get_icon() returns SiteOrigin-generated, safe markup. ?>
			</div>
		<?php endif; ?>

		<div class="zaso-counter__number" role="img" aria-label="<?php echo esc_attr( $aria_value ); ?>">
			<?php if ( '' !== $prefix ) : ?>
				<span class="zaso-counter__prefix" aria-hidden="true"><?php echo esc_html( $prefix ); ?></span>
			<?php endif; ?>
			<span class="zaso-counter__value" aria-hidden="true"
				data-start="<?php echo esc_attr( $start ); ?>"
				data-end="<?php echo esc_attr( $end ); ?>"
				data-duration="<?php echo esc_attr( $duration ); ?>"
				data-decimals="<?php echo esc_attr( $decimals ); ?>"
				data-separator="<?php echo esc_attr( $separator ); ?>"><?php echo esc_html( $formatted_end ); ?></span>
			<?php if ( '' !== $suffix ) : ?>
				<span class="zaso-counter__suffix" aria-hidden="true"><?php echo esc_html( $suffix ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( '' !== trim( $title ) ) : ?>
			<div class="zaso-counter__title"><?php echo esc_html( $title ); ?></div>
		<?php endif; ?>

	</div>
</div>
