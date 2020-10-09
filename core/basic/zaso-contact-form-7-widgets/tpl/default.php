<?php
/**
 * [ZASO] Contact Form 7 Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.7
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-contact-form-7 <?php echo $instance['extra_class']; ?>">
	<div class="zaso-contact-form-7__block" role="form">
        <?php echo do_shortcode( '[contact-form-7 id="' . $instance['cf7_id'] . '"]' ); ?>
    </div>
</div>