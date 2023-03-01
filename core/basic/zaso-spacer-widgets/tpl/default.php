<?php
/**
 * [ZASO] Spacer Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-spacer <?php echo $instance['extra_class']; ?>">
	<div class="zaso-spacer__block" style="<?php printf( 'height: %1$s', $instance['height'] ); ?>" role="separator"></div>
</div>