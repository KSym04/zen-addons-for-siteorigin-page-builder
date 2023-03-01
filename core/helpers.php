<?php
/**
 * Helpers
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Format ZASO Extra ID field.
 *
 * @since 1.0.2
 *
 * @param string $id Extra ID field text string.
 *
 * @return string Formatted HTML id.
 */
function zaso_format_field_extra_id( $id ) {
    $id = apply_filters( 'zaso_format_field_extra_id_before', sanitize_text_field( $id ) );

    if( ! empty( $id ) && is_main_query() )
        $id = sprintf( 'id="%s"', $id );

    return apply_filters( 'zaso_format_field_extra_id_after', $id );
}
