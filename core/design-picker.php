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
 * helper and its pro_url() upsell-link builder. It deliberately does NOT call
 * the parent constructor, so no second admin menu is registered.
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
		 * Number of Pro designs the upsell copy advertises.
		 *
		 * Seven galleries carry twenty-four Pro designs each (Alert Box, Counter,
		 * Call to Action, Pricing Table, Testimonial Slider, Hover Card, Services
		 * Grid). It cannot be counted at runtime: on a free site the Pro plugin is
		 * not installed, so nothing has registered those designs to count. Verify
		 * against the live filters before changing it, e.g.
		 * `count( apply_filters( 'zaso_alert_designs', array() ) )` on a licensed site.
		 *
		 * Excludes the Portfolio widget's thirty designs, which belong to a Pro-only
		 * widget a free user never sees in this picker.
		 *
		 * @since 1.10.17
		 */
		const PRO_DESIGN_COUNT = 168;

		/**
		 * Lowest Pro price, including the currency symbol.
		 *
		 * Matches the Personal tier (EDD product 17502). Shipped in a translatable
		 * string, so a price change needs a plugin release to reach existing installs
		 * - update the tier in EDD and this constant together, or the picker will
		 * advertise a price the checkout does not honour.
		 *
		 * @since 1.10.17
		 */
		const PRO_PRICE_FROM = '$39';

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
				'gradient'      => esc_html__( 'Gradient', 'zen-addons-for-siteorigin-page-builder' ),
				'dark'          => esc_html__( 'Dark Mode', 'zen-addons-for-siteorigin-page-builder' ),
				'split-panel'   => esc_html__( 'Split Panel', 'zen-addons-for-siteorigin-page-builder' ),
				'glass'         => esc_html__( 'Glass', 'zen-addons-for-siteorigin-page-builder' ),
				'toast'         => esc_html__( 'Toast', 'zen-addons-for-siteorigin-page-builder' ),
				'big-icon'      => esc_html__( 'Big Icon', 'zen-addons-for-siteorigin-page-builder' ),
				'pill'          => esc_html__( 'Pill', 'zen-addons-for-siteorigin-page-builder' ),
				'dashed'        => esc_html__( 'Dashed', 'zen-addons-for-siteorigin-page-builder' ),
				'double'        => esc_html__( 'Double Border', 'zen-addons-for-siteorigin-page-builder' ),
				'banner'        => esc_html__( 'Banner', 'zen-addons-for-siteorigin-page-builder' ),
				'tag-label'     => esc_html__( 'Tag Label', 'zen-addons-for-siteorigin-page-builder' ),
				'ghost'         => esc_html__( 'Ghost Tint', 'zen-addons-for-siteorigin-page-builder' ),
				'underline'     => esc_html__( 'Underline', 'zen-addons-for-siteorigin-page-builder' ),
				'round-badge'   => esc_html__( 'Round Badge', 'zen-addons-for-siteorigin-page-builder' ),
				'two-tone'      => esc_html__( 'Two-tone', 'zen-addons-for-siteorigin-page-builder' ),
				'right-accent'  => esc_html__( 'Right Accent', 'zen-addons-for-siteorigin-page-builder' ),
				'floating-icon' => esc_html__( 'Floating Icon', 'zen-addons-for-siteorigin-page-builder' ),
				'dotted'        => esc_html__( 'Dotted', 'zen-addons-for-siteorigin-page-builder' ),
				'deep-solid'    => esc_html__( 'Deep Solid', 'zen-addons-for-siteorigin-page-builder' ),
				'terminal'      => esc_html__( 'Terminal', 'zen-addons-for-siteorigin-page-builder' ),
				'title-bar'     => esc_html__( 'Title Bar', 'zen-addons-for-siteorigin-page-builder' ),
				'action-cta'    => esc_html__( 'Action CTA', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-ring' => esc_html__( 'Gradient Ring', 'zen-addons-for-siteorigin-page-builder' ),
				'centered'      => esc_html__( 'Centered', 'zen-addons-for-siteorigin-page-builder' ),
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
				'gradient-text'  => esc_html__( 'Gradient Text', 'zen-addons-for-siteorigin-page-builder' ),
				'dark-card'      => esc_html__( 'Dark Card', 'zen-addons-for-siteorigin-page-builder' ),
				'progress-ring'  => esc_html__( 'Progress Ring', 'zen-addons-for-siteorigin-page-builder' ),
				'trend'          => esc_html__( 'Trend', 'zen-addons-for-siteorigin-page-builder' ),
				'solid-fill'     => esc_html__( 'Solid Fill', 'zen-addons-for-siteorigin-page-builder' ),
				'split-panel'    => esc_html__( 'Split Panel', 'zen-addons-for-siteorigin-page-builder' ),
				'progress-bar'   => esc_html__( 'Progress Bar', 'zen-addons-for-siteorigin-page-builder' ),
				'sparkline'      => esc_html__( 'Sparkline', 'zen-addons-for-siteorigin-page-builder' ),
				'glass'          => esc_html__( 'Glass', 'zen-addons-for-siteorigin-page-builder' ),
				'currency'       => esc_html__( 'Currency', 'zen-addons-for-siteorigin-page-builder' ),
				'ghost-outline'  => esc_html__( 'Ghost Outline', 'zen-addons-for-siteorigin-page-builder' ),
				'centered-badge' => esc_html__( 'Centered Badge', 'zen-addons-for-siteorigin-page-builder' ),
				'mono-metric'    => esc_html__( 'Mono Metric', 'zen-addons-for-siteorigin-page-builder' ),
				'watermark'      => esc_html__( 'Watermark', 'zen-addons-for-siteorigin-page-builder' ),
				'pill'           => esc_html__( 'Pill', 'zen-addons-for-siteorigin-page-builder' ),
				'accent-bar'     => esc_html__( 'Accent Bar', 'zen-addons-for-siteorigin-page-builder' ),
				'eyebrow'        => esc_html__( 'Eyebrow', 'zen-addons-for-siteorigin-page-builder' ),
				'two-tone'       => esc_html__( 'Two-tone', 'zen-addons-for-siteorigin-page-builder' ),
				'suffix'         => esc_html__( 'Suffix', 'zen-addons-for-siteorigin-page-builder' ),
				'ring-icon'      => esc_html__( 'Ring Icon', 'zen-addons-for-siteorigin-page-builder' ),
				'banner'         => esc_html__( 'Banner', 'zen-addons-for-siteorigin-page-builder' ),
				'footer-note'    => esc_html__( 'Footer Note', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-ring'  => esc_html__( 'Gradient Ring', 'zen-addons-for-siteorigin-page-builder' ),
				'centered-icon'  => esc_html__( 'Centered Icon', 'zen-addons-for-siteorigin-page-builder' ),
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
				'bold-gradient'    => esc_html__( 'Bold Gradient', 'zen-addons-for-siteorigin-page-builder' ),
				'dark-glow'        => esc_html__( 'Dark Glow', 'zen-addons-for-siteorigin-page-builder' ),
				'image-scrim'      => esc_html__( 'Image Scrim', 'zen-addons-for-siteorigin-page-builder' ),
				'image-horizontal' => esc_html__( 'Image Horizontal', 'zen-addons-for-siteorigin-page-builder' ),
				'split-block'      => esc_html__( 'Split Block', 'zen-addons-for-siteorigin-page-builder' ),
				'eyebrow'          => esc_html__( 'Eyebrow', 'zen-addons-for-siteorigin-page-builder' ),
				'icon-row'         => esc_html__( 'Icon Row', 'zen-addons-for-siteorigin-page-builder' ),
				'pastel-pill'      => esc_html__( 'Pastel Pill', 'zen-addons-for-siteorigin-page-builder' ),
				'dotted-gradient'  => esc_html__( 'Dotted Gradient', 'zen-addons-for-siteorigin-page-builder' ),
				'stacked-dark'     => esc_html__( 'Stacked Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'glass'            => esc_html__( 'Glass', 'zen-addons-for-siteorigin-page-builder' ),
				'arrow-link'       => esc_html__( 'Arrow Link', 'zen-addons-for-siteorigin-page-builder' ),
				'big-type'         => esc_html__( 'Big Type', 'zen-addons-for-siteorigin-page-builder' ),
				'badge-tag'        => esc_html__( 'Badge Tag', 'zen-addons-for-siteorigin-page-builder' ),
				'stats'            => esc_html__( 'Stats', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-heading' => esc_html__( 'Gradient Heading', 'zen-addons-for-siteorigin-page-builder' ),
				'image-bottom'     => esc_html__( 'Image Bottom', 'zen-addons-for-siteorigin-page-builder' ),
				'two-column'       => esc_html__( 'Two Column', 'zen-addons-for-siteorigin-page-builder' ),
				'pill-banner'      => esc_html__( 'Pill Banner', 'zen-addons-for-siteorigin-page-builder' ),
				'left-accent'      => esc_html__( 'Left Accent', 'zen-addons-for-siteorigin-page-builder' ),
				'vibrant-mesh'     => esc_html__( 'Vibrant Mesh', 'zen-addons-for-siteorigin-page-builder' ),
				'corporate'        => esc_html__( 'Corporate', 'zen-addons-for-siteorigin-page-builder' ),
				'bold-solid'       => esc_html__( 'Bold Solid', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-ring'    => esc_html__( 'Gradient Ring', 'zen-addons-for-siteorigin-page-builder' ),
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
				'ribbon-indigo'        => esc_html__( 'Ribbon (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'ribbon-dark'          => esc_html__( 'Ribbon (Dark)', 'zen-addons-for-siteorigin-page-builder' ),
				'featured-violet'      => esc_html__( 'Featured (Violet)', 'zen-addons-for-siteorigin-page-builder' ),
				'featured-teal'        => esc_html__( 'Featured (Teal)', 'zen-addons-for-siteorigin-page-builder' ),
				'comparison-light'     => esc_html__( 'Comparison (Light)', 'zen-addons-for-siteorigin-page-builder' ),
				'comparison-dark'      => esc_html__( 'Comparison (Dark)', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-sunset'      => esc_html__( 'Gradient Header (Sunset)', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-blue'        => esc_html__( 'Gradient Header (Blue)', 'zen-addons-for-siteorigin-page-builder' ),
				'split-slate'          => esc_html__( 'Split Panel (Slate)', 'zen-addons-for-siteorigin-page-builder' ),
				'split-emerald'        => esc_html__( 'Split Panel (Emerald)', 'zen-addons-for-siteorigin-page-builder' ),
				'darkpremium-indigo'   => esc_html__( 'Dark Premium (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'darkpremium-teal'     => esc_html__( 'Dark Premium (Teal)', 'zen-addons-for-siteorigin-page-builder' ),
				'twotone-amber'        => esc_html__( 'Two-tone (Amber)', 'zen-addons-for-siteorigin-page-builder' ),
				'twotone-emerald'      => esc_html__( 'Two-tone (Emerald)', 'zen-addons-for-siteorigin-page-builder' ),
				'gradientring-fuchsia' => esc_html__( 'Gradient Ring (Fuchsia)', 'zen-addons-for-siteorigin-page-builder' ),
				'gradientring-blue'    => esc_html__( 'Gradient Ring (Blue)', 'zen-addons-for-siteorigin-page-builder' ),
				'iconfeatures-indigo'  => esc_html__( 'Icon Features (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'iconfeatures-teal'    => esc_html__( 'Icon Features (Teal)', 'zen-addons-for-siteorigin-page-builder' ),
				'compact-slate'        => esc_html__( 'Compact (Slate)', 'zen-addons-for-siteorigin-page-builder' ),
				'compact-violet'       => esc_html__( 'Compact (Violet)', 'zen-addons-for-siteorigin-page-builder' ),
				'stacked-rose'         => esc_html__( 'Stacked Badge (Rose)', 'zen-addons-for-siteorigin-page-builder' ),
				'stacked-amber'        => esc_html__( 'Stacked Badge (Amber)', 'zen-addons-for-siteorigin-page-builder' ),
				'minitable-light'      => esc_html__( 'Mini Table (Light)', 'zen-addons-for-siteorigin-page-builder' ),
				'minitable-dark'       => esc_html__( 'Mini Table (Dark)', 'zen-addons-for-siteorigin-page-builder' ),
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
				'gradient-quote-indigo' => esc_html__( 'Gradient Quote (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-quote-sunset' => esc_html__( 'Gradient Quote (Sunset)', 'zen-addons-for-siteorigin-page-builder' ),
				'dark-sky'              => esc_html__( 'Dark (Sky Glow)', 'zen-addons-for-siteorigin-page-builder' ),
				'dark-violet'           => esc_html__( 'Dark (Violet Glow)', 'zen-addons-for-siteorigin-page-builder' ),
				'arrows-footer-teal'    => esc_html__( 'Arrows Footer (Teal)', 'zen-addons-for-siteorigin-page-builder' ),
				'arrows-footer-indigo'  => esc_html__( 'Arrows Footer (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'tinted-violet'         => esc_html__( 'Soft Tint (Violet)', 'zen-addons-for-siteorigin-page-builder' ),
				'tinted-emerald'        => esc_html__( 'Soft Tint (Emerald)', 'zen-addons-for-siteorigin-page-builder' ),
				'logo-quote-slate'      => esc_html__( 'Company Logo (Slate)', 'zen-addons-for-siteorigin-page-builder' ),
				'logo-quote-blue'       => esc_html__( 'Company Logo (Blue)', 'zen-addons-for-siteorigin-page-builder' ),
				'stat-indigo'           => esc_html__( 'Stat Highlight (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'stat-rose'             => esc_html__( 'Stat Highlight (Rose)', 'zen-addons-for-siteorigin-page-builder' ),
				'split-panel-indigo'    => esc_html__( 'Split Panel (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'split-panel-teal'      => esc_html__( 'Split Panel (Teal)', 'zen-addons-for-siteorigin-page-builder' ),
				'accent-top-amber'      => esc_html__( 'Accent Top (Amber)', 'zen-addons-for-siteorigin-page-builder' ),
				'accent-top-cyan'       => esc_html__( 'Accent Top (Cyan)', 'zen-addons-for-siteorigin-page-builder' ),
				'verified-emerald'      => esc_html__( 'Verified (Emerald)', 'zen-addons-for-siteorigin-page-builder' ),
				'verified-indigo'       => esc_html__( 'Verified (Indigo)', 'zen-addons-for-siteorigin-page-builder' ),
				'minimal-slate'         => esc_html__( 'Minimal (Slate)', 'zen-addons-for-siteorigin-page-builder' ),
				'minimal-violet'        => esc_html__( 'Minimal (Violet)', 'zen-addons-for-siteorigin-page-builder' ),
				'watermark-fuchsia'     => esc_html__( 'Quote Watermark (Fuchsia)', 'zen-addons-for-siteorigin-page-builder' ),
				'watermark-blue'        => esc_html__( 'Quote Watermark (Blue)', 'zen-addons-for-siteorigin-page-builder' ),
				'spotlight-violet'      => esc_html__( 'Spotlight (Violet)', 'zen-addons-for-siteorigin-page-builder' ),
				'spotlight-sunset'      => esc_html__( 'Spotlight (Sunset)', 'zen-addons-for-siteorigin-page-builder' ),
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
				'side-panel-white'  => esc_html__( 'Side Panel White', 'zen-addons-for-siteorigin-page-builder' ),
				'side-panel-dark'   => esc_html__( 'Side Panel Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'side-panel-amber'  => esc_html__( 'Side Panel Amber', 'zen-addons-for-siteorigin-page-builder' ),
				'strips-white'      => esc_html__( 'Strips White', 'zen-addons-for-siteorigin-page-builder' ),
				'strips-dark'       => esc_html__( 'Strips Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'strips-accent'     => esc_html__( 'Strips Accent', 'zen-addons-for-siteorigin-page-builder' ),
				'corner-white'      => esc_html__( 'Corner White', 'zen-addons-for-siteorigin-page-builder' ),
				'corner-dark'       => esc_html__( 'Corner Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'corner-accent'     => esc_html__( 'Corner Accent', 'zen-addons-for-siteorigin-page-builder' ),
				'centered-light'    => esc_html__( 'Centered Light', 'zen-addons-for-siteorigin-page-builder' ),
				'centered-dark'     => esc_html__( 'Centered Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'centered-accent'   => esc_html__( 'Centered Accent', 'zen-addons-for-siteorigin-page-builder' ),
				'type-strip-white'  => esc_html__( 'Type Strip White', 'zen-addons-for-siteorigin-page-builder' ),
				'type-strip-dark'   => esc_html__( 'Type Strip Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'type-strip-tinted' => esc_html__( 'Type Strip Tinted', 'zen-addons-for-siteorigin-page-builder' ),
				'tint-blue'         => esc_html__( 'Tint Blue', 'zen-addons-for-siteorigin-page-builder' ),
				'tint-dark'         => esc_html__( 'Tint Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'tint-amber'        => esc_html__( 'Tint Amber', 'zen-addons-for-siteorigin-page-builder' ),
				'glass-light'       => esc_html__( 'Glass Light', 'zen-addons-for-siteorigin-page-builder' ),
				'glass-dark'        => esc_html__( 'Glass Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'glass-frosted'     => esc_html__( 'Glass Frosted', 'zen-addons-for-siteorigin-page-builder' ),
				'editorial-white'   => esc_html__( 'Editorial White', 'zen-addons-for-siteorigin-page-builder' ),
				'editorial-dark'    => esc_html__( 'Editorial Dark', 'zen-addons-for-siteorigin-page-builder' ),
				'editorial-accent'  => esc_html__( 'Editorial Accent', 'zen-addons-for-siteorigin-page-builder' ),
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
				'gradient-tile'  => esc_html__( 'Gradient Tile', 'zen-addons-for-siteorigin-page-builder' ),
				'icon-ring'      => esc_html__( 'Icon Ring', 'zen-addons-for-siteorigin-page-builder' ),
				'dark-glow'      => esc_html__( 'Dark Glow', 'zen-addons-for-siteorigin-page-builder' ),
				'accent-bar'     => esc_html__( 'Accent Bar', 'zen-addons-for-siteorigin-page-builder' ),
				'tinted-bg'      => esc_html__( 'Tinted Background', 'zen-addons-for-siteorigin-page-builder' ),
				'split-panel'    => esc_html__( 'Split Panel', 'zen-addons-for-siteorigin-page-builder' ),
				'ghost-number'   => esc_html__( 'Ghost Number', 'zen-addons-for-siteorigin-page-builder' ),
				'stat-footer'    => esc_html__( 'Stat Footer', 'zen-addons-for-siteorigin-page-builder' ),
				'button-footer'  => esc_html__( 'Button Footer', 'zen-addons-for-siteorigin-page-builder' ),
				'tag-list'       => esc_html__( 'Tag List', 'zen-addons-for-siteorigin-page-builder' ),
				'corner-icon'    => esc_html__( 'Corner Icon', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-card'  => esc_html__( 'Gradient Card', 'zen-addons-for-siteorigin-page-builder' ),
				'banner-header'  => esc_html__( 'Banner Header', 'zen-addons-for-siteorigin-page-builder' ),
				'progress-bar'   => esc_html__( 'Progress Bar', 'zen-addons-for-siteorigin-page-builder' ),
				'dashed-ghost'   => esc_html__( 'Dashed Ghost', 'zen-addons-for-siteorigin-page-builder' ),
				'timeline-step'  => esc_html__( 'Timeline Step', 'zen-addons-for-siteorigin-page-builder' ),
				'diagonal-split' => esc_html__( 'Diagonal Split', 'zen-addons-for-siteorigin-page-builder' ),
				'watermark'      => esc_html__( 'Watermark', 'zen-addons-for-siteorigin-page-builder' ),
				'price-tag'      => esc_html__( 'Price Tag', 'zen-addons-for-siteorigin-page-builder' ),
				'checklist'      => esc_html__( 'Checklist', 'zen-addons-for-siteorigin-page-builder' ),
				'gradient-ring'  => esc_html__( 'Gradient Ring', 'zen-addons-for-siteorigin-page-builder' ),
				'corner-ribbon'  => esc_html__( 'Corner Ribbon', 'zen-addons-for-siteorigin-page-builder' ),
				'team-stack'     => esc_html__( 'Team Stack', 'zen-addons-for-siteorigin-page-builder' ),
				'cta-bar'        => esc_html__( 'CTA Bar', 'zen-addons-for-siteorigin-page-builder' ),
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
				'browse'   => esc_html__( 'Browse designs', 'zen-addons-for-siteorigin-page-builder' ),
				'choose'   => esc_html__( 'Choose a pre-made design', 'zen-addons-for-siteorigin-page-builder' ),
				'subtitle' => sprintf(
					/* translators: %s: widget noun, e.g. "alert" or "counter". */
					esc_html__( 'A pre-made design styles the whole %s in one click. Pick one, then Apply.', 'zen-addons-for-siteorigin-page-builder' ),
					$noun
				),
				'free'      => esc_html__( 'Free', 'zen-addons-for-siteorigin-page-builder' ),
				'pro'       => esc_html__( 'Pro', 'zen-addons-for-siteorigin-page-builder' ),
				'locked'    => sprintf(
					/* translators: 1: number of Pro designs, 2: lowest Pro price including currency symbol. */
					esc_html__( 'Part of Zen Addons Pro: %1$d designs across 7 widgets, from %2$s.', 'zen-addons-for-siteorigin-page-builder' ),
					self::PRO_DESIGN_COUNT,
					self::PRO_PRICE_FROM
				),
				'unlock'    => sprintf(
					/* translators: 1: number of Pro designs, 2: lowest Pro price including currency symbol. */
					esc_html__( 'Get all %1$d Pro designs from %2$s, ready to use on every widget above.', 'zen-addons-for-siteorigin-page-builder' ),
					self::PRO_DESIGN_COUNT,
					self::PRO_PRICE_FROM
				),
				'unlockAll' => sprintf(
					/* translators: 1: number of Pro designs, 2: lowest Pro price including currency symbol. */
					esc_html__( 'Unlock %1$d designs from %2$s', 'zen-addons-for-siteorigin-page-builder' ),
					self::PRO_DESIGN_COUNT,
					self::PRO_PRICE_FROM
				),
				'close'     => esc_html__( 'Close', 'zen-addons-for-siteorigin-page-builder' ),
				'apply'     => esc_html__( 'Apply', 'zen-addons-for-siteorigin-page-builder' ),
				'cancel'    => esc_html__( 'Cancel', 'zen-addons-for-siteorigin-page-builder' ),
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
				'proUrl'        => self::pro_url( 'design_picker' ),
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
					'noun'        => esc_html__( 'alert', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (classic box)', 'zen-addons-for-siteorigin-page-builder' ),
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
					'noun'        => esc_html__( 'counter', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (classic counter)', 'zen-addons-for-siteorigin-page-builder' ),
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
					'noun'        => esc_html__( 'call to action', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (classic banner)', 'zen-addons-for-siteorigin-page-builder' ),
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
					'noun'        => esc_html__( 'pricing table', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (classic table)', 'zen-addons-for-siteorigin-page-builder' ),
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
					'noun'        => esc_html__( 'testimonial slider', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (simple card)', 'zen-addons-for-siteorigin-page-builder' ),
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
					'noun'        => esc_html__( 'hover card', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (classic hover card)', 'zen-addons-for-siteorigin-page-builder' ),
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
					'noun'        => esc_html__( 'services grid', 'zen-addons-for-siteorigin-page-builder' ),
					'default'     => esc_html__( 'Default (grid card)', 'zen-addons-for-siteorigin-page-builder' ),
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
