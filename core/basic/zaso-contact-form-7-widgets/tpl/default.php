<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Contact Form 7 Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.7
 */
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-contact-form-7 <?php echo esc_attr( $instance['extra_class'] ); ?>">
	<div class="zaso-contact-form-7__block">
        <?php echo do_shortcode( '[contact-form-7 id="' . absint( $instance['cf7_id'] ) . '"]' ); ?>
    </div>
</div>