<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Simple Accordion Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */

$zaso_aria_level = 3; // Fixed, sensible heading depth (item count is not a heading level).
$zaso_aria_control = 1;
// Per-instance unique id prefix so aria-controls/labelledby never collide across
// multiple accordion instances on the same page.
$zaso_acc_uid = ! empty( $args['widget_id'] ) ? sanitize_html_class( $args['widget_id'] ) : uniqid( 'zaso-acc-' );

$zacc_collapsible_icon_open = wp_get_attachment_image_src( $instance['accordion_collapsible_icon_open'], 'full' )[0];
$zacc_collapsible_icon_close = wp_get_attachment_image_src( $instance['accordion_collapsible_icon_close'], 'full' )[0];

?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<dl <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-simple-accordion <?php echo esc_attr( $instance['extra_class'] ); ?> <?php echo esc_attr( $instance['accordion_settings'] ); ?>" role="presentation">
  <?php foreach ( $instance['accordion'] as $a ) : ?>
    <?php $zaso_is_open = ( $a['accordion_field_state'] == 'zaso-simple-accordion--open' ); ?>
    <dt class="zaso-simple-accordion__title <?php echo $zaso_is_open ? 'activate' : ''; ?>" role="heading" aria-level="<?php echo (int) $zaso_aria_level; ?>">
      <button aria-expanded="<?php echo $zaso_is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $zaso_acc_uid ); ?>-controls-<?php echo esc_attr( $zaso_aria_control ); ?>" id="<?php echo esc_attr( $zaso_acc_uid ); ?>-id-<?php echo esc_attr( $zaso_aria_control ); ?>" type="button">
        <span class="zacc-title"><?php echo esc_html( $a['accordion_field_title'] ); ?></span>

        <?php if( ! empty( $zacc_collapsible_icon_open ) ) : ?>
            <span class="zacc-collapsible-icon-open" aria-hidden="true">
                <img src="<?php echo esc_url( $zacc_collapsible_icon_open ); ?>" alt="" />
            </span>
        <?php endif; ?>

        <?php if( ! empty( $zacc_collapsible_icon_close ) ) : ?>
            <span class="zacc-collapsible-icon-close" aria-hidden="true">
                <img src="<?php echo esc_url( $zacc_collapsible_icon_close ); ?>" alt="" />
            </span>
        <?php endif; ?>
      </button>
    </dt>
    <dd id="<?php echo esc_attr( $zaso_acc_uid ); ?>-controls-<?php echo esc_attr( $zaso_aria_control ); ?>" aria-labelledby="<?php echo esc_attr( $zaso_acc_uid ); ?>-id-<?php echo esc_attr( $zaso_aria_control ); ?>" class="zaso-simple-accordion__content <?php echo esc_attr( $a['accordion_field_state'] ); ?>" role="region">
      <?php echo wp_kses_post( $a['accordion_field_content'] ); ?>
    </dd>
  <?php $zaso_aria_control++; ?>
  <?php endforeach; ?>
</dl>