<?php
/**
 * Zen Addons management page view.
 *
 * Rendered by ZASO_Admin::render_page(); $this is the ZASO_Admin instance.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$zaso_bundle_ok = (bool) $this->bundle();
$zaso_active    = $this->get_active_map();
$zaso_cats      = $this->get_categories();
$zaso_ids       = $this->get_widget_ids();
$zaso_total     = count( $zaso_ids );
$zaso_active_ct = 0;
foreach ( $zaso_ids as $zaso_id ) {
	if ( ! empty( $zaso_active[ $zaso_id ] ) ) {
		$zaso_active_ct++;
	}
}
?>
<div class="wrap zaso-admin">
	<h1><?php esc_html_e( 'Zen Addons for SiteOrigin', 'zaso' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag set by our own nonce-verified redirect. ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Widget settings saved.', 'zaso' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! $zaso_bundle_ok ) : ?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'The SiteOrigin Widgets Bundle is not active. Install and activate it to use Zen Addons widgets.', 'zaso' ); ?></p>
		</div>
	<?php endif; ?>

	<p class="zaso-admin__intro">
		<?php
		printf(
			/* translators: 1: number of active widgets, 2: total number of widgets. */
			esc_html__( '%1$d of %2$d widgets active. Turn on the widgets you need, then add them in Page Builder under the "ZASO Widgets" tab.', 'zaso' ),
			(int) $zaso_active_ct,
			(int) $zaso_total
		);
		?>
	</p>

	<div class="zaso-admin__layout">
		<div class="zaso-admin__main">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="zaso_save_widgets" />
				<?php wp_nonce_field( ZASO_Admin::NONCE_ACTION ); ?>

				<?php foreach ( $zaso_cats as $zaso_cat ) : ?>
					<h2 class="zaso-admin__cat"><?php echo esc_html( $zaso_cat['label'] ); ?></h2>
					<div class="zaso-admin__grid">
						<?php
						foreach ( $zaso_cat['widgets'] as $zaso_wid ) :
							$zaso_meta = $this->get_widget_meta( $zaso_wid );
							if ( ! $zaso_meta ) {
								continue;
							}
							$zaso_on   = ! empty( $zaso_active[ $zaso_wid ] );
							$zaso_name = preg_replace( '/^ZASO\s*-\s*/', '', $zaso_meta['name'] );
							?>
							<label class="zaso-card<?php echo $zaso_on ? ' is-active' : ''; ?>">
								<input type="checkbox" name="zaso_widgets[]" value="<?php echo esc_attr( $zaso_wid ); ?>" <?php checked( $zaso_on ); ?> <?php disabled( ! $zaso_bundle_ok ); ?> />
								<span class="zaso-card__body">
									<span class="zaso-card__title"><?php echo esc_html( $zaso_name ); ?></span>
									<span class="zaso-card__desc"><?php echo esc_html( $zaso_meta['desc'] ); ?></span>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>

				<?php if ( $zaso_bundle_ok ) : ?>
					<p class="submit zaso-admin__actions">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'zaso' ); ?></button>
						<button type="submit" name="bulk" value="enable_all" class="button"><?php esc_html_e( 'Enable All', 'zaso' ); ?></button>
						<button type="submit" name="bulk" value="disable_all" class="button"><?php esc_html_e( 'Disable All', 'zaso' ); ?></button>
					</p>
				<?php endif; ?>
			</form>
		</div>

		<div class="zaso-admin__aside">
			<div class="zaso-admin__box">
				<h2><?php esc_html_e( 'Where to find your widgets', 'zaso' ); ?></h2>
				<p><?php esc_html_e( 'Active widgets appear in the SiteOrigin Page Builder widget picker under the "ZASO Widgets" tab, and on the Plugins > SiteOrigin Widgets screen.', 'zaso' ); ?></p>
			</div>
			<div class="zaso-admin__box zaso-admin__more">
				<h2><?php esc_html_e( 'More from DopeThemes', 'zaso' ); ?></h2>
				<p><?php esc_html_e( 'Tutorials, themes, and more widgets for the SiteOrigin builder.', 'zaso' ); ?></p>
				<a class="button" href="https://www.dopethemes.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Visit DopeThemes', 'zaso' ); ?></a>
			</div>
		</div>
	</div>
</div>
