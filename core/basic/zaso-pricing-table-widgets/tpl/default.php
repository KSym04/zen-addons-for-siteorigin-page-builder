<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Pricing Table Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.7.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $plans    Processed plans list.
 * @var string $currency Currency symbol.
 * @var string $classes  Root element class string.
 *
 * Also available directly:
 * @var array  $instance Full widget instance.
 * @var array  $args     Widget sidebar args.
 */

if ( empty( $plans ) ) {
	return;
}

$checkmark_svg = '<svg aria-hidden="true" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<ul <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
	class="<?php echo esc_attr( $classes ); ?>"
	role="list">
	<?php foreach ( $plans as $plan ) : ?>
	<li class="zaso-pricing-table__item<?php echo $plan['featured'] ? ' zaso-pricing-table__item--featured' : ''; ?>">
		<div class="zaso-pricing-table__card">
			<div class="zaso-pricing-table__name"><?php echo esc_html( $plan['name'] ); ?></div>
			<div class="zaso-pricing-table__price-wrap">
				<?php if ( '' !== $currency ) : ?>
				<span class="zaso-pricing-table__currency"><?php echo esc_html( $currency ); ?></span>
				<?php endif; ?>
				<span class="zaso-pricing-table__price"><?php echo esc_html( $plan['price'] ); ?></span>
				<?php if ( '' !== $plan['period'] ) : ?>
				<span class="zaso-pricing-table__period"><?php echo esc_html( $plan['period'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php if ( '' !== $plan['description'] ) : ?>
			<p class="zaso-pricing-table__description"><?php echo esc_html( $plan['description'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $plan['features'] ) ) : ?>
			<ul class="zaso-pricing-table__features">
				<?php foreach ( $plan['features'] as $feature ) : ?>
				<li>
					<?php echo $checkmark_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded static SVG string, no user input. ?>
					<?php echo esc_html( $feature ); ?>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
			<?php if ( '' !== $plan['cta_text'] ) : ?>
			<a class="zaso-pricing-table__btn"
				href="<?php echo sow_esc_url( $plan['cta_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's vetted URL escaper. ?>"
				<?php echo $plan['cta_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( $plan['cta_text'] ); ?>
			</a>
			<?php endif; ?>
		</div>
	</li>
	<?php endforeach; ?>
</ul>
