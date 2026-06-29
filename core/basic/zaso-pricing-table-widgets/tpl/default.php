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

// Optional structural layout. Default ('default') is the original bordered cards
// and adds NO extra class, so existing instances render byte-identical. Only the
// alternate layouts (bordered / elevated / compact) append a modifier class to
// the root list. The Style skin still drives all colours independently.
$zaso_layout         = ! empty( $instance['layout'] ) ? $instance['layout'] : 'default';
$zaso_layout_allowed = array( 'default', 'bordered', 'elevated', 'compact' );
if ( ! in_array( $zaso_layout, $zaso_layout_allowed, true ) ) {
	$zaso_layout = 'default';
}
$zaso_table_classes = $classes;
if ( 'default' !== $zaso_layout ) {
	$zaso_table_classes .= ' zaso-pricing-table--layout-' . $zaso_layout;
}
?>
<?php
/**
 * Fires before the pricing grid. Pro hooks this to render the billing toggle.
 * No output in free core (no callback attached).
 */
do_action( 'zaso_pricing_table_before_plans', $instance, $plans );
?>
<ul <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
	class="<?php echo esc_attr( $zaso_table_classes ); ?>"
	role="list">
	<?php foreach ( $plans as $plan ) : ?>
	<li class="zaso-pricing-table__item<?php echo $plan['featured'] ? ' zaso-pricing-table__item--featured' : ''; ?>">
		<div class="zaso-pricing-table__card">
			<?php do_action( 'zaso_pricing_table_plan_meta', $plan, $instance ); /* Pro hook: ribbon + annual data. Inlined on the existing tag so free output stays byte-identical. */ if ( $plan['featured'] ) : ?><span class="screen-reader-text"><?php esc_html_e( 'Featured plan', 'zaso' ); ?></span><?php endif; ?>
				<h3 class="zaso-pricing-table__name"><?php echo esc_html( $plan['name'] ); ?></h3>
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
			<?php
			ob_start();
			if ( ! empty( $plan['features'] ) ) :
			?>
			<ul class="zaso-pricing-table__features">
				<?php foreach ( $plan['features'] as $feature ) : ?>
				<li>
					<?php echo $checkmark_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded static SVG string, no user input. ?>
					<?php echo esc_html( $feature ); ?>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php
			endif;
			$zaso_features_html = ob_get_clean();
			/**
			 * Filter the rendered features list. Pro hooks this to swap in a
			 * comparison (check / cross) list. Default returns the core markup.
			 */
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- default is pre-escaped template markup; the Pro callback escapes its own output.
			echo apply_filters( 'zaso_pricing_table_features_render', $zaso_features_html, $plan, $instance );
			?>
			<?php if ( '' !== $plan['cta_text'] ) : ?>
			<a class="zaso-pricing-table__btn"
				href="<?php echo sow_esc_url( $plan['cta_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's vetted URL escaper. ?>"
				<?php echo $plan['cta_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( $plan['cta_text'] ); ?><?php if ( $plan['cta_new_tab'] ) : ?><span class="screen-reader-text"><?php esc_html_e( '(opens in new tab)', 'zaso' ); ?></span><?php endif; ?>
			</a>
			<?php endif; ?>
		</div>
	</li>
	<?php endforeach; ?>
</ul>
