<?php
/**
 * [ZASO] bbPress Forum Index Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.11
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-bbpress-forum-index <?php echo $instance['extra_class']; ?>">
	<div class="zaso-bbpress-forum-index__block">
        <?php echo do_shortcode( '[bbp-forum-index]' ); ?>
    </div>
</div>