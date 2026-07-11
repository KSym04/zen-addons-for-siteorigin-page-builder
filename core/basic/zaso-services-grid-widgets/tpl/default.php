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

// Optional structural layout. Default ('default') is the original stacked card and
// adds NO extra class, so existing instances render byte-identical. Only the
// alternate layouts (boxed / icon-left / centered) add a modifier class.
$zaso_layout         = ! empty( $instance['layout'] ) ? $instance['layout'] : 'default';
$zaso_layout_allowed = array( 'default', 'boxed', 'icon-left', 'centered' );
if ( ! in_array( $zaso_layout, $zaso_layout_allowed, true ) ) {
	$zaso_layout = 'default';
}
$zaso_layout_class = ( 'default' !== $zaso_layout ) ? ' zaso-services-grid--layout-' . $zaso_layout : '';

// Optional pre-made design (the "Browse designs" gallery). Default ('') adds NO
// class so existing instances render byte-identical. The saved id is whitelisted
// against the live option list, so a Pro design saved on a site whose license has
// since lapsed (its id no longer in the list) falls back to the default look
// instead of emitting a broken, unstyled class.
$zaso_design_variant = ! empty( $instance['design_variant'] ) ? $instance['design_variant'] : '';
$zaso_design_class   = '';
if ( '' !== $zaso_design_variant && function_exists( 'zaso_services_grid_design_options' ) ) {
	$zaso_design_allowed = array_keys( zaso_services_grid_design_options() );
	if ( in_array( $zaso_design_variant, $zaso_design_allowed, true ) ) {
		$zaso_design_class = ' zaso-services-grid--design-' . sanitize_html_class( $zaso_design_variant );
	}
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $container_classes . $zaso_layout_class . $zaso_design_class ); ?>">
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
