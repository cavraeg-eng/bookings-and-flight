<?php
/**
 * Structured editor meta boxes for travel content modules.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Post_Types;

defined( 'ABSPATH' ) || exit;

final class Editor_Meta_Boxes {

	private const NONCE_ACTION = 'baf_core_save_editor_meta';
	private const NONCE_FIELD  = 'baf_core_editor_meta_nonce';
	private const FIELD_GROUP  = 'baf_core_editor_meta';

	public static function bootstrap(): void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_boxes' ) );
		add_action( 'save_post_' . Post_Type_Registrar::DESTINATION, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'save_post_' . Post_Type_Registrar::ROUTE, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'save_post_' . Post_Type_Registrar::TRAVEL_DEAL, array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function register_meta_boxes( string $post_type ): void {
		if ( ! in_array( $post_type, self::post_types(), true ) ) {
			return;
		}

		if ( ! function_exists( 'add_meta_box' ) ) {
			return;
		}

		add_meta_box(
			'baf-core-editor-modules',
			self::box_title( $post_type ),
			array( __CLASS__, 'render' ),
			$post_type,
			'normal',
			'high',
			array( 'post_type' => $post_type )
		);
	}

	public static function render( \WP_Post $post, array $meta_box ): void {
		$post_type = isset( $meta_box['args']['post_type'] ) ? (string) $meta_box['args']['post_type'] : $post->post_type;
		$groups    = self::field_groups( $post_type );

		if ( empty( $groups ) ) {
			return;
		}

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );

		echo '<div class="baf-core-editor-fields">';
		printf(
			'<p class="description">%s</p>',
			esc_html__( 'Use these fields for editable guide modules. Travelpayouts widgets and handoff scripts stay governed by approved placements, not raw editor content.', 'bookings-flights-core' )
		);

		foreach ( $groups as $section => $fields ) {
			printf(
				'<section class="baf-core-editor-fields__section" aria-label="%s">',
				esc_attr( $section )
			);
			printf( '<h3>%s</h3>', esc_html( $section ) );

			foreach ( $fields as $key => $field ) {
				self::render_field( $post->ID, $key, $field );
			}

			echo '</section>';
		}

		echo '</div>';
	}

	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! in_array( $post->post_type, self::post_types(), true ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$nonce = isset( $_POST[ self::NONCE_FIELD ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		$posted = isset( $_POST[ self::FIELD_GROUP ] ) && is_array( $_POST[ self::FIELD_GROUP ] )
			? wp_unslash( $_POST[ self::FIELD_GROUP ] )
			: array();

		if ( ! is_array( $posted ) ) {
			return;
		}

		foreach ( self::fields_for_post_type( $post->post_type ) as $key => $field ) {
			if ( ! array_key_exists( $key, $posted ) ) {
				continue;
			}

			$value = self::sanitize_value( $posted[ $key ], $field );

			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
				continue;
			}

			update_post_meta( $post_id, $key, $value );
		}
	}

	private static function render_field( int $post_id, string $key, array $field ): void {
		$field_id    = 'baf-core-field-' . sanitize_key( $key );
		$description = isset( $field['description'] ) ? (string) $field['description'] : '';
		$type        = isset( $field['type'] ) ? (string) $field['type'] : 'text';
		$value       = get_post_meta( $post_id, $key, true );

		echo '<div class="baf-core-editor-fields__field">';
		printf(
			'<label for="%1$s">%2$s</label>',
			esc_attr( $field_id ),
			esc_html( (string) $field['label'] )
		);

		if ( 'textarea' === $type ) {
			printf(
				'<textarea id="%1$s" name="%2$s[%3$s]" rows="%4$d" class="widefat">%5$s</textarea>',
				esc_attr( $field_id ),
				esc_attr( self::FIELD_GROUP ),
				esc_attr( $key ),
				isset( $field['rows'] ) ? absint( $field['rows'] ) : 4,
				esc_textarea( (string) $value )
			);
		} else {
			printf(
				'<input id="%1$s" name="%2$s[%3$s]" type="%4$s" value="%5$s" class="%6$s"%7$s>',
				esc_attr( $field_id ),
				esc_attr( self::FIELD_GROUP ),
				esc_attr( $key ),
				esc_attr( 'number' === $type ? 'number' : 'text' ),
				esc_attr( (string) $value ),
				esc_attr( 'number' === $type ? 'small-text' : 'widefat' ),
				'number' === $type ? ' min="0" step="1"' : ''
			);
		}

		if ( '' !== $description ) {
			printf( '<p class="description">%s</p>', esc_html( $description ) );
		}

		echo '</div>';
	}

	private static function sanitize_value( mixed $value, array $field ): string|float {
		$type = isset( $field['type'] ) ? (string) $field['type'] : 'text';

		if ( 'number' === $type ) {
			if ( '' === trim( (string) $value ) ) {
				return '';
			}

			return Post_Type_Registrar::sanitize_non_negative_number( $value );
		}

		if ( ! empty( $field['airport_code'] ) ) {
			return Post_Type_Registrar::sanitize_code( $value );
		}

		if ( 'textarea' === $type ) {
			return sanitize_textarea_field( (string) $value );
		}

		return sanitize_text_field( (string) $value );
	}

	private static function post_types(): array {
		return array(
			Post_Type_Registrar::DESTINATION,
			Post_Type_Registrar::ROUTE,
			Post_Type_Registrar::TRAVEL_DEAL,
		);
	}

	private static function box_title( string $post_type ): string {
		$titles = array(
			Post_Type_Registrar::DESTINATION => __( 'Destination Guide Modules', 'bookings-flights-core' ),
			Post_Type_Registrar::ROUTE       => __( 'Route Guide Modules', 'bookings-flights-core' ),
			Post_Type_Registrar::TRAVEL_DEAL => __( 'Travel Deal Modules', 'bookings-flights-core' ),
		);

		return $titles[ $post_type ] ?? __( 'Travel Content Modules', 'bookings-flights-core' );
	}

	private static function field_groups( string $post_type ): array {
		$groups = array();

		foreach ( self::fields_for_post_type( $post_type ) as $key => $field ) {
			$section = isset( $field['section'] ) ? (string) $field['section'] : __( 'Details', 'bookings-flights-core' );

			if ( ! isset( $groups[ $section ] ) ) {
				$groups[ $section ] = array();
			}

			$groups[ $section ][ $key ] = $field;
		}

		return $groups;
	}

	private static function fields_for_post_type( string $post_type ): array {
		$common_route = array(
			'baf_origin'              => self::field( __( 'Origin city or place', 'bookings-flights-core' ), __( 'Editorial origin label shown on route and deal pages.', 'bookings-flights-core' ) ),
			'baf_destination'         => self::field( __( 'Destination city or place', 'bookings-flights-core' ), __( 'Editorial destination label shown on destination, route, and deal pages.', 'bookings-flights-core' ) ),
			'baf_origin_airport'      => self::field( __( 'Origin airport code', 'bookings-flights-core' ), __( 'Optional IATA or provider route code. Saved uppercase without punctuation.', 'bookings-flights-core' ), 'text', __( 'Route context', 'bookings-flights-core' ), array( 'airport_code' => true ) ),
			'baf_destination_airport' => self::field( __( 'Destination airport code', 'bookings-flights-core' ), __( 'Optional IATA or provider route code. Saved uppercase without punctuation.', 'bookings-flights-core' ), 'text', __( 'Route context', 'bookings-flights-core' ), array( 'airport_code' => true ) ),
			'baf_departure_window'    => self::field( __( 'Departure window', 'bookings-flights-core' ), __( 'Human-readable timing context, not live availability.', 'bookings-flights-core' ) ),
			'baf_return_window'       => self::field( __( 'Return window', 'bookings-flights-core' ), __( 'Human-readable return timing context, not live availability.', 'bookings-flights-core' ) ),
			'baf_travel_style'        => self::field( __( 'Primary travel style', 'bookings-flights-core' ), __( 'Used as editorial context and a handoff hint.', 'bookings-flights-core' ) ),
		);

		if ( Post_Type_Registrar::DESTINATION === $post_type ) {
			return array(
				'baf_destination'             => self::field( __( 'Destination city or place', 'bookings-flights-core' ), __( 'Primary guide destination label.', 'bookings-flights-core' ), 'text', __( 'Guide context', 'bookings-flights-core' ) ),
				'baf_destination_airport'     => self::field( __( 'Destination airport code', 'bookings-flights-core' ), __( 'Optional airport code used for provider handoff links.', 'bookings-flights-core' ), 'text', __( 'Guide context', 'bookings-flights-core' ), array( 'airport_code' => true ) ),
				'baf_travel_style'            => self::field( __( 'Primary travel style', 'bookings-flights-core' ), __( 'Optional style context for hotel and discovery handoffs.', 'bookings-flights-core' ), 'text', __( 'Guide context', 'bookings-flights-core' ) ),
				'baf_destination_best_time'   => self::field( __( 'Best time to visit', 'bookings-flights-core' ), __( 'Editable seasonal guidance for the destination guide.', 'bookings-flights-core' ), 'textarea', __( 'Destination modules', 'bookings-flights-core' ) ),
				'baf_destination_facts'       => self::field( __( 'Arrival and local context', 'bookings-flights-core' ), __( 'Editable fact-sheet notes for arrival, transit, trip length, or accessibility.', 'bookings-flights-core' ), 'textarea', __( 'Destination modules', 'bookings-flights-core' ) ),
				'baf_destination_activities'  => self::field( __( 'Activity planning notes', 'bookings-flights-core' ), __( 'Editable activity, attraction, dining, and day-trip ideas.', 'bookings-flights-core' ), 'textarea', __( 'Destination modules', 'bookings-flights-core' ) ),
				'baf_destination_seasonal'    => self::field( __( 'Seasonal trip ideas', 'bookings-flights-core' ), __( 'Editable seasonal planning angle without live-rate or scarcity claims.', 'bookings-flights-core' ), 'textarea', __( 'Destination modules', 'bookings-flights-core' ) ),
				'baf_hotel_guide_summary'     => self::field( __( 'Hotel guide summary', 'bookings-flights-core' ), __( 'Editable hotel-guide lead-in shown before provider handoff.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
				'baf_hotel_neighborhoods'     => self::field( __( 'Neighborhood guidance', 'bookings-flights-core' ), __( 'Editable stay-area guidance.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
				'baf_hotel_best_for'          => self::field( __( 'Best-fit hotel lens', 'bookings-flights-core' ), __( 'Editable traveler-fit guidance.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
				'baf_hotel_family_notes'      => self::field( __( 'Family hotel notes', 'bookings-flights-core' ), __( 'Editable family stay planning context.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
				'baf_hotel_luxury_notes'      => self::field( __( 'Luxury hotel notes', 'bookings-flights-core' ), __( 'Editable luxury stay planning context.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
				'baf_hotel_budget_notes'      => self::field( __( 'Budget hotel notes', 'bookings-flights-core' ), __( 'Editable budget stay planning context.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
				'baf_hotel_landmark_notes'    => self::field( __( 'Landmark-area hotel notes', 'bookings-flights-core' ), __( 'Editable landmark and area planning context.', 'bookings-flights-core' ), 'textarea', __( 'Hotel modules', 'bookings-flights-core' ) ),
			);
		}

		if ( Post_Type_Registrar::ROUTE === $post_type ) {
			return $common_route + array(
				'baf_route_travel_time'       => self::field( __( 'Travel-time context', 'bookings-flights-core' ), __( 'Editable route travel-time guidance.', 'bookings-flights-core' ), 'textarea', __( 'Route modules', 'bookings-flights-core' ) ),
				'baf_route_airport_notes'     => self::field( __( 'Airport and connection notes', 'bookings-flights-core' ), __( 'Editable airport, transfer, and connection guidance.', 'bookings-flights-core' ), 'textarea', __( 'Route modules', 'bookings-flights-core' ) ),
				'baf_route_flexible_dates'    => self::field( __( 'Flexible-date guidance', 'bookings-flights-core' ), __( 'Editable flexible-date planning guidance.', 'bookings-flights-core' ), 'textarea', __( 'Route modules', 'bookings-flights-core' ) ),
				'baf_route_destination_notes' => self::field( __( 'Destination hotel and activity notes', 'bookings-flights-core' ), __( 'Editable follow-up planning notes for the destination side of the route.', 'bookings-flights-core' ), 'textarea', __( 'Route modules', 'bookings-flights-core' ) ),
			);
		}

		if ( Post_Type_Registrar::TRAVEL_DEAL === $post_type ) {
			return $common_route + array(
				'baf_budget_min'             => self::field( __( 'Minimum editorial budget', 'bookings-flights-core' ), __( 'Optional budget context. Not a live fare or guaranteed price.', 'bookings-flights-core' ), 'number', __( 'Deal context', 'bookings-flights-core' ) ),
				'baf_budget_max'             => self::field( __( 'Maximum editorial budget', 'bookings-flights-core' ), __( 'Optional budget context. Not a live fare or guaranteed price.', 'bookings-flights-core' ), 'number', __( 'Deal context', 'bookings-flights-core' ) ),
				'baf_deal_seasonal_context'  => self::field( __( 'Seasonal context', 'bookings-flights-core' ), __( 'Editable seasonal deal-planning context.', 'bookings-flights-core' ), 'textarea', __( 'Deal modules', 'bookings-flights-core' ) ),
				'baf_deal_weekend_ideas'     => self::field( __( 'Weekend ideas', 'bookings-flights-core' ), __( 'Editable weekend trip ideas.', 'bookings-flights-core' ), 'textarea', __( 'Deal modules', 'bookings-flights-core' ) ),
				'baf_deal_theme_notes'       => self::field( __( 'Travel-style notes', 'bookings-flights-core' ), __( 'Editable theme or traveler-style context.', 'bookings-flights-core' ), 'textarea', __( 'Deal modules', 'bookings-flights-core' ) ),
				'baf_deal_activity_notes'    => self::field( __( 'Activity notes', 'bookings-flights-core' ), __( 'Editable activity planning ideas.', 'bookings-flights-core' ), 'textarea', __( 'Deal modules', 'bookings-flights-core' ) ),
				'baf_deal_partner_notes'     => self::field( __( 'Partner handoff notes', 'bookings-flights-core' ), __( 'Editable note shown near provider-owned handoff modules.', 'bookings-flights-core' ), 'textarea', __( 'Deal modules', 'bookings-flights-core' ) ),
				'baf_deal_source_note'       => self::field( __( 'Source and claim-safety note', 'bookings-flights-core' ), __( 'Editable source note for reviewers. Do not paste provider scripts here.', 'bookings-flights-core' ), 'textarea', __( 'Deal modules', 'bookings-flights-core' ) ),
			);
		}

		return array();
	}

	private static function field( string $label, string $description, string $type = 'text', string $section = '', array $extra = array() ): array {
		return array_merge(
			array(
				'label'       => $label,
				'description' => $description,
				'type'        => $type,
				'section'     => '' !== $section ? $section : __( 'Route context', 'bookings-flights-core' ),
				'rows'        => 4,
			),
			$extra
		);
	}
}
