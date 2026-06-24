<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Vimeo Lightbox Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.7
 */
$build_url_temp = 'https://player.vimeo.com/video/' . sanitize_text_field( $instance['video_url'] );
$build_url = 'https://player.vimeo.com/video/' . sanitize_text_field( $instance['video_url'] ) . '?';

if( $instance['video_loop'] ) {
    $build_url .= '&loop=' . sanitize_text_field( $instance['video_loop'] );
}

if( $instance['video_do_not_track'] ) {
    $build_url .= '&dnt=' . sanitize_text_field( $instance['video_do_not_track'] );
}

if( $instance['video_muted'] ) {
    $build_url .= '&muted=' . sanitize_text_field( $instance['video_muted'] );
}

$video_thumb = wp_get_attachment_image_src( $instance['video_thumb'], 'full' )[0];
$video_play_button = wp_get_attachment_image_src( $instance['video_play_button'], 'full' );
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-vimeo-lightbox <?php echo esc_attr( $instance['extra_class'] ); ?>">
    <div class="zaso-vimeo-lightbox__inner">
        <a href="<?php echo esc_url( $build_url ); ?>" data-lity aria-label="<?php esc_attr_e( 'Play video', 'zaso' ); ?>">
            <?php if ( $video_thumb ) : ?>
                <img src="<?php echo esc_url( $video_thumb ); ?>" alt="" />
            <?php else : ?>
                <?php esc_html_e( 'Play video', 'zaso' ); ?>
            <?php endif; ?>

            <?php if ( $video_play_button && $video_thumb ) : ?>
                <div class="zaso-vimeo-lightbox__playbutton" style="background: url(<?php echo esc_url( $video_play_button[0] ); ?>) no-repeat center center; display: inline-block; width: <?php echo (int) $video_play_button[1]; ?>px; height: <?php echo (int) $video_play_button[2]; ?>px;"></div>
            <?php endif; ?>
        </a>
    </div>
</div>