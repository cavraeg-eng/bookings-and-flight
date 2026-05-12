<?php
/**
 * Public travel entity collection REST controller.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

use BAF\Core\Services\Travel_Entity_Service;

defined( 'ABSPATH' ) || exit;

final class Travel_Entity_Controller extends Base_Controller {

	private string $post_type;

	private Travel_Entity_Service $service;

	public function __construct( string $post_type, string $rest_base, ?Travel_Entity_Service $service = null ) {
		parent::__construct();

		$this->post_type = sanitize_key( $post_type );
		$this->rest_base = sanitize_title( $rest_base );
		$this->service   = $service ?? new Travel_Entity_Service();
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	public function get_items_permissions_check( $request ) {
		return Permissions::can_read_public_collection( $request );
	}

	public function get_items( $request ) {
		$query = $this->service->list_public(
			$this->post_type,
			array(
				'page'     => $request['page'],
				'per_page' => $request['per_page'],
				'search'   => $request['search'],
				'orderby'  => $request['orderby'],
				'order'    => $request['order'],
			)
		);

		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$items = array();

		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}

			$items[] = $this->prepare_response_for_collection( $this->prepare_item_for_response( $post, $request ) );
		}

		$response = rest_ensure_response( $items );
		$this->add_collection_pagination_headers( $response, $query, $request );

		return $response;
	}

	public function prepare_item_for_response( $item, $request ) {
		$post = $item instanceof \WP_Post ? $item : get_post( (int) $item );

		if ( ! $post instanceof \WP_Post ) {
			return new \WP_REST_Response( array(), 404 );
		}

		$data = array(
			'id'       => (int) $post->ID,
			'type'     => $post->post_type,
			'slug'     => $post->post_name,
			'link'     => (string) get_permalink( $post ),
			'date'     => $this->format_post_date( $post->post_date_gmt, $post->post_date ),
			'modified' => $this->format_post_date( $post->post_modified_gmt, $post->post_modified ),
			'title'    => array(
				'rendered' => wp_kses_post( get_the_title( $post ) ),
			),
			'excerpt'  => array(
				'rendered' => wp_kses_post( wpautop( $this->get_excerpt( $post ) ) ),
			),
		);

		$data = $this->filter_response_by_context( $data, 'view' );

		return rest_ensure_response( $data );
	}

	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->schema;
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'       => array(
					'description' => __( 'Unique identifier for the travel entity.', 'bookings-flights-core' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'type'     => array(
					'description' => __( 'Travel entity post type.', 'bookings-flights-core' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'slug'     => array(
					'description' => __( 'URL-friendly entity slug.', 'bookings-flights-core' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'link'     => array(
					'description' => __( 'Public permalink for the entity.', 'bookings-flights-core' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'date'     => array(
					'description' => __( 'Published date in RFC3339 format.', 'bookings-flights-core' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'modified' => array(
					'description' => __( 'Last modified date in RFC3339 format.', 'bookings-flights-core' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'title'    => array(
					'description' => __( 'Rendered entity title.', 'bookings-flights-core' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'rendered' => array(
							'type' => 'string',
						),
					),
				),
				'excerpt'  => array(
					'description' => __( 'Rendered entity excerpt.', 'bookings-flights-core' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'rendered' => array(
							'type' => 'string',
						),
					),
				),
			),
		);

		return $this->schema;
	}

	private function get_excerpt( \WP_Post $post ): string {
		if ( has_excerpt( $post ) ) {
			return $post->post_excerpt;
		}

		return wp_trim_words( wp_strip_all_tags( $post->post_content ), 35 );
	}

	private function format_post_date( string $date_gmt, string $fallback_date ): string {
		$date = '0000-00-00 00:00:00' === $date_gmt ? get_gmt_from_date( $fallback_date ) : $date_gmt;

		return mysql_to_rfc3339( $date );
	}
}