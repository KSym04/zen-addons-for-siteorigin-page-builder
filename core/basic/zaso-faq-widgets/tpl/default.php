<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] FAQ Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.7.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $items       Processed FAQ items (question, answer).
 * @var bool   $schema      Whether to output JSON-LD schema markup.
 * @var bool   $open_first  Whether the first item starts open.
 * @var string $classes     Root element class string.
 * @var string $schema_json Encoded JSON-LD string (empty string when disabled).
 *
 * Also available directly:
 * @var array  $instance    Full widget instance.
 * @var array  $args        Widget sidebar args.
 */

if ( empty( $items ) ) {
	return;
}
?>
<dl <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
	class="<?php echo esc_attr( $classes ); ?>">
	<?php foreach ( $items as $index => $item ) :
		$is_open = ( $open_first && 0 === $index );
	?>
	<div class="zaso-faq__item<?php echo $is_open ? ' zaso-faq__item--open' : ''; ?>">
		<dt class="zaso-faq__question"
			role="button"
			tabindex="0"
			aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
			<?php echo esc_html( $item['question'] ); ?>
		</dt>
		<dd class="zaso-faq__answer">
			<?php echo wp_kses_post( $item['answer'] ); ?>
		</dd>
	</div>
	<?php endforeach; ?>
</dl>
<?php if ( $schema && ! empty( $schema_json ) ) : ?>
<script type="application/ld+json"><?php echo $schema_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() output is safe for JSON context. ?></script>
<?php endif; ?>
