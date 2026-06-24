<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Image Gallery — default template.
 */

if ( empty( $images ) ) {
	return;
}
?>
<ul <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?> class="<?php echo esc_attr( $container_classes ); ?>" role="list">
	<?php foreach ( $images as $image ) : ?>
		<li class="zaso-image-gallery__item">
			<figure class="zaso-image-gallery__figure">
				<?php if ( $lightbox ) : ?>
					<a class="zaso-image-gallery__link" href="<?php echo esc_url( $image['full_src'] ); ?>" data-lity aria-label="<?php echo esc_attr( $image['alt'] ? $image['alt'] : __( 'View image', 'zaso' ) ); ?>">
				<?php else : ?>
					<span class="zaso-image-gallery__link">
				<?php endif; ?>
					<img class="zaso-image-gallery__img"
						src="<?php echo esc_url( $image['thumb_src'] ); ?>"
						alt="<?php echo esc_attr( $image['alt'] ); ?>"
						<?php if ( '' !== (string) $image['thumb_width'] ) : ?>width="<?php echo absint( $image['thumb_width'] ); ?>"<?php endif; ?>
						<?php if ( '' !== (string) $image['thumb_height'] ) : ?>height="<?php echo absint( $image['thumb_height'] ); ?>"<?php endif; ?>
						loading="lazy"
						decoding="async"
					/>
					<?php if ( $lightbox ) : ?><span class="zaso-image-gallery__overlay" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg></span><?php endif; ?>
				<?php if ( $lightbox ) : ?>
					</a>
				<?php else : ?>
					</span>
				<?php endif; ?>
				<?php if ( ! empty( $image['caption'] ) ) : ?>
					<figcaption class="zaso-image-gallery__caption"><?php echo esc_html( $image['caption'] ); ?></figcaption>
				<?php endif; ?>
			</figure>
		</li>
	<?php endforeach; ?>
</ul>
