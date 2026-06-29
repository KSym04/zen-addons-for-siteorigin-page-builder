<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Basic Tabs Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.2
 */
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-basic-tabs <?php echo esc_attr( $instance['extra_class'] ); ?>">
    <div class="zaso-basic-tabs__list" role="tablist" aria-label="<?php echo esc_attr( $instance['tab_main_title'] ); ?>">
      <?php
        // Per-instance unique id prefix so tab/panel ids never collide across
        // multiple tab instances (or duplicate tab titles) on one page. The id
        // is index-based so each tab button stays matched to its panel.
        $zaso_tabs_uid = ! empty( $args['widget_id'] ) ? sanitize_html_class( $args['widget_id'] ) : uniqid( 'zaso-tabs-' );
        // Defensive: a tabs repeater with no rows saves the key absent. Default to
        // an empty array so an item-less instance renders nothing instead of a
        // warning; a populated instance is unaffected (output byte-identical).
        $zaso_tabs = ( ! empty( $instance['tabs'] ) && is_array( $instance['tabs'] ) ) ? $instance['tabs'] : array();
        // counter
        $tt_count = 0;
        foreach ( $zaso_tabs as $t1 ) :
        $tt_aria_selected = ( $tt_count == 0 ) ? "true" : "false";
        $tt_aria_tabindex = ( $tt_count == 0 ) ? '' : '-1';
        $tt_tab_id = $zaso_tabs_uid . '-' . $tt_count;
      ?>
            <button class="zaso-basic-tabs__title"
                  role="tab"
                  aria-selected="<?php echo esc_attr( $tt_aria_selected ); ?>"
                  aria-controls="<?php echo esc_attr( $tt_tab_id ); ?>-tab"
                  id="<?php echo esc_attr( $tt_tab_id ); ?>"
                  <?php echo ( '' !== $tt_aria_tabindex ) ? 'tabindex="' . esc_attr( $tt_aria_tabindex ) . '"' : ''; ?>>
              <?php echo esc_html( $t1['tab_field_title'] ); ?>
            </button>
      <?php $tt_count++; ?>
      <?php endforeach; ?>
    </div>

    <?php
      // counter
      $tc_count = 0;
      foreach ( $zaso_tabs as $t2 ) :
      $tt_aria_selected = ( $tc_count == 0 ) ? "true" : "false";
      $zaso_panel_id = $zaso_tabs_uid . '-' . $tc_count;
    ?>
        <div class="zaso-basic-tabs__content" tabindex="0" role="tabpanel"
             id="<?php echo esc_attr( $zaso_panel_id ); ?>-tab"
             aria-labelledby="<?php echo esc_attr( $zaso_panel_id ); ?>" <?php echo ( $tc_count == 0 ) ? "" : "hidden"; ?>>
           <?php echo wp_kses_post( $t2['tab_field_content'] ); ?>
        </div>
    <?php $tc_count++; ?>
    <?php endforeach; ?>
</div>