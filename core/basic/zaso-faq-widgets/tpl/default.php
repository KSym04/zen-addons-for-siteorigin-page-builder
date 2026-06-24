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

// Stable base id for wiring each question to its answer (unique per widget instance).
$zaso_faq_uid = ! empty( $args['widget_id'] ) ? sanitize_html_class( $args['widget_id'] ) : uniqid( 'zaso-faq-' );
?>
<dl <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- zaso_format_field_extra_id() returns a safe id="" attribute string. ?>
	class="<?php echo esc_attr( $classes ); ?>">
	<?php foreach ( $items as $index => $item ) :
		$is_open  = ( $open_first && 0 === $index );
		$q_id     = $zaso_faq_uid . '-q-' . $index;
		$a_id     = $zaso_faq_uid . '-a-' . $index;
	?>
	<div class="zaso-faq__item<?php echo $is_open ? ' zaso-faq__item--open' : ''; ?>">
		<dt class="zaso-faq__question"
			id="<?php echo esc_attr( $q_id ); ?>"
			role="button"
			tabindex="0"
			aria-controls="<?php echo esc_attr( $a_id ); ?>"
			aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
			<?php echo esc_html( $item['question'] ); ?>
		</dt>
		<dd class="zaso-faq__answer"
			id="<?php echo esc_attr( $a_id ); ?>"
			role="region"
			aria-labelledby="<?php echo esc_attr( $q_id ); ?>">
			<?php echo wp_kses_post( $item['answer'] ); ?>
		</dd>
	</div>
	<?php endforeach; ?>
</dl>
<?php if ( $schema && ! empty( $schema_json ) ) : ?>
<script type="application/ld+json"><?php echo $schema_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() output is safe for JSON context. ?></script>
<?php endif; ?>
