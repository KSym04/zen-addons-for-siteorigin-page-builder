<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.
/**
 * [ZASO] Team Member Template
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.4.0
 *
 * Available variables (set by get_template_variables):
 * @var array  $members           Processed member list.
 * @var string $container_classes Space-separated class string.
 *
 * Also available directly from $instance:
 * @var array  $instance          Full widget instance.
 * @var array  $args              Widget sidebar args.
 */

if ( empty( $members ) ) {
	return;
}

$social_icons = array(
	'twitter'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.843L1.072 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
	'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
	'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
	'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
	'website'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
);
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is escaped with esc_attr() inside zaso_format_field_extra_id(). ?>
<div <?php echo zaso_format_field_extra_id( $instance['extra_id'] ); ?> class="<?php echo esc_attr( $container_classes ); ?>">
	<ul class="zaso-team-member__grid" role="list">
		<?php foreach ( $members as $member ) : ?>
			<li class="zaso-team-member__item">
				<article class="zaso-team-member__card" aria-label="<?php echo esc_attr( $member['name'] ); ?>">

					<?php if ( ! empty( $member['photo']['src'] ) ) : ?>
						<div class="zaso-team-member__photo-wrap">
							<img
								class="zaso-team-member__photo"
								src="<?php echo esc_url( $member['photo']['src'] ); ?>"
								alt="<?php echo esc_attr( $member['photo']['alt'] ); ?>"
								loading="lazy"
								decoding="async"
							/>
						</div>
					<?php endif; ?>

					<div class="zaso-team-member__info">
						<?php if ( ! empty( $member['name'] ) ) : ?>
							<h3 class="zaso-team-member__name"><?php echo esc_html( $member['name'] ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $member['role'] ) ) : ?>
							<p class="zaso-team-member__role"><?php echo esc_html( $member['role'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $member['bio'] ) ) : ?>
							<p class="zaso-team-member__bio"><?php echo esc_html( $member['bio'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $member['social_links'] ) ) : ?>
							<ul class="zaso-team-member__social" role="list">
								<?php foreach ( $member['social_links'] as $platform => $link ) : ?>
									<?php if ( isset( $social_icons[ $platform ] ) ) : ?>
										<li class="zaso-team-member__social-item">
											<a
												class="zaso-team-member__social-link zaso-team-member__social-link--<?php echo esc_attr( $platform ); ?>"
												href="<?php echo sow_esc_url( $link['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sow_esc_url() is SiteOrigin's URL escaper. ?>"
												aria-label="<?php echo esc_attr( $link['label'] . ' ' . __( '(opens in new tab)', 'zaso' ) ); ?>"
												target="_blank"
												rel="noopener noreferrer"
											>
												<?php echo $social_icons[ $platform ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup, no user input. ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>

				</article>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
