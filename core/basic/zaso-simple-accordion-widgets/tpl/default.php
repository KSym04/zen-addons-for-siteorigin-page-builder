<?php
/**
 * [ZASO] Simple Accordion Template
 * @since 1.0.0
 */

$zaso_accordion_extra_id = ( ! empty( $instance['extra_id'] ) ) ? $instance['extra_id'] : ''; ?>

<dl <?php printf( 'id="%s"', $zaso_accordion_extra_id ); ?> class="zaso-simple-accordion <?php echo $instance['extra_class']; ?>">
    <?php foreach ( $instance['accordion'] as $a ) : ?>
        <dt class="zaso-simple-accordion__title">
          <?php echo $a['accordion_field_title']; ?>
        </dt>
        <dd class="zaso-simple-accordion__content <?php echo $a['accordion_field_state']; ?>">
          <?php echo $a['accordion_field_content']; ?>
        </dd>
    <?php endforeach; ?>
</dl>