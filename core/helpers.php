<?php
/**
 * Helpers
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.0.0
 */

// Ensure that the code is only run from within WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Format the ZASO Extra ID field.
 * Sanitizes the given ID, and then wraps it in an HTML 'id' attribute if it's not empty and the main query.
 *
 * @since 1.0.2
 *
 * @param string $id The Extra ID field text string.
 * @return string Formatted HTML ID, or an empty string if the ID was empty or not the main query.
 */
function zaso_format_field_extra_id( $id ) {
	// Apply a filter before sanitization, then sanitize the text field.
	$id = apply_filters( 'zaso_format_field_extra_id_before', sanitize_text_field( $id ) );

	// Check if the ID is not empty and is part of the main query.
	if ( ! empty( $id ) && is_main_query() ) {
		$id = sprintf( 'id="%s"', $id );
	}

	// Apply a filter after the ID is formatted, then return the result.
	return apply_filters( 'zaso_format_field_extra_id_after', $id );
}
