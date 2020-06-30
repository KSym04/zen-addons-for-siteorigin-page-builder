<?php
/**
 * [ZASO] Widgetized Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.5
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-widgetized <?php echo $instance['extra_class']; ?>">
	<div class="zaso-widgetized__block">
        <?php dynamic_sidebar( $sidebar_id ); ?>
    </div>
</div>