<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * Widget Name: Zen Addons - Countdown
 * Widget ID: zen-addons-siteorigin-countdown
 * Description: A countdown timer to a target date and time for launches and promotions.
 * Author: DopeThemes
 * Author URI: https://www.dopethemes.com/
 */

if ( ! class_exists( 'Zen_Addons_SiteOrigin_Countdown_Widget' ) ) :


class Zen_Addons_SiteOrigin_Countdown_Widget extends SiteOrigin_Widget {

	function __construct() {

		// ZASO field array.
		$zaso_countdown_field_array = array(
			'target_date' => array(
				'type'        => 'text',
				'label'       => __( 'Target Date and Time', 'zaso' ),
				'description' => __( 'Format: YYYY-MM-DD HH:MM (24-hour), in your site timezone. Example: 2026-12-31 23:59', 'zaso' ),
			),
			'show_days' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Days', 'zaso' ),
				'default' => true,
			),
			'show_hours' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Hours', 'zaso' ),
				'default' => true,
			),
			'show_minutes' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Minutes', 'zaso' ),
				'default' => true,
			),
			'show_seconds' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Show Seconds', 'zaso' ),
				'default' => true,
			),
			'label_days' => array(
				'type'    => 'text',
				'label'   => __( 'Days Label', 'zaso' ),
				'default' => __( 'Days', 'zaso' ),
			),
			'label_hours' => array(
				'type'    => 'text',
				'label'   => __( 'Hours Label', 'zaso' ),
				'default' => __( 'Hours', 'zaso' ),
			),
			'label_minutes' => array(
				'type'    => 'text',
				'label'   => __( 'Minutes Label', 'zaso' ),
				'default' => __( 'Minutes', 'zaso' ),
			),
			'label_seconds' => array(
				'type'    => 'text',
				'label'   => __( 'Seconds Label', 'zaso' ),
				'default' => __( 'Seconds', 'zaso' ),
			),
			'on_expire' => array(
				'type'    => 'select',
				'label'   => __( 'When the countdown ends', 'zaso' ),
				'default' => 'hide',
				'options' => array(
					'hide'    => __( 'Hide the timer', 'zaso' ),
					'message' => __( 'Show a message', 'zaso' ),
				),
			),
			'expire_message' => array(
				'type'        => 'tinymce',
				'label'       => __( 'Expiry Message', 'zaso' ),
				'description' => __( 'Shown when the countdown reaches zero (if "Show a message" is selected).', 'zaso' ),
			),
			'extra_id' => array(
				'type'        => 'text',
				'label'       => __( 'Extra ID', 'zaso' ),
				'description' => __( 'Add an extra ID.', 'zaso' ),
			),
			'extra_class' => array(
				'type'        => 'text',
				'label'       => __( 'Extra Class', 'zaso' ),
				'description' => __( 'Add an extra class for styling overrides.', 'zaso' ),
			),
			'design' => array(
				'type'   => 'section',
				'label'  => __( 'Design', 'zaso' ),
				'hide'   => true,
				'fields' => array(
					'alignment' => array(
						'type'    => 'select',
						'label'   => __( 'Alignment', 'zaso' ),
						'default' => 'center',
						'options' => array(
							'left'   => __( 'Left', 'zaso' ),
							'center' => __( 'Center', 'zaso' ),
							'right'  => __( 'Right', 'zaso' ),
						),
					),
					'number_color' => array(
						'type'    => 'color',
						'label'   => __( 'Number Color', 'zaso' ),
						'default' => '#ffffff',
					),
					'number_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Number Size', 'zaso' ),
						'default' => '2.5rem',
					),
					'label_color' => array(
						'type'    => 'color',
						'label'   => __( 'Label Color', 'zaso' ),
						'default' => '#e2e8f0',
					),
					'label_size' => array(
						'type'    => 'measurement',
						'label'   => __( 'Label Size', 'zaso' ),
						'default' => '0.875rem',
					),
					'box_bg' => array(
						'type'    => 'color',
						'label'   => __( 'Unit Background', 'zaso' ),
						'default' => '#1e293b',
					),
					'box_padding' => array(
						'type'    => 'measurement',
						'label'   => __( 'Unit Padding', 'zaso' ),
						'default' => '1rem',
					),
					'box_radius' => array(
						'type'    => 'measurement',
						'label'   => __( 'Unit Border Radius', 'zaso' ),
						'default' => '8px',
					),
					'gap' => array(
						'type'    => 'measurement',
						'label'   => __( 'Gap Between Units', 'zaso' ),
						'default' => '0.75rem',
					),
				),
			),
		);

		// Add filter.
		$zaso_countdown_fields = apply_filters( 'zaso_countdown_fields', $zaso_countdown_field_array );

		parent::__construct(
			'zen-addons-siteorigin-countdown',
			__( 'Zen Addons - Countdown', 'zaso' ),
			array(
				'description'   => __( 'A countdown timer to a target date for launches and promotions.', 'zaso' ),
				'help'          => 'https://www.dopethemes.com/',
				'panels_groups' => array( 'zaso-plugin-widgets' ),
			),
			array(),
			$zaso_countdown_fields,
			ZASO_WIDGET_BASIC_DIR
		);

	}

	function get_less_variables( $instance ) {

		$design = $instance['design'];

		return apply_filters( 'zaso_countdown_less_variables', array(
			'number_color' => $design['number_color'],
			'number_size'  => $design['number_size'],
			'label_color'  => $design['label_color'],
			'label_size'   => $design['label_size'],
			'box_bg'       => $design['box_bg'],
			'box_padding'  => $design['box_padding'],
			'box_radius'   => $design['box_radius'],
			'gap'          => $design['gap'],
		) );

	}

	function get_template_variables( $instance, $args ) {

		// Compute the target as a UTC timestamp from a site-timezone string.
		// The browser clock is never trusted for the deadline; JS only counts
		// down to this server-resolved instant.
		$deadline_ts = 0;
		$raw         = trim( (string) $instance['target_date'] );
		if ( '' !== $raw ) {
			try {
				$target      = new DateTime( $raw, wp_timezone() );
				$deadline_ts = $target->getTimestamp();
			} catch ( Exception $e ) {
				$deadline_ts = 0;
			}
		}

		// Remaining time at render (server side) for the no-JS / initial state.
		$remaining = max( 0, $deadline_ts - time() );

		// Build the ordered list of enabled units.
		$units = array();
		if ( ! empty( $instance['show_days'] ) ) {
			$units['days'] = array( 'value' => (int) floor( $remaining / DAY_IN_SECONDS ), 'label' => $instance['label_days'] );
		}
		if ( ! empty( $instance['show_hours'] ) ) {
			$units['hours'] = array( 'value' => (int) floor( ( $remaining % DAY_IN_SECONDS ) / HOUR_IN_SECONDS ), 'label' => $instance['label_hours'] );
		}
		if ( ! empty( $instance['show_minutes'] ) ) {
			$units['minutes'] = array( 'value' => (int) floor( ( $remaining % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS ), 'label' => $instance['label_minutes'] );
		}
		if ( ! empty( $instance['show_seconds'] ) ) {
			$units['seconds'] = array( 'value' => (int) ( $remaining % MINUTE_IN_SECONDS ), 'label' => $instance['label_seconds'] );
		}

		// Human-readable deadline for the accessible label.
		$aria_label = '';
		if ( $deadline_ts > 0 ) {
			/* translators: %s: formatted target date and time. */
			$aria_label = sprintf( __( 'Countdown to %s', 'zaso' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $deadline_ts ) );
		}

		return apply_filters( 'zaso_countdown_template_variables', array(
			'deadline_ms' => $deadline_ts * 1000,
			'units'       => $units,
			'on_expire'   => $instance['on_expire'],
			'alignment'   => $instance['design']['alignment'],
			'aria_label'  => $aria_label,
			'is_expired'  => ( 0 === $deadline_ts || $remaining <= 0 ),
		) );

	}

	function initialize() {

		$this->register_frontend_scripts(
			array(
				array(
					'zen-addons-siteorigin-countdown',
					ZASO_WIDGET_BASIC_DIR . basename( dirname( __FILE__ ) ) . '/js/script.js',
					array(),
					ZASO_VERSION,
					true,
				),
			)
		);

	}

}
siteorigin_widget_register( 'zen-addons-siteorigin-countdown', __FILE__, 'Zen_Addons_SiteOrigin_Countdown_Widget' );


endif;
