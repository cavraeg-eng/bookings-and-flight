<?php
/**
 * Base REST controller patterns.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

defined( 'ABSPATH' ) || exit;

abstract class Base_Controller extends \WP_REST_Controller {

	public const NAMESPACE = 'baf/v1';

	public function __construct() {
		$this->namespace = self::NAMESPACE;
	}

	public function get_collection_params() {
		return array(
			'page'     => array(
				'description'       => __( 'Current page of the collection.', 'bookings-flights-core' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_positive_integer' ),
				'validate_callback' => array( Request_Parameters::class, 'validate_positive_integer' ),
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to return. Capped at 50.', 'bookings-flights-core' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 50,
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_per_page' ),
				'validate_callback' => array( Request_Parameters::class, 'validate_per_page' ),
			),
			'search'   => array(
				'description'       => __( 'Limit results to matching titles or content.', 'bookings-flights-core' ),
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_search' ),
				'validate_callback' => array( Request_Parameters::class, 'validate_search' ),
			),
			'orderby'  => array(
				'description'       => __( 'Sort collection by a supported field.', 'bookings-flights-core' ),
				'type'              => 'string',
				'default'           => 'date',
				'enum'              => Request_Parameters::allowed_orderby(),
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_orderby' ),
				'validate_callback' => array( Request_Parameters::class, 'validate_orderby' ),
			),
			'order'    => array(
				'description'       => __( 'Sort direction.', 'bookings-flights-core' ),
				'type'              => 'string',
				'default'           => 'DESC',
				'enum'              => array( 'ASC', 'DESC' ),
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_order' ),
				'validate_callback' => array( Request_Parameters::class, 'validate_order' ),
			),
		);
	}

	protected function add_collection_pagination_headers( \WP_REST_Response $response, \WP_Query $query, \WP_REST_Request $request ): void {
		$total_items = (int) $query->found_posts;
		$total_pages = (int) $query->max_num_pages;
		$page        = max( 1, (int) $request['page'] );

		$response->header( 'X-WP-Total', $total_items );
		$response->header( 'X-WP-TotalPages', $total_pages );

		if ( $total_pages <= 1 ) {
			return;
		}

		$base = rest_url( trailingslashit( $this->namespace ) . $this->rest_base );
		$args = $request->get_query_params();

		if ( $page > 1 ) {
			$args['page'] = $page - 1;
			$response->add_link( 'prev', add_query_arg( $args, $base ) );
		}

		if ( $page < $total_pages ) {
			$args['page'] = $page + 1;
			$response->add_link( 'next', add_query_arg( $args, $base ) );
		}
	}
}