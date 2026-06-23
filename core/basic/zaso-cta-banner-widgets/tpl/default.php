<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Call to Action Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.3.0
 */

$layout    = in_array( $instance['layout'], array( 'stacked', 'inline' ), true ) ? $instance['layout'] : 'stacked';
$alignment = in_array( $instance['alignment'], array( 'left', 'center', 'right' ), true ) ? $instance['alignment'] : 'center';

// Use the heading as the region's accessible name; fall back to a generic label.
$region_label = '' !== trim( (string) $instance['heading'] ) ? $instance['heading'] : __( 'Call to action', 'zaso' );

// Button rel/target attributes.
$button_rel = array();
if ( ! empty( $instance['button_new_tab'] ) ) {
	$button_rel[] = 'noopener';
	$button_rel[] = 'noreferrer';
}
if ( ! empty( $instance['button_nofollow'] ) ) {
	$button_rel[] = 'nofollow';
}

// Inline background image style for the "image" background type.
$inline_style = '';
if ( 'image' === $bg_type && ! empty( $bg_image_url ) ) {
	$inline_style = ' style="background-image:url(' . esc_url( $bg_image_url ) . ');"';
}
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<section <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-cta-banner zaso-cta-banner--<?php echo esc_attr( $layout ); ?> zaso-cta-banner--align-<?php echo esc_attr( $alignment ); ?> zaso-cta-banner--bg-<?php echo esc_attr( $bg_type ); ?> <?php echo esc_attr( $instance['extra_class'] ); ?>" role="region" aria-label="<?php echo esc_attr( $region_label ); ?>"<?php echo $inline_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() applied above. ?>>

	<?php if ( 'image' === $bg_type && ! empty( $bg_image_url ) ) : ?>
		<span class="zaso-cta-banner__overlay" aria-hidden="true"></span>
	<?php endif; ?>

	<div class="zaso-cta-banner__inner">

		<div class="zaso-cta-banner__content">
			<?php if ( '' !== trim( (string) $instance['heading'] ) ) : ?>
				<h2 class="zaso-cta-banner__heading"><?php echo esc_html( $instance['heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $instance['subheading'] ) ) : ?>
				<p class="zaso-cta-banner__subheading"><?php echo esc_html( $instance['subheading'] ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $instance['content'] ) ) : ?>
				<div class="zaso-cta-banner__text"><?php echo wp_kses_post( $instance['content'] ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( '' !== trim( (string) $instance['button_text'] ) && ! empty( $instance['button_url'] ) ) : ?>
			<div class="zaso-cta-banner__action">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
				<a class="zaso-cta-banner__button" href="<?php echo sow_esc_url( $instance['button_url'] ); ?>"<?php
					if ( ! empty( $instance['button_new_tab'] ) ) {
						echo ' target="_blank"';
					}
					if ( ! empty( $button_rel ) ) {
						echo ' rel="' . esc_attr( implode( ' ', $button_rel ) ) . '"';
					}
				?>><?php echo esc_html( $instance['button_text'] ); ?></a>
			</div>
		<?php endif; ?>

	</div>
</section>
