<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Image Icon Group Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.11
 */
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-image-icon-group <?php echo esc_attr( $instance['extra_class'] ); ?>">
    <ul class="zaso-image-icon-group__list <?php echo esc_attr( $instance['image_icon_group_orientation'] ); ?>">
        <?php foreach ( $instance['image_icon_group'] as $iig ) : ?>
            <?php $image_icon_group_photo = siteorigin_widgets_get_attachment_image_src( $iig['image_icon_group_photo'], 'full' )[0]; ?>
            <li class="zaso-image-icon-group__list-item">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
                <a class="zaso-image-icon-group__list-item-action" href="<?php echo sow_esc_url( $iig['image_icon_group_link'] ) ?>">
                    <img src="<?php echo esc_url( $image_icon_group_photo ); ?>" alt="<?php echo esc_attr( $iig['image_icon_group_title'] ); ?>" />
                    <?php if ( 'block' == $image_icon_group_text_display ) : ?>
                        <?php echo esc_html( $iig['image_icon_group_title'] ); ?>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>