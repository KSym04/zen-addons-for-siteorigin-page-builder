<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Services Grid Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.5.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $services          Processed service list.
 * @var string $container_classes Space-separated class string.
 *
 * Also available directly from $instance:
 * @var array  $instance          Full widget instance.
 * @var array  $args              Widget sidebar args.
 */

if ( empty( $services ) ) {
	return;
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $container_classes ); ?>">
	<ul class="zaso-services-grid__grid" role="list">
		<?php foreach ( $services as $service ) : ?>
			<li class="zaso-services-grid__item">
				<article class="zaso-services-grid__card">

					<?php if ( ! empty( $service['image_attr']['src'] ) ) : ?>
						<div class="zaso-services-grid__icon">
							<img class="zaso-services-grid__icon-image"<?php foreach ( $service['image_attr'] as $n => $v ) { if ( '' !== (string) $v ) { echo ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"'; } } ?> loading="lazy" decoding="async" />
						</div>
					<?php elseif ( ! empty( $service['icon'] ) ) : ?>
						<div class="zaso-services-grid__icon">
							<?php echo siteorigin_widget_get_icon( $service['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- siteorigin_widget_get_icon() returns SiteOrigin-generated, safe markup. ?>
						</div>
					<?php endif; ?>

					<div class="zaso-services-grid__body">
						<?php if ( ! empty( $service['title'] ) ) : ?>
							<h3 class="zaso-services-grid__title"><?php echo esc_html( $service['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $service['description'] ) ) : ?>
							<p class="zaso-services-grid__description"><?php echo esc_html( $service['description'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $service['has_link'] ) ) : ?>
							<a
								class="zaso-services-grid__link"
								href="<?php echo sow_esc_url( $service['link_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's URL escaper. ?>"
								<?php if ( ! empty( $service['link_new_tab'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
							><?php echo esc_html( $service['link_text'] ); ?></a>
						<?php endif; ?>
					</div>

				</article>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
