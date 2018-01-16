<?php
/**
 * [ZASO] Icon Group Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.4
 */
?>

<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="zaso-icon-group <?php echo $instance['extra_class']; ?>">

    <?php if( ! empty( $instance['title'] ) ) echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title']; ?>

    <?php foreach ( $instance['icon_group'] as $ig ) : ?>

        <?php
        // set custom icon src
        $src = siteorigin_widgets_get_attachment_image_src(
            $ig['image'],
            'full',
            ! empty( $ig['image_fallback'] ) ? $ig['image_fallback'] : false
        );

        // set custom icon attributes
        $attr = array();
        if( !empty($src) ) {
            $attr = array( 'src' => $src[0] );

            if ( ! empty( $src[1] ) )
                $attr['width'] = $src[1];

            if ( ! empty( $src[2] ) )
                $attr['height'] = $src[2];

            if ( function_exists( 'wp_get_attachment_image_srcset' ) )
                $attr['srcset'] = wp_get_attachment_image_srcset( $ig['image'], 'full' );

            // Hotfix Photon
            if ( ! ( class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' ) ) ) {
                if ( function_exists( 'wp_get_attachment_image_sizes' ) ) {
                    $attr['sizes'] = wp_get_attachment_image_sizes( $ig['image'], 'full' );
                }
            }
        }

        // set custom icon title
        $file_name = pathinfo( get_post_meta( $ig['image'], '_wp_attached_file', true ), PATHINFO_FILENAME );
        $title = get_the_title( $ig['image'] );

        if ( $title == $file_name )
            $title = '';

        $attr['title'] = $title;

        // set custom icon alt
        $attr['alt'] = get_post_meta( $ig['image'], '_wp_attachment_image_alt', true );

        // return the goodies
        $proc = array(
            'icon' => $ig['icon'],
            'url' => $ig['url'],
            'new_window' => $ig['new_window'],
            'icon_text' => $ig['icon_text'],
            'attributes' => $attr,
            'image' => $ig['image'],
            'classes' => array( 'zaso-icon__image' )
        );
        ?>

        <div class="zaso-icon-group__block">
            <div class="zaso-icon-group__media">
                <?php if ( ! empty( $proc['url'] ) ) : ?>
                    <a href="<?php echo sow_esc_url( $proc['url'] ) ?>" <?php if ( ! empty( $proc['new_window'] ) ) echo 'target="_blank" rel="noopener noreferrer"'; ?>>
                <?php endif; ?>

                    <?php if ( ! empty( $proc['image'] ) ) : ?>
                        <img <?php foreach( $proc['attributes'] as $n => $v ) if ( ! empty( $v ) ) : echo $n.'="' . esc_attr( $v ) . '" '; endif; ?> class="<?php echo esc_attr( implode(' ', $proc['classes'] ) ) ?>"/>
                    <?php else : ?>
                        <?php echo siteorigin_widget_get_icon( $proc['icon'] ); ?>
                    <?php endif; ?>

                <?php if ( ! empty( $proc['url'] ) ) : ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $proc['icon_text'] ) ) : ?>
                <div class="zaso-icon-group__text">
                    <?php echo wpautop( wp_kses_post( $proc['icon_text'] ) ); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>