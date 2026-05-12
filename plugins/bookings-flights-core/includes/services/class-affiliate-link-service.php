<?php
/**
 * Affiliate card and link generation service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Affiliate_Link_Service {

	public const PROVIDER_TRAVELPAYOUTS = 'travelpayouts';

	private const HANDOFF_TTL = DAY_IN_SECONDS;

	public function build_card( array $args ): array|\WP_Error {
		$provider = sanitize_key( (string) ( $args['provider'] ?? self::PROVIDER_TRAVELPAYOUTS ) );
		$post_id  = absint( $args['post_id'] ?? 0 );

		if ( self::PROVIDER_TRAVELPAYOUTS !== $provider ) {
			return new \WP_Error( 'baf_provider_not_supported', __( 'This affiliate provider is not supported yet.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$subid      = $this->generate_subid( $post_id, (string) ( $args['subid_source'] ?? '' ) );
		$target_url = $this->build_travelpayouts_url( $subid );

		if ( is_wp_error( $target_url ) ) {
			return $target_url;
		}

		$destination = $this->destination_label( $post_id, (string) ( $args['destination'] ?? '' ) );
		$title       = '' !== $destination ? sprintf( __( 'Explore travel options for %s', 'bookings-flights-core' ), $destination ) : __( 'Explore travel options', 'bookings-flights-core' );
		$description = sanitize_text_field( (string) ( $args['description'] ?? '' ) );

		if ( '' === $description ) {
			$description = __( 'Compare options with our partner. Booking is completed on the provider site, not on Bookings and Flights.', 'bookings-flights-core' );
		}

		return array(
			'post_id'        => $post_id,
			'provider'       => $provider,
			'provider_label' => __( 'Travelpayouts partner link', 'bookings-flights-core' ),
			'title'          => $title,
			'description'    => $description,
			'label'          => sanitize_text_field( (string) ( $args['label'] ?? __( 'Compare travel options', 'bookings-flights-core' ) ) ),
			'subid'          => $subid,
			'target_url'     => $target_url,
			'handoff_url'    => $this->build_handoff_url( $target_url, $post_id, $provider, $subid ),
		);
	}

	public function validate_handoff( array $params ): array|\WP_Error {
		$consent = Settings_Manager::get_consent();
		$post_id    = absint( $params['post_id'] ?? 0 );
		$provider   = sanitize_key( (string) ( $params['provider'] ?? '' ) );
		$subid      = $this->sanitize_subid( (string) ( $params['subid'] ?? '' ) );
		$expires    = absint( $params['expires'] ?? 0 );
		$signature  = sanitize_text_field( (string) ( $params['sig'] ?? '' ) );
		$target_url = $this->decode_target( (string) ( $params['target'] ?? '' ) );

		if ( true !== (bool) $consent['allow_provider_requests'] ) {
			return new \WP_Error( 'baf_provider_consent_required', __( 'Affiliate handoff is not available until provider request consent is enabled.', 'bookings-flights-core' ), array( 'status' => 503 ) );
		}

		if ( time() > $expires ) {
			return new \WP_Error( 'baf_affiliate_handoff_expired', __( 'This affiliate handoff link has expired.', 'bookings-flights-core' ), array( 'status' => 410 ) );
		}

		if ( ! $this->is_allowed_target( $target_url ) ) {
			return new \WP_Error( 'baf_affiliate_target_invalid', __( 'This affiliate handoff target is not allowed.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$configured = $this->validate_current_provider_config( $provider, $target_url );

		if ( is_wp_error( $configured ) ) {
			return $configured;
		}

		if ( ! hash_equals( $this->signature( $target_url, $post_id, $provider, $subid, $expires ), $signature ) ) {
			return new \WP_Error( 'baf_affiliate_handoff_invalid', __( 'This affiliate handoff link could not be verified.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		return array(
			'post_id'    => $post_id,
			'provider'   => $provider,
			'subid'      => $subid,
			'target_url' => $target_url,
		);
	}

	public static function disclosure_markup( string $extra_class = '' ): string {
		$class = 'baf-affiliate-disclosure';

		if ( '' !== $extra_class ) {
			$class .= ' ' . sanitize_html_class( $extra_class );
		}

		return sprintf(
			'<p class="%1$s">%2$s</p>',
			esc_attr( $class ),
			esc_html__( 'Disclosure: Some links are affiliate links. We may earn a commission if you click through and book with a partner, at no extra cost to you.', 'bookings-flights-core' )
		);
	}

	private function build_travelpayouts_url( string $subid ): string|\WP_Error {
		$consent  = Settings_Manager::get_consent();
		$settings = Settings_Manager::get_travelpayouts();
		$marker   = sanitize_text_field( (string) $settings['marker'] );

		if ( true !== (bool) $consent['allow_provider_requests'] ) {
			return new \WP_Error( 'baf_provider_consent_required', __( 'Affiliate links are not available until provider request consent is enabled.', 'bookings-flights-core' ), array( 'status' => 503 ) );
		}

		if ( '' === $marker ) {
			return new \WP_Error( 'baf_provider_not_configured', __( 'Affiliate links are not configured yet.', 'bookings-flights-core' ), array( 'status' => 503 ) );
		}

		return add_query_arg( 'marker', $marker . '.' . $subid, 'https://www.aviasales.com/' );
	}

	private function build_handoff_url( string $target_url, int $post_id, string $provider, string $subid ): string {
		$expires = time() + self::HANDOFF_TTL;

		return add_query_arg(
			array(
				'post_id'  => $post_id,
				'provider' => $provider,
				'subid'    => $subid,
				'target'   => $this->encode_target( $target_url ),
				'expires'  => $expires,
				'sig'      => $this->signature( $target_url, $post_id, $provider, $subid, $expires ),
			),
			rest_url( 'baf/v1/affiliate/click' )
		);
	}

	private function generate_subid( int $post_id, string $source ): string {
		$tracking = Settings_Manager::get_tracking();
		$prefix   = $this->sanitize_subid( (string) $tracking['subid_prefix'] );
		$source   = $this->sanitize_subid( $source );

		if ( '' === $source && $post_id > 0 ) {
			$post   = get_post( $post_id );
			$source = $post instanceof \WP_Post ? $this->sanitize_subid( $post->post_type . '_' . $post->post_name ) : '';
		}

		if ( '' === $source ) {
			$source = 'card';
		}

		$parts = array_filter( array( $prefix, $source, $post_id > 0 ? (string) $post_id : '' ) );

		return substr( $this->sanitize_subid( implode( '_', $parts ) ), 0, 96 );
	}

	private function destination_label( int $post_id, string $destination ): string {
		$destination = sanitize_text_field( $destination );

		if ( '' === $destination && $post_id > 0 ) {
			$destination = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );
		}

		if ( '' === $destination && $post_id > 0 ) {
			$post = get_post( $post_id );
			$destination = $post instanceof \WP_Post ? get_the_title( $post ) : '';
		}

		return $destination;
	}

	private function sanitize_subid( string $subid ): string {
		$subid = strtolower( $subid );
		$subid = preg_replace( '/[^a-z0-9_]+/', '_', $subid );
		$subid = trim( (string) $subid, '_' );

		return $subid;
	}

	private function encode_target( string $target_url ): string {
		return rtrim( strtr( base64_encode( $target_url ), '+/', '-_' ), '=' );
	}

	private function decode_target( string $encoded_target ): string {
		$encoded_target = sanitize_text_field( $encoded_target );
		$padding        = strlen( $encoded_target ) % 4;

		if ( $padding > 0 ) {
			$encoded_target .= str_repeat( '=', 4 - $padding );
		}

		$decoded = base64_decode( strtr( $encoded_target, '-_', '+/' ), true );

		return false === $decoded ? '' : esc_url_raw( $decoded );
	}

	private function signature( string $target_url, int $post_id, string $provider, string $subid, int $expires ): string {
		return hash_hmac( 'sha256', implode( '|', array( $target_url, $post_id, $provider, $subid, $expires ) ), wp_salt( 'auth' ) );
	}

	private function is_allowed_target( string $target_url ): bool {
		$host = strtolower( (string) wp_parse_url( $target_url, PHP_URL_HOST ) );

		return in_array( $host, array( 'aviasales.com', 'www.aviasales.com' ), true );
	}

	private function validate_current_provider_config( string $provider, string $target_url ): true|\WP_Error {
		if ( self::PROVIDER_TRAVELPAYOUTS !== $provider ) {
			return new \WP_Error( 'baf_provider_not_supported', __( 'This affiliate provider is not supported yet.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$settings = Settings_Manager::get_travelpayouts();
		$marker   = sanitize_text_field( (string) $settings['marker'] );

		if ( '' === $marker ) {
			return new \WP_Error( 'baf_provider_not_configured', __( 'Affiliate links are not configured yet.', 'bookings-flights-core' ), array( 'status' => 503 ) );
		}

		$query = (string) wp_parse_url( $target_url, PHP_URL_QUERY );
		parse_str( $query, $query_args );

		$target_marker = sanitize_text_field( (string) ( $query_args['marker'] ?? '' ) );

		if ( $target_marker !== $marker && ! str_starts_with( $target_marker, $marker . '.' ) ) {
			return new \WP_Error( 'baf_affiliate_target_invalid', __( 'This affiliate handoff target is not allowed.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		return true;
	}
}