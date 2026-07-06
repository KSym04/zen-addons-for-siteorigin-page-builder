<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Hover Card Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.9
 */
// Defensive: an unset / empty image field makes the SiteOrigin helper return
// false, so guarding the [0] access avoids an "array offset on false" warning.
// A real attachment yields the same URL as before (output byte-identical).
$hover_card_image_src = ! empty( $instance['hover_card_image'] )
	? siteorigin_widgets_get_attachment_image_src( $instance['hover_card_image'], 'full' )
	: false;
$hover_card_image     = ( is_array( $hover_card_image_src ) && isset( $hover_card_image_src[0] ) ) ? $hover_card_image_src[0] : '';

// Optional structural layout. Default ('default') is the original caption overlay
// and adds NO extra class, so existing instances render byte-identical. Only the
// alternate layouts (caption-below / slide-up / zoom) add a modifier class.
$zaso_layout         = ! empty( $instance['layout'] ) ? $instance['layout'] : 'default';
$zaso_layout_allowed = array( 'default', 'caption-below', 'slide-up', 'zoom' );
if ( ! in_array( $zaso_layout, $zaso_layout_allowed, true ) ) {
	$zaso_layout = 'default';
}
$zaso_wrapper_class = 'zaso-hover-card';
if ( 'default' !== $zaso_layout ) {
	$zaso_wrapper_class .= ' zaso-hover-card--layout-' . $zaso_layout;
}

// Optional pre-made design. Empty ('') is the classic look and adds NO class, so
// existing instances (which have no design_variant key) render byte-identical.
// The value is whitelisted against the live design list, so a Pro design saved on
// a now-unlicensed site (where the Pro filter no longer registers it) falls back
// to the default render instead of emitting an unstyled Pro class.
$zaso_design_variant = ! empty( $instance['design_variant'] ) ? $instance['design_variant'] : '';
if ( '' !== $zaso_design_variant && function_exists( 'zaso_hover_card_design_options' ) ) {
	$zaso_design_allowed = array_keys( zaso_hover_card_design_options() );
	if ( in_array( $zaso_design_variant, $zaso_design_allowed, true ) ) {
		$zaso_wrapper_class .= ' zaso-hover-card--design-' . sanitize_html_class( $zaso_design_variant );
	}
}
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $zaso_wrapper_class ); ?> <?php echo esc_attr( $instance['extra_class'] ); ?>">
  <div class="zaso-hover-card__box">

    <div class="zaso-hover-card__media">
      <?php // The image is painted as the card background via JS; this element is only a src source (visibility:hidden), so it is decorative (alt=""). The card's meaning is exposed via the visible title heading. ?>
      <img src="<?php echo esc_url( $hover_card_image ); ?>" alt="" aria-hidden="true" />
    </div>

    <div class="zaso-hover-card__caption zaso-hover-card__caption--<?php echo esc_attr( $instance['hover_card_animation'] ); ?>">
      <h3 class="zaso-hover-card__caption-title"><?php echo esc_html( $instance['hover_card_title'] ); ?></h3>
    </div>

    <div class="zaso-hover-card__modal zaso-hover-card__modal--<?php echo esc_attr( $instance['hover_card_animation'] ); ?>">
      <?php // Not a heading: the always-visible caption already provides the card's <h3>; repeating it here as a heading would create duplicate headings for screen readers. ?>
      <div class="zaso-hover-card__modal-title"><?php echo esc_html( $instance['hover_card_title'] ); ?></div>

      <?php if( $instance['hover_card_text_content'] ) : ?>
        <?php echo wp_kses_post( $instance['hover_card_text_content'] ); ?>
      <?php endif; ?>

      <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
      <a class="zaso-hover-card__modal-action" href="<?php echo sow_esc_url( $instance['hover_card_action_url'] ) ?>">
        <?php echo esc_html( $instance['hover_card_action_text'] ); ?>
      </a>
    </div><!-- .zaso-hover-card__modal -->

  </div><!-- .zaso-hover-card__box -->
</div><!-- .zaso-hover-card -->