<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Flip Card Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.9.0
 *
 * Available variables (set by get_template_variables):
 * @var string $image_src      Resolved front image URL (may be empty).
 * @var string $front_title    Front title text.
 * @var string $front_subtitle Front subtitle text.
 * @var string $back_heading   Back heading text.
 * @var string $back_text      Back body text (plain, wp_kses_post on output).
 * @var string $button_text    Back call-to-action label.
 * @var string $button_url     Back call-to-action URL.
 * @var string $classes        Root element class string.
 *
 * Also available directly:
 * @var array  $instance       Full widget instance.
 * @var array  $args           Widget sidebar args.
 */
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $classes ); ?>">
	<div class="zaso-flip-card__inner" tabindex="0">

		<div class="zaso-flip-card__face zaso-flip-card__front"<?php
			if ( $image_src ) {
				echo ' style="background-image:url(' . esc_url( $image_src ) . ');"';
			}
		?>>
			<div class="zaso-flip-card__front-content">
				<?php if ( '' !== $front_title ) : ?>
					<h3 class="zaso-flip-card__title"><?php echo esc_html( $front_title ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== $front_subtitle ) : ?>
					<p class="zaso-flip-card__subtitle"><?php echo esc_html( $front_subtitle ); ?></p>
				<?php endif; ?>
			</div>
		</div><!-- .zaso-flip-card__front -->

		<div class="zaso-flip-card__face zaso-flip-card__back">
			<div class="zaso-flip-card__back-content">
				<?php if ( '' !== $back_heading ) : ?>
					<h4 class="zaso-flip-card__back-heading"><?php echo esc_html( $back_heading ); ?></h4>
				<?php endif; ?>
				<?php if ( '' !== $back_text ) : ?>
					<div class="zaso-flip-card__back-text"><?php echo wp_kses_post( wpautop( $back_text ) ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $button_text ) : ?>
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
					<a class="zaso-flip-card__button" href="<?php echo sow_esc_url( $button_url ); ?>"><?php echo esc_html( $button_text ); ?></a>
				<?php endif; ?>
			</div>
		</div><!-- .zaso-flip-card__back -->

	</div><!-- .zaso-flip-card__inner -->
</div><!-- .zaso-flip-card -->
