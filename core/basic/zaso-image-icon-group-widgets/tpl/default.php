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
        <?php
        // Defensive: a group repeater with no rows saves the key absent. Default
        // to an empty array so an item-less instance renders nothing instead of a
        // warning; a populated instance is unaffected (output byte-identical).
        $zaso_iig_items = ( ! empty( $instance['image_icon_group'] ) && is_array( $instance['image_icon_group'] ) ) ? $instance['image_icon_group'] : array();
        ?>
        <?php foreach ( $zaso_iig_items as $iig ) : ?>
            <?php
            // An empty photo field makes the helper return false; guard the [0]
            // access so a row without an image cannot emit a warning.
            $image_icon_group_photo_src = ! empty( $iig['image_icon_group_photo'] )
                ? siteorigin_widgets_get_attachment_image_src( $iig['image_icon_group_photo'], 'full' )
                : false;
            $image_icon_group_photo = ( is_array( $image_icon_group_photo_src ) && isset( $image_icon_group_photo_src[0] ) ) ? $image_icon_group_photo_src[0] : '';
            ?>
            <?php
            // Ensure the link always has an accessible name: use the title, else
            // fall back to the link host, then a generic label.
            $iig_title = isset( $iig['image_icon_group_title'] ) ? (string) $iig['image_icon_group_title'] : '';
            $iig_label = $iig_title;
            if ( '' === trim( $iig_label ) && ! empty( $iig['image_icon_group_link'] ) ) {
                $iig_host  = wp_parse_url( $iig['image_icon_group_link'], PHP_URL_HOST );
                $iig_label = $iig_host ? $iig_host : __( 'Open link', 'zaso' );
            }
            ?>
            <li class="zaso-image-icon-group__list-item">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's esc_url() wrapper. ?>
                <a class="zaso-image-icon-group__list-item-action" href="<?php echo sow_esc_url( $iig['image_icon_group_link'] ) ?>"<?php if ( '' === trim( $iig_title ) ) : ?> aria-label="<?php echo esc_attr( $iig_label ); ?>"<?php endif; ?>>
                    <img src="<?php echo esc_url( $image_icon_group_photo ); ?>" alt="<?php echo esc_attr( $iig_title ); ?>" />
                    <?php if ( 'block' == $image_icon_group_text_display ) : ?>
                        <?php echo esc_html( $iig['image_icon_group_title'] ); ?>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>