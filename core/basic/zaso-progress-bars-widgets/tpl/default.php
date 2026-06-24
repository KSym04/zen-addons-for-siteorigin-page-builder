<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Progress Bars Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.5.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $bars               Processed bar list (label, percentage 0-100, bar_color).
 * @var bool   $show_percentage    Whether to print the numeric percentage.
 * @var bool   $animate            Whether to animate the fill on scroll-into-view.
 * @var int    $animation_duration Fill animation duration in ms.
 * @var string $classes            Root element class string.
 *
 * Also available directly:
 * @var array  $instance           Full widget instance.
 * @var array  $args               Widget sidebar args.
 */

if ( empty( $bars ) ) {
	return;
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?>
	class="<?php echo esc_attr( $classes ); ?>"
	data-animate="<?php echo $animate ? '1' : '0'; ?>"
	data-duration="<?php echo esc_attr( $animation_duration ); ?>"
>
	<?php foreach ( $bars as $bar ) : ?>
		<?php
		// The fill always renders at its target width so the no-JS and
		// reduced-motion states are correct; the script resets to 0 and
		// transitions up only when animation is enabled.
		$fill_style = 'width: ' . $bar['percentage'] . '%;';
		if ( '' !== $bar['bar_color'] ) {
			$fill_style .= ' background-color: ' . $bar['bar_color'] . ';';
		}
		$aria_label = '' !== trim( $bar['label'] )
			? sprintf(
				/* translators: 1: bar label, 2: percentage */
				__( '%1$s: %2$d%%', 'zaso' ),
				$bar['label'],
				$bar['percentage']
			)
			: sprintf(
				/* translators: percentage */
				__( '%d%%', 'zaso' ),
				$bar['percentage']
			);
		?>
		<div class="zaso-progress-bars__item">
			<div class="zaso-progress-bars__head">
				<?php if ( '' !== trim( $bar['label'] ) ) : ?>
					<span class="zaso-progress-bars__label"><?php echo esc_html( $bar['label'] ); ?></span>
				<?php endif; ?>
				<?php if ( $show_percentage ) : ?>
					<span class="zaso-progress-bars__percentage" aria-hidden="true"><?php echo esc_html( $bar['percentage'] ); ?>%</span>
				<?php endif; ?>
			</div>
			<div
				class="zaso-progress-bars__track"
				role="progressbar"
				aria-valuenow="<?php echo esc_attr( $bar['percentage'] ); ?>"
				aria-valuemin="0"
				aria-valuemax="100"
				aria-label="<?php echo esc_attr( $aria_label ); ?>"
			>
				<span
					class="zaso-progress-bars__fill"
					style="<?php echo esc_attr( $fill_style ); ?>"
					data-percentage="<?php echo esc_attr( $bar['percentage'] ); ?>"
				></span>
			</div>
		</div>
	<?php endforeach; ?>
</div>
