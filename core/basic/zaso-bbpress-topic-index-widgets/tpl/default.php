<?php
/**
 * [ZASO] bbPress Topic Index Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.11
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-bbpress-topic-index <?php echo $instance['extra_class']; ?>">
	<div class="zaso-bbpress-topic-index__block <?php //echo $bbpress_topic_index_theme; ?>">
        <?php echo do_shortcode( '[bbp-topic-index]' ); ?>
    </div>
</div>