<?php
/**
 * [ZASO] YouTube Lightbox Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.6
 */
$build_url = esc_url( $instance['video_url'] );
if( $instance['video_rel'] ) {
    $build_url .= '&rel=' . sanitize_text_field( $instance['video_rel'] );
}

if( $instance['video_showinfo'] ) {
    $build_url .= '&showinfo=' . sanitize_text_field( $instance['video_showinfo'] );
}

$video_thumb = wp_get_attachment_image_src( $instance['video_thumb'], 'full' )[0];
$video_play_button = wp_get_attachment_image_src( $instance['video_play_button'], 'full' );
$video_play_button_hover = wp_get_attachment_image_src( $instance['video_play_button_hover'], 'full' );
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-youtube-lightbox <?php echo $instance['extra_class']; ?>" role="dialog">
    <div class="zaso-youtube-lightbox__inner">
        <a href="<?php echo $build_url; ?>" data-lity>
            <?php if ( $video_thumb ) : ?>
                <img src="<?php echo $video_thumb; ?>" alt="<?php echo $instance['video_url']; ?>" />
            <?php else : ?>
                <?php echo $instance['video_url']; ?>
            <?php endif; ?>

            <?php if ( $video_play_button && $video_thumb ) : ?>
                <div class="zaso-youtube-lightbox__playbutton" style="background: url(<?php echo $video_play_button[0]; ?>) no-repeat center center; width: <?php echo $video_play_button[1]; ?>px; height: <?php echo $video_play_button[2]; ?>px;"></div>
                
                <?php if ( $video_play_button_hover ) : ?>
                    <div class="zaso-youtube-lightbox__playbutton-hover" style="background: url(<?php echo $video_play_button_hover[0]; ?>) no-repeat center center; width: <?php echo $video_play_button_hover[1]; ?>px; height: <?php echo $video_play_button_hover[2]; ?>px;"></div>
                <?php endif; ?>
            <?php endif; ?>
        </a>
    </div>
</div>