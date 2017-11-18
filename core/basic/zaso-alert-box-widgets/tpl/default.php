<?php
/**
 * [ZASO] Alert Box Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.3
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-alert-box <?php echo $instance['extra_class']; ?>">
  <div class="zaso-alert-box__messagebox" role="alert">
    <?php if( $instance['alert_closebtn'] == 'show' ) : ?>
      <button type="button" class="zaso-alert-box__closebtn" data-dismiss="alert" aria-label="<?php _e( 'Close', 'zaso' ); ?>">
        <span aria-hidden="true"><?php _e( '&times;', 'zaso' ); ?></span>
      </button>
    <?php endif; ?>
    <?php echo wp_kses_post( $instance['alert_message'] ); ?>
  </div>
</div>