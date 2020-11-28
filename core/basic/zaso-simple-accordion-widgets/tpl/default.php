<?php
/**
 * [ZASO] Simple Accordion Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */

$zaso_aria_level = count( $instance['accordion'] );
$zaso_aria_control = 1; 

$zacc_collapsible_icon_open = wp_get_attachment_image_src( $instance['accordion_collapsible_icon_open'], 'full' )[0];
$zacc_collapsible_icon_close = wp_get_attachment_image_src( $instance['accordion_collapsible_icon_close'], 'full' )[0];

?>

<dl <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-simple-accordion <?php echo $instance['extra_class']; ?> <?php echo $instance['accordion_settings']; ?>" role="presentation">
  <?php foreach ( $instance['accordion'] as $a ) : ?>
    <dt class="zaso-simple-accordion__title <?php echo ($a['accordion_field_state'] == 'zaso-simple-accordion--open') ? 'activate' : ''; ?>" role="heading" aria-level="<?php echo $zaso_aria_level; ?>">
      <button aria-expanded="true" aria-controls="zacc-controls-<?php echo $zaso_aria_control; ?>" id="zacc-id-<?php echo $zaso_aria_control; ?>" type="button">
        <span class="zacc-title"><?php echo $a['accordion_field_title']; ?></span>

        <?php if( ! empty( $zacc_collapsible_icon_open ) ) : ?>
            <span class="zacc-collapsible-icon-open">
                <img src="<?php echo $zacc_collapsible_icon_open; ?>" alt="<?php esc_html_e( 'Accordion Open', 'zaso' ); ?>" />
            </span>
        <?php endif; ?>

        <?php if( ! empty( $zacc_collapsible_icon_close ) ) : ?>
            <span class="zacc-collapsible-icon-close">
                <img src="<?php echo $zacc_collapsible_icon_close; ?>" alt="<?php esc_html_e( 'Accordion Close', 'zaso' ); ?>" />
            </span>
        <?php endif; ?>
      </button>
    </dt>
    <dd id="zacc-controls-<?php echo $zaso_aria_control; ?>" aria-labelledby="zacc-id-<?php echo $zaso_aria_control; ?>" class="zaso-simple-accordion__content <?php echo $a['accordion_field_state']; ?>" role="region">
      <?php echo wp_kses_post( $a['accordion_field_content'] ); ?>
    </dd>
  <?php $zaso_aria_control++; ?>
  <?php endforeach; ?>
</dl>