<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Hover Card Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.9
 */
$hover_card_image = siteorigin_widgets_get_attachment_image_src( $instance['hover_card_image'], 'full' )[0];
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-hover-card <?php echo esc_attr( $instance['extra_class'] ); ?>">
  <div class="zaso-hover-card__box">

    <div class="zaso-hover-card__media">
      <img src="<?php echo esc_url( $hover_card_image ); ?>" alt="<?php echo esc_attr( $instance['hover_card_title'] ); ?>" />
    </div>

    <div class="zaso-hover-card__caption zaso-hover-card__caption--<?php echo esc_attr( $instance['hover_card_animation'] ); ?>">
      <h3 class="zaso-hover-card__caption-title"><?php echo esc_html( $instance['hover_card_title'] ); ?></h3>
    </div>

    <div class="zaso-hover-card__modal zaso-hover-card__modal--<?php echo esc_attr( $instance['hover_card_animation'] ); ?>">
      <h3 class="zaso-hover-card__modal-title"><?php echo esc_html( $instance['hover_card_title'] ); ?></h3>

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