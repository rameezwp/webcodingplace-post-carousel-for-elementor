<?php
/**
 * Build the WP_Query for the carousel.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Query
 */
class DPCE_Query {

	/**
	 * Run a query based on widget settings.
	 *
	 * @param array $settings Widget settings.
	 * @return WP_Query
	 */
	public static function run( $settings ) {
		$args = array(
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => isset( $settings['posts_per_page'] ) ? max( -1, (int) $settings['posts_per_page'] ) : 10,
			'orderby'             => isset( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'               => isset( $settings['order'] ) ? $settings['order'] : 'DESC',
			'no_found_rows'       => true,
		);

		$display_by = isset( $settings['display_by'] ) ? $settings['display_by'] : 'post_type';

		if ( 'taxonomy' === $display_by ) {
			$taxonomy = isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : '';
			$terms    = isset( $settings['terms'] ) ? (array) $settings['terms'] : array();
			$terms    = array_filter( array_map( 'absint', $terms ) );

			if ( $taxonomy && ! empty( $terms ) ) {
				$tax_obj = get_taxonomy( $taxonomy );
				if ( $tax_obj && ! empty( $tax_obj->object_type ) ) {
					$args['post_type'] = $tax_obj->object_type;
				}
				$args['tax_query'] = array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $terms,
					),
				);
			} elseif ( $taxonomy ) {
				$tax_obj = get_taxonomy( $taxonomy );
				if ( $tax_obj && ! empty( $tax_obj->object_type ) ) {
					$args['post_type'] = $tax_obj->object_type;
				}
			}
		} else {
			$post_type         = isset( $settings['post_type'] ) ? $settings['post_type'] : 'post';
			$args['post_type'] = $post_type;

			$post_type_key = 'posts__' . $post_type;
			$selected      = isset( $settings[ $post_type_key ] ) ? (array) $settings[ $post_type_key ] : array();
			$selected      = array_filter( array_map( 'absint', $selected ) );

			if ( ! empty( $selected ) ) {
				$args['post__in'] = $selected;
				$args['orderby']  = 'post__in';
			}
		}

		// Exclude posts.
		$exclude = array();
		if ( isset( $settings['exclude_ids'] ) ) {
			$exclude = dpce_parse_id_list( $settings['exclude_ids'] );
		}

		// Disable current post.
		if ( ! empty( $settings['disable_current_post'] ) && is_singular() ) {
			$current = get_queried_object_id();
			if ( $current ) {
				$exclude[] = (int) $current;
			}
		}

		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = array_values( array_unique( $exclude ) );
		}

		/**
		 * Filter the WP_Query args used by the carousel widget.
		 *
		 * @param array $args     Query args.
		 * @param array $settings Widget settings.
		 */
		$args = apply_filters( 'dpce_query_args', $args, $settings );

		return new WP_Query( $args );
	}
}
