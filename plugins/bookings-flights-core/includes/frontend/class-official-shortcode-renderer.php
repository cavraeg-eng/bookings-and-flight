<?php
/**
 * Renderer for approved official Travelpayouts plugin shortcodes.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

defined( 'ABSPATH' ) || exit;

final class Official_Shortcode_Renderer {

	public static function render( array $placement, array $attributes, string $subid ): string {
		$embed     = (array) ( $placement['embed'] ?? array() );
		$reference = sanitize_key( (string) ( $embed['reference'] ?? '' ) );

		if ( '' === $reference || ! str_starts_with( $reference, 'tp_' ) || ! shortcode_exists( $reference ) ) {
			return '';
		}

		add_filter( 'bookings_and_flights_has_official_travelpayouts_output', '__return_true' );

		$output = do_shortcode( self::build_shortcode( $reference, self::build_shortcode_attributes( $embed, $attributes, $subid ) ) );

		if ( '' === trim( $output ) ) {
			return '';
		}

		$wrapper_id      = wp_unique_id( 'baf-official-widget-' );
		$wrapper_id_json = wp_json_encode( $wrapper_id );

		if ( false === $wrapper_id_json ) {
			return '';
		}

		return sprintf(
			'<div class="baf-travelpayouts-widget__provider baf-travelpayouts-widget__provider--official is-loading" id="%1$s" data-baf-official-shortcode="%2$s"><p class="baf-travelpayouts-widget__loading" role="status">%3$s</p><div class="baf-travelpayouts-widget__official-output">%4$s</div><div class="baf-travelpayouts-widget__fallback" role="status">%5$s</div></div><script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">(function(){var wrapper=document.getElementById(%6$s);if(!wrapper){return;}var output=wrapper.querySelector(".baf-travelpayouts-widget__official-output");if(!output){return;}var observer=null;function textLength(){var clone=output.cloneNode(true);clone.querySelectorAll("script,noscript,style").forEach(function(node){node.remove();});return clone.textContent.trim().length;}function hasShadowContent(){return Array.prototype.some.call(output.querySelectorAll("*"),function(node){return !!(node.shadowRoot&&(node.shadowRoot.children.length>0||node.shadowRoot.textContent.trim()!==""));});}function hasContent(){return !!output.querySelector("iframe,form,table,canvas,svg,[data-reactroot]")||hasShadowContent()||textLength()>20;}function refreshFrames(){output.querySelectorAll("iframe").forEach(function(frame){if(frame.getAttribute("data-baf-visible-refresh")==="1"){return;}frame.setAttribute("data-baf-visible-refresh","1");if(frame.src){frame.src=frame.src;}});}function markLoaded(){wrapper.classList.remove("is-loading");wrapper.classList.remove("is-unavailable");wrapper.classList.add("is-loaded");if(observer){observer.disconnect();}}function markUnavailable(){wrapper.classList.remove("is-loading");wrapper.classList.remove("is-loaded");wrapper.classList.add("is-unavailable");if(observer){observer.disconnect();}}function update(forceUnavailable){if(hasContent()){markLoaded();return true;}if(forceUnavailable){markUnavailable();return true;}return false;}if("MutationObserver" in window){observer=new MutationObserver(function(){update(false);});observer.observe(output,{childList:true,subtree:true});}if("IntersectionObserver" in window){var frameObserver=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){refreshFrames();frameObserver.disconnect();}});});frameObserver.observe(wrapper);}else{window.setTimeout(refreshFrames,1200);}var checks=0;var maxChecks=24;var timer=window.setInterval(function(){checks+=1;var done=update(checks>=maxChecks);if(done||checks>=maxChecks){window.clearInterval(timer);}},500);update(false);}());</script>',
			esc_attr( $wrapper_id ),
			esc_attr( $reference ),
			esc_html__( 'Loading partner flight discovery...', 'bookings-flights-core' ),
			// Approved official Travelpayouts shortcode output can include provider scripts/iframes.
			$output,
			esc_html__( 'This Travelpayouts discovery widget could not load. Try refreshing the page or use the main flight handoff above.', 'bookings-flights-core' ),
			$wrapper_id_json
		);
	}

	private static function build_shortcode_attributes( array $embed, array $attributes, string $subid ): array {
		$shortcode_attributes = is_array( $embed['shortcode_attrs'] ?? null ) ? $embed['shortcode_attrs'] : array();
		$shortcode_attributes['subid'] = $subid;

		$origin = self::sanitize_iata( (string) ( $attributes['origin'] ?? '' ) );
		if ( '' !== $origin ) {
			$shortcode_attributes['origin'] = $origin;
		}

		$destination = self::sanitize_iata( (string) ( $attributes['destination'] ?? '' ) );
		if ( '' !== $destination ) {
			$shortcode_attributes['destination'] = $destination;
		}

		return array_filter(
			$shortcode_attributes,
			static fn( $value ): bool => is_scalar( $value ) && '' !== (string) $value
		);
	}

	private static function build_shortcode( string $reference, array $attributes ): string {
		$parts = array( $reference );

		foreach ( $attributes as $name => $value ) {
			$name = sanitize_key( (string) $name );

			if ( '' === $name ) {
				continue;
			}

			$parts[] = sprintf( '%1$s="%2$s"', $name, esc_attr( sanitize_text_field( (string) $value ) ) );
		}

		return '[' . implode( ' ', $parts ) . ']';
	}

	private static function sanitize_iata( string $value ): string {
		$value = strtoupper( trim( $value ) );

		return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
	}
}
