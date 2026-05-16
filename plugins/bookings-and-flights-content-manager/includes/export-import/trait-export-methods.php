<?php
/**
 * Export/import method group.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined( 'ABSPATH' ) || exit;

trait Bookings_And_Flights_Export_Import_Export_Methods {
    private function export_pages() {
        $pages = array();
        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        $templates = $fields_registry->get_templates();

        foreach ( $templates as $template ) {
            $query = new WP_Query( array(
                'post_type'      => 'page',
                'posts_per_page' => -1,
                'post_status'    => 'any',
                'meta_key'       => '_wp_page_template',
                'meta_value'     => $template,
                'no_found_rows'  => true,
            ) );

            if ( ! $query->have_posts() ) {
                continue;
            }

            $fields = $fields_registry->get_fields( $template );

            foreach ( $query->posts as $page ) {
                $page_key = $template . '/' . $page->post_name;
                $field_values = array();

                foreach ( $fields as $key => $field ) {
                    $value = get_post_meta( $page->ID, Bookings_And_Flights_Config::meta_key( $key ), true );
                    $value = maybe_unserialize( $value );
                    $value = $this->resolve_media_for_export( $value, $field );
                    $field_values[ $key ] = $value;
                }

                $pages[ $page_key ] = array(
                    'post_name'      => $page->post_name,
                    'source_post_id' => $page->ID,
                    'template'       => $template,
                    'fields'         => $field_values,
                );
            }

            wp_reset_postdata();
        }

        return apply_filters( 'bookings_and_flights_export_pages', $pages );
    }

    /**
     * Export global options.
     *
     * @return array
     */
    private function export_global_options() {
        $options = get_option( Bookings_And_Flights_Options::OPTION_NAME, array() );
        $fields  = Bookings_And_Flights_Options::get_fields();

        foreach ( $fields as $key => $field ) {
            if ( ! isset( $options[ $key ] ) ) {
                continue;
            }

            $type = isset( $field['type'] ) ? $field['type'] : 'text';

            if ( 'image' === $type ) {
                $attachment_id = absint( $options[ $key ] );
                $options[ $key ] = $this->attachment_to_portable( $attachment_id );
            } elseif ( 'gallery' === $type ) {
                $ids = $options[ $key ];
                if ( is_string( $ids ) && ! empty( $ids ) ) {
                    $ids = array_map( 'absint', explode( ',', $ids ) );
                }
                if ( is_array( $ids ) ) {
                    $portable = array();
                    foreach ( $ids as $id ) {
                        $portable[] = $this->attachment_to_portable( absint( $id ) );
                    }
                    $options[ $key ] = array(
                        '_attachment_urls' => $portable,
                        'ids'             => array_map( 'absint', $ids ),
                    );
                }
            }
        }

        return apply_filters( 'bookings_and_flights_export_global_options', $options );
    }

    /**
     * Export all CPTs declared in config.
     *
     * @return array
     */
    private function export_custom_post_types() {
        $cpt_data    = array();
        $definitions = Bookings_And_Flights_Config::get_cpt_definitions();

        foreach ( $definitions as $post_type => $definition ) {
            $orderby = isset( $definition['orderby'] ) ? $definition['orderby'] : 'menu_order';
            $order   = isset( $definition['order'] ) ? $definition['order'] : 'ASC';

            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                'post_status'    => 'any',
                'orderby'        => $orderby,
                'order'          => $order,
                'no_found_rows'  => true,
            ) );

            $posts = array();

            foreach ( $query->posts as $post ) {
                $entry = array(
                    'source_post_id' => $post->ID,
                    'title'          => $post->post_title,
                    'post_name'      => $post->post_name,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'status'         => $post->post_status,
                    'menu_order'     => $post->menu_order,
                );

                // Featured image.
                $thumb_id = get_post_thumbnail_id( $post->ID );
                $entry['featured_image'] = $thumb_id ? $this->attachment_to_portable( $thumb_id ) : null;

                // Meta.
                $entry['meta'] = $this->resolve_cpt_meta_for_export( $post->ID, $definition );

                // Taxonomies.
                $entry['taxonomies'] = array();
                $taxonomies = isset( $definition['taxonomies'] ) ? $definition['taxonomies'] : array();
                foreach ( $taxonomies as $taxonomy ) {
                    $terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'all' ) );
                    if ( is_wp_error( $terms ) ) {
                        continue;
                    }
                    $entry['taxonomies'][ $taxonomy ] = array();
                    foreach ( $terms as $term ) {
                        $entry['taxonomies'][ $taxonomy ][] = array(
                            'slug' => $term->slug,
                            'name' => $term->name,
                        );
                    }
                }

                $posts[] = $entry;
            }

            wp_reset_postdata();

            $posts = apply_filters( 'bookings_and_flights_export_cpt_posts', $posts, $post_type );
            $cpt_data[ $post_type ] = $posts;
        }

        return $cpt_data;
    }

    private function resolve_cpt_meta_for_export( $post_id, $definition ) {
        $meta_fields  = isset( $definition['meta_fields'] ) ? $definition['meta_fields'] : array();
        $image_keys   = isset( $definition['image_keys'] ) ? $definition['image_keys'] : array();
        $gallery_keys = isset( $definition['gallery_keys'] ) ? $definition['gallery_keys'] : array();
        $meta_prefix  = isset( $definition['meta_prefix'] ) ? $definition['meta_prefix'] : '';

        $exported = array();

        // Export declared meta_fields.
        foreach ( $meta_fields as $key => $type ) {
            $value = get_post_meta( $post_id, $key, true );
            $value = maybe_unserialize( $value );
            $exported[ $key ] = $value;
        }

        // Export image keys as portable payloads.
        foreach ( $image_keys as $key ) {
            $value = get_post_meta( $post_id, $key, true );
            $exported[ $key ] = $this->attachment_to_portable( absint( $value ) );
        }

        // Export gallery keys as portable payloads.
        foreach ( $gallery_keys as $key ) {
            $value = get_post_meta( $post_id, $key, true );
            $value = maybe_unserialize( $value );
            $ids   = $value;
            if ( is_string( $ids ) && ! empty( $ids ) ) {
                $ids = array_map( 'absint', explode( ',', $ids ) );
            }
            if ( is_array( $ids ) ) {
                $portable = array();
                foreach ( $ids as $id ) {
                    $portable[] = $this->attachment_to_portable( absint( $id ) );
                }
                $exported[ $key ] = array(
                    '_attachment_urls' => $portable,
                    'ids'             => array_map( 'absint', $ids ),
                );
            } else {
                $exported[ $key ] = $value;
            }
        }

        return $exported;
    }

    // =========================================================================
}
