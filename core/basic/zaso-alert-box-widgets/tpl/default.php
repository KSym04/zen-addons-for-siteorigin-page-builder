<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Alert Box Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.3
 */
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-alert-box <?php echo esc_attr( $instance['extra_class'] ); ?>">
  <div class="zaso-alert-box__messagebox" role="alert">
    <?php if( $instance['alert_closebtn'] == 'show' ) : ?>
      <button type="button" class="zaso-alert-box__closebtn" data-dismiss="alert" aria-label="<?php esc_attr_e( 'Close', 'zaso' ); ?>">
        <span aria-hidden="true"><?php esc_html_e( '&times;', 'zaso' ); ?></span>
      </button>
    <?php endif; ?>
    <?php echo wp_kses_post( $instance['alert_message'] ); ?>
  </div>
</div>