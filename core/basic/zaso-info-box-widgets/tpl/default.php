<?php
/**
 * [ZASO] Info Box Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.8
 */
$info_image = siteorigin_widgets_get_attachment_image_src( $instance['info_image'], 'full' )[0];
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-info-box <?php echo $instance['extra_class']; ?>" role="banner">
  <div class="zaso-info-box__wrapper">
    <?php if( $instance['info_image'] ) : ?>
      <div class="zaso-info-box__image">
        <img src="<?php echo $info_image; ?>" alt="<?php echo $instance['info_title']; ?>" />
      </div>
    <?php endif; ?>

    <?php if( $instance['info_title'] || $instance['info_description'] ) : ?>
      <div class="zaso-info-box__content">
        <?php if( $instance['info_title'] ) : ?>
          <h3 class="zaso-info-box__content-title">
            <?php echo $instance['info_title']; ?>
          </h3>
        <?php endif; ?>

        <?php if( $instance['info_description'] ) : ?>
          <div class="zaso-info-box__content-description">
            <?php echo wp_kses_post( $instance['info_description'] ); ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if( $instance['info_button_text'] && $instance['info_button_url'] ) : ?>
      <div class="zaso-info-box__link">
          <a href="<?php echo sow_esc_url( $instance['info_button_url'] ) ?>"><?php echo $instance['info_button_text']; ?></a>
      </div>
    <?php endif; ?>
  </div>
</div>