<?php
/**
 * [ZASO] Video Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.4
 */

$zaso_video_attributes = apply_filters( 'zaso_video_template_variables',  array(
    'src'      => $instance['video_url'],
    'class'    => 'wp-video-shortcode zaso-video__box'
));
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-video <?php echo $instance['extra_class']; ?>" role="banner">
    <?php echo wp_video_shortcode( $zaso_video_attributes ); ?>
    <div class="zaso-video__content">
        <?php echo wp_kses_post( $instance['video_content'] ); ?>
    </div>
</div>