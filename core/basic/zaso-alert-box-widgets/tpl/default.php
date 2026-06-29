<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Alert Box Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.3
 */
?>

<?php
// Optional alert type adds a leading icon + screen-reader label so the type is
// not conveyed by colour alone. Defaults to 'none' for existing instances.
$alert_type = ! empty( $instance['alert_type'] ) ? $instance['alert_type'] : 'none';

$zaso_alert_types = array(
	'info'    => array(
		'label' => __( 'Information:', 'zaso' ),
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
	),
	'success' => array(
		'label' => __( 'Success:', 'zaso' ),
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
	),
	'warning' => array(
		'label' => __( 'Warning:', 'zaso' ),
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
	),
	'error'   => array(
		'label' => __( 'Error:', 'zaso' ),
		'icon'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
	),
);
$zaso_has_type = isset( $zaso_alert_types[ $alert_type ] );

$zaso_box_class = 'zaso-alert-box__messagebox';
if ( $zaso_has_type ) {
	$zaso_box_class .= ' zaso-alert-box__messagebox--has-icon zaso-alert-box__messagebox--' . $alert_type;
}

// Optional structural layout. Default ('default') is the original bordered box
// and adds NO extra class, so existing instances render byte-identical. Only the
// alternate layouts (card / left-accent / banner) add a modifier class.
$zaso_layout         = ! empty( $instance['layout'] ) ? $instance['layout'] : 'default';
$zaso_layout_allowed = array( 'default', 'card', 'left-accent', 'banner' );
if ( ! in_array( $zaso_layout, $zaso_layout_allowed, true ) ) {
	$zaso_layout = 'default';
}
$zaso_wrapper_class = 'zaso-alert-box';
if ( 'default' !== $zaso_layout ) {
	$zaso_wrapper_class .= ' zaso-alert-box--layout-' . $zaso_layout;
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $zaso_wrapper_class ); ?> <?php echo esc_attr( $instance['extra_class'] ); ?>">
  <div class="<?php echo esc_attr( $zaso_box_class ); ?>">
    <?php if( $instance['alert_closebtn'] == 'show' ) : ?>
      <button type="button" class="zaso-alert-box__closebtn" data-dismiss="alert" aria-label="<?php esc_attr_e( 'Close', 'zaso' ); ?>">
        <span aria-hidden="true"><?php esc_html_e( '&times;', 'zaso' ); ?></span>
      </button>
    <?php endif; ?>
    <?php if ( $zaso_has_type ) : ?>
      <span class="zaso-alert-box__icon" aria-hidden="true"><?php echo $zaso_alert_types[ $alert_type ]['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded static inline SVG, no user input. ?></span>
      <span class="zaso-alert-box__sr"><?php echo esc_html( $zaso_alert_types[ $alert_type ]['label'] ); ?></span>
      <div class="zaso-alert-box__body"><?php echo wp_kses_post( $instance['alert_message'] ); ?></div>
    <?php else : ?>
      <?php echo wp_kses_post( $instance['alert_message'] ); ?>
    <?php endif; ?>
  </div>
</div>