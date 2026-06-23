<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Before / After Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.3.0
 */

$has_images = ! empty( $before['src'] ) && ! empty( $after['src'] );
$slider_label = sprintf(
	/* translators: 1: before label, 2: after label. */
	__( 'Comparison slider. Drag or use the arrow keys to reveal the %1$s and %2$s images.', 'zaso' ),
	'' !== trim( (string) $instance['before_label'] ) ? $instance['before_label'] : __( 'before', 'zaso' ),
	'' !== trim( (string) $instance['after_label'] ) ? $instance['after_label'] : __( 'after', 'zaso' )
);

// Initial clip and handle position (server-rendered; JS keeps them in sync).
$reveal = 100 - (int) $position;
if ( 'vertical' === $orientation ) {
	$clip_style   = sprintf( 'clip-path:inset(0 0 %d%% 0);', $reveal );
	$handle_style = sprintf( 'top:%d%%;', (int) $position );
} else {
	$clip_style   = sprintf( 'clip-path:inset(0 %d%% 0 0);', $reveal );
	$handle_style = sprintf( 'left:%d%%;', (int) $position );
}
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-before-after <?php echo esc_attr( $instance['extra_class'] ); ?>">

	<?php if ( ! $has_images ) : ?>
		<p class="zaso-before-after__placeholder"><?php esc_html_e( 'Select a before and an after image to build the comparison slider.', 'zaso' ); ?></p>
	<?php else : ?>
		<div class="zaso-before-after__container zaso-before-after__container--<?php echo esc_attr( $orientation ); ?>" data-orientation="<?php echo esc_attr( $orientation ); ?>">

			<img class="zaso-before-after__img zaso-before-after__after-img" src="<?php echo esc_url( $after['src'] ); ?>" alt="<?php echo esc_attr( $after['alt'] ); ?>"<?php
				echo '' !== $after['width'] ? ' width="' . esc_attr( $after['width'] ) . '"' : '';
				echo '' !== $after['height'] ? ' height="' . esc_attr( $after['height'] ) . '"' : '';
			?> loading="lazy" draggable="false" />

			<img class="zaso-before-after__img zaso-before-after__before-img" src="<?php echo esc_url( $before['src'] ); ?>" alt="<?php echo esc_attr( $before['alt'] ); ?>" style="<?php echo esc_attr( $clip_style ); ?>" loading="lazy" draggable="false" aria-hidden="true" />

			<?php if ( ! empty( $instance['show_labels'] ) ) : ?>
				<?php if ( '' !== trim( (string) $instance['before_label'] ) ) : ?>
					<span class="zaso-before-after__label zaso-before-after__label--before" aria-hidden="true"><?php echo esc_html( $instance['before_label'] ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $instance['after_label'] ) ) : ?>
					<span class="zaso-before-after__label zaso-before-after__label--after" aria-hidden="true"><?php echo esc_html( $instance['after_label'] ); ?></span>
				<?php endif; ?>
			<?php endif; ?>

			<button type="button" class="zaso-before-after__handle" role="slider" tabindex="0" style="<?php echo esc_attr( $handle_style ); ?>" aria-label="<?php echo esc_attr( $slider_label ); ?>" aria-orientation="<?php echo esc_attr( $orientation ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $position ); ?>">
				<span class="zaso-before-after__handle-icon" aria-hidden="true"></span>
			</button>

		</div>
	<?php endif; ?>

</div>
