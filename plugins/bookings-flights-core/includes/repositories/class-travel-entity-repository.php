<?php
/**
 * WordPress post-backed travel entity repository.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Repositories;

use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Travel_Entity_Repository implements Repository_Interface {

	private array $post_types;

	public function __construct( array $post_types = array() ) {
		$this->post_types = empty( $post_types ) ? Post_Type_Registrar::keys() : array_values( array_intersect( $post_types, Post_Type_Registrar::keys() ) );
	}

	public function find( int $post_id ): ?\WP_Post {
		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post || ! in_array( $post->post_type, $this->post_types, true ) ) {
			return null;
		}

		return $post;
	}

	public function query( array $args = array() ): \WP_Query {
		$post_types = $this->sanitize_post_types( $args['post_type'] ?? $this->post_types );
		$per_page   = absint( $args['posts_per_page'] ?? 10 );
		$paged      = absint( $args['paged'] ?? 1 );

		$query_args = array_merge(
			$args,
			array(
				'post_type'      => empty( $post_types ) ? $this->post_types : $post_types,
				'post_status'    => $args['post_status'] ?? array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page' => min( max( 1, $per_page ), 50 ),
				'paged'          => max( 1, $paged ),
			)
		);

		return new \WP_Query( $query_args );
	}

	public function create( array $data ): int|\WP_Error {
		$post_data = $this->sanitize_post_data( $data );

		if ( is_wp_error( $post_data ) ) {
			return $post_data;
		}

		return wp_insert_post( $post_data, true );
	}

	public function update( int $post_id, array $data ): int|\WP_Error {
		$post = $this->find( $post_id );

		if ( null === $post ) {
			return new \WP_Error( 'baf_invalid_entity', __( 'Travel entity was not found.', 'bookings-flights-core' ) );
		}

		$post_data = $this->sanitize_post_data( array_merge( $data, array( 'post_type' => $post->post_type ) ) );

		if ( is_wp_error( $post_data ) ) {
			return $post_data;
		}

		$post_data['ID'] = $post_id;

		return wp_update_post( $post_data, true );
	}

	private function sanitize_post_types( mixed $post_types ): array {
		$post_types = is_array( $post_types ) ? $post_types : array( $post_types );
		$post_types = array_map( 'sanitize_key', $post_types );

		return array_values( array_intersect( $post_types, $this->post_types ) );
	}

	private function sanitize_post_data( array $data ): array|\WP_Error {
		$post_type = sanitize_key( (string) ( $data['post_type'] ?? '' ) );

		if ( ! in_array( $post_type, $this->post_types, true ) ) {
			return new \WP_Error( 'baf_invalid_post_type', __( 'Invalid Bookings and Flights post type.', 'bookings-flights-core' ) );
		}

		$post_status = sanitize_key( (string) ( $data['post_status'] ?? 'draft' ) );
		$allowed     = array( 'draft', 'pending', 'publish', 'private' );

		return array(
			'post_type'    => $post_type,
			'post_status'  => in_array( $post_status, $allowed, true ) ? $post_status : 'draft',
			'post_title'   => sanitize_text_field( (string) ( $data['post_title'] ?? '' ) ),
			'post_content' => wp_kses_post( (string) ( $data['post_content'] ?? '' ) ),
			'post_excerpt' => sanitize_textarea_field( (string) ( $data['post_excerpt'] ?? '' ) ),
		);
	}
}