<?php
/**
 * Export/import method group.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined( 'ABSPATH' ) || exit;

trait Bookings_And_Flights_Export_Import_Media_Methods {
    // =========================================================================
    // MEDIA HELPERS
    // =========================================================================

    /**
     * Convert an attachment ID to a portable payload for export.
     *
     * @param int $attachment_id Attachment ID.
     * @return array|null Portable payload or null.
     */
    private function attachment_to_portable( $attachment_id ) {
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id ) {
            return null;
        }

        $url = wp_get_attachment_url( $attachment_id );
        if ( ! $url ) {
            return null;
        }

        return array(
            '_attachment_url' => $url,
            '_alt'            => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
            'id'              => $attachment_id,
        );
    }

    /**
     * Resolve media fields for export based on field type.
     *
     * @param mixed $value Raw meta value.
     * @param array $field Field definition.
     * @return mixed Resolved value.
     */
    private function resolve_media_for_export( $value, $field ) {
        $type = isset( $field['type'] ) ? $field['type'] : 'text';

        switch ( $type ) {
            case 'image':
                return $this->attachment_to_portable( absint( $value ) );

            case 'gallery':
                $ids = $value;
                if ( is_string( $ids ) && ! empty( $ids ) ) {
                    $ids = array_map( 'absint', explode( ',', $ids ) );
                }
                if ( ! is_array( $ids ) ) {
                    return $value;
                }
                $portable = array();
                foreach ( $ids as $id ) {
                    $portable[] = $this->attachment_to_portable( absint( $id ) );
                }
                return array(
                    '_attachment_urls' => $portable,
                    'ids'             => array_map( 'absint', $ids ),
                );

            case 'repeater':
                if ( ! is_array( $value ) ) {
                    return $value;
                }
                $subfields = isset( $field['subfields'] ) ? $field['subfields'] : array();
                $resolved  = array();
                foreach ( $value as $row ) {
                    if ( ! is_array( $row ) ) {
                        $resolved[] = $row;
                        continue;
                    }
                    $resolved_row = array();
                    foreach ( $row as $sub_key => $sub_value ) {
                        $sub_field = isset( $subfields[ $sub_key ] ) ? $subfields[ $sub_key ] : array( 'type' => 'text' );
                        $resolved_row[ $sub_key ] = $this->resolve_media_for_export( $sub_value, $sub_field );
                    }
                    $resolved[] = $resolved_row;
                }
                return $resolved;

            default:
                return $value;
        }
    }

    /**
     * Resolve media fields for import based on field type.
     *
     * @param mixed $value Exported value.
     * @param array $field Field definition.
     * @return mixed Resolved value (attachment IDs).
     */
    private function resolve_media_for_import( $value, $field ) {
        $type = isset( $field['type'] ) ? $field['type'] : 'text';

        switch ( $type ) {
            case 'image':
                return $this->resolve_portable_image( $value, 0 );

            case 'gallery':
                return $this->resolve_portable_gallery( $value, 0 );

            case 'repeater':
                if ( ! is_array( $value ) ) {
                    return $value;
                }
                $subfields = isset( $field['subfields'] ) ? $field['subfields'] : array();
                $resolved  = array();
                foreach ( $value as $row ) {
                    if ( ! is_array( $row ) ) {
                        $resolved[] = $row;
                        continue;
                    }
                    $resolved_row = array();
                    foreach ( $row as $sub_key => $sub_value ) {
                        $sub_field = isset( $subfields[ $sub_key ] ) ? $subfields[ $sub_key ] : array( 'type' => 'text' );
                        $resolved_row[ $sub_key ] = $this->resolve_media_for_import( $sub_value, $sub_field );
                    }
                    $resolved[] = $resolved_row;
                }
                return $resolved;

            default:
                return $value;
        }
    }

    /**
     * Resolve a portable image payload back to a local attachment ID.
     *
     * @param mixed $value   Portable payload or raw value.
     * @param int   $post_id Post to attach sideloaded image to.
     * @return int Attachment ID or 0.
     */
    private function resolve_portable_image( $value, $post_id ) {
        if ( ! is_array( $value ) || empty( $value['_attachment_url'] ) ) {
            return is_numeric( $value ) ? absint( $value ) : 0;
        }

        $url = $value['_attachment_url'];
        $alt = isset( $value['_alt'] ) ? $value['_alt'] : '';

        return $this->sideload_image( $url, $post_id, $alt );
    }

    /**
     * Resolve a portable gallery payload back to local attachment IDs.
     *
     * @param mixed $value   Portable gallery payload or raw value.
     * @param int   $post_id Post to attach sideloaded images to.
     * @return string Comma-separated attachment IDs.
     */
    private function resolve_portable_gallery( $value, $post_id ) {
        if ( ! is_array( $value ) || empty( $value['_attachment_urls'] ) ) {
            // Raw value — could be comma-separated IDs.
            if ( is_string( $value ) ) {
                return $value;
            }
            if ( is_array( $value ) ) {
                return implode( ',', array_map( 'absint', $value ) );
            }
            return '';
        }

        $ids = array();
        foreach ( $value['_attachment_urls'] as $portable ) {
            $id = $this->resolve_portable_image( $portable, $post_id );
            if ( $id ) {
                $ids[] = $id;
            }
        }

        return implode( ',', $ids );
    }

    /**
     * Sideload an image from URL, with caching and SSRF protection.
     *
     * @param string $url     Image URL.
     * @param int    $post_id Post to attach to.
     * @param string $alt     Alt text to restore.
     * @return int Attachment ID or 0.
     */
    private function sideload_image( $url, $post_id, $alt = '' ) {
        if ( empty( $url ) ) {
            return 0;
        }

        // Check cache.
        if ( isset( $this->sideload_cache[ $url ] ) ) {
            return $this->sideload_cache[ $url ];
        }

        // SSRF protection: only http/https.
        $scheme = wp_parse_url( $url, PHP_URL_SCHEME );
        if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
            $this->warnings[] = sprintf(
                /* translators: %s: URL */
                __( 'Skipped image with invalid scheme: %s', 'bookings_and_flights-content-manager' ),
                $url
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        if ( ! wp_http_validate_url( $url ) ) {
            $this->warnings[] = sprintf(
                /* translators: %s: URL */
                __( 'Skipped image that failed URL validation: %s', 'bookings_and_flights-content-manager' ),
                $url
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        // Local URL detection: skip sideload if already on this site.
        $local_id = attachment_url_to_postid( $url );
        if ( $local_id ) {
            $this->sideload_cache[ $url ] = $local_id;
            // Still update alt text if provided.
            if ( '' !== $alt ) {
                update_post_meta( $local_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
            }
            return $local_id;
        }

        // Download to temp file.
        $tmp = download_url( $url );
        if ( is_wp_error( $tmp ) ) {
            $this->warnings[] = sprintf(
                /* translators: %1$s: URL, %2$s: error */
                __( 'Failed to download image "%1$s": %2$s', 'bookings_and_flights-content-manager' ),
                $url,
                $tmp->get_error_message()
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        $file_array = array(
            'name'     => basename( wp_parse_url( $url, PHP_URL_PATH ) ),
            'tmp_name' => $tmp,
        );

        $attachment_id = media_handle_sideload( $file_array, $post_id );

        // Clean up temp file if sideload failed.
        if ( is_wp_error( $attachment_id ) ) {
            if ( file_exists( $tmp ) ) {
                unlink( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
            }
            $this->warnings[] = sprintf(
                /* translators: %1$s: URL, %2$s: error */
                __( 'Failed to sideload image "%1$s": %2$s', 'bookings_and_flights-content-manager' ),
                $url,
                $attachment_id->get_error_message()
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        // Restore alt text.
        if ( '' !== $alt ) {
            update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
        }

        $this->sideload_cache[ $url ] = $attachment_id;
        return $attachment_id;
    }

    /**
     * Resolve CPT meta for export — handles image_keys, gallery_keys, and plain meta.
     *
     * @param int   $post_id    Post ID.
     * @param array $definition CPT definition.
     * @return array Exported meta.
     */
}
