<?php
/**
 * [ZASO] Spacer Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-spacer <?php echo esc_attr( $instance['extra_class'] ); ?>">
	<div class="zaso-spacer__block" style="height: <?php echo esc_attr( $instance['height'] ); ?>;" role="separator"></div>
</div>
