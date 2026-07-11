<?php
/**
 * Zen Addons "Visual Design Picker" editor enhancer (Alert Box + Counter + Call to Action + Pricing Table + Testimonial Slider).
 *
 * Replaces a widget's plain "Design" ( design_variant ) dropdown in the Page
 * Builder / widgets editor with a "Browse designs" button that opens a modal
 * gallery of REAL rendered screenshots, one per design. Picking a card stages
 * the choice; the modal's Apply button writes the value to the native <select>
 * and dispatches a `change` event so SiteOrigin persists it. The <select> stays
 * in the DOM as the source of truth and the no-JS fallback.
 *
 * The picker serves MORE than one widget. Each supported widget contributes a
 * self-contained entry to the localized `widgets` array: its own design cards,
 * its own design-id set (used by the JS to match the right <select>), its own
 * blurred/locked Pro upsell cards and its own UI strings. The id sets are kept
 * disjoint per widget, so the JS never binds one widget's gallery to another's
 * dropdown.
 *
 * Free ships six design thumbnails per widget; Zen Addons Pro contributes the
 * remaining twenty-four. On the free plugin (Pro off), those twenty-four are
 * still shown, but as BLURRED, LOCKED upsell cards fed by a separate render-only
 * `lockedDesigns` channel built from bundled previews plus a static label list.
 * Locked cards are never written to the design <select>; clicking one opens the
 * upgrade page.
 *
 * This class extends ZASO_Widget_Design only to reuse its ensure_widget_class()
 * helper and PRO_URL constant. It deliberately does NOT call the parent
 * constructor, so no second admin menu is registered.
 *
 * @package Zen Addons for SiteOrigin Page Builder
 * @since 1.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'zaso_alert_box_free_design_previews' ) ) :
	/**
	 * Supply the six free Alert Box design thumbnails to the preview filter.
	 *
	 * Keyed by design id; each maps to a webp rendered from the real design. Pro
	 * appends its twenty-four through the same filter at a later priority.
	 *
	 * @since  1.11.0
	 *
	 * @param  array $previews Existing id => URL map.
	 * @return array
	 */
	function zaso_alert_box_free_design_previews( $previews ) {
		$previews = (array) $previews;
		$free_ids = array( 'left-accent', 'soft-tint', 'outlined', 'icon-badge', 'top-bar', 'solid' );

		foreach ( $free_ids as $id ) {
			$previews[ $id ] = ZASO_BASE_DIR . 'assets/design-previews/' . $id . '.webp';
		}

		return $previews;
	}
	add_filter( 'zaso_alert_design_previews', 'zaso_alert_box_free_design_previews', 5 );
endif;

if ( ! class_exists( 'ZASO_Design_Picker' ) && class_exists( 'ZASO_Widget_Design' ) ) :

	/**
	 * Class ZASO_Design_Picker
	 *
	 * Enqueues the design-picker assets on editor screens and localizes one entry
	 * per supported widget ( Alert Box, Counter, Call to Action ), each entry carrying its design
	 * cards with rendered thumbnail URLs, its id set and its Pro upsell cards.
	 *
	 * @since 1.11.0
	 */
	class ZASO_Design_Picker extends ZASO_Widget_Design {

		/**
		 * Script + style handle.
		 *
		 * @since 1.11.0
		 * @var string
		 */
		const HANDLE = 'zaso-design-picker';

		/**
		 * The six free Alert Box design ids. Everything else is a Pro design, so
		 * the picker can badge cards and gate the upsell without a license probe
		 * per card.
		 *
		 * @since 1.11.0
		 * @var array
		 */
		const FREE_IDS = array( 'left-accent', 'soft-tint', 'outlined', 'icon-badge', 'top-bar', 'solid' );

		/**
		 * The six free Counter design ids.
		 *
		 * @since 1.12.0
		 * @var array
		 */
		const COUNTER_FREE_IDS = array( 'icon-card', 'centered', 'icon-top', 'badge', 'divider', 'underline' );

		/**
		 * The six free Call to Action design ids.
		 *
		 * @since 1.10.7
		 * @var array
		 */
		const CTA_FREE_IDS = array( 'solid-centered', 'horizontal-split', 'soft-tint', 'gradient-centered', 'outlined', 'dark' );

		/**
		 * The six free Pricing Table design ids.
		 *
		 * @since 1.11.0
		 * @var array
		 */
		const PRICING_TABLE_FREE_IDS = array( 'classic-indigo', 'classic-teal', 'accent-indigo', 'accent-rose', 'minimal-slate', 'minimal-violet' );

		/**
		 * The six free Testimonial Slider design ids.
		 *
		 * @since 1.11.0
		 * @var array
		 */
		const TESTIMONIAL_SLIDER_FREE_IDS = array( 'centered-indigo', 'centered-teal', 'avatar-left-slate', 'avatar-left-violet', 'quote-mark-rose', 'quote-mark-amber' );

		/**
		 * The six free Hover Card design ids.
		 *
		 * @since 1.12.0
		 * @var array
		 */
		const HOVER_CARD_FREE_IDS = array( 'slide-up-frosted', 'slide-up-dark', 'slide-up-tinted', 'overlay-scrim', 'overlay-solid', 'overlay-gradient' );

		/**
		 * The six free Services Grid design ids.
		 *
		 * @since 1.10.12
		 * @var array
		 */
		const SERVICES_GRID_FREE_IDS = array( 'centered', 'inline', 'chip-badge', 'borderless', 'icon-top-right', 'icon-over-title' );

		/**
		 * The twenty-four Pro Alert Box designs ( id => label ), mirrored from the
		 * Pro plugin's Zanp_Alert_Designs::pro_designs() so the FREE plugin can show
		 * them as blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/{id}.webp. This
		 * list is render-only: these ids are NEVER added to designIds, so they are
		 * never matched against or written to the design_variant <select>.
		 *
		 * @since 1.11.0
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_designs() {
			return array(
				'gradient'      => esc_html__( 'Gradient', 'zaso' ),
				'dark'          => esc_html__( 'Dark Mode', 'zaso' ),
				'split-panel'   => esc_html__( 'Split Panel', 'zaso' ),
				'glass'         => esc_html__( 'Glass', 'zaso' ),
				'toast'         => esc_html__( 'Toast', 'zaso' ),
				'big-icon'      => esc_html__( 'Big Icon', 'zaso' ),
				'pill'          => esc_html__( 'Pill', 'zaso' ),
				'dashed'        => esc_html__( 'Dashed', 'zaso' ),
				'double'        => esc_html__( 'Double Border', 'zaso' ),
				'banner'        => esc_html__( 'Banner', 'zaso' ),
				'tag-label'     => esc_html__( 'Tag Label', 'zaso' ),
				'ghost'         => esc_html__( 'Ghost Tint', 'zaso' ),
				'underline'     => esc_html__( 'Underline', 'zaso' ),
				'round-badge'   => esc_html__( 'Round Badge', 'zaso' ),
				'two-tone'      => esc_html__( 'Two-tone', 'zaso' ),
				'right-accent'  => esc_html__( 'Right Accent', 'zaso' ),
				'floating-icon' => esc_html__( 'Floating Icon', 'zaso' ),
				'dotted'        => esc_html__( 'Dotted', 'zaso' ),
				'deep-solid'    => esc_html__( 'Deep Solid', 'zaso' ),
				'terminal'      => esc_html__( 'Terminal', 'zaso' ),
				'title-bar'     => esc_html__( 'Title Bar', 'zaso' ),
				'action-cta'    => esc_html__( 'Action CTA', 'zaso' ),
				'gradient-ring' => esc_html__( 'Gradient Ring', 'zaso' ),
				'centered'      => esc_html__( 'Centered', 'zaso' ),
			);
		}

		/**
		 * The twenty-four Pro Counter designs ( id => label ), mirrored from the
		 * Pro plugin's Zanp_Counter_Designs so the FREE plugin can show them as
		 * blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/counter/{id}.webp.
		 * Render-only: these ids are NEVER added to designIds.
		 *
		 * @since 1.12.0
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_counter_designs() {
			return array(
				'gradient-text'  => esc_html__( 'Gradient Text', 'zaso' ),
				'dark-card'      => esc_html__( 'Dark Card', 'zaso' ),
				'progress-ring'  => esc_html__( 'Progress Ring', 'zaso' ),
				'trend'          => esc_html__( 'Trend', 'zaso' ),
				'solid-fill'     => esc_html__( 'Solid Fill', 'zaso' ),
				'split-panel'    => esc_html__( 'Split Panel', 'zaso' ),
				'progress-bar'   => esc_html__( 'Progress Bar', 'zaso' ),
				'sparkline'      => esc_html__( 'Sparkline', 'zaso' ),
				'glass'          => esc_html__( 'Glass', 'zaso' ),
				'currency'       => esc_html__( 'Currency', 'zaso' ),
				'ghost-outline'  => esc_html__( 'Ghost Outline', 'zaso' ),
				'centered-badge' => esc_html__( 'Centered Badge', 'zaso' ),
				'mono-metric'    => esc_html__( 'Mono Metric', 'zaso' ),
				'watermark'      => esc_html__( 'Watermark', 'zaso' ),
				'pill'           => esc_html__( 'Pill', 'zaso' ),
				'accent-bar'     => esc_html__( 'Accent Bar', 'zaso' ),
				'eyebrow'        => esc_html__( 'Eyebrow', 'zaso' ),
				'two-tone'       => esc_html__( 'Two-tone', 'zaso' ),
				'suffix'         => esc_html__( 'Suffix', 'zaso' ),
				'ring-icon'      => esc_html__( 'Ring Icon', 'zaso' ),
				'banner'         => esc_html__( 'Banner', 'zaso' ),
				'footer-note'    => esc_html__( 'Footer Note', 'zaso' ),
				'gradient-ring'  => esc_html__( 'Gradient Ring', 'zaso' ),
				'centered-icon'  => esc_html__( 'Centered Icon', 'zaso' ),
			);
		}

		/**
		 * The twenty-four Pro Call to Action designs ( id => label ), mirrored from
		 * the Pro plugin's Zanp_Cta_Designs so the FREE plugin can show them as
		 * blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/cta-banner/{id}.webp.
		 * Render-only: these ids are NEVER added to designIds.
		 *
		 * @since 1.10.7
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_cta_designs() {
			return array(
				'bold-gradient'    => esc_html__( 'Bold Gradient', 'zaso' ),
				'dark-glow'        => esc_html__( 'Dark Glow', 'zaso' ),
				'image-scrim'      => esc_html__( 'Image Scrim', 'zaso' ),
				'image-horizontal' => esc_html__( 'Image Horizontal', 'zaso' ),
				'split-block'      => esc_html__( 'Split Block', 'zaso' ),
				'eyebrow'          => esc_html__( 'Eyebrow', 'zaso' ),
				'icon-row'         => esc_html__( 'Icon Row', 'zaso' ),
				'pastel-pill'      => esc_html__( 'Pastel Pill', 'zaso' ),
				'dotted-gradient'  => esc_html__( 'Dotted Gradient', 'zaso' ),
				'stacked-dark'     => esc_html__( 'Stacked Dark', 'zaso' ),
				'glass'            => esc_html__( 'Glass', 'zaso' ),
				'arrow-link'       => esc_html__( 'Arrow Link', 'zaso' ),
				'big-type'         => esc_html__( 'Big Type', 'zaso' ),
				'badge-tag'        => esc_html__( 'Badge Tag', 'zaso' ),
				'stats'            => esc_html__( 'Stats', 'zaso' ),
				'gradient-heading' => esc_html__( 'Gradient Heading', 'zaso' ),
				'image-bottom'     => esc_html__( 'Image Bottom', 'zaso' ),
				'two-column'       => esc_html__( 'Two Column', 'zaso' ),
				'pill-banner'      => esc_html__( 'Pill Banner', 'zaso' ),
				'left-accent'      => esc_html__( 'Left Accent', 'zaso' ),
				'vibrant-mesh'     => esc_html__( 'Vibrant Mesh', 'zaso' ),
				'corporate'        => esc_html__( 'Corporate', 'zaso' ),
				'bold-solid'       => esc_html__( 'Bold Solid', 'zaso' ),
				'gradient-ring'    => esc_html__( 'Gradient Ring', 'zaso' ),
			);
		}

		/**
		 * The twenty-four Pro Pricing Table designs ( id => label ), mirrored from
		 * the Pro plugin's Zanp_Pricing_Table_Designs so the FREE plugin can show
		 * them as blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/pricing-table/{id}.webp.
		 * Render-only: these ids are NEVER added to designIds.
		 *
		 * @since 1.11.0
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_pricing_table_designs() {
			return array(
				'ribbon-indigo'        => esc_html__( 'Ribbon (Indigo)', 'zaso' ),
				'ribbon-dark'          => esc_html__( 'Ribbon (Dark)', 'zaso' ),
				'featured-violet'      => esc_html__( 'Featured (Violet)', 'zaso' ),
				'featured-teal'        => esc_html__( 'Featured (Teal)', 'zaso' ),
				'comparison-light'     => esc_html__( 'Comparison (Light)', 'zaso' ),
				'comparison-dark'      => esc_html__( 'Comparison (Dark)', 'zaso' ),
				'gradient-sunset'      => esc_html__( 'Gradient Header (Sunset)', 'zaso' ),
				'gradient-blue'        => esc_html__( 'Gradient Header (Blue)', 'zaso' ),
				'split-slate'          => esc_html__( 'Split Panel (Slate)', 'zaso' ),
				'split-emerald'        => esc_html__( 'Split Panel (Emerald)', 'zaso' ),
				'darkpremium-indigo'   => esc_html__( 'Dark Premium (Indigo)', 'zaso' ),
				'darkpremium-teal'     => esc_html__( 'Dark Premium (Teal)', 'zaso' ),
				'twotone-amber'        => esc_html__( 'Two-tone (Amber)', 'zaso' ),
				'twotone-emerald'      => esc_html__( 'Two-tone (Emerald)', 'zaso' ),
				'gradientring-fuchsia' => esc_html__( 'Gradient Ring (Fuchsia)', 'zaso' ),
				'gradientring-blue'    => esc_html__( 'Gradient Ring (Blue)', 'zaso' ),
				'iconfeatures-indigo'  => esc_html__( 'Icon Features (Indigo)', 'zaso' ),
				'iconfeatures-teal'    => esc_html__( 'Icon Features (Teal)', 'zaso' ),
				'compact-slate'        => esc_html__( 'Compact (Slate)', 'zaso' ),
				'compact-violet'       => esc_html__( 'Compact (Violet)', 'zaso' ),
				'stacked-rose'         => esc_html__( 'Stacked Badge (Rose)', 'zaso' ),
				'stacked-amber'        => esc_html__( 'Stacked Badge (Amber)', 'zaso' ),
				'minitable-light'      => esc_html__( 'Mini Table (Light)', 'zaso' ),
				'minitable-dark'       => esc_html__( 'Mini Table (Dark)', 'zaso' ),
			);
		}

		/**
		 * The twenty-four Pro Testimonial Slider designs ( id => label ), mirrored
		 * from the Pro plugin's Zanp_Testimonial_Slider_Designs so the FREE plugin can
		 * show them as blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/testimonial-slider/{id}.webp.
		 * Render-only: these ids are NEVER added to designIds.
		 *
		 * @since 1.11.0
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_testimonial_slider_designs() {
			return array(
				'gradient-quote-indigo' => esc_html__( 'Gradient Quote (Indigo)', 'zaso' ),
				'gradient-quote-sunset' => esc_html__( 'Gradient Quote (Sunset)', 'zaso' ),
				'dark-sky'              => esc_html__( 'Dark (Sky Glow)', 'zaso' ),
				'dark-violet'           => esc_html__( 'Dark (Violet Glow)', 'zaso' ),
				'arrows-footer-teal'    => esc_html__( 'Arrows Footer (Teal)', 'zaso' ),
				'arrows-footer-indigo'  => esc_html__( 'Arrows Footer (Indigo)', 'zaso' ),
				'tinted-violet'         => esc_html__( 'Soft Tint (Violet)', 'zaso' ),
				'tinted-emerald'        => esc_html__( 'Soft Tint (Emerald)', 'zaso' ),
				'logo-quote-slate'      => esc_html__( 'Company Logo (Slate)', 'zaso' ),
				'logo-quote-blue'       => esc_html__( 'Company Logo (Blue)', 'zaso' ),
				'stat-indigo'           => esc_html__( 'Stat Highlight (Indigo)', 'zaso' ),
				'stat-rose'             => esc_html__( 'Stat Highlight (Rose)', 'zaso' ),
				'split-panel-indigo'    => esc_html__( 'Split Panel (Indigo)', 'zaso' ),
				'split-panel-teal'      => esc_html__( 'Split Panel (Teal)', 'zaso' ),
				'accent-top-amber'      => esc_html__( 'Accent Top (Amber)', 'zaso' ),
				'accent-top-cyan'       => esc_html__( 'Accent Top (Cyan)', 'zaso' ),
				'verified-emerald'      => esc_html__( 'Verified (Emerald)', 'zaso' ),
				'verified-indigo'       => esc_html__( 'Verified (Indigo)', 'zaso' ),
				'minimal-slate'         => esc_html__( 'Minimal (Slate)', 'zaso' ),
				'minimal-violet'        => esc_html__( 'Minimal (Violet)', 'zaso' ),
				'watermark-fuchsia'     => esc_html__( 'Quote Watermark (Fuchsia)', 'zaso' ),
				'watermark-blue'        => esc_html__( 'Quote Watermark (Blue)', 'zaso' ),
				'spotlight-violet'      => esc_html__( 'Spotlight (Violet)', 'zaso' ),
				'spotlight-sunset'      => esc_html__( 'Spotlight (Sunset)', 'zaso' ),
			);
		}

		/**
		 * The twenty-four Pro Hover Card designs ( id => label ), mirrored from the
		 * Pro plugin's Zanp_Hover_Card_Designs so the FREE plugin can show them as
		 * blurred, locked upsell cards WITHOUT depending on the Pro filter.
		 *
		 * Each id has a bundled thumbnail at assets/design-previews/hover-card/{id}.webp.
		 * Render-only: these ids are NEVER added to designIds.
		 *
		 * @since 1.12.0
		 * @return array Map of Pro design id => human label.
		 */
		protected function locked_pro_hover_card_designs() {
			return array(
				'side-panel-white'  => esc_html__( 'Side Panel White', 'zaso' ),
				'side-panel-dark'   => esc_html__( 'Side Panel Dark', 'zaso' ),
				'side-panel-amber'  => esc_html__( 'Side Panel Amber', 'zaso' ),
				'strips-white'      => esc_html__( 'Strips White', 'zaso' ),
				'strips-dark'       => esc_html__( 'Strips Dark', 'zaso' ),
				'strips-accent'     => esc_html__( 'Strips Accent', 'zaso' ),
				'corner-white'      => esc_html__( 'Corner White', 'zaso' ),
				'corner-dark'       => esc_html__( 'Corner Dark', 'zaso' ),
				'corner-accent'     => esc_html__( 'Corner Accent', 'zaso' ),
				'centered-light'    => esc_html__( 'Centered Light', 'zaso' ),
				'centered-dark'     => esc_html__( 'Centered Dark', 'zaso' ),
				'centered-accent'   => esc_html__( 'Centered Accent', 'zaso' ),
				'type-strip-white'  => esc_html__( 'Type Strip White', 'zaso' ),
				'type-strip-dark'   => esc_html__( 'Type Strip Dark', 'zaso' ),
				'type-strip-tinted' => esc_html__( 'Type Strip Tinted', 'zaso' ),
				'tint-blue'         => esc_html__( 'Tint Blue', 'zaso' ),
				'tint-dark'         => esc_html__( 'Tint Dark', 'zaso' ),
				'tint-amber'        => esc_html__( 'Tint Amber', 'zaso' ),
				'glass-light'       => esc_html__( 'Glass Light', 'zaso' ),
				'glass-dark'        => esc_html__( 'Glass Dark', 'zaso' ),
				'glass-frosted'     => esc_html__( 'Glass Frosted', 'zaso' ),
				'editorial-white'   => esc_html__( 'Editorial White', 'zaso' ),
				'editorial-dark'    => esc_html__( 'Editorial Dark', 'zaso' ),
				'editorial-accent'  => esc_html__( 'Editorial Accent', 'zaso' ),
			);
		}

		/**
		 * The twenty-four Pro Services Grid designs ( id => label ), mirrored from
		 * the Pro plugin's Zanp_Services_Grid_Designs::pro_designs() so the FREE
		 * plugin can show them as blurred, locked upsell cards WITHOUT depending on
		 * the Pro filter.
		 *
		 * Each id has a bundled thumbnail at
		 * assets/design-previews/services-grid/{id}.webp. This list is render-only:
		 * these ids are NEVER added to designIds, so they are never written to the
		 * design <select>.
		 *
		 * @since  1.10.12
		 * @return array Map of design id => human label.
		 */
		protected function locked_pro_services_grid_designs() {
			return array(
				'gradient-tile'  => esc_html__( 'Gradient Tile', 'zaso' ),
				'icon-ring'      => esc_html__( 'Icon Ring', 'zaso' ),
				'dark-glow'      => esc_html__( 'Dark Glow', 'zaso' ),
				'accent-bar'     => esc_html__( 'Accent Bar', 'zaso' ),
				'tinted-bg'      => esc_html__( 'Tinted Background', 'zaso' ),
				'split-panel'    => esc_html__( 'Split Panel', 'zaso' ),
				'ghost-number'   => esc_html__( 'Ghost Number', 'zaso' ),
				'stat-footer'    => esc_html__( 'Stat Footer', 'zaso' ),
				'button-footer'  => esc_html__( 'Button Footer', 'zaso' ),
				'tag-list'       => esc_html__( 'Tag List', 'zaso' ),
				'corner-icon'    => esc_html__( 'Corner Icon', 'zaso' ),
				'gradient-card'  => esc_html__( 'Gradient Card', 'zaso' ),
				'banner-header'  => esc_html__( 'Banner Header', 'zaso' ),
				'progress-bar'   => esc_html__( 'Progress Bar', 'zaso' ),
				'dashed-ghost'   => esc_html__( 'Dashed Ghost', 'zaso' ),
				'timeline-step'  => esc_html__( 'Timeline Step', 'zaso' ),
				'diagonal-split' => esc_html__( 'Diagonal Split', 'zaso' ),
				'watermark'      => esc_html__( 'Watermark', 'zaso' ),
				'price-tag'      => esc_html__( 'Price Tag', 'zaso' ),
				'checklist'      => esc_html__( 'Checklist', 'zaso' ),
				'gradient-ring'  => esc_html__( 'Gradient Ring', 'zaso' ),
				'corner-ribbon'  => esc_html__( 'Corner Ribbon', 'zaso' ),
				'team-stack'     => esc_html__( 'Team Stack', 'zaso' ),
				'cta-bar'        => esc_html__( 'CTA Bar', 'zaso' ),
			);
		}

		/**
		 * Hook only the editor asset enqueue. The parent constructor is NOT called
		 * on purpose: this subclass must not register the Design Library menu again.
		 *
		 * @since 1.11.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * Editor screens where a SiteOrigin widget form can appear.
		 *
		 * @since  1.11.0
		 *
		 * @param  string $hook Current admin page hook suffix.
		 * @return bool True when the design picker should load.
		 */
		protected function is_editor_screen( $hook ) {
			return in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php' ), true );
		}

		/**
		 * Translated UI strings for one widget's modal, with the widget noun woven
		 * into the subtitle.
		 *
		 * @since  1.12.0
		 *
		 * @param  string $noun Widget noun used in the subtitle ( "alert", "counter" ).
		 * @return array Map of i18n key => translated string.
		 */
		protected function build_i18n( $noun ) {
			return array(
				'browse'   => esc_html__( 'Browse designs', 'zaso' ),
				'choose'   => esc_html__( 'Choose a pre-made design', 'zaso' ),
				'subtitle' => sprintf(
					/* translators: %s: widget noun, e.g. "alert" or "counter". */
					esc_html__( 'A pre-made design styles the whole %s in one click. Pick one, then Apply.', 'zaso' ),
					$noun
				),
				'free'      => esc_html__( 'Free', 'zaso' ),
				'pro'       => esc_html__( 'Pro', 'zaso' ),
				'locked'    => esc_html__( 'This design is part of Zen Addons Pro. Upgrade to use it.', 'zaso' ),
				'unlock'    => esc_html__( 'Unlock the full design library with Zen Addons Pro.', 'zaso' ),
				'unlockAll' => esc_html__( 'Unlock all designs with Pro', 'zaso' ),
				'close'     => esc_html__( 'Close', 'zaso' ),
				'apply'     => esc_html__( 'Apply', 'zaso' ),
				'cancel'    => esc_html__( 'Cancel', 'zaso' ),
			);
		}

		/**
		 * Assemble one self-contained widget entry for the localized data.
		 *
		 * Shared by every supported widget. The caller supplies the widget's design
		 * options ( id => label, already license-aware ), its free id set, its
		 * static Pro id => label map for the locked upsell channel, a preview-URL
		 * resolver and the modal noun. License + white-label state are resolved
		 * here once.
		 *
		 * @since  1.12.0
		 *
		 * @param  array    $args {
		 *     @type string   $key         Widget key ( "alert", "counter" ).
		 *     @type array    $options     Design options ( id => label, '' = default ).
		 *     @type array    $free_ids    Ids that are usable without Pro.
		 *     @type array    $locked_map  Static Pro id => label map for upsell cards.
		 *     @type callable $preview_url Resolver: ( string $id ) => string URL.
		 *     @type string   $noun        Modal subtitle noun.
		 *     @type string   $default     Fallback label for the classic default card.
		 * }
		 * @return array The entry, or an empty array when the widget has no designs.
		 */
		protected function build_entry( $args ) {
			$options = isset( $args['options'] ) ? $args['options'] : null;
			if ( ! is_array( $options ) || empty( $options ) ) {
				return array();
			}

			$free_ids    = isset( $args['free_ids'] ) ? (array) $args['free_ids'] : array();
			$preview_url = $args['preview_url'];

			$default_label = isset( $options[''] ) ? (string) $options[''] : (string) $args['default'];

			$cards = array();
			$ids   = array();
			foreach ( $options as $id => $label ) {
				$id = (string) $id;
				if ( '' === $id ) {
					continue; // Classic default: rendered as the built-in reset card client-side.
				}

				$url = (string) call_user_func( $preview_url, $id );

				$cards[] = array(
					'id'    => $id,
					'label' => (string) $label,
					'isPro' => ! in_array( $id, $free_ids, true ),
					'img'   => '' !== $url ? esc_url( $url ) : '',
				);
				$ids[] = $id;
			}

			if ( empty( $cards ) ) {
				return array();
			}

			$licensed    = ( class_exists( 'Zanp_Pro' ) && Zanp_Pro::is_licensed() );
			$white_label = ( class_exists( 'Zanp_Settings' ) && Zanp_Settings::is_white_label() );

			// Locked upsell cards: render-only previews of the Pro designs, shown
			// blurred to unlicensed (non-white-label) sites. Sourced from the bundled
			// webp + a STATIC label list, so no Pro filter is required and an
			// unlicensed site has no undefined access. When licensed, the Pro designs
			// are already real, usable cards above, so the locked channel is empty.
			// White-labelled sites never see an upsell.
			$locked = array();
			if ( ! $licensed && ! $white_label && ! empty( $args['locked_map'] ) ) {
				foreach ( (array) $args['locked_map'] as $locked_id => $locked_label ) {
					$url = (string) call_user_func( $preview_url, (string) $locked_id );
					$locked[] = array(
						'id'    => (string) $locked_id,
						'label' => (string) $locked_label,
						'thumb' => '' !== $url ? esc_url( $url ) : '',
					);
				}
			}

			return array(
				'key'           => (string) $args['key'],
				'designs'       => $cards,
				'designIds'     => $ids,
				// Separate, render-only channel for the blurred Pro upsell cards. These
				// ids are deliberately NOT in designIds, so the JS never matches them
				// against, or writes them to, the design_variant <select>.
				'lockedDesigns' => $locked,
				'proUrl'        => self::PRO_URL,
				'licensed'      => $licensed,
				// White-labelled Pro sites must not expose the Free / Pro tier badges
				// to the agency's client. The JS hides every badge when this is true.
				'whiteLabel'    => $white_label,
				'defaultLabel'  => $default_label,
				'i18n'          => $this->build_i18n( (string) $args['noun'] ),
			);
		}

		/**
		 * Build the Alert Box entry.
		 *
		 * The design list ( zaso_alert_box_design_options() ) already reflects the
		 * license: six entries unlicensed, thirty when Pro is active. Preview URLs
		 * come from the `zaso_alert_design_previews` filter ( free supplies six, Pro
		 * twenty-four ).
		 *
		 * @since  1.12.0
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_alert_entry() {
			if ( ! function_exists( 'zaso_alert_box_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Alert_Box_Widget', 'zaso-alert-box-widgets' );
			}
			if ( ! function_exists( 'zaso_alert_box_design_options' ) ) {
				return array();
			}

			$previews = apply_filters( 'zaso_alert_design_previews', array() ); // id => url.
			$previews = is_array( $previews ) ? $previews : array();

			return $this->build_entry(
				array(
					'key'         => 'alert',
					'options'     => zaso_alert_box_design_options(),
					'free_ids'    => self::FREE_IDS,
					'locked_map'  => $this->locked_pro_designs(),
					'noun'        => esc_html__( 'alert', 'zaso' ),
					'default'     => esc_html__( 'Default (classic box)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $previews ) {
						return isset( $previews[ $id ] ) ? (string) $previews[ $id ] : '';
					},
				)
			);
		}

		/**
		 * Build the Counter entry.
		 *
		 * The design list ( zaso_counter_design_options() ) already reflects the
		 * license: six entries unlicensed, thirty when Pro is active ( the Pro
		 * `zaso_counter_designs` filter registers the twenty-four ). Preview URLs
		 * are resolved directly from the bundled thumbnails ( all thirty ship in the
		 * free plugin ), so the picker is fully self-sufficient with Pro off.
		 *
		 * @since  1.12.0
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_counter_entry() {
			if ( ! function_exists( 'zaso_counter_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Counter_Widget', 'zaso-counter-widgets' );
			}
			if ( ! function_exists( 'zaso_counter_design_options' ) ) {
				return array();
			}

			$base = ZASO_BASE_DIR . 'assets/design-previews/counter/';

			return $this->build_entry(
				array(
					'key'         => 'counter',
					'options'     => zaso_counter_design_options(),
					'free_ids'    => self::COUNTER_FREE_IDS,
					'locked_map'  => $this->locked_pro_counter_designs(),
					'noun'        => esc_html__( 'counter', 'zaso' ),
					'default'     => esc_html__( 'Default (classic counter)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $base ) {
						return $base . $id . '.webp';
					},
				)
			);
		}

		/**
		 * Build the Call to Action entry.
		 *
		 * The design list ( zaso_cta_banner_design_options() ) already reflects the
		 * license: six entries unlicensed, thirty when Pro is active ( the Pro
		 * `zaso_cta_designs` filter registers the twenty-four ). Preview URLs are
		 * resolved directly from the bundled thumbnails ( all thirty ship in the free
		 * plugin ), so the picker is fully self-sufficient with Pro off.
		 *
		 * @since  1.10.7
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_cta_entry() {
			if ( ! function_exists( 'zaso_cta_banner_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Cta_Banner_Widget', 'zaso-cta-banner-widgets' );
			}
			if ( ! function_exists( 'zaso_cta_banner_design_options' ) ) {
				return array();
			}

			$base = ZASO_BASE_DIR . 'assets/design-previews/cta-banner/';

			return $this->build_entry(
				array(
					'key'         => 'cta',
					'options'     => zaso_cta_banner_design_options(),
					'free_ids'    => self::CTA_FREE_IDS,
					'locked_map'  => $this->locked_pro_cta_designs(),
					'noun'        => esc_html__( 'call to action', 'zaso' ),
					'default'     => esc_html__( 'Default (classic banner)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $base ) {
						return $base . $id . '.webp';
					},
				)
			);
		}

		/**
		 * Build the Pricing Table entry.
		 *
		 * The design list ( zaso_pricing_table_design_options() ) already reflects the
		 * license: six entries unlicensed, thirty when Pro is active ( the Pro
		 * `zaso_pricing_table_designs` filter registers the twenty-four ). Preview URLs
		 * are resolved directly from the bundled thumbnails ( all thirty ship in the
		 * free plugin ), so the picker is fully self-sufficient with Pro off.
		 *
		 * @since  1.11.0
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_pricing_table_entry() {
			if ( ! function_exists( 'zaso_pricing_table_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Pricing_Table_Widget', 'zaso-pricing-table-widgets' );
			}
			if ( ! function_exists( 'zaso_pricing_table_design_options' ) ) {
				return array();
			}

			$base = ZASO_BASE_DIR . 'assets/design-previews/pricing-table/';

			return $this->build_entry(
				array(
					'key'         => 'pricing-table',
					'options'     => zaso_pricing_table_design_options(),
					'free_ids'    => self::PRICING_TABLE_FREE_IDS,
					'locked_map'  => $this->locked_pro_pricing_table_designs(),
					'noun'        => esc_html__( 'pricing table', 'zaso' ),
					'default'     => esc_html__( 'Default (classic table)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $base ) {
						return $base . $id . '.webp';
					},
				)
			);
		}

		/**
		 * Build the Testimonial Slider entry.
		 *
		 * The design list ( zaso_testimonial_slider_design_options() ) already reflects
		 * the license: six entries unlicensed, thirty when Pro is active ( the Pro
		 * `zaso_testimonial_slider_designs` filter registers the twenty-four ). Preview
		 * URLs are resolved directly from the bundled thumbnails ( all thirty ship in
		 * the free plugin ), so the picker is fully self-sufficient with Pro off.
		 *
		 * @since  1.11.0
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_testimonial_slider_entry() {
			if ( ! function_exists( 'zaso_testimonial_slider_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Testimonial_Slider_Widget', 'zaso-testimonial-slider-widgets' );
			}
			if ( ! function_exists( 'zaso_testimonial_slider_design_options' ) ) {
				return array();
			}

			$base = ZASO_BASE_DIR . 'assets/design-previews/testimonial-slider/';

			return $this->build_entry(
				array(
					'key'         => 'testimonial-slider',
					'options'     => zaso_testimonial_slider_design_options(),
					'free_ids'    => self::TESTIMONIAL_SLIDER_FREE_IDS,
					'locked_map'  => $this->locked_pro_testimonial_slider_designs(),
					'noun'        => esc_html__( 'testimonial slider', 'zaso' ),
					'default'     => esc_html__( 'Default (simple card)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $base ) {
						return $base . $id . '.webp';
					},
				)
			);
		}

		/**
		 * Build the Hover Card entry.
		 *
		 * The design list ( zaso_hover_card_design_options() ) already reflects the
		 * license: six entries unlicensed, thirty when Pro is active ( the Pro
		 * `zaso_hover_card_designs` filter registers the twenty-four ). Preview URLs
		 * are resolved directly from the bundled thumbnails ( all thirty ship in the
		 * free plugin ), so the picker is fully self-sufficient with Pro off.
		 *
		 * @since  1.12.0
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_hover_card_entry() {
			if ( ! function_exists( 'zaso_hover_card_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Hover_Card_Widget', 'zaso-hover-card-widgets' );
			}
			if ( ! function_exists( 'zaso_hover_card_design_options' ) ) {
				return array();
			}

			$base = ZASO_BASE_DIR . 'assets/design-previews/hover-card/';

			return $this->build_entry(
				array(
					'key'         => 'hover-card',
					'options'     => zaso_hover_card_design_options(),
					'free_ids'    => self::HOVER_CARD_FREE_IDS,
					'locked_map'  => $this->locked_pro_hover_card_designs(),
					'noun'        => esc_html__( 'hover card', 'zaso' ),
					'default'     => esc_html__( 'Default (classic hover card)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $base ) {
						return $base . $id . '.webp';
					},
				)
			);
		}

		/**
		 * Build the Services Grid entry.
		 *
		 * The design list ( zaso_services_grid_design_options() ) already reflects
		 * the license: six entries unlicensed, thirty when Pro is active ( the Pro
		 * `zaso_services_grid_designs` filter registers the twenty-four ). Preview
		 * URLs are resolved directly from the bundled thumbnails ( all thirty ship
		 * in the free plugin ), so the picker is fully self-sufficient with Pro off.
		 *
		 * @since  1.10.12
		 * @return array Entry array, or empty when unavailable.
		 */
		protected function build_services_grid_entry() {
			if ( ! function_exists( 'zaso_services_grid_design_options' ) ) {
				$this->ensure_widget_class( 'Zen_Addons_SiteOrigin_Services_Grid_Widget', 'zaso-services-grid-widgets' );
			}
			if ( ! function_exists( 'zaso_services_grid_design_options' ) ) {
				return array();
			}

			$base = ZASO_BASE_DIR . 'assets/design-previews/services-grid/';

			return $this->build_entry(
				array(
					'key'         => 'services-grid',
					'options'     => zaso_services_grid_design_options(),
					'free_ids'    => self::SERVICES_GRID_FREE_IDS,
					'locked_map'  => $this->locked_pro_services_grid_designs(),
					'noun'        => esc_html__( 'services grid', 'zaso' ),
					'default'     => esc_html__( 'Default (grid card)', 'zaso' ),
					'preview_url' => static function ( $id ) use ( $base ) {
						return $base . $id . '.webp';
					},
				)
			);
		}

		/**
		 * Build the localized data map the picker JS consumes.
		 *
		 * Returns a `widgets` array: one self-contained entry per supported widget.
		 * Entries with no designs are dropped, so a missing widget never breaks the
		 * others.
		 *
		 * @since  1.11.0
		 * @return array {
		 *     @type array $widgets List of widget entries ( see build_entry() ).
		 * }
		 */
		public function build_localized_data() {
			$widgets = array();

			foreach ( array( $this->build_alert_entry(), $this->build_counter_entry(), $this->build_cta_entry(), $this->build_pricing_table_entry(), $this->build_testimonial_slider_entry(), $this->build_hover_card_entry(), $this->build_services_grid_entry() ) as $entry ) {
				if ( ! empty( $entry['designs'] ) ) {
					$widgets[] = $entry;
				}
			}

			/**
			 * Let companion plugins contribute picker entries for widgets the free
			 * core does not ship. Zen Addons Pro uses this to give its Pro-only
			 * widgets (e.g. the Portfolio Grid) the same "Browse designs" experience
			 * as the free widgets. Each entry must be the same self-contained shape
			 * build_entry() produces; malformed entries are dropped, so a filter
			 * mistake degrades to the plain dropdown instead of breaking the picker.
			 *
			 * @since 1.10.13
			 *
			 * @param array $widgets Picker entries built so far.
			 */
			$widgets = array_values( array_filter( (array) apply_filters( 'zaso_design_picker_entries', $widgets ), static function ( $entry ) {
				return is_array( $entry ) && ! empty( $entry['designs'] ) && ! empty( $entry['designIds'] );
			} ) );

			if ( empty( $widgets ) ) {
				return array();
			}

			return array( 'widgets' => $widgets );
		}

		/**
		 * Enqueue the picker script + style and localize the per-widget design map.
		 *
		 * Bails on every screen except the editors, and when no supported widget has
		 * designs available, leaving the native dropdowns untouched.
		 *
		 * @since 1.11.0
		 *
		 * @param string $hook Current admin page hook suffix.
		 */
		public function enqueue_assets( $hook ) {
			if ( ! $this->is_editor_screen( $hook ) ) {
				return;
			}

			$data = $this->build_localized_data();
			if ( empty( $data['widgets'] ) ) {
				return;
			}

			wp_enqueue_style(
				self::HANDLE,
				ZASO_BASE_DIR . 'assets/css/design-picker.css',
				array(),
				ZASO_VERSION
			);

			wp_enqueue_script(
				self::HANDLE,
				ZASO_BASE_DIR . 'assets/js/design-picker.js',
				array(),
				ZASO_VERSION,
				true
			);

			wp_localize_script( self::HANDLE, 'ZasoDesignPicker', $data );
		}
	}

	new ZASO_Design_Picker();

endif; // class_exists checks.
