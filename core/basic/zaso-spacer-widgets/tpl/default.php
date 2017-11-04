<?php
/**
 * [ZASO] Spacer Template
 * @since 1.0.0
 */

$zaso_spacer_extra_id = ( ! empty( $instance['extra_id'] ) ) ? $instance['extra_id'] : ''; ?>

<div <?php printf( 'id="%s"', $zaso_spacer_extra_id ); ?> class="zaso-spacer <?php echo $instance['extra_class']; ?>">
	<div class="zaso-spacer__block" style="<?php printf( 'height: %1$s', $instance['height'] ); ?>"></div>
</div>