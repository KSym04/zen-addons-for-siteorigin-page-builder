<?php
/**
 * [ZASO] bbPress Lost Password Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.16
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-bbpress-lost-password <?php echo $instance['extra_class']; ?>">
	<div class="zaso-bbpress-lost-password__block">
        <?php echo do_shortcode( '[bbp-lost-pass]' ); ?>
    </div>
</div>