<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Countdown Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.3.0
 */

$alignment = in_array( $instance['design']['alignment'], array( 'left', 'center', 'right' ), true ) ? $instance['design']['alignment'] : 'center';
$on_expire = 'message' === $instance['on_expire'] ? 'message' : 'hide';

// Initial visibility based on the server-side expiry state.
$units_hidden   = $is_expired;
$message_hidden = ! ( $is_expired && 'message' === $on_expire );
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-countdown zaso-countdown--align-<?php echo esc_attr( $alignment ); ?> <?php echo esc_attr( $instance['extra_class'] ); ?>" role="timer" aria-label="<?php echo esc_attr( $aria_label ); ?>" data-deadline="<?php echo esc_attr( $deadline_ms ); ?>" data-on-expire="<?php echo esc_attr( $on_expire ); ?>">

	<div class="zaso-countdown__units"<?php echo $units_hidden ? ' style="display:none;"' : ''; ?>>
		<?php foreach ( $units as $key => $unit ) : ?>
			<div class="zaso-countdown__unit" data-unit="<?php echo esc_attr( $key ); ?>">
				<span class="zaso-countdown__value"><?php echo esc_html( str_pad( (string) $unit['value'], 2, '0', STR_PAD_LEFT ) ); ?></span>
				<span class="zaso-countdown__label"><?php echo esc_html( $unit['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( 'message' === $on_expire && '' !== trim( (string) $instance['expire_message'] ) ) : ?>
		<div class="zaso-countdown__message"<?php echo $message_hidden ? ' style="display:none;"' : ''; ?>>
			<?php echo wp_kses_post( $instance['expire_message'] ); ?>
		</div>
	<?php endif; ?>

</div>
