<?php
/**
 * Build the WP_Query for the carousel.
 *
 * Every option added after 1.4 defaults to the 1.4 behaviour, so saved
 * carousels produce exactly the same query as before.
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
		$mode = isset( $settings['query_mode'] ) ? (string) $settings['query_mode'] : 'custom';

		if ( 'current' === $mode ) {
			$args = self::current_query_args( $settings );
		} elseif ( 'related' === $mode ) {
			$args = self::apply_filters_to_args( self::related_query_args( $settings ), $settings );
		} else {
			$args = self::apply_filters_to_args( self::custom_query_args( $settings ), $settings );
		}

		$query = new WP_Query( self::filter_args( $args, $settings ) );

		// Related posts: fall back to recent posts of the same type when nothing matches.
		if ( 'related' === $mode && ! $query->have_posts() && 'yes' === ( isset( $settings['related_fallback'] ) ? $settings['related_fallback'] : 'yes' ) ) {
			unset( $args['tax_query'] );
			if ( isset( $args['post__in'] ) && array( 0 ) === $args['post__in'] ) {
				unset( $args['post__in'] );
			}
			$query = new WP_Query( self::filter_args( $args, $settings ) );
		}

		return $query;
	}

	/**
	 * Apply the public query args filter.
	 *
	 * @param array $args     Query args.
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private static function filter_args( $args, $settings ) {
		/**
		 * Filter the WP_Query args used by the carousel widget.
		 *
		 * @param array $args     Query args.
		 * @param array $settings Widget settings.
		 */
		return apply_filters( 'dpce_query_args', $args, $settings );
	}

	/**
	 * Base arguments shared by all modes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private static function base_args( $settings ) {
		return array(
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => self::sanitize_posts_per_page( isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 10 ),
			'orderby'             => self::sanitize_orderby( isset( $settings['orderby'] ) ? $settings['orderby'] : 'date' ),
			'order'               => ( isset( $settings['order'] ) && 'ASC' === strtoupper( (string) $settings['order'] ) ) ? 'ASC' : 'DESC',
			'no_found_rows'       => true,
		);
	}

	/**
	 * "Posts I choose": the query used since 1.0.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private static function custom_query_args( $settings ) {
		$args       = self::base_args( $settings );
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
				$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Filtering by term is the feature; the query is limited by posts_per_page.
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

		return $args;
	}

	/**
	 * "Current query": the posts of the archive or search page being viewed,
	 * in the archive's own order.
	 *
	 * Outside an archive (for example in the editor) it shows recent posts.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private static function current_query_args( $settings ) {
		global $wp_query;

		$args = self::base_args( $settings );

		$is_listing = $wp_query instanceof WP_Query && ( $wp_query->is_archive() || $wp_query->is_search() || $wp_query->is_home() );
		if ( ! $is_listing ) {
			$args['post_type'] = 'post';
			return $args;
		}

		$vars = $wp_query->query_vars;
		unset( $vars['posts_per_page'], $vars['nopaging'], $vars['fields'], $vars['no_found_rows'], $vars['suppress_filters'], $vars['cache_results'], $vars['orderby'], $vars['order'] );

		$args            = array_merge( $vars, $args );
		$args['orderby'] = ! empty( $wp_query->query_vars['orderby'] ) ? $wp_query->query_vars['orderby'] : 'date';
		$args['order']   = ! empty( $wp_query->query_vars['order'] ) ? $wp_query->query_vars['order'] : 'DESC';

		return $args;
	}

	/**
	 * "Related posts": posts that share terms with the post being viewed.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private static function related_query_args( $settings ) {
		$args    = self::base_args( $settings );
		$post_id = is_singular() ? (int) get_queried_object_id() : 0;

		if ( ! $post_id ) {
			// Editor preview or a page that is not a single post: show recent posts.
			$args['post_type'] = isset( $settings['post_type'] ) ? $settings['post_type'] : 'post';
			return $args;
		}

		$post_type            = get_post_type( $post_id );
		$args['post_type']    = $post_type ? $post_type : 'post';
		$args['post__not_in'] = array( $post_id );

		$related_by = isset( $settings['related_by'] ) ? (string) $settings['related_by'] : 'any';
		$tax_query  = array( 'relation' => 'OR' );

		foreach ( get_object_taxonomies( $args['post_type'], 'objects' ) as $taxonomy ) {
			if ( ! $taxonomy->public || 'post_format' === $taxonomy->name ) {
				continue;
			}
			if ( 'any' !== $related_by && $taxonomy->name !== $related_by ) {
				continue;
			}
			$term_ids = wp_get_post_terms( $post_id, $taxonomy->name, array( 'fields' => 'ids' ) );
			if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) {
				continue;
			}
			$tax_query[] = array(
				'taxonomy' => $taxonomy->name,
				'field'    => 'term_id',
				'terms'    => array_map( 'intval', $term_ids ),
			);
		}

		if ( count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Related posts are found by shared terms.
		} else {
			// No terms to match: return nothing, the fallback may take over.
			$args['post__in'] = array( 0 );
		}

		return $args;
	}

	/**
	 * Extra filters (added in 2.0). Each one is off by default.
	 *
	 * @param array $args     Query args.
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private static function apply_filters_to_args( $args, $settings ) {
		$tax_query = array();

		// Include and exclude terms from any taxonomy.
		$include_terms = self::ids( isset( $settings['include_terms'] ) ? $settings['include_terms'] : array() );
		$groups        = self::group_terms_by_taxonomy( $include_terms );
		if ( $groups ) {
			$relation = ( isset( $settings['terms_relation'] ) && 'AND' === $settings['terms_relation'] ) ? 'AND' : 'OR';
			$include  = array( 'relation' => $relation );
			foreach ( $groups as $taxonomy => $ids ) {
				$include[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $ids,
					'operator' => 'AND' === $relation ? 'AND' : 'IN',
				);
			}
			$tax_query[] = $include;
		}

		$exclude_terms = self::ids( isset( $settings['exclude_terms'] ) ? $settings['exclude_terms'] : array() );
		foreach ( self::group_terms_by_taxonomy( $exclude_terms ) as $taxonomy => $ids ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $ids,
				'operator' => 'NOT IN',
			);
		}

		// Authors.
		$authors = self::ids( isset( $settings['include_authors'] ) ? $settings['include_authors'] : array() );
		if ( $authors ) {
			$args['author__in'] = $authors;
		}
		$not_authors = self::ids( isset( $settings['exclude_authors'] ) ? $settings['exclude_authors'] : array() );
		if ( $not_authors ) {
			$args['author__not_in'] = $not_authors;
		}

		// Date range.
		$date_query = self::date_query( $settings );
		if ( $date_query ) {
			$args['date_query'] = array( $date_query );
		}

		// Offset.
		$offset = isset( $settings['offset'] ) ? absint( $settings['offset'] ) : 0;
		if ( $offset > 0 ) {
			$args['offset'] = $offset;
		}

		// Sticky posts.
		$sticky_mode = isset( $settings['sticky_posts'] ) ? (string) $settings['sticky_posts'] : 'ignore';
		if ( in_array( $sticky_mode, array( 'only', 'exclude' ), true ) ) {
			$sticky = array_map( 'intval', (array) get_option( 'sticky_posts', array() ) );
			if ( 'only' === $sticky_mode ) {
				$args['post__in'] = $sticky ? $sticky : array( 0 );
			} elseif ( $sticky ) {
				$current              = isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array();
				$args['post__not_in'] = array_values( array_unique( array_merge( $current, $sticky ) ) );
			}
		}

		// Order by a custom field.
		if ( isset( $args['orderby'] ) && in_array( $args['orderby'], array( 'meta_value', 'meta_value_num' ), true ) ) {
			$meta_key = isset( $settings['orderby_meta_key'] ) ? sanitize_key( $settings['orderby_meta_key'] ) : '';
			if ( '' !== $meta_key ) {
				$args['meta_key'] = $meta_key; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Sorting by a custom field the user picked.
			} else {
				$args['orderby'] = 'date';
			}
		}

		// WooCommerce product filters.
		if ( self::is_product_query( $args ) ) {
			list( $args, $tax_query ) = self::apply_product_filters( $args, $settings, $tax_query );
		}

		if ( $tax_query ) {
			if ( ! empty( $args['tax_query'] ) ) {
				$tax_query[] = $args['tax_query'];
			}
			$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Term filters are the feature; the query is limited by posts_per_page.
		}

		return $args;
	}

	/**
	 * Whether the query targets WooCommerce products only.
	 *
	 * @param array $args Query args.
	 * @return bool
	 */
	private static function is_product_query( $args ) {
		if ( ! function_exists( 'wc_get_product_ids_on_sale' ) || empty( $args['post_type'] ) ) {
			return false;
		}
		return array( 'product' ) === array_values( (array) $args['post_type'] );
	}

	/**
	 * Featured, on sale, best selling, top rated and stock filters.
	 *
	 * @param array $args      Query args.
	 * @param array $settings  Widget settings.
	 * @param array $tax_query Tax query clauses collected so far.
	 * @return array{0: array, 1: array} Updated args and tax query clauses.
	 */
	private static function apply_product_filters( $args, $settings, $tax_query ) {
		$filter = isset( $settings['product_filter'] ) ? (string) $settings['product_filter'] : 'none';

		switch ( $filter ) {
			case 'featured':
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'featured' ),
				);
				break;

			case 'on_sale':
				$on_sale          = array_map( 'intval', wc_get_product_ids_on_sale() );
				$args['post__in'] = empty( $args['post__in'] ) ? $on_sale : array_values( array_intersect( array_map( 'intval', (array) $args['post__in'] ), $on_sale ) );
				if ( ! $args['post__in'] ) {
					$args['post__in'] = array( 0 );
				}
				break;

			case 'best_selling':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Sorting by sales is the feature.
				$args['orderby']  = array(
					'meta_value_num' => 'DESC',
					'date'           => 'DESC',
				);
				break;

			case 'top_rated':
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Sorting by rating is the feature.
				$args['orderby']  = array(
					'meta_value_num' => 'DESC',
					'date'           => 'DESC',
				);
				break;
		}

		if ( 'yes' === ( isset( $settings['hide_out_of_stock'] ) ? $settings['hide_out_of_stock'] : '' ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'outofstock' ),
				'operator' => 'NOT IN',
			);
		}

		return array( $args, $tax_query );
	}

	/**
	 * Build a date_query clause from the Date Range setting.
	 *
	 * @param array $settings Widget settings.
	 * @return array Empty when no date limit is set.
	 */
	private static function date_query( $settings ) {
		$range = isset( $settings['date_range'] ) ? (string) $settings['date_range'] : 'anytime';

		$relative = array(
			'day'     => '1 day ago',
			'week'    => '1 week ago',
			'month'   => '1 month ago',
			'quarter' => '3 months ago',
			'year'    => '1 year ago',
		);

		if ( isset( $relative[ $range ] ) ) {
			return array(
				'after'     => $relative[ $range ],
				'inclusive' => true,
			);
		}

		if ( 'custom' === $range ) {
			$clause = array( 'inclusive' => true );
			if ( ! empty( $settings['date_after'] ) ) {
				$clause['after'] = sanitize_text_field( (string) $settings['date_after'] );
			}
			if ( ! empty( $settings['date_before'] ) ) {
				$clause['before'] = sanitize_text_field( (string) $settings['date_before'] );
			}
			return count( $clause ) > 1 ? $clause : array();
		}

		return array();
	}

	/**
	 * Group term IDs by their taxonomy.
	 *
	 * @param int[] $term_ids Term IDs.
	 * @return array<string, int[]>
	 */
	private static function group_terms_by_taxonomy( $term_ids ) {
		if ( ! $term_ids ) {
			return array();
		}
		$terms = get_terms(
			array(
				'include'    => $term_ids,
				'hide_empty' => false,
				'taxonomy'   => array_keys( dpce_get_taxonomies() ),
			)
		);
		if ( is_wp_error( $terms ) ) {
			return array();
		}
		$groups = array();
		foreach ( $terms as $term ) {
			$groups[ $term->taxonomy ][] = (int) $term->term_id;
		}
		return $groups;
	}

	/**
	 * Clean a list of IDs.
	 *
	 * @param mixed $value Array or comma separated string.
	 * @return int[]
	 */
	private static function ids( $value ) {
		return dpce_parse_id_list( is_array( $value ) ? $value : (string) $value );
	}

	/**
	 * Keep the number of posts within a safe range.
	 *
	 * Versions up to 1.4 accepted -1 (all posts). That still works, but is
	 * capped by the `dpce_max_posts` filter (100 by default) so a carousel
	 * can never run an unbounded query.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public static function sanitize_posts_per_page( $value ) {
		/**
		 * Filter the largest number of posts one carousel may query.
		 *
		 * @since 2.0.0
		 *
		 * @param int $max Maximum number of posts. Default 100.
		 */
		$max   = max( 1, (int) apply_filters( 'dpce_max_posts', 100 ) );
		$value = (int) $value;

		if ( -1 === $value || $value > $max ) {
			return $max;
		}

		// WP_Query treats 0 as 1 and other negative numbers as their absolute value.
		return $value;
	}

	/**
	 * Limit orderby to values the widget can produce.
	 *
	 * @param mixed $orderby Raw value.
	 * @return string
	 */
	public static function sanitize_orderby( $orderby ) {
		$allowed = array( 'date', 'title', 'menu_order', 'rand', 'comment_count', 'modified', 'ID', 'author', 'name', 'meta_value', 'meta_value_num', 'post__in' );
		return in_array( $orderby, $allowed, true ) ? $orderby : 'date';
	}
}
