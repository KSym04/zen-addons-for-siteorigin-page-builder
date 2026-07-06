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
 * @var string $agg_rating        Optional headline score for the "Stat highlight" designs.
 * @var string $agg_rating_label  Optional caption under the score.
 * @var string $classes           Root element class string.
 *
 * Also available directly:
 * @var array  $instance          Full widget instance.
 * @var array  $args              Widget sidebar args.
 */

if ( empty( $testimonials ) ) {
	return;
}

// Optional structural layout. Default ('default') is the original simple card and
// adds NO extra class, so existing instances render byte-identical. Only the
// alternate layouts (card / quote / minimal) add a modifier class.
$zaso_layout         = ! empty( $instance['layout'] ) ? $instance['layout'] : 'default';
$zaso_layout_allowed = array( 'default', 'card', 'quote', 'minimal' );
if ( ! in_array( $zaso_layout, $zaso_layout_allowed, true ) ) {
	$zaso_layout = 'default';
}
$zaso_root_class = $classes;
if ( 'default' !== $zaso_layout ) {
	$zaso_root_class .= ' zaso-testimonial-slider--layout-' . $zaso_layout;
}

// Optional pre-made design. The default ('') adds NO class, so every existing
// instance (which lacks the key) renders byte-identical through the original card
// path below. A recognised design id appends a modifier AND switches this template
// to the richer, fully-skinned card path (SVG stars, split avatar, footer nav,
// optional company/stat blocks) that the design stylesheets style. Free ships six
// designs; Zen Addons Pro appends its twenty-four via the shared
// `zaso_testimonial_slider_designs` filter, so an unknown or unlicensed id is
// rejected here and falls back to the default look.
$zaso_design_variant = ! empty( $instance['design_variant'] ) ? $instance['design_variant'] : '';
$zaso_has_skin       = false;
if ( '' !== $zaso_design_variant && function_exists( 'zaso_testimonial_slider_design_options' ) ) {
	$zaso_design_allowed = array_keys( zaso_testimonial_slider_design_options() );
	if ( in_array( $zaso_design_variant, $zaso_design_allowed, true ) ) {
		$zaso_root_class .= ' zaso-testimonial-slider--design-' . sanitize_html_class( $zaso_design_variant );
		$zaso_has_skin    = true;
	}
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?>
	class="<?php echo esc_attr( $zaso_root_class ); ?>"
	role="region"
	aria-label="<?php esc_attr_e( 'Testimonials', 'zaso' ); ?>"
	data-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"
	data-duration="<?php echo esc_attr( $autoplay_duration ); ?>"
	data-count="<?php echo esc_attr( $count ); ?>"
>

	<div class="zaso-testimonial-slider__viewport">
		<div class="zaso-testimonial-slider__track">
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

						<?php
						// Clamp defensively so the star markup can never receive a negative count.
						$zaso_stars = max( 0, min( 5, (int) $testimonial['rating'] ) );
						?>

						<?php if ( $zaso_has_skin ) : ?>

							<?php // ── Skinned card path ─────────────────────────────────────── ?>
							<?php if ( ! empty( $testimonial['company_name'] ) ) : ?>
								<div class="zaso-testimonial-slider__company">
									<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from zaso_testimonial_slider_icon(). ?>
									<span class="zaso-testimonial-slider__logo"><?php echo zaso_testimonial_slider_icon( 'hub' ); ?></span>
									<span class="zaso-testimonial-slider__company-name"><?php echo esc_html( $testimonial['company_name'] ); ?></span>
								</div>
							<?php endif; ?>

							<div class="zaso-testimonial-slider__badge">
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from zaso_testimonial_slider_icon(). ?>
								<?php echo zaso_testimonial_slider_icon( 'verified' ); ?>
								<span><?php esc_html_e( 'Verified', 'zaso' ); ?></span>
							</div>

							<?php if ( '' !== $agg_rating ) : ?>
								<div class="zaso-testimonial-slider__stat">
									<span class="zaso-testimonial-slider__stat-num"><?php echo esc_html( $agg_rating ); ?></span>
									<span class="zaso-testimonial-slider__stat-stars" role="img" aria-label="<?php echo esc_attr( $testimonial['rating_label'] ); ?>">
										<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from zaso_testimonial_slider_stars(). ?>
										<?php echo zaso_testimonial_slider_stars( 5 ); ?>
									</span>
									<?php if ( '' !== $agg_rating_label ) : ?>
										<span class="zaso-testimonial-slider__stat-label"><?php echo esc_html( $agg_rating_label ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( $zaso_stars > 0 ) : ?>
								<div class="zaso-testimonial-slider__rating" role="img" aria-label="<?php echo esc_attr( $testimonial['rating_label'] ); ?>">
									<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from zaso_testimonial_slider_stars(). ?>
									<?php echo zaso_testimonial_slider_stars( $zaso_stars ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $testimonial['quote'] ) ) : ?>
								<blockquote class="zaso-testimonial-slider__quote">
									<p><?php echo esc_html( $testimonial['quote'] ); ?></p>
								</blockquote>
							<?php endif; ?>

							<div class="zaso-testimonial-slider__person">
								<div class="zaso-testimonial-slider__avatar">
									<?php if ( ! empty( $testimonial['photo_src'] ) ) : ?>
										<img
											class="zaso-testimonial-slider__author-photo"
											src="<?php echo esc_url( $testimonial['photo_src'] ); ?>"
											alt="<?php echo esc_attr( $testimonial['photo_alt'] ); ?>"
											loading="lazy"
											decoding="async"
										/>
									<?php else : ?>
										<span class="zaso-testimonial-slider__initials" aria-hidden="true"><?php echo esc_html( zaso_testimonial_slider_initials( $testimonial['author_name'] ) ); ?></span>
									<?php endif; ?>
								</div>
								<div class="zaso-testimonial-slider__authorinfo">
									<?php if ( ! empty( $testimonial['author_name'] ) ) : ?>
										<span class="zaso-testimonial-slider__author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $testimonial['author_title'] ) ) : ?>
										<span class="zaso-testimonial-slider__author-title"><?php echo esc_html( $testimonial['author_title'] ); ?></span>
									<?php endif; ?>
								</div>
							</div>

						<?php else : ?>

							<?php // ── Original card path (byte-identical for existing instances) ── ?>
							<?php if ( $zaso_stars > 0 ) : ?>
								<div class="zaso-testimonial-slider__rating">
									<span role="img" aria-label="<?php echo esc_attr( $testimonial['rating_label'] ); ?>">
										<span aria-hidden="true">
											<?php echo esc_html( str_repeat( '★', $zaso_stars ) . str_repeat( '☆', 5 - $zaso_stars ) ); ?>
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

						<?php endif; ?>

					</div><!-- .zaso-testimonial-slider__card -->
				</div><!-- .zaso-testimonial-slider__slide -->
			<?php endforeach; ?>
		</div><!-- .zaso-testimonial-slider__track -->
	</div><!-- .zaso-testimonial-slider__viewport -->

	<?php if ( $autoplay && $count > 1 ) : ?>
		<button
			class="zaso-testimonial-slider__playpause"
			type="button"
			aria-label="<?php esc_attr_e( 'Pause testimonials', 'zaso' ); ?>"
			data-label-pause="<?php esc_attr_e( 'Pause testimonials', 'zaso' ); ?>"
			data-label-play="<?php esc_attr_e( 'Play testimonials', 'zaso' ); ?>"
		>
			<svg class="zaso-testimonial-slider__pp-icon zaso-testimonial-slider__pp-icon--pause" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><rect x="6" y="5" width="4" height="14" rx="1"></rect><rect x="14" y="5" width="4" height="14" rx="1"></rect></svg>
			<svg class="zaso-testimonial-slider__pp-icon zaso-testimonial-slider__pp-icon--play" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z"></path></svg>
		</button>
	<?php endif; ?>

	<?php
	$zaso_show_arrows = ( $show_arrows && $count > 1 );
	$zaso_show_dots   = ( $show_dots && $count > 1 );
	?>

	<?php if ( $zaso_has_skin && ( $zaso_show_arrows || $zaso_show_dots ) ) : ?>

		<?php // Skinned footer: dots (left) + a slide counter + arrows (right), styled per design. ?>
		<div class="zaso-testimonial-slider__footer">
			<?php if ( $zaso_show_dots ) : ?>
				<div class="zaso-testimonial-slider__dots" aria-label="<?php esc_attr_e( 'Testimonial slides', 'zaso' ); ?>">
					<?php foreach ( $testimonials as $index => $testimonial ) : ?>
						<button
							class="zaso-testimonial-slider__dot<?php echo 0 === $index ? ' zaso-testimonial-slider__dot--active' : ''; ?>"
							type="button"
							aria-label="<?php echo esc_attr( sprintf(
								/* translators: testimonial number */
								__( 'Go to testimonial %d', 'zaso' ),
								$index + 1
							) ); ?>"
							<?php echo 0 === $index ? 'aria-current="true"' : ''; ?>
							data-index="<?php echo esc_attr( $index ); ?>"
						></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<span class="zaso-testimonial-slider__counter" aria-hidden="true"><span class="zaso-testimonial-slider__counter-current">1</span> / <?php echo esc_html( $count ); ?></span>

			<?php if ( $zaso_show_arrows ) : ?>
				<div class="zaso-testimonial-slider__arrows" aria-hidden="true">
					<button class="zaso-testimonial-slider__arrow zaso-testimonial-slider__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'zaso' ); ?>" tabindex="-1">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from zaso_testimonial_slider_icon(). ?>
						<?php echo zaso_testimonial_slider_icon( 'chevron_left' ); ?>
					</button>
					<button class="zaso-testimonial-slider__arrow zaso-testimonial-slider__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'zaso' ); ?>" tabindex="-1">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from zaso_testimonial_slider_icon(). ?>
						<?php echo zaso_testimonial_slider_icon( 'chevron_right' ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div><!-- .zaso-testimonial-slider__footer -->

	<?php else : ?>

		<?php // Original nav path (byte-identical for existing instances). ?>
		<?php if ( $zaso_show_arrows ) : ?>
			<div class="zaso-testimonial-slider__arrows" aria-hidden="true">
				<button class="zaso-testimonial-slider__arrow zaso-testimonial-slider__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'zaso' ); ?>" tabindex="-1">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="15 18 9 12 15 6"></polyline></svg>
				</button>
				<button class="zaso-testimonial-slider__arrow zaso-testimonial-slider__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'zaso' ); ?>" tabindex="-1">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"></polyline></svg>
				</button>
			</div>
		<?php endif; ?>

		<?php if ( $zaso_show_dots ) : ?>
			<div class="zaso-testimonial-slider__dots" aria-label="<?php esc_attr_e( 'Testimonial slides', 'zaso' ); ?>">
				<?php foreach ( $testimonials as $index => $testimonial ) : ?>
					<button
						class="zaso-testimonial-slider__dot<?php echo 0 === $index ? ' zaso-testimonial-slider__dot--active' : ''; ?>"
						type="button"
						aria-label="<?php echo esc_attr( sprintf(
							/* translators: testimonial number */
							__( 'Go to testimonial %d', 'zaso' ),
							$index + 1
						) ); ?>"
						<?php echo 0 === $index ? 'aria-current="true"' : ''; ?>
						data-index="<?php echo esc_attr( $index ); ?>"
					></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

</div><!-- .zaso-testimonial-slider -->
