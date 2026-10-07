<?php
/**
 * REST endpoint used by the editor's post and term search fields.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Rest
 */
class DPCE_Rest {

	/**
	 * REST namespace.
	 */
	const NAMESPACE_V1 = 'dpce/v1';

	/**
	 * Results per page.
	 */
	const PER_PAGE = 20;

	/**
	 * Hook up.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register routes.
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'search' ),
				'permission_callback' => array( __CLASS__, 'can_search' ),
				'args'                => array(
					'kind'    => array(
						'type'     => 'string',
						'enum'     => array( 'post', 'term', 'author' ),
						'required' => true,
					),
					'source'  => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_key',
					),
					'search'  => array(
						'type'              => 'string',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'include' => array(
						'type'    => 'array',
						'items'   => array( 'type' => 'integer' ),
						'default' => array(),
					),
					'page'    => array(
						'type'    => 'integer',
						'default' => 1,
						'minimum' => 1,
					),
				),
			)
		);
	}

	/**
	 * Only people who can edit content can search.
	 *
	 * @return bool
	 */
	public static function can_search() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Search posts or terms.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function search( $request ) {
		$kind    = $request->get_param( 'kind' );
		$source  = (string) $request->get_param( 'source' );
		$search  = (string) $request->get_param( 'search' );
		$include = array_values( array_filter( array_map( 'absint', (array) $request->get_param( 'include' ) ) ) );
		$page    = max( 1, (int) $request->get_param( 'page' ) );

		if ( 'author' === $kind ) {
			return rest_ensure_response( self::search_authors( $search, $include, $page ) );
		}

		if ( 'term' === $kind ) {
			// "any" searches every public taxonomy (used by the include and exclude term filters).
			if ( 'any' !== $source && ! array_key_exists( $source, dpce_get_taxonomies() ) ) {
				return new WP_Error( 'dpce_invalid_taxonomy', __( 'Unknown taxonomy.', 'webcodingplace-post-carousel-for-elementor' ), array( 'status' => 400 ) );
			}
			return rest_ensure_response( self::search_terms( $source, $search, $include, $page ) );
		}

		if ( ! array_key_exists( $source, dpce_get_post_types() ) ) {
			return new WP_Error( 'dpce_invalid_post_type', __( 'Unknown post type.', 'webcodingplace-post-carousel-for-elementor' ), array( 'status' => 400 ) );
		}
		return rest_ensure_response( self::search_posts( $source, $search, $include, $page ) );
	}

	/**
	 * Search published posts of one post type.
	 *
	 * @param string $post_type Post type.
	 * @param string $search    Search text.
	 * @param int[]  $ids       Only these IDs (used to label saved values).
	 * @param int    $page      Page number.
	 * @return array{results: array<int, array{id: string, text: string}>, more: bool}
	 */
	public static function search_posts( $post_type, $search, $ids, $page ) {
		$args = array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => self::PER_PAGE,
			'paged'                  => $page,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if ( $ids ) {
			$args['post__in']       = $ids;
			$args['orderby']        = 'post__in';
			$args['posts_per_page'] = count( $ids );
			$args['paged']          = 1;
		} elseif ( '' !== $search ) {
			$args['s']       = $search;
			$args['orderby'] = 'relevance';
		}

		$query   = new WP_Query( $args );
		$results = array();
		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof WP_Post ) {
				continue;
			}
			$results[] = array(
				'id'   => (string) $post->ID,
				'text' => self::label( $post->post_title, $post->ID ),
			);
		}

		return array(
			'results' => $results,
			'more'    => ! $ids && $page < (int) $query->max_num_pages,
		);
	}

	/**
	 * Search terms of one taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $search   Search text.
	 * @param int[]  $ids      Only these IDs.
	 * @param int    $page     Page number.
	 * @return array{results: array<int, array{id: string, text: string}>, more: bool}
	 */
	public static function search_terms( $taxonomy, $search, $ids, $page ) {
		$any        = 'any' === $taxonomy;
		$taxonomies = $any ? array_keys( dpce_get_taxonomies() ) : array( $taxonomy );

		$args = array(
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
			'orderby'    => 'name',
			'number'     => self::PER_PAGE + 1,
			'offset'     => ( $page - 1 ) * self::PER_PAGE,
		);

		if ( $ids ) {
			$args['include'] = $ids;
			$args['number']  = count( $ids );
			$args['offset']  = 0;
		} elseif ( '' !== $search ) {
			$args['search'] = $search;
		}

		$terms = get_terms( $args );
		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}

		$more    = ! $ids && count( $terms ) > self::PER_PAGE;
		$results = array();
		foreach ( array_slice( $terms, 0, self::PER_PAGE ) as $term ) {
			$text = self::label( $term->name, $term->term_id );
			if ( $any ) {
				$tax  = get_taxonomy( $term->taxonomy );
				$text = sprintf(
					/* translators: 1: term name, 2: taxonomy name, for example "News (Categories)". */
					__( '%1$s (%2$s)', 'webcodingplace-post-carousel-for-elementor' ),
					$text,
					$tax ? $tax->labels->name : $term->taxonomy
				);
			}
			$results[] = array(
				'id'   => (string) $term->term_id,
				'text' => $text,
			);
		}

		return array(
			'results' => $results,
			'more'    => $more,
		);
	}

	/**
	 * Search users who have published posts.
	 *
	 * @param string $search Search text.
	 * @param int[]  $ids    Only these IDs.
	 * @param int    $page   Page number.
	 * @return array{results: array<int, array{id: string, text: string}>, more: bool}
	 */
	public static function search_authors( $search, $ids, $page ) {
		$args = array(
			'has_published_posts' => true,
			'orderby'             => 'display_name',
			'number'              => self::PER_PAGE + 1,
			'offset'              => ( $page - 1 ) * self::PER_PAGE,
			'fields'              => array( 'ID', 'display_name' ),
		);

		if ( $ids ) {
			$args['include'] = $ids;
			$args['number']  = count( $ids );
			$args['offset']  = 0;
		} elseif ( '' !== $search ) {
			$args['search']         = '*' . $search . '*';
			$args['search_columns'] = array( 'display_name', 'user_login', 'user_nicename' );
		}

		$users   = get_users( $args );
		$more    = ! $ids && count( $users ) > self::PER_PAGE;
		$results = array();
		foreach ( array_slice( $users, 0, self::PER_PAGE ) as $user ) {
			$results[] = array(
				'id'   => (string) $user->ID,
				'text' => self::label( $user->display_name, (int) $user->ID ),
			);
		}

		return array(
			'results' => $results,
			'more'    => $more,
		);
	}

	/**
	 * Plain text label with a fallback for empty titles.
	 *
	 * @param string $title Title.
	 * @param int    $id    ID.
	 * @return string
	 */
	private static function label( $title, $id ) {
		$title = trim( wp_strip_all_tags( html_entity_decode( (string) $title, ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
		return '' === $title ? '#' . $id : $title;
	}
}
