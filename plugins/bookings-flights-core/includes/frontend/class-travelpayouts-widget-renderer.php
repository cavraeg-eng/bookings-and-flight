<?php
/**
 * Safe frontend renderer for approved Travelpayouts widget placements.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Services\Travelpayouts_Widget_Registry_Service;
use BAF\Core\Services\Travelpayouts_Widget_Subid_Service;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Widget_Renderer {

	private const DEFAULT_CLASS = 'baf-travelpayouts-widget';

	public static function render( array $attributes = array() ): string {
		$attributes = self::normalize_attributes( $attributes );
		$key        = (string) $attributes['placement'];

		if ( '' === $key ) {
			return self::render_state(
				'missing',
				__( 'Travel search placement is missing.', 'bookings-flights-core' ),
				$attributes
			);
		}

		$service   = new Travelpayouts_Widget_Registry_Service();
		$placement = $service->get_for_rendering( $key );

		if ( is_wp_error( $placement ) ) {
			return self::render_unavailable_placement( $service, $key, $placement, $attributes );
		}

		$placement = (array) $placement;
		$consent   = Settings_Manager::get_consent();

		if ( true === (bool) ( $placement['consent_required'] ?? true ) && true !== (bool) $consent['allow_provider_requests'] ) {
			return self::render_state(
				'consent-disabled',
				self::consent_disabled_message( $consent ),
				$attributes,
				$placement
			);
		}

		if ( ! self::placement_allows_surface( $placement, (string) $attributes['surface'] ) ) {
			return self::render_state(
				'surface-unavailable',
				__( 'This travel search placement is not available for this page.', 'bookings-flights-core' ),
				$attributes,
				$placement
			);
		}

		wp_enqueue_style( Frontend_Manager::ASSET_HANDLE );

		$subid = self::build_subid( $placement, $attributes );
		$body  = self::render_body( $placement, $attributes, $subid );

		if ( '' === $body ) {
			return self::render_state(
				'error',
				__( 'Travel search placement could not be rendered safely.', 'bookings-flights-core' ),
				$attributes,
				$placement
			);
		}

		return self::render_frame( $placement, $attributes, $body, 'configured', $subid );
	}

	private static function normalize_attributes( array $attributes ): array {
		$key     = (string) ( $attributes['placement'] ?? $attributes['key'] ?? $attributes['id'] ?? '' );
		$surface = self::sanitize_segment( (string) ( $attributes['surface'] ?? '' ) );
		$slug    = self::sanitize_segment( (string) ( $attributes['slug'] ?? '' ) );

		if ( '' === $surface ) {
			$surface = self::sanitize_segment( self::current_surface() );
		}

		if ( '' === $slug ) {
			$slug = self::sanitize_segment( self::current_slug() );
		}

		return array(
			'placement' => sanitize_key( $key ),
			'surface'   => $surface,
			'channel'   => self::sanitize_segment( (string) ( $attributes['channel'] ?? '' ) ),
			'slug'      => $slug,
			'class'     => sanitize_html_class( (string) ( $attributes['class'] ?? '' ) ),
		);
	}

	private static function render_unavailable_placement( Travelpayouts_Widget_Registry_Service $service, string $key, \WP_Error $error, array $attributes ): string {
		$public = $service->get( $key, false );

		if ( is_wp_error( $public ) ) {
			return self::render_state(
				'missing',
				__( 'Travel search placement is not available.', 'bookings-flights-core' ),
				$attributes
			);
		}

		$public = (array) $public;
		$status = sanitize_key( (string) ( $public['status'] ?? 'missing' ) );

		if ( 'disabled' === $status || 'archived' === $status ) {
			return self::render_state(
				'disabled',
				__( 'This travel search placement is currently disabled.', 'bookings-flights-core' ),
				$attributes,
				$public
			);
		}

		if ( false === (bool) ( $public['configured'] ?? false ) ) {
			return self::render_state(
				'missing-config',
				__( 'Travel search placement is not configured yet.', 'bookings-flights-core' ),
				$attributes,
				$public
			);
		}

		return self::render_state(
			sanitize_key( $error->get_error_code() ),
			__( 'Travel search placement is not available.', 'bookings-flights-core' ),
			$attributes,
			$public
		);
	}

	private static function render_body( array $placement, array $attributes, string $subid ): string {
		$embed = (array) ( $placement['embed'] ?? array() );
		$mode  = sanitize_key( (string) ( $placement['render_mode'] ?? $embed['mode'] ?? '' ) );

		if ( 'dashboard_script' === $mode && 'travelpayouts_white_label' === (string) ( $embed['source'] ?? '' ) ) {
			return self::render_white_label( $placement, $attributes, $subid );
		}

		if ( 'dashboard_script' === $mode ) {
			return self::render_dashboard_script( $placement, $attributes, $subid );
		}

		if ( 'iframe' === $mode ) {
			return self::render_iframe( $placement, $attributes, $subid );
		}

		if ( 'handoff_link' === $mode ) {
			return self::render_handoff( $placement, $subid );
		}

		if ( 'official_shortcode' === $mode ) {
			return self::render_official_shortcode( $placement, $subid );
		}

		return '';
	}

	private static function render_white_label( array $placement, array $attributes, string $subid ): string {
		$embed     = (array) ( $placement['embed'] ?? array() );
		$widget_id = (string) ( $embed['reference'] ?? '' );

		if ( '' === $widget_id ) {
			return '';
		}

		$settings      = Settings_Manager::get_travelpayouts();
		$script_src    = Travelpayouts_Widget_Subid_Service::add_to_url( add_query_arg( 'wl_id', rawurlencode( $widget_id ), 'https://tpwgts.com/wl_web/main.js' ), $subid );
		$configuration = array();

		if ( '' !== (string) $settings['white_label_results_url'] ) {
			$configuration['resultsURL'] = Travelpayouts_Widget_Subid_Service::add_to_url(
				(string) $settings['white_label_results_url'],
				$subid,
				(string) $settings['marker']
			);
		}

		$configuration_json = wp_json_encode( $configuration );
		$script_src_json    = wp_json_encode( esc_url_raw( $script_src ) );

		if ( false === $configuration_json || false === $script_src_json ) {
			return '';
		}

		$instance_id        = wp_unique_id( 'baf-tpwl-' );
		$search_id          = $instance_id . 'search';
		$results_id         = $instance_id . 'tickets';
		$instance_id_json   = wp_json_encode( $instance_id );
		$search_id_json     = wp_json_encode( $search_id );
		$results_id_json    = wp_json_encode( $results_id );
		$fixed_search_json  = wp_json_encode( 'tpwl-search' );
		$fixed_results_json = wp_json_encode( 'tpwl-tickets' );

		if ( false === $instance_id_json || false === $search_id_json || false === $results_id_json || false === $fixed_search_json || false === $fixed_results_json ) {
			return '';
		}

		return sprintf(
			'<div class="baf-travelpayouts-widget__provider baf-travelpayouts-widget__provider--white-label is-loading" id="%1$s"><p class="baf-travelpayouts-widget__loading" role="status">%2$s</p><div id="%3$s" class="baf-travelpayouts-widget__white-label-node" tabindex="-1" aria-hidden="true"></div><div id="%4$s" class="baf-travelpayouts-widget__white-label-node" tabindex="-1" aria-hidden="true"></div><div class="baf-travelpayouts-widget__fallback" role="status">%5$s</div>%6$s</div><script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">(function(){var wrapper=document.getElementById(%7$s);var search=document.getElementById(%8$s);var results=document.getElementById(%9$s);if(!wrapper||!search||!results){return;}function focusPlaceholders(enabled){var value=enabled?"0":"-1";search.setAttribute("tabindex",value);results.setAttribute("tabindex",value);if(enabled){search.removeAttribute("aria-hidden");results.removeAttribute("aria-hidden");}else{search.setAttribute("aria-hidden","true");results.setAttribute("aria-hidden","true");}}function markUnavailable(){wrapper.classList.remove("is-loading");wrapper.classList.remove("is-loaded");wrapper.classList.add("is-unavailable");focusPlaceholders(false);}if(document.getElementById(%10$s)||document.getElementById(%11$s)){markUnavailable();return;}search.id=%10$s;results.id=%11$s;window.TPWL_CONFIGURATION=Object.assign({},window.TPWL_CONFIGURATION||{},%12$s);var observer=null;function hasProviderContent(){return search.children.length>0||results.children.length>0||search.textContent.trim()!==""||results.textContent.trim()!=="";}function update(forceUnavailable){if(hasProviderContent()){wrapper.classList.remove("is-loading");wrapper.classList.remove("is-unavailable");wrapper.classList.add("is-loaded");focusPlaceholders(true);if(observer){observer.disconnect();}return true;}if(forceUnavailable){markUnavailable();if(observer){observer.disconnect();}}return false;}if("MutationObserver" in window){observer=new MutationObserver(function(){update(false);});observer.observe(search,{childList:true,subtree:true});observer.observe(results,{childList:true,subtree:true});}var checks=0;var maxChecks=24;var timer=window.setInterval(function(){checks+=1;var loaded=update(checks>=maxChecks);if(loaded||checks>=maxChecks){window.clearInterval(timer);}},500);var script=document.createElement("script");script.async=true;script.type="module";script.src=%13$s;script.addEventListener("load",function(){window.setTimeout(function(){update(false);},0);});script.addEventListener("error",function(){window.clearInterval(timer);markUnavailable();if(observer){observer.disconnect();}});document.head.appendChild(script);update(false);}());</script>',
			esc_attr( $instance_id ),
			esc_html__( 'Loading partner travel search...', 'bookings-flights-core' ),
			esc_attr( $search_id ),
			esc_attr( $results_id ),
			esc_html__( 'Travel search could not load because another White Label search is already active on this page.', 'bookings-flights-core' ),
			self::render_noscript( $placement, $subid ),
			$instance_id_json,
			$search_id_json,
			$results_id_json,
			$fixed_search_json,
			$fixed_results_json,
			$configuration_json,
			$script_src_json
		);
	}

	private static function render_dashboard_script( array $placement, array $attributes, string $subid ): string {
		$embed      = (array) ( $placement['embed'] ?? array() );
		$script_url = Travelpayouts_Widget_Subid_Service::add_to_url( (string) ( $embed['url'] ?? '' ), $subid );

		if ( '' === $script_url ) {
			return '';
		}

		$wrapper_id      = wp_unique_id( 'baf-widget-placement-' );
		$wrapper_id_json = wp_json_encode( $wrapper_id );
		$script_url_json = wp_json_encode( esc_url_raw( $script_url ) );

		if ( false === $wrapper_id_json || false === $script_url_json ) {
			return '';
		}

		return sprintf(
			'<div class="baf-travelpayouts-widget__provider baf-travelpayouts-widget__provider--script is-loading" id="%1$s"><p class="baf-travelpayouts-widget__loading" role="status">%2$s</p><div class="baf-travelpayouts-widget__fallback" role="status">%3$s</div>%4$s</div><script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">(function(){var wrapperId=%5$s;var scriptSrc=%6$s;var wrapper=document.getElementById(wrapperId);if(!wrapper){return;}var observer=null;function update(markUnavailable){var current=document.getElementById(wrapperId);if(!current){if(observer){observer.disconnect();}return true;}var hasFrame=!!current.querySelector("iframe");current.classList.toggle("is-loaded",hasFrame);if(hasFrame){current.classList.remove("is-loading");current.classList.remove("is-unavailable");if(observer){observer.disconnect();}return true;}if(markUnavailable){current.classList.remove("is-loading");current.classList.add("is-unavailable");}else{current.classList.add("is-loading");}return false;}if("MutationObserver" in window){observer=new MutationObserver(function(){update(false);});observer.observe(wrapper,{childList:true,subtree:true});}var checks=0;var maxChecks=24;var timer=window.setInterval(function(){checks+=1;var loaded=update(checks>=maxChecks);if(loaded||checks>=maxChecks){window.clearInterval(timer);}},500);var script=document.createElement("script");script.async=true;script.src=scriptSrc;script.setAttribute("data-noptimize","1");script.setAttribute("data-cfasync","false");script.setAttribute("data-wpfc-render","false");script.addEventListener("load",function(){window.setTimeout(function(){update(false);},0);});script.addEventListener("error",function(){update(true);});wrapper.insertBefore(script,wrapper.firstChild);update(false);}());</script>',
			esc_attr( $wrapper_id ),
			esc_html__( 'Loading partner travel search...', 'bookings-flights-core' ),
			esc_html__( 'Travel search could not load in this browser. Try refreshing the page or opening the partner search link.', 'bookings-flights-core' ),
			self::render_noscript( $placement, $subid ),
			$wrapper_id_json,
			$script_url_json
		);
	}

	private static function render_iframe( array $placement, array $attributes, string $subid ): string {
		$embed      = (array) ( $placement['embed'] ?? array() );
		$iframe_url = Travelpayouts_Widget_Subid_Service::add_to_url( (string) ( $embed['url'] ?? '' ), $subid );

		if ( '' === $iframe_url ) {
			return '';
		}

		$title = sprintf(
			/* translators: %s: placement name. */
			__( '%s travel search', 'bookings-flights-core' ),
			(string) ( $placement['name'] ?? __( 'Partner', 'bookings-flights-core' ) )
		);

		return sprintf(
			'<div class="baf-travelpayouts-widget__provider baf-travelpayouts-widget__provider--iframe"><div class="baf-travelpayouts-widget__iframe-frame"><iframe src="%1$s" title="%2$s" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>%3$s%4$s</div>',
			esc_url( $iframe_url ),
			esc_attr( $title ),
			self::render_handoff( $placement, $subid ),
			self::render_noscript( $placement, $subid )
		);
	}

	private static function render_handoff( array $placement, string $subid = '' ): string {
		$fallback = (array) ( $placement['fallback'] ?? array() );
		$url      = (string) ( $fallback['url'] ?? '' );

		if ( '' === $url ) {
			$embed = (array) ( $placement['embed'] ?? array() );
			$url   = (string) ( $embed['url'] ?? '' );
		}

		if ( '' === $url ) {
			return '';
		}

		$url = Travelpayouts_Widget_Subid_Service::add_to_url( $url, $subid );

		if ( '' === $url ) {
			return '';
		}

		$path = strtolower( (string) wp_parse_url( $url, PHP_URL_PATH ) );

		if ( str_ends_with( $path, '.js' ) || str_starts_with( $path, '/content' ) || str_starts_with( $path, '/wl_web/' ) ) {
			return '';
		}

		$label = (string) ( $fallback['label'] ?? '' );

		if ( '' === $label ) {
			$label = __( 'Open partner search', 'bookings-flights-core' );
		}

		return sprintf(
			'<p class="baf-travelpayouts-widget__handoff"><a href="%1$s" target="_blank" rel="nofollow sponsored noopener noreferrer">%2$s</a></p>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	private static function render_official_shortcode( array $placement, string $subid ): string {
		$embed     = (array) ( $placement['embed'] ?? array() );
		$reference = sanitize_key( (string) ( $embed['reference'] ?? '' ) );

		if ( '' === $reference || ! str_starts_with( $reference, 'tp_' ) || ! shortcode_exists( $reference ) ) {
			return '';
		}

		return do_shortcode(
			sprintf(
				'[%1$s subid="%2$s"]',
				$reference,
				esc_attr( $subid )
			)
		);
	}

	private static function render_frame( array $placement, array $attributes, string $body, string $state, string $subid = '' ): string {
		$frame       = (array) ( $placement['frame'] ?? array() );
		$classes     = array(
			self::DEFAULT_CLASS,
			self::DEFAULT_CLASS . '--' . sanitize_html_class( (string) ( $placement['vertical'] ?? 'travel' ) ),
			self::DEFAULT_CLASS . '--' . sanitize_html_class( (string) ( $placement['render_mode'] ?? 'placement' ) ),
			'is-' . sanitize_html_class( $state ),
		);
		$extra_class = (string) ( $attributes['class'] ?? '' );

		if ( '' !== $extra_class ) {
			$classes[] = $extra_class;
		}

		$style = sprintf(
			'--baf-widget-desktop-min-height:%1$dpx;--baf-widget-tablet-min-height:%2$dpx;--baf-widget-mobile-min-height:%3$dpx;',
			absint( $frame['desktop_min_height'] ?? 520 ),
			absint( $frame['tablet_min_height'] ?? 520 ),
			absint( $frame['mobile_min_height'] ?? 640 )
		);

		if ( '' === $subid ) {
			$subid = self::build_subid( $placement, $attributes );
		}

		return sprintf(
			'<section class="%1$s" data-baf-placement="%2$s" data-baf-subid="%3$s" data-baf-surface="%4$s" data-baf-state="%5$s" data-baf-render-mode="%6$s" style="%7$s">%8$s<div class="baf-travelpayouts-widget__body">%9$s</div></section>',
			esc_attr( implode( ' ', array_filter( $classes ) ) ),
			esc_attr( (string) ( $placement['key'] ?? $attributes['placement'] ?? '' ) ),
			esc_attr( $subid ),
			esc_attr( (string) ( $attributes['surface'] ?? '' ) ),
			esc_attr( $state ),
			esc_attr( (string) ( $placement['render_mode'] ?? 'disabled' ) ),
			esc_attr( $style ),
			self::render_disclosure( $placement ),
			$body
		);
	}

	private static function render_state( string $state, string $message, array $attributes, array $placement = array() ): string {
		wp_enqueue_style( Frontend_Manager::ASSET_HANDLE );

		$placement = array_merge(
			array(
				'key'         => (string) ( $attributes['placement'] ?? '' ),
				'vertical'    => 'travel',
				'render_mode' => 'disabled',
				'frame'       => array(
					'desktop_min_height' => 0,
					'tablet_min_height'  => 0,
					'mobile_min_height'  => 0,
				),
			),
			$placement
		);

		$body = sprintf(
			'<div class="baf-travelpayouts-widget__state baf-travelpayouts-widget__state--%1$s" role="status">%2$s</div>',
			esc_attr( sanitize_html_class( $state ) ),
			esc_html( $message )
		);

		return self::render_frame( $placement, $attributes, $body, $state );
	}

	private static function render_disclosure( array $placement ): string {
		$disclosure = (array) ( $placement['disclosure'] ?? array() );

		if ( true !== (bool) ( $disclosure['required'] ?? true ) ) {
			return '';
		}

		$copy = (string) ( $disclosure['copy'] ?? '' );

		if ( '' === $copy ) {
			$copy = __( 'Sponsored travel search. Booking is completed with the partner provider.', 'bookings-flights-core' );
		}

		return sprintf(
			'<p class="baf-travelpayouts-widget__disclosure">%s</p>',
			esc_html( $copy )
		);
	}

	private static function render_noscript( array $placement, string $subid = '' ): string {
		$handoff = self::render_handoff( $placement, $subid );

		if ( '' !== $handoff ) {
			return sprintf(
				'<noscript><div class="baf-travelpayouts-widget__noscript">%1$s%2$s</div></noscript>',
				esc_html__( 'Enable JavaScript to load this partner travel search.', 'bookings-flights-core' ),
				$handoff
			);
		}

		return sprintf(
			'<noscript><p class="baf-travelpayouts-widget__noscript">%s</p></noscript>',
			esc_html__( 'Enable JavaScript to load this partner travel search.', 'bookings-flights-core' )
		);
	}

	private static function build_subid( array $placement, array $attributes ): string {
		return Travelpayouts_Widget_Subid_Service::build( $placement, $attributes );
	}

	private static function consent_disabled_message( array $consent ): string {
		$override = wp_strip_all_tags( (string) ( $consent['consent_notice_override'] ?? '' ) );

		if ( '' !== $override ) {
			return $override;
		}

		return __( 'Travel search is paused until provider request consent is enabled.', 'bookings-flights-core' );
	}

	private static function current_surface(): string {
		if ( is_front_page() ) {
			return 'home';
		}

		if ( is_singular() ) {
			return (string) get_post_type();
		}

		if ( is_archive() ) {
			return 'archive';
		}

		return 'site';
	}

	private static function current_slug(): string {
		if ( is_singular() ) {
			$post = get_post();

			if ( null !== $post ) {
				return (string) $post->post_name;
			}
		}

		return 'page';
	}

	private static function sanitize_segment( string $value ): string {
		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/[^a-z0-9_]+/', '_', $value );
		$value = trim( preg_replace( '/_+/', '_', (string) $value ), '_' );

		return substr( $value, 0, 64 );
	}

	private static function placement_allows_surface( array $placement, string $surface ): bool {
		$surfaces = array_filter(
			array_map(
				array( self::class, 'sanitize_segment' ),
				(array) ( $placement['public_surfaces'] ?? array() )
			)
		);

		if ( array() === $surfaces ) {
			return true;
		}

		return in_array( self::sanitize_segment( $surface ), $surfaces, true );
	}

}
