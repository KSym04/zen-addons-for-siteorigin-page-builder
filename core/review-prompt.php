<?php
/**
 * Review prompt and feedback channel.
 *
 * Asks long-term users, once and politely, to leave a WordPress.org review, and
 * points anyone who is not happy at the support forum instead. Reviews are the
 * largest trust signal on the plugin's listing and inside wp-admin search, and
 * the support forum is the feedback channel that tells us what to build next.
 *
 * BACKWARD-COMPAT GUARANTEE: nothing renders until at least 14 days after the
 * clock starts, and the clock starts on the first qualifying admin screen view
 * after this version arrives, so existing installs are never prompted on
 * update day. One choice - review, later, or no thanks - is stored in a single
 * new option; no existing option, widget, or data shape is touched. The notice
 * appears only on the Plugins screen and Zen Addons' own admin pages, never
 * across the whole dashboard.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zaso_review_prompt_state' ) ) :

	/**
	 * Option name holding the prompt state.
	 *
	 * @since 1.10.15
	 * @var string
	 */
	define( 'ZASO_REVIEW_PROMPT_OPTION', 'zaso_review_prompt' );

	/**
	 * URL of the plugin's review form on WordPress.org.
	 *
	 * @since 1.10.15
	 * @var string
	 */
	define( 'ZASO_REVIEW_URL', 'https://wordpress.org/support/plugin/zen-addons-for-siteorigin-page-builder/reviews/#new-post' );

	/**
	 * URL of the plugin's support forum on WordPress.org.
	 *
	 * @since 1.10.15
	 * @var string
	 */
	define( 'ZASO_SUPPORT_URL', 'https://wordpress.org/support/plugin/zen-addons-for-siteorigin-page-builder/' );

	/**
	 * Read the stored prompt state, starting the 14-day clock on first read.
	 *
	 * @since 1.10.15
	 *
	 * @return array{since:int,state:string,later_until:int}
	 */
	function zaso_review_prompt_state() {
		$defaults = array(
			'since'       => 0,
			'state'       => '',
			'later_until' => 0,
		);

		$state = get_option( ZASO_REVIEW_PROMPT_OPTION, array() );
		$state = is_array( $state ) ? array_merge( $defaults, $state ) : $defaults;

		if ( empty( $state['since'] ) ) {
			$state['since'] = time();
			update_option( ZASO_REVIEW_PROMPT_OPTION, $state, false );
		}

		return $state;
	}

	/**
	 * Whether the prompt should render on the current screen for this user.
	 *
	 * @since 1.10.15
	 *
	 * @return bool
	 */
	function zaso_review_prompt_should_show() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		$on_plugins = ( 'plugins' === $screen->id );
		$on_zen     = ( false !== strpos( $screen->id, 'zen-addons' ) );

		if ( ! $on_plugins && ! $on_zen ) {
			return false;
		}

		$state = zaso_review_prompt_state();

		if ( 'dismissed' === $state['state'] ) {
			return false;
		}

		if ( 'later' === $state['state'] && time() < (int) $state['later_until'] ) {
			return false;
		}

		// At least 14 days of real use before we ask.
		return ( time() - (int) $state['since'] ) >= 14 * DAY_IN_SECONDS;
	}

	/**
	 * Handle the prompt's action links (review clicked, later, no thanks).
	 *
	 * Runs on admin_init so the redirect happens before any output. Requires
	 * the capability and a valid nonce; anything else is ignored.
	 *
	 * @since 1.10.15
	 *
	 * @return void
	 */
	function zaso_review_prompt_handle_action() {
		if ( ! isset( $_GET['zaso_review_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'zaso_review_prompt' );

		$action = sanitize_key( wp_unslash( $_GET['zaso_review_action'] ) );

		if ( ! in_array( $action, array( 'reviewed', 'later', 'dismiss' ), true ) ) {
			return;
		}

		$state = zaso_review_prompt_state();

		if ( 'later' === $action ) {
			$state['state']       = 'later';
			$state['later_until'] = time() + 30 * DAY_IN_SECONDS;
		} else {
			// 'reviewed' and 'dismiss' both mean: never ask again.
			$state['state'] = 'dismissed';
		}

		update_option( ZASO_REVIEW_PROMPT_OPTION, $state, false );

		if ( 'reviewed' === $action ) {
			// Fixed, plugin-owned destination; not user input, so wp_redirect
			// (wp_safe_redirect would strip the external wordpress.org host).
			wp_redirect( ZASO_REVIEW_URL ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- constant wordpress.org URL, never user input.
			exit;
		}

		wp_safe_redirect( remove_query_arg( array( 'zaso_review_action', '_wpnonce' ) ) );
		exit;
	}
	add_action( 'admin_init', 'zaso_review_prompt_handle_action' );

	/**
	 * Build a self-returning action URL for the prompt.
	 *
	 * @since 1.10.15
	 *
	 * @param string $action Action key: 'reviewed', 'later', or 'dismiss'.
	 * @return string Nonce-protected URL back to the current admin page.
	 */
	function zaso_review_prompt_action_url( $action ) {
		return wp_nonce_url( add_query_arg( 'zaso_review_action', $action ), 'zaso_review_prompt' );
	}

	/**
	 * Render the notice.
	 *
	 * @since 1.10.15
	 *
	 * @return void
	 */
	function zaso_review_prompt_render() {
		if ( ! zaso_review_prompt_should_show() ) {
			return;
		}
		?>
		<div class="notice notice-info zaso-review-notice">
			<p>
				<strong><?php esc_html_e( 'Enjoying Zen Addons for SiteOrigin?', 'zaso' ); ?></strong>
				<?php esc_html_e( 'A short review on WordPress.org helps other SiteOrigin users find the plugin, and it genuinely keeps the project going.', 'zaso' ); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( zaso_review_prompt_action_url( 'reviewed' ) ); ?>"><?php esc_html_e( 'Leave a review', 'zaso' ); ?></a>
				<a class="button" href="<?php echo esc_url( zaso_review_prompt_action_url( 'later' ) ); ?>"><?php esc_html_e( 'Maybe later', 'zaso' ); ?></a>
				<a class="button" href="<?php echo esc_url( zaso_review_prompt_action_url( 'dismiss' ) ); ?>"><?php esc_html_e( 'No thanks', 'zaso' ); ?></a>
				<a style="margin-left:8px" href="<?php echo esc_url( ZASO_SUPPORT_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Something not working? Tell us in the support forum.', 'zaso' ); ?></a>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'zaso_review_prompt_render' );

endif;
