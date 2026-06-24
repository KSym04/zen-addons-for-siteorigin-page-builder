<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Post Carousel Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.8.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $query_args        WP_Query args (already capped/sanitized).
 * @var int    $slides_to_show    Slides visible on desktop (1-4).
 * @var bool   $autoplay          Whether autoplay is enabled.
 * @var int    $autoplay_speed    Autoplay interval in ms.
 * @var bool   $show_arrows       Whether to render prev/next arrows.
 * @var bool   $show_dots         Whether to render pagination dots.
 * @var bool   $show_image        Whether to render the featured image.
 * @var string $image_size        Registered image size for the thumbnail.
 * @var bool   $show_date         Whether to render the post date.
 * @var bool   $show_author       Whether to render the author.
 * @var bool   $show_excerpt      Whether to render the excerpt.
 * @var int    $excerpt_length    Excerpt word count.
 * @var bool   $show_readmore     Whether to render the read-more link.
 * @var string $readmore_text     Read-more link text.
 * @var string $container_classes Root element class string.
 *
 * Also available directly:
 * @var array  $instance          Full widget instance.
 * @var array  $args              Widget sidebar args.
 */

$zaso_q = new WP_Query( $query_args );

if ( ! $zaso_q->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
	class="<?php echo esc_attr( $container_classes ); ?>"
	data-slides="<?php echo esc_attr( $slides_to_show ); ?>"
	data-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"
	data-speed="<?php echo esc_attr( $autoplay_speed ); ?>"
	data-arrows="<?php echo $show_arrows ? '1' : '0'; ?>"
	data-dots="<?php echo $show_dots ? '1' : '0'; ?>">
	<div class="zaso-post-carousel__viewport">
		<div class="zaso-post-carousel__track">
			<?php
			while ( $zaso_q->have_posts() ) :
				$zaso_q->the_post();
				?>
				<div class="zaso-post-carousel__slide">
					<article class="zaso-post-carousel__card">
						<?php if ( $show_image && has_post_thumbnail() ) : ?>
							<a class="zaso-post-carousel__image" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() returns escaped core markup.
									get_the_ID(),
									$image_size,
									array(
										'class'   => 'zaso-post-carousel__img',
										'loading' => 'lazy',
									)
								);
								?>
							</a>
						<?php endif; ?>
						<div class="zaso-post-carousel__body">
							<h3 class="zaso-post-carousel__title">
								<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
							</h3>
							<?php if ( $show_date || $show_author ) : ?>
								<div class="zaso-post-carousel__meta">
									<?php if ( $show_date ) : ?>
										<time class="zaso-post-carousel__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									<?php endif; ?>
									<?php if ( $show_date && $show_author ) : ?>
										<span class="zaso-post-carousel__sep" aria-hidden="true">&middot;</span>
									<?php endif; ?>
									<?php if ( $show_author ) : ?>
										<span class="zaso-post-carousel__author"><?php echo esc_html( get_the_author() ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( $show_excerpt ) : ?>
								<p class="zaso-post-carousel__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length, '...' ) ); ?></p>
							<?php endif; ?>
							<?php if ( $show_readmore && '' !== trim( $readmore_text ) ) : ?>
								<a class="zaso-post-carousel__readmore" href="<?php echo esc_url( get_permalink() ); ?>">
									<?php echo esc_html( $readmore_text ); ?>
									<span aria-hidden="true">&rarr;</span>
								</a>
							<?php endif; ?>
						</div>
					</article>
				</div>
				<?php
			endwhile;
			?>
		</div>
	</div>
	<?php if ( $show_arrows ) : ?>
		<button type="button" class="zaso-post-carousel__arrow zaso-post-carousel__arrow--prev" aria-label="<?php esc_attr_e( 'Previous', 'zaso' ); ?>">
			<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</button>
		<button type="button" class="zaso-post-carousel__arrow zaso-post-carousel__arrow--next" aria-label="<?php esc_attr_e( 'Next', 'zaso' ); ?>">
			<svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</button>
	<?php endif; ?>
	<?php if ( $show_dots ) : ?>
		<div class="zaso-post-carousel__dots" role="tablist" aria-label="<?php esc_attr_e( 'Carousel pagination', 'zaso' ); ?>"></div>
	<?php endif; ?>
</div>
<?php
wp_reset_postdata();
