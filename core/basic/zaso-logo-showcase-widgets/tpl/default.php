<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Logo Showcase — default template.
 */

if ( empty( $logos ) ) {
	return;
}
?>
<ul <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?> class="<?php echo esc_attr( $container_classes ); ?>" role="list">
	<?php foreach ( $logos as $logo ) : ?>
		<li class="zaso-logo-showcase__item">
			<?php if ( ! empty( $logo['link_url'] ) ) : ?>
				<a class="zaso-logo-showcase__link" href="<?php echo sow_esc_url( $logo['link_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's vetted URL escaper. ?>"<?php if ( '' === (string) $logo['img']['alt'] ) : ?> aria-label="<?php echo esc_attr( $logo['link_label'] . ( $logo['link_new_tab'] ? ' ' . __( '(opens in new tab)', 'zaso' ) : '' ) ); ?>"<?php endif; ?><?php if ( $logo['link_new_tab'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
					<img class="zaso-logo-showcase__img"<?php foreach ( $logo['img'] as $n => $v ) { if ( 'alt' === $n || '' !== (string) $v ) { echo ' ' . esc_attr( $n ) . '="' . esc_attr( (string) $v ) . '"'; } } ?> loading="lazy" decoding="async" />
				</a>
			<?php else : ?>
				<span class="zaso-logo-showcase__link">
					<img class="zaso-logo-showcase__img"<?php foreach ( $logo['img'] as $n => $v ) { if ( 'alt' === $n || '' !== (string) $v ) { echo ' ' . esc_attr( $n ) . '="' . esc_attr( (string) $v ) . '"'; } } ?> loading="lazy" decoding="async" />
				</span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
