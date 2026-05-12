<?php
/**
 * Base repository contract.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Repositories;

defined( 'ABSPATH' ) || exit;

interface Repository_Interface {

	public function find( int $post_id ): ?\WP_Post;

	public function query( array $args = array() ): \WP_Query;

	public function create( array $data ): int|\WP_Error;

	public function update( int $post_id, array $data ): int|\WP_Error;
}