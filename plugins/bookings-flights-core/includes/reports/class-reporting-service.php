<?php

namespace BAF\Core\Reports;

use BAF\Core\Services\Travelpayouts_Subid_Map_Service;

defined( 'ABSPATH' ) || exit;

final class Reporting_Service {

	private Reporting_Repository $repository;
	private Travelpayouts_Subid_Map_Service $subid_map;

	public function __construct( ?Reporting_Repository $repository = null, ?Travelpayouts_Subid_Map_Service $subid_map = null ) {
		$this->repository = $repository ?? new Reporting_Repository();
		$this->subid_map  = $subid_map ?? new Travelpayouts_Subid_Map_Service();
	}

	public function dashboard( int $days = 30 ): array {
		$days = $this->normalize_days( $days );

		return array(
			'days'           => $days,
			'generated_at'   => wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ),
			'clicks'         => $this->repository->click_summary( $days ),
			'ai_sessions'    => $this->repository->ai_session_summary( $days ),
			'provider_stats' => $this->repository->provider_stats(),
			'subid_map'      => $this->subid_map->entries(),
			'content'        => $this->repository->content_summary(),
			'conversion'     => array(
				'available' => false,
				'message'   => __( 'Revenue, bookings, conversion rate, and partner search totals are provider-owned. Use Travelpayouts Performance reports as the source of truth; this WordPress report only shows local operational signals and must not be treated as zero revenue.', 'bookings-flights-core' ),
			),
		);
	}

	public function export_rows( int $days = 30 ): array {
		$dashboard = $this->dashboard( $days );
		$rows      = array(
			array( 'section', 'metric', 'value', 'context' ),
			array( 'clicks', 'total', (string) $dashboard['clicks']['total'], (string) $dashboard['days'] . ' days' ),
			array( 'ai_sessions', 'total', (string) $dashboard['ai_sessions']['total'], (string) $dashboard['days'] . ' days' ),
			array( 'conversion', 'available', 'no', (string) $dashboard['conversion']['message'] ),
		);

		foreach ( $dashboard['clicks']['by_provider'] as $row ) {
			$rows[] = array( 'clicks_by_provider', (string) $row['label'], (string) $row['count'], (string) $dashboard['days'] . ' days' );
		}

		foreach ( $dashboard['clicks']['by_subid'] as $row ) {
			$rows[] = array( 'local_clicks_by_subid', (string) $row['subid'], (string) $row['count'], (string) $row['provider'] . '|' . (string) $row['target_host'] );
		}

		foreach ( $dashboard['subid_map'] as $row ) {
			$rows[] = array( 'travelpayouts_subid_map', (string) $row['placement_key'], (string) $row['example_subid'], (string) $row['reporting_source'] );
		}

		foreach ( $dashboard['ai_sessions']['by_status'] as $row ) {
			$rows[] = array( 'ai_sessions_by_status', (string) $row['label'], (string) $row['count'], (string) $dashboard['days'] . ' days' );
		}

		foreach ( $dashboard['content'] as $row ) {
			$rows[] = array( 'content', (string) $row['post_type'], (string) $row['publish'], 'published' );
			$rows[] = array( 'content', (string) $row['post_type'], (string) $row['draft'], 'draft' );
			$rows[] = array( 'content', (string) $row['post_type'], (string) $row['pending'], 'pending' );
			$rows[] = array( 'content', (string) $row['post_type'], (string) $row['private'], 'private' );
		}

		return $rows;
	}

	private function normalize_days( int $days ): int {
		return min( 365, max( 1, absint( $days ) ) );
	}
}
