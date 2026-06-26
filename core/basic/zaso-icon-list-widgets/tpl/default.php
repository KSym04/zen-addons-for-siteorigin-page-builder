<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Icon List Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.10.0
 */

if ( empty( $items ) ) {
	return;
}
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-icon-list zaso-icon-list--<?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $instance['extra_class'] ); ?>">
	<ul class="zaso-icon-list__list">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$item_text = isset( $item['text'] ) ? (string) $item['text'] : '';
			$item_icon = ! empty( $item['icon'] ) ? $item['icon'] : $default_icon;
			$item_link = isset( $item['link'] ) ? $item['link'] : '';

			// Accessible name for a linked item with no visible text: fall back to
			// the link host, then a generic label, so it is never a nameless link.
			$item_label = $item_text;
			if ( '' === trim( $item_label ) && ! empty( $item_link ) ) {
				$item_host  = wp_parse_url( $item_link, PHP_URL_HOST );
				$item_label = $item_host ? $item_host : __( 'Open link', 'zaso' );
			}
			?>
			<li class="zaso-icon-list__item">
				<?php if ( ! empty( $item_icon ) ) : ?>
					<span class="zaso-icon-list__icon" aria-hidden="true">
						<?php echo siteorigin_widget_get_icon( $item_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- siteorigin_widget_get_icon() returns SiteOrigin-generated, safe markup. ?>
					</span>
				<?php endif; ?>
				<span class="zaso-icon-list__text">
					<?php if ( ! empty( $item_link ) ) : ?>
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
						<a href="<?php echo sow_esc_url( $item_link ); ?>"<?php if ( '' === trim( $item_text ) ) : ?> aria-label="<?php echo esc_attr( $item_label ); ?>"<?php endif; ?>><?php echo esc_html( $item_text ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $item_text ); ?>
					<?php endif; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
