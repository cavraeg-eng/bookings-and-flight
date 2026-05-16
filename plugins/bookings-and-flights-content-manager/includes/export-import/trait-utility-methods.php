<?php
/**
 * Export/import method group.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined( 'ABSPATH' ) || exit;

trait Bookings_And_Flights_Export_Import_Utility_Methods {
    // LOOKUP HELPERS
    // =========================================================================

    /**
     * Find a page by template and slug.
     *
     * @param string $template  Page template filename.
     * @param string $post_name Post slug.
     * @return int|false Post ID or false.
     */
    private function find_page_by_template_and_slug( $template, $post_name ) {
        $args = array(
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'post_status'    => 'any',
            'no_found_rows'  => true,
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $template,
            'fields'         => 'ids',
        );

        if ( ! empty( $post_name ) ) {
            $args['name'] = $post_name;
        }

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            return $query->posts[0];
        }

        return false;
    }

    /**
     * Find a CPT post by cascade: source_id meta → slug → title.
     *
     * @param string $post_type CPT slug.
     * @param int    $source_id Source post ID from export.
     * @param string $slug      Post slug.
     * @param string $title     Post title.
     * @return int|false Post ID or false.
     */
    private function find_cpt_post( $post_type, $source_id, $slug, $title ) {
        // 1. Match by _source_post_id meta.
        if ( $source_id ) {
            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'no_found_rows'  => true,
                'meta_key'       => '_source_post_id',
                'meta_value'     => $source_id,
                'fields'         => 'ids',
            ) );

            if ( $query->have_posts() ) {
                return $query->posts[0];
            }
        }

        // 2. Match by slug.
        if ( ! empty( $slug ) ) {
            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'no_found_rows'  => true,
                'name'           => $slug,
                'fields'         => 'ids',
            ) );

            if ( $query->have_posts() ) {
                return $query->posts[0];
            }
        }

        // 3. Match by title (last resort).
        if ( ! empty( $title ) ) {
            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'no_found_rows'  => true,
                'title'          => $title,
                'fields'         => 'ids',
            ) );

            if ( $query->have_posts() ) {
                return $query->posts[0];
            }
        }

        return false;
    }

    // =========================================================================
    // URL REWRITING
    // =========================================================================

    /**
     * Rewrite URLs in a value — recursive for arrays, string-only.
     *
     * Only runs if source and target hosts differ.
     *
     * @param mixed  $value      Value to rewrite.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return mixed Rewritten value.
     */
    private function rewrite_urls( $value, $source_url, $target_url ) {
        if ( empty( $source_url ) || empty( $target_url ) ) {
            return $value;
        }

        $source_host = wp_parse_url( $source_url, PHP_URL_HOST );
        $target_host = wp_parse_url( $target_url, PHP_URL_HOST );

        if ( $source_host === $target_host ) {
            return $value;
        }

        return $this->rewrite_urls_recursive( $value, $source_url, $target_url );
    }

    /**
     * Recursively rewrite URLs in a value.
     *
     * @param mixed  $value      Value to process.
     * @param string $source_url Full source origin (scheme + host).
     * @param string $target_url Full target origin (scheme + host).
     * @return mixed
     */
    private function rewrite_urls_recursive( $value, $source_url, $target_url ) {
        if ( is_string( $value ) ) {
            // Replace full origin.
            $source_origin = $this->get_origin( $source_url );
            $target_origin = $this->get_origin( $target_url );

            if ( $source_origin && $target_origin ) {
                $value = str_replace( $source_origin, $target_origin, $value );
            }

            return $value;
        }

        if ( is_array( $value ) ) {
            foreach ( $value as $k => $v ) {
                $value[ $k ] = $this->rewrite_urls_recursive( $v, $source_url, $target_url );
            }
            return $value;
        }

        // Integers, booleans, null — untouched.
        return $value;
    }

    /**
     * Extract origin (scheme + host) from a URL.
     *
     * @param string $url Full URL.
     * @return string|false Origin string or false.
     */
    private function get_origin( $url ) {
        $parts = wp_parse_url( $url );
        if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
            return false;
        }

        $origin = $parts['scheme'] . '://' . $parts['host'];
        if ( ! empty( $parts['port'] ) ) {
            $origin .= ':' . $parts['port'];
        }

        return $origin;
    }

    /**
     * Rewrite URLs only for text-like field types.
     *
     * @param mixed  $value      Field value.
     * @param string $type       Field type.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return mixed
     */
    private function rewrite_urls_for_field( $value, $type, $source_url, $target_url ) {
        $rewritable_types = array( 'text', 'textarea', 'wysiwyg', 'url' );

        if ( in_array( $type, $rewritable_types, true ) ) {
            return $this->rewrite_urls( $value, $source_url, $target_url );
        }

        // Link field: rewrite only the 'url' subfield.
        if ( 'link' === $type && is_array( $value ) && isset( $value['url'] ) ) {
            $value['url'] = $this->rewrite_urls( $value['url'], $source_url, $target_url );
            return $value;
        }

        return $value;
    }

    // =========================================================================
    // ADMIN NOTICES
    // =========================================================================

    /**
     * Store a notice in a transient for display after redirect.
     *
     * @param string $type    Notice type: success, error, warning.
     * @param string $message Notice message.
     */
    private function set_transient_notice( $type, $message ) {
        set_transient(
            'bookings_and_flights_export_import_notice',
            array(
                'type'    => $type,
                'message' => $message,
            ),
            60
        );
    }

    /**
     * Display admin notices from transient.
     */
    public function display_admin_notices() {
        $notice = get_transient( 'bookings_and_flights_export_import_notice' );

        if ( ! $notice ) {
            return;
        }

        delete_transient( 'bookings_and_flights_export_import_notice' );

        $type    = isset( $notice['type'] ) ? $notice['type'] : 'info';
        $message = isset( $notice['message'] ) ? $notice['message'] : '';

        if ( empty( $message ) ) {
            return;
        }

        $class = 'notice notice-' . esc_attr( $type ) . ' is-dismissible';

        // Support multi-line messages (warnings appended).
        $lines = explode( "\n", $message );
        $html  = '<p><strong>' . esc_html( array_shift( $lines ) ) . '</strong></p>';

        if ( ! empty( $lines ) ) {
            $html .= '<ul style="list-style:disc;margin-left:1.5em;">';
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( '' !== $line ) {
                    $html .= '<li>' . esc_html( $line ) . '</li>';
                }
            }
            $html .= '</ul>';
        }

        printf( '<div class="%s">%s</div>', esc_attr( $class ), $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Redirect back to the tools page (PRG pattern).
     */
    private function redirect_back() {
        wp_safe_redirect( admin_url( 'tools.php?page=bookings_and_flights-export-import' ) );
        exit;
    }
}
