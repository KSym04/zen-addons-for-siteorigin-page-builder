<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Testimonial Slider Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.4.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $testimonials      Processed testimonial list.
 * @var int    $count             Total slide count.
 * @var bool   $autoplay          Whether auto-play is on.
 * @var int    $autoplay_duration Auto-play interval in ms.
 * @var bool   $show_arrows       Whether to render prev/next arrows.
 * @var bool   $show_dots         Whether to render dot pagination.
 * @var string $classes           Root element class string.
 *
 * Also available directly:
 * @var array  $instance          Full widget instance.
 * @var array  $args              Widget sidebar args.
 */

if ( empty( $testimonials ) ) {
	return;
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?>
	class="<?php echo esc_attr( $classes ); ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'Testimonials', 'zaso' ); ?>"
	data-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"
	data-duration="<?php echo esc_attr( $autoplay_duration ); ?>"
	data-count="<?php echo esc_attr( $count ); ?>"
>

	<div class="zaso-testimonial-slider__viewport">
		<div class="zaso-testimonial-slider__track" aria-live="polite" aria-atomic="false">
			<?php foreach ( $testimonials as $index => $testimonial ) : ?>
				<div
					class="zaso-testimonial-slider__slide"
					role="group"
					aria-roledescription="<?php esc_attr_e( 'slide', 'zaso' ); ?>"
					aria-label="<?php echo esc_attr( sprintf(
						/* translators: 1: current slide number, 2: total slides */
						__( 'Testimonial %1$d of %2$d', 'zaso' ),
						$index + 1,
						$count
					) ); ?>"
					<?php if ( 0 !== $index ) : ?>aria-hidden="true"<?php endif; ?>
				>
					<div class="zaso-testimonial-slider__card">

						<?php if ( ! empty( $testimonial['rating'] ) ) : ?>
							<div class="zaso-testimonial-slider__rating">
								<span role="img" aria-label="<?php echo esc_attr( $testimonial['rating_label'] ); ?>">
									<span aria-hidden="true">
										<?php echo esc_html( str_repeat( '★', $testimonial['rating'] ) . str_repeat( '☆', 5 - $testimonial['rating'] ) ); ?>
									</span>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $testimonial['quote'] ) ) : ?>
							<blockquote class="zaso-testimonial-slider__quote">
								<p><?php echo esc_html( $testimonial['quote'] ); ?></p>
							</blockquote>
						<?php endif; ?>

						<div class="zaso-testimonial-slider__author">
							<?php if ( ! empty( $testimonial['photo_src'] ) ) : ?>
								<img
									class="zaso-testimonial-slider__author-photo"
									src="<?php echo esc_url( $testimonial['photo_src'] ); ?>"
									alt="<?php echo esc_attr( $testimonial['photo_alt'] ); ?>"
									loading="lazy"
									decoding="async"
								/>
							<?php endif; ?>
							<div class="zaso-testimonial-slider__author-info">
								<?php if ( ! empty( $testimonial['author_name'] ) ) : ?>
									<span class="zaso-testimonial-slider__author-name">
										<?php echo esc_html( $testimonial['author_name'] ); ?>
									</span>
								<?php endif; ?>
								<?php if ( ! empty( $testimonial['author_title'] ) ) : ?>
									<span class="zaso-testimonial-slider__author-title">
										<?php echo esc_html( $testimonial['author_title'] ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>

					</div><!-- .zaso-testimonial-slider__card -->
				</div><!-- .zaso-testimonial-slider__slide -->
			<?php endforeach; ?>
		</div><!-- .zaso-testimonial-slider__track -->
	</div><!-- .zaso-testimonial-slider__viewport -->

	<?php if ( $show_arrows && $count > 1 ) : ?>
		<div class="zaso-testimonial-slider__arrows" aria-hidden="true">
			<button class="zaso-testimonial-slider__arrow zaso-testimonial-slider__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'zaso' ); ?>" tabindex="-1">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="15 18 9 12 15 6"></polyline></svg>
			</button>
			<button class="zaso-testimonial-slider__arrow zaso-testimonial-slider__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'zaso' ); ?>" tabindex="-1">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"></polyline></svg>
			</button>
		</div>
	<?php endif; ?>

	<?php if ( $show_dots && $count > 1 ) : ?>
		<div class="zaso-testimonial-slider__dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial slides', 'zaso' ); ?>">
			<?php foreach ( $testimonials as $index => $testimonial ) : ?>
				<button
					class="zaso-testimonial-slider__dot<?php echo 0 === $index ? ' zaso-testimonial-slider__dot--active' : ''; ?>"
					role="tab"
					type="button"
					aria-label="<?php echo esc_attr( sprintf(
						/* translators: slide number */
						__( 'Slide %d', 'zaso' ),
						$index + 1
					) ); ?>"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					data-index="<?php echo esc_attr( $index ); ?>"
				></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div><!-- .zaso-testimonial-slider -->
