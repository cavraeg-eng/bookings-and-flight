<?php
/**
 * Travel entity service boundary.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Repositories\Repository_Interface;
use BAF\Core\Repositories\Travel_Entity_Repository;

defined( 'ABSPATH' ) || exit;

final class Travel_Entity_Service {

	private Repository_Interface $repository;

	public function __construct( ?Repository_Interface $repository = null ) {
		$this->repository = $repository ?? new Travel_Entity_Repository();
	}

	public function find_readable( int $post_id ): \WP_Post|\WP_Error {
		$post = $this->repository->find( $post_id );

		if ( null === $post ) {
			return new \WP_Error( 'baf_entity_not_found', __( 'Travel entity was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		if ( ! current_user_can( 'read_post', $post_id ) && ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'baf_forbidden', __( 'You are not allowed to view this travel entity.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		return $post;
	}

	public function list_public( string $post_type, array $args = array() ): \WP_Query|\WP_Error {
		$post_type = sanitize_key( $post_type );

		if ( ! in_array( $post_type, array( Post_Type_Registrar::DESTINATION, Post_Type_Registrar::ROUTE ), true ) ) {
			return new \WP_Error( 'baf_invalid_collection', __( 'Invalid public travel collection.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$orderby = sanitize_key( (string) ( $args['orderby'] ?? 'date' ) );
		$order   = strtoupper( (string) ( $args['order'] ?? 'DESC' ) );

		if ( ! in_array( $orderby, array( 'date', 'modified', 'title', 'menu_order' ), true ) ) {
			$orderby = 'date';
		}

		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}

		$query_args = array(
			'post_type'           => array( $post_type ),
			'post_status'         => 'publish',
			'posts_per_page'      => min( 50, max( 1, absint( $args['per_page'] ?? 10 ) ) ),
			'paged'               => max( 1, absint( $args['page'] ?? 1 ) ),
			'orderby'             => $orderby,
			'order'               => $order,
			'ignore_sticky_posts' => true,
			'has_password'        => false,
		);

		$search = sanitize_text_field( (string) ( $args['search'] ?? '' ) );

		if ( '' !== $search ) {
			$query_args['s'] = $search;
		}

		return $this->repository->query( $query_args );
	}

	public function create( array $data ): int|\WP_Error {
		$post_type = sanitize_key( (string) ( $data['post_type'] ?? '' ) );

		if ( ! $this->can_write_post_type( $post_type, (string) ( $data['post_status'] ?? 'draft' ) ) ) {
			return new \WP_Error( 'baf_forbidden', __( 'You are not allowed to create this travel entity.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		return $this->repository->create( $data );
	}

	public function update( int $post_id, array $data ): int|\WP_Error {
		$post = $this->repository->find( $post_id );

		if ( null === $post ) {
			return new \WP_Error( 'baf_entity_not_found', __( 'Travel entity was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'baf_forbidden', __( 'You are not allowed to update this travel entity.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		return $this->repository->update( $post_id, $data );
	}

	private function can_write_post_type( string $post_type, string $post_status ): bool {
		if ( Post_Type_Registrar::TRAVEL_PARTNER === $post_type ) {
			return current_user_can( Capability_Manager::MANAGE_AFFILIATES );
		}

		if ( Post_Type_Registrar::TRAVEL_ALERT === $post_type ) {
			return current_user_can( Capability_Manager::MANAGE_ALERTS );
		}

		if ( 'publish' === sanitize_key( $post_status ) && ! current_user_can( Capability_Manager::PUBLISH_CONTENT ) ) {
			return false;
		}

		return current_user_can( Capability_Manager::EDIT_CONTENT );
	}
}