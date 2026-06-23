<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Widgetized Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.5
 */
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-widgetized <?php echo esc_attr( $instance['extra_class'] ); ?>">
	<div class="zaso-widgetized__block">
        <?php dynamic_sidebar( $sidebar_id ); ?>
    </div>
</div>