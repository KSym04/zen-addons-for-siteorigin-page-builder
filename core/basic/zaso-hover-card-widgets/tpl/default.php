<?php
/**
 * [ZASO] Hover Card Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.9
 */
$hover_card_image = siteorigin_widgets_get_attachment_image_src( $instance['hover_card_image'], 'full' )[0];
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-hover-card <?php echo $instance['extra_class']; ?>">
  <div class="zaso-hover-card__box" role="figure">

    <div class="zaso-hover-card__media">
      <img src="<?php echo $hover_card_image; ?>" alt="<?php echo $instance['hover_card_title']; ?>" />
    </div>

    <div class="zaso-hover-card__caption zaso-hover-card__caption--<?php echo $instance['hover_card_animation']; ?>">
      <h3 class="zaso-hover-card__caption-title"><?php echo $instance['hover_card_title']; ?></h3>
    </div>

    <div class="zaso-hover-card__modal zaso-hover-card__modal--<?php echo $instance['hover_card_animation']; ?>">
      <h3 class="zaso-hover-card__modal-title"><?php echo $instance['hover_card_title']; ?></h3>
      <a class="zaso-hover-card__modal-action" href="<?php echo sow_esc_url( $instance['hover_card_action_url'] ) ?>">
        <?php echo $instance['hover_card_action_text']; ?>
      </a>
    </div><!-- .zaso-hover-card__modal -->

  </div><!-- .zaso-hover-card__box -->
</div><!-- .zaso-hover-card -->