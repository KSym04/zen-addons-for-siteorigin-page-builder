<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] bbPress Registration Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.12
 */
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-bbpress-registration <?php echo esc_attr( $instance['extra_class'] ); ?>">
	<div class="zaso-bbpress-registration__block">
        <?php echo do_shortcode( '[bbp-register]' ); ?>
    </div>
</div>