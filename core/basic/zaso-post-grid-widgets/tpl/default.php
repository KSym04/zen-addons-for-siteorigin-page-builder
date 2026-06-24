<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Post Grid Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.8.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $query_args        WP_Query args (already capped/sanitized).
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
	class="<?php echo esc_attr( $container_classes ); ?>">
	<div class="zaso-post-grid__grid">
		<?php
		while ( $zaso_q->have_posts() ) :
			$zaso_q->the_post();
			?>
			<article class="zaso-post-grid__card">
				<?php if ( $show_image && has_post_thumbnail() ) : ?>
					<a class="zaso-post-grid__image" href="<?php echo esc_url( get_permalink() ); ?>">
						<?php
						echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() returns escaped core markup.
							get_the_ID(),
							$image_size,
							array(
								'class'   => 'zaso-post-grid__img',
								'loading' => 'lazy',
							)
						);
						?>
					</a>
				<?php endif; ?>
				<div class="zaso-post-grid__body">
					<h3 class="zaso-post-grid__title">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					</h3>
					<?php if ( $show_date || $show_author ) : ?>
						<div class="zaso-post-grid__meta">
							<?php if ( $show_date ) : ?>
								<time class="zaso-post-grid__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<?php endif; ?>
							<?php if ( $show_date && $show_author ) : ?>
								<span class="zaso-post-grid__sep" aria-hidden="true">&middot;</span>
							<?php endif; ?>
							<?php if ( $show_author ) : ?>
								<span class="zaso-post-grid__author"><?php echo esc_html( get_the_author() ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $show_excerpt ) : ?>
						<p class="zaso-post-grid__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length, '...' ) ); ?></p>
					<?php endif; ?>
					<?php if ( $show_readmore && '' !== trim( $readmore_text ) ) : ?>
						<a class="zaso-post-grid__readmore" href="<?php echo esc_url( get_permalink() ); ?>">
							<?php echo esc_html( $readmore_text ); ?>
							<span aria-hidden="true">&rarr;</span>
						</a>
					<?php endif; ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>
</div>
<?php
wp_reset_postdata();
