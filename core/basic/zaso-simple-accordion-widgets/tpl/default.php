<?php
/**
 * [ZASO] Accordion Template
 * @since 1.0.0
 */
?>

<dl class="zaso-simple-accordion <?php echo $instance['extra_class']; ?>">
    <?php foreach ($instance['accordion'] as $a ) : ?>
        <dt class="zaso-simple-accordion__title">
            <a href="#"><?php echo $a['accordion_field_title']; ?></a>
        </dt>
        <dd class="zaso-simple-accordion__content">
            <p><?php echo $a['accordion_field_content']; ?></p>
        </dd>
    <?php endforeach; ?>
</dl>