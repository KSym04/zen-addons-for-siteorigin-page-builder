<?php
/**
 * [ZASO] Basic Tabs Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.2
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-basic-tabs <?php echo $instance['extra_class']; ?>">
    <div class="zaso-basic-tabs__list" role="tablist" aria-label="<?php echo sanitize_title( $instance['tab_main_title'] ); ?>">
      <?php
        // counter
        $tt_count = 0;
        foreach ( $instance['tabs'] as $t1 ) :
        $tt_aria_selected = ( $tt_count == 0 ) ? "true" : "false";
        $tt_aria_tabindex = ( $tt_count == 0 ) ? '' : 'tabindex="-1"';
        $tt_title_formatted = sanitize_key( $t1['tab_field_title'] );
      ?>
            <button class="zaso-basic-tabs__title"
                  role="tab"
                  aria-selected="<?php echo $tt_aria_selected; ?>"
                  aria-controls="<?php echo $tt_title_formatted; ?>-tab"
                  id="<?php echo $tt_title_formatted; ?>"
                  <?php echo $tt_aria_tabindex; ?>>
              <?php echo $t1['tab_field_title']; ?>
            </button>
      <?php $tt_count++; ?>
      <?php endforeach; ?>
    </div>

    <?php
      // counter
      $tc_count = 0;
      foreach ( $instance['tabs'] as $t2 ) :
      $tt_aria_selected = ( $tc_count == 0 ) ? "true" : "false";
      $tt_title_formatted = sanitize_key( $t2['tab_field_title'] );
    ?>
        <div class="zaso-basic-tabs__content" tabindex="0" role="tabpanel"
             id="<?php echo $tt_title_formatted; ?>-tab"
             aria-labelledby="<?php echo $tt_title_formatted; ?>" <?php echo ( $tc_count == 0 ) ? "" : "hidden"; ?>>
           <?php echo wp_kses_post( $t2['tab_field_content'] ); ?>
        </div>
    <?php $tc_count++; ?>
    <?php endforeach; ?>
</div>