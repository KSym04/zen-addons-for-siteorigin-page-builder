<?php
/**
 * [ZASO] bbPress Login Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.12
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-bbpress-login <?php echo $instance['extra_class']; ?>">
	<div class="zaso-bbpress-login__block">
        <?php echo do_shortcode( '[bbp-login]' ); ?>
    </div>
</div>