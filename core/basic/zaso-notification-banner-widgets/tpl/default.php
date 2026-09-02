<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Notification Banner Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.11.0
 */

$zaso_message = ! empty( $instance['banner_message'] ) ? $instance['banner_message'] : '';

// Nothing to announce, nothing to render.
if ( '' === trim( wp_strip_all_tags( $zaso_message ) ) ) {
	return;
}

$zaso_position    = ! empty( $instance['banner_position'] ) ? $instance['banner_position'] : 'inline';
$zaso_dismissible = ! empty( $instance['banner_dismissible'] );
$zaso_remember    = ! empty( $instance['banner_remember'] ) ? $instance['banner_remember'] : 'forever';
$zaso_design      = ! empty( $instance['design'] ) ? $instance['design'] : array();
$zaso_align       = ! empty( $zaso_design['align'] ) ? $zaso_design['align'] : 'center';

// A sticky banner with no way to close it would trap the visitor, so force the
// dismiss button on whenever the banner is pinned to the viewport.
if ( 'inline' !== $zaso_position ) {
	$zaso_dismissible = true;
}

// Remembering a dismissal needs a key that is stable across page loads but that
// CHANGES when the message is edited, so a new announcement is shown to everyone
// again rather than staying hidden behind an old dismissal.
$zaso_key = substr( md5( $zaso_message ), 0, 12 );

$zaso_classes = array( 'zaso-notification-banner' );
$zaso_classes[] = 'zaso-notification-banner--' . sanitize_html_class( $zaso_position );
$zaso_classes[] = 'zaso-notification-banner--align-' . sanitize_html_class( $zaso_align );
if ( ! empty( $instance['extra_class'] ) ) {
	$zaso_classes[] = $instance['extra_class'];
}
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( implode( ' ', $zaso_classes ) ); ?>" role="region" aria-label="<?php esc_attr_e( 'Site announcement', 'zen-addons-for-siteorigin-page-builder' ); ?>" data-zaso-banner-key="<?php echo esc_attr( $zaso_key ); ?>" data-zaso-banner-remember="<?php echo esc_attr( $zaso_remember ); ?>">
	<div class="zaso-notification-banner__inner">

		<div class="zaso-notification-banner__message">
			<?php echo wp_kses_post( $zaso_message ); ?>
		</div>

		<?php if ( ! empty( $instance['banner_link_text'] ) && ! empty( $instance['banner_link_url'] ) ) : ?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
			<a class="zaso-notification-banner__button" href="<?php echo sow_esc_url( $instance['banner_link_url'] ); ?>"<?php echo ! empty( $instance['banner_link_new_tab'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( $instance['banner_link_text'] ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $zaso_dismissible ) : ?>
			<button type="button" class="zaso-notification-banner__dismiss" aria-label="<?php esc_attr_e( 'Dismiss this announcement', 'zen-addons-for-siteorigin-page-builder' ); ?>">
				<span aria-hidden="true">&times;</span>
			</button>
		<?php endif; ?>

	</div>
</div>
