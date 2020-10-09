<?php
/**
 * [ZASO] Icon Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.4
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-icon <?php echo $instance['extra_class']; ?>">
	<div class="zaso-icon__block" role="button">

        <div class="zaso-icon__media">
            <?php if ( ! empty( $url ) ) : ?>
                <a href="<?php echo sow_esc_url( $url ) ?>" <?php if ( ! empty( $new_window ) ) echo 'target="_blank" rel="noopener noreferrer"'; ?>>
            <?php endif; ?>

                <?php if ( ! empty( $image ) ) : ?>
                    <img <?php foreach( $attributes as $n => $v ) if ( ! empty( $v ) ) : echo $n.'="' . esc_attr( $v ) . '" '; endif; ?> class="<?php echo esc_attr( implode(' ', $classes ) ) ?>"/>
                <?php else : ?>
                    <?php echo siteorigin_widget_get_icon( $icon ); ?>
                <?php endif; ?>

            <?php if ( ! empty( $url ) ) : ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $icon_text ) ) : ?>

            <div class="zaso-icon__text">
                <?php if ( ! empty( $url ) ) : ?>
                    <a href="<?php echo sow_esc_url( $url ) ?>" <?php if ( ! empty( $new_window ) ) echo 'target="_blank" rel="noopener noreferrer"'; ?>>
                <?php endif; ?>

                        <?php echo nl2br( wp_kses_post( $icon_text ) ); ?>

                <?php if ( ! empty( $url ) ) : ?>
                    </a>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>
</div>