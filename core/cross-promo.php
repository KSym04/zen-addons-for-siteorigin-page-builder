<?php
/**
 * Cross-promotion notice for FeedProof for WooCommerce.
 *
 * Tells the subset of Zen Addons users who actually run a WooCommerce store
 * that we also make a free product-feed auditor. WordPress.org search ranks by
 * active installs, so a new plugin cannot be found through the directory; the
 * installed base of an existing plugin is the only audience we own.
 *
 * BACKWARD-COMPAT GUARANTEE: nothing renders until at least 7 days after the
 * clock starts, and the clock starts on the first qualifying admin screen view
 * after this version arrives, so existing installs are never prompted on update
 * day. One choice - later or no thanks - is stored in a single new option; no
 * existing option, widget, or data shape is touched. The notice appears only on
 * the Plugins screen and Zen Addons' own admin pages, never across the whole
 * dashboard, and only when WooCommerce is active and FeedProof is not already
 * present.
 *
 * ONE NOTICE AT A TIME: this yields to the review prompt whenever that prompt
 * would render, so a user never sees two DopeThemes notices stacked on one
 * screen. The two clocks run in parallel, so yielding delays this notice only
 * on the days both would have shown.
 *
 * No external HTTP request is made from this file. The install call to action
 * is a core WordPress plugin-details modal, and the secondary link is an
 * ordinary outbound anchor the user chooses to click.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zaso_cross_promo_state' ) ) :

	/**
	 * Option name holding the notice state.
	 *
	 * @since 1.10.18
	 * @var string
	 */
	define( 'ZASO_CROSS_PROMO_OPTION', 'zaso_cross_promo' );

	/**
	 * WordPress.org slug of the promoted plugin.
	 *
	 * @since 1.10.18
	 * @var string
	 */
	define( 'ZASO_CROSS_PROMO_SLUG', 'feedproof-for-woocommerce' );

	/**
	 * Product page for the promoted plugin, without campaign parameters.
	 *
	 * @since 1.10.18
	 * @var string
	 */
	define( 'ZASO_CROSS_PROMO_INFO_URL', 'https://www.dopethemes.com/downloads/feedproof-for-woocommerce/' );

	/**
	 * Days of real use before the notice is allowed to appear.
	 *
	 * @since 1.10.18
	 * @var int
	 */
	define( 'ZASO_CROSS_PROMO_DELAY_DAYS', 7 );

	/**
	 * Days the notice stays hidden after the user picks "Maybe later".
	 *
	 * @since 1.10.18
	 * @var int
	 */
	define( 'ZASO_CROSS_PROMO_SNOOZE_DAYS', 30 );

	/**
	 * Read the stored notice state, starting the delay clock on first read.
	 *
	 * Only ever called once the identity gates have passed, so the clock starts
	 * when the site first becomes a genuine candidate rather than on activation.
	 *
	 * @since 1.10.18
	 *
	 * @return array{since:int,state:string,later_until:int}
	 */
	function zaso_cross_promo_state() {
		$defaults = array(
			'since'       => 0,
			'state'       => '',
			'later_until' => 0,
		);

		$state = get_option( ZASO_CROSS_PROMO_OPTION, array() );
		$state = is_array( $state ) ? array_merge( $defaults, $state ) : $defaults;

		if ( empty( $state['since'] ) ) {
			$state['since'] = time();
			update_option( ZASO_CROSS_PROMO_OPTION, $state, false );
		}

		return $state;
	}

	/**
	 * Whether FeedProof is already on this site, in either edition.
	 *
	 * Checks the plugin directory rather than the active-plugins list on
	 * purpose: someone who installed it and deactivated it has already made
	 * their decision and should not be pitched again.
	 *
	 * @since 1.10.18
	 *
	 * @return bool True when the free or Pro plugin folder exists.
	 */
	function zaso_cross_promo_is_feedproof_present() {
		if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
			$present = true; // Cannot tell; stay quiet.
		} elseif ( is_dir( WP_PLUGIN_DIR . '/' . ZASO_CROSS_PROMO_SLUG ) ) {
			$present = true;
		} else {
			$present = is_dir( WP_PLUGIN_DIR . '/' . ZASO_CROSS_PROMO_SLUG . '-pro' );
		}

		/**
		 * Filter whether FeedProof counts as already present on this site.
		 *
		 * Returning true suppresses the cross-promotion notice permanently
		 * without storing any state, which is the supported way for a site owner
		 * or an mu-plugin to opt out of it.
		 *
		 * @since 1.10.18
		 *
		 * @param bool $present Whether the free or Pro plugin folder was found.
		 */
		return (bool) apply_filters( 'zaso_cross_promo_feedproof_present', $present );
	}

	/**
	 * Screen key used for campaign attribution, or an empty string off-screen.
	 *
	 * @since 1.10.18
	 *
	 * @return string 'plugins_screen', 'zen_screen', or ''.
	 */
	function zaso_cross_promo_screen_key() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return '';
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return '';
		}

		if ( 'plugins' === $screen->id ) {
			return 'plugins_screen';
		}

		if ( false !== strpos( $screen->id, 'zen-addons' ) ) {
			return 'zen_screen';
		}

		return '';
	}

	/**
	 * Whether the notice should render on the current screen for this user.
	 *
	 * Gate order matters. The identity gates run first so the delay clock only
	 * starts for a site that is genuinely a candidate, and the review-prompt
	 * yield runs last so the two clocks advance in parallel.
	 *
	 * @since 1.10.18
	 *
	 * @return bool
	 */
	function zaso_cross_promo_should_show() {
		// The call to action installs a plugin, so anyone who cannot install one
		// has nothing to act on.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		if ( '' === zaso_cross_promo_screen_key() ) {
			return false;
		}

		// FeedProof audits WooCommerce products. Without WooCommerce it is noise.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		if ( zaso_cross_promo_is_feedproof_present() ) {
			return false;
		}

		$state = zaso_cross_promo_state();

		if ( 'dismissed' === $state['state'] ) {
			return false;
		}

		if ( 'later' === $state['state'] && time() < (int) $state['later_until'] ) {
			return false;
		}

		if ( ( time() - (int) $state['since'] ) < ZASO_CROSS_PROMO_DELAY_DAYS * DAY_IN_SECONDS ) {
			return false;
		}

		// One DopeThemes notice at a time: the review prompt always wins.
		if ( function_exists( 'zaso_review_prompt_should_show' ) && zaso_review_prompt_should_show() ) {
			return false;
		}

		return true;
	}

	/**
	 * Handle the notice's action links (later, no thanks).
	 *
	 * Runs on admin_init so the redirect happens before any output. Requires the
	 * capability and a valid nonce; an unrecognised action is ignored rather
	 * than falling through to a permanent dismiss.
	 *
	 * There is deliberately no "installed" action. Once FeedProof is present the
	 * folder check suppresses the notice on its own, so recording the click
	 * would duplicate state we can already read from the filesystem.
	 *
	 * @since 1.10.18
	 *
	 * @return void
	 */
	function zaso_cross_promo_handle_action() {
		if ( ! isset( $_GET['zaso_cross_promo_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		check_admin_referer( 'zaso_cross_promo' );

		$action = sanitize_key( wp_unslash( $_GET['zaso_cross_promo_action'] ) );

		if ( ! in_array( $action, array( 'later', 'dismiss' ), true ) ) {
			return;
		}

		$state = zaso_cross_promo_state();

		if ( 'later' === $action ) {
			$state['state']       = 'later';
			$state['later_until'] = time() + ZASO_CROSS_PROMO_SNOOZE_DAYS * DAY_IN_SECONDS;
		} else {
			$state['state'] = 'dismissed';
		}

		update_option( ZASO_CROSS_PROMO_OPTION, $state, false );

		wp_safe_redirect( remove_query_arg( array( 'zaso_cross_promo_action', '_wpnonce' ) ) );
		exit;
	}
	add_action( 'admin_init', 'zaso_cross_promo_handle_action' );

	/**
	 * Build a self-returning action URL for the notice.
	 *
	 * @since 1.10.18
	 *
	 * @param string $action Action key: 'later' or 'dismiss'.
	 * @return string Nonce-protected URL back to the current admin page.
	 */
	function zaso_cross_promo_action_url( $action ) {
		return wp_nonce_url( add_query_arg( 'zaso_cross_promo_action', $action ), 'zaso_cross_promo' );
	}

	/**
	 * URL of the core plugin-details modal for the promoted plugin.
	 *
	 * Opening core's own modal keeps the user inside wp-admin and gives them the
	 * standard Install Now button, the changelog, and the reviews.
	 *
	 * @since 1.10.18
	 *
	 * @return string
	 */
	function zaso_cross_promo_details_url() {
		return add_query_arg(
			array(
				'tab'       => 'plugin-information',
				'plugin'    => ZASO_CROSS_PROMO_SLUG,
				'TB_iframe' => 'true',
				'width'     => '772',
				'height'    => '577',
			),
			self_admin_url( 'plugin-install.php' )
		);
	}

	/**
	 * Product page URL carrying this campaign's attribution parameters.
	 *
	 * Mirrors the scheme already used by the Pro upsell in core/widget-design.php
	 * so both campaigns can be read from one server-side log query.
	 *
	 * @since 1.10.18
	 *
	 * @return string
	 */
	function zaso_cross_promo_info_url() {
		return add_query_arg(
			array(
				'utm_source'   => 'zen-addons',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'cross-promo',
				'utm_content'  => zaso_cross_promo_screen_key(),
			),
			ZASO_CROSS_PROMO_INFO_URL
		);
	}

	/**
	 * Load core's thickbox assets when the notice is about to render.
	 *
	 * Enqueued conditionally so no site that never sees the notice pays for the
	 * scripts. The Plugins screen does not load thickbox by default.
	 *
	 * @since 1.10.18
	 *
	 * @return void
	 */
	function zaso_cross_promo_enqueue() {
		if ( ! zaso_cross_promo_should_show() ) {
			return;
		}

		add_thickbox();
		wp_enqueue_script( 'plugin-install' );
	}
	add_action( 'admin_enqueue_scripts', 'zaso_cross_promo_enqueue' );

	/**
	 * Render the notice.
	 *
	 * @since 1.10.18
	 *
	 * @return void
	 */
	function zaso_cross_promo_render() {
		if ( ! zaso_cross_promo_should_show() ) {
			return;
		}
		?>
		<div class="notice notice-info zaso-cross-promo-notice">
			<p>
				<strong><?php esc_html_e( 'Running a WooCommerce store?', 'zen-addons-for-siteorigin-page-builder' ); ?></strong>
				<?php esc_html_e( 'We also make FeedProof, a free plugin that checks your products against Google Merchant Center feed rules before Google does, and tells you exactly what to fix. Same team as Zen Addons.', 'zen-addons-for-siteorigin-page-builder' ); ?>
			</p>
			<p>
				<a class="button button-primary thickbox open-plugin-details-modal" href="<?php echo esc_url( zaso_cross_promo_details_url() ); ?>"><?php esc_html_e( 'View FeedProof', 'zen-addons-for-siteorigin-page-builder' ); ?></a>
				<a class="button" href="<?php echo esc_url( zaso_cross_promo_action_url( 'later' ) ); ?>"><?php esc_html_e( 'Maybe later', 'zen-addons-for-siteorigin-page-builder' ); ?></a>
				<a class="button" href="<?php echo esc_url( zaso_cross_promo_action_url( 'dismiss' ) ); ?>"><?php esc_html_e( 'No thanks', 'zen-addons-for-siteorigin-page-builder' ); ?></a>
				<a style="margin-left:8px" href="<?php echo esc_url( zaso_cross_promo_info_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'See what it checks', 'zen-addons-for-siteorigin-page-builder' ); ?></a>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'zaso_cross_promo_render' );

endif;
