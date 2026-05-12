<?php

namespace BAF\Core\Reports;

defined( 'ABSPATH' ) || exit;

final class Reporting_Service {

	private Reporting_Repository $repository;

	public function __construct( ?Reporting_Repository $repository = null ) {
		$this->repository = $repository ?? new Reporting_Repository();
	}

	public function dashboard( int $days = 30 ): array {
		$days = $this->normalize_days( $days );

		return array(
			'days'           => $days,
			'generated_at'   => wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ),
			'clicks'         => $this->repository->click_summary( $days ),
			'ai_sessions'    => $this->repository->ai_session_summary( $days ),
			'provider_stats' => $this->repository->provider_stats(),
			'content'        => $this->repository->content_summary(),
			'conversion'     => array(
				'available' => false,
				'message'   => __( 'Revenue and conversion postback data is not available yet. Reports only show WordPress-local searches, clicks, AI sessions, provider status, and content signals.', 'bookings-flights-core' ),
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