<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Info Box Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.8
 */
$info_image = siteorigin_widgets_get_attachment_image_src( $instance['info_image'], $instance['info_image_size'], ! empty( $instance['info_image_fallback'] ) ? $instance['info_image_fallback'] : false );

$attr = array();
if( !empty($info_image) ) {
    $attr = array( 'src' => $info_image[0] );

    if ( ! empty( $info_image[1] ) ) {
        $attr['width'] = $info_image[1];
    }

    if ( ! empty( $info_image[2] ) ) {
        $attr['height'] = $info_image[2];
    }

    if ( function_exists( 'wp_get_attachment_image_srcset' ) ) {
        $attr['srcset'] = wp_get_attachment_image_srcset( $instance['info_image'], $instance['info_image_size'] );
    }
    
    // Jetpack Photon hotfix.
    if ( ! ( class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' ) ) ) {
        if ( function_exists( 'wp_get_attachment_image_sizes' ) ) {
            $attr['sizes'] = wp_get_attachment_image_sizes( $instance['info_image'], $instance['info_image_size'] );
        }
    }
}

$attr['title'] = $instance['info_title'];

// Always provide an alt: attachment alt text, falling back to the info title.
$attr['alt'] = '';
if ( ! empty( $instance['info_image'] ) ) {
    $attr['alt'] = get_post_meta( $instance['info_image'], '_wp_attachment_image_alt', true );
}
if ( '' === $attr['alt'] ) {
    $attr['alt'] = $instance['info_title'];
}
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-info-box <?php echo esc_attr( $instance['extra_class'] ); ?>">
  <div class="zaso-info-box__wrapper">
    <?php if( $instance['info_image'] ) : ?>
      <div class="zaso-info-box__image">
        <img <?php foreach( $attr as $n => $v ) if ( $n === 'alt' || ! empty( $v ) ) : echo esc_attr( $n ).'="' . esc_attr( $v ) . '" '; endif; ?> />
      </div>
    <?php endif; ?>

    <?php if( $instance['info_title'] || $instance['info_description'] ) : ?>
      <div class="zaso-info-box__content">
        <?php if( $instance['info_title'] ) : ?>
          <h3 class="zaso-info-box__content-title">
            <?php echo esc_html( $instance['info_title'] ); ?>
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
          <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
          <a href="<?php echo sow_esc_url( $instance['info_button_url'] ) ?>"><?php echo esc_html( $instance['info_button_text'] ); ?></a>
      </div>
    <?php endif; ?>
  </div>
</div>