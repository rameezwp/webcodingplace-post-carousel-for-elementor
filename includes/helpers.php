<?php
/**
 * Helper functions.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trim a string to a given number of words and append a suffix.
 *
 * Falls back gracefully when the source contains HTML.
 *
 * @param string $text   Source string.
 * @param int    $words  Maximum number of words. Use 0 to disable trimming.
 * @param string $append Suffix appended when text was trimmed.
 * @return string
 */
function dpce_trim_words( $text, $words, $append = '...' ) {
	$text  = (string) $text;
	$words = (int) $words;

	if ( $words <= 0 || '' === trim( wp_strip_all_tags( $text ) ) ) {
		return $text;
	}

	return wp_trim_words( $text, $words, $append );
}

/**
 * Get the value of a "field" off a post.
 *
 * Built-in identifiers:
 *  - title
 *  - excerpt
 *  - content
 *  - author
 *  - date
 *  - none
 * Anything else is treated as a meta key.
 *
 * @param int    $post_id  Post ID.
 * @param string $field    Field identifier.
 * @param string $meta_key Optional meta key when $field === 'meta'.
 * @return string
 */
function dpce_get_field_value( $post_id, $field, $meta_key = '' ) {
	$post_id = (int) $post_id;
	$field   = (string) $field;

	if ( ! $post_id ) {
		return '';
	}

	switch ( $field ) {
		case 'title':
			return get_the_title( $post_id );

		case 'excerpt':
			$post = get_post( $post_id );
			if ( ! $post ) {
				return '';
			}
			$excerpt = $post->post_excerpt;
			if ( '' === trim( $excerpt ) ) {
				$excerpt = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
			}
			return $excerpt;

		case 'content':
			$post = get_post( $post_id );
			return $post ? $post->post_content : '';

		case 'author':
			$post = get_post( $post_id );
			return $post ? get_the_author_meta( 'display_name', (int) $post->post_author ) : '';

		case 'date':
			return get_the_date( '', $post_id );

		case 'none':
			return '';

		case 'meta':
			$meta_key = sanitize_key( $meta_key );
			if ( '' === $meta_key ) {
				return '';
			}
			$value = get_post_meta( $post_id, $meta_key, true );
			if ( is_array( $value ) || is_object( $value ) ) {
				return '';
			}
			return (string) $value;
	}

	// Backwards-compat: treat any other value as a meta key directly.
	$value = get_post_meta( $post_id, sanitize_key( $field ), true );
	if ( is_array( $value ) || is_object( $value ) ) {
		return '';
	}
	return (string) $value;
}

/**
 * Get the list of standard "field" choices used by the meta-key style controls.
 *
 * @return array
 */
function dpce_get_field_choices() {
	return apply_filters(
		'dpce_field_choices',
		array(
			'title'   => esc_html__( 'Post Title', 'webcodingplace-post-carousel-for-elementor' ),
			'excerpt' => esc_html__( 'Post Excerpt', 'webcodingplace-post-carousel-for-elementor' ),
			'content' => esc_html__( 'Post Content', 'webcodingplace-post-carousel-for-elementor' ),
			'author'  => esc_html__( 'Author Name', 'webcodingplace-post-carousel-for-elementor' ),
			'date'    => esc_html__( 'Post Date', 'webcodingplace-post-carousel-for-elementor' ),
			'meta'    => esc_html__( 'Custom Meta Key', 'webcodingplace-post-carousel-for-elementor' ),
			'none'    => esc_html__( 'None / Hide', 'webcodingplace-post-carousel-for-elementor' ),
		)
	);
}

/**
 * List all public registered post types as id => label.
 *
 * @return array
 */
function dpce_get_post_types() {
	$types  = get_post_types( array( 'public' => true ), 'objects' );
	$result = array();
	foreach ( $types as $type ) {
		if ( 'attachment' === $type->name ) {
			continue;
		}
		$result[ $type->name ] = $type->label;
	}
	return apply_filters( 'dpce_post_types', $result );
}

/**
 * List all public registered taxonomies as id => label.
 *
 * @return array
 */
function dpce_get_taxonomies() {
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
	$result     = array();
	foreach ( $taxonomies as $tax ) {
		$result[ $tax->name ] = $tax->label;
	}
	return apply_filters( 'dpce_taxonomies', $result );
}

/**
 * Get all posts of a given post type as id => title.
 *
 * Capped to a sensible limit to avoid memory issues in the editor.
 *
 * @param string $post_type Post type slug.
 * @param int    $limit     Max number of posts to return.
 * @return array
 */
function dpce_get_posts_for_select( $post_type, $limit = 200 ) {
	$post_type = sanitize_key( $post_type );
	if ( '' === $post_type ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => (int) $limit,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'suppress_filters'       => true,
		)
	);

	$result = array();
	foreach ( $posts as $post ) {
		$result[ $post->ID ] = $post->post_title ? $post->post_title : '#' . $post->ID;
	}
	return $result;
}

/**
 * Get all terms of a given taxonomy as id => name.
 *
 * @param string $taxonomy Taxonomy slug.
 * @param int    $limit    Max terms to return.
 * @return array
 */
function dpce_get_terms_for_select( $taxonomy, $limit = 500 ) {
	$taxonomy = sanitize_key( $taxonomy );
	if ( '' === $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => (int) $limit,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	$result = array();
	foreach ( $terms as $term ) {
		$result[ $term->term_id ] = $term->name;
	}
	return $result;
}

/**
 * List registered image sizes as slug => label.
 *
 * @return array
 */
function dpce_get_image_sizes() {
	$sizes = array(
		'thumbnail' => esc_html__( 'Thumbnail', 'webcodingplace-post-carousel-for-elementor' ),
		'medium'    => esc_html__( 'Medium', 'webcodingplace-post-carousel-for-elementor' ),
		'large'     => esc_html__( 'Large', 'webcodingplace-post-carousel-for-elementor' ),
		'full'      => esc_html__( 'Full', 'webcodingplace-post-carousel-for-elementor' ),
	);

	foreach ( array_keys( wp_get_additional_image_sizes() ) as $size ) {
		$sizes[ $size ] = $size;
	}

	return apply_filters( 'dpce_image_sizes', $sizes );
}

/**
 * Convert comma/space separated IDs to a clean array of positive integers.
 *
 * @param string|array $value Raw input.
 * @return int[]
 */
function dpce_parse_id_list( $value ) {
	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}
	$value = (string) $value;
	if ( '' === trim( $value ) ) {
		return array();
	}
	$ids = preg_split( '/[\s,]+/', $value );
	$ids = array_filter( array_map( 'absint', (array) $ids ) );
	return array_values( array_unique( $ids ) );
}

/**
 * Render the title heading element for a carousel slide.
 *
 * Templates call this to get the correct heading tag (driven by the widget's
 * Heading HTML Tag setting) with the `dpce-title` class baked in. The actual
 * text comes from the dpce_carousel_title action so the existing trim / meta
 * key plumbing keeps working.
 *
 * @param int          $post_id           Post ID.
 * @param array        $carousel_settings Settings forwarded by the widget.
 * @param array|string $args       Optional args: 'inner_wrap' (string, e.g. 'span')
 *                                 to wrap the title text, 'extra_class' (string).
 */
function dpce_render_title( $post_id, $carousel_settings, $args = array() ) {
	if ( is_string( $args ) ) {
		$args = array( 'extra_class' => $args );
	}
	$args = wp_parse_args(
		(array) $args,
		array(
			'inner_wrap'  => '',
			'extra_class' => '',
		)
	);

	$tag = dpce_get_title_tag( $carousel_settings );

	$class = 'dpce-title';
	if ( '' !== trim( (string) $args['extra_class'] ) ) {
		$class .= ' ' . preg_replace( '/[^a-z0-9 _-]/i', '', $args['extra_class'] );
	}

	$inner = tag_escape( $args['inner_wrap'] );

	echo '<' . esc_attr( $tag ) . ' class="' . esc_attr( $class ) . '">';
	if ( $inner ) {
		echo '<' . esc_attr( $inner ) . '>';
	}
	do_action( 'dpce_carousel_title', $post_id, $carousel_settings );
	if ( $inner ) {
		echo '</' . esc_attr( $inner ) . '>';
	}
	echo '</' . esc_attr( $tag ) . '>';
}


/**
 * Get a safe link target from the widget settings.
 *
 * @param array $settings Carousel settings.
 * @return string Either "_self" or "_blank".
 */
function dpce_get_link_target( $settings ) {
	$target = isset( $settings['read_more_target'] ) ? $settings['read_more_target'] : '_self';
	return '_blank' === $target ? '_blank' : '_self';
}

/**
 * Build the target and rel attributes for post links.
 *
 * @param array $settings Carousel settings.
 * @return string Attribute string with a leading space, already escaped.
 */
function dpce_link_target_attrs( $settings ) {
	if ( '_blank' === dpce_get_link_target( $settings ) ) {
		return ' target="_blank" rel="noopener noreferrer"';
	}
	return ' target="_self"';
}

/**
 * Normalize an Elementor icon value.
 *
 * Versions up to 1.4 saved the default icon with the library "solid"
 * instead of "fa-solid", so Elementor could not load it and the icon
 * did not show on the front end. Map the short names to the real ones.
 *
 * @param mixed $icon Icon control value.
 * @return array
 */
function dpce_normalize_icon( $icon ) {
	if ( ! is_array( $icon ) || empty( $icon['value'] ) ) {
		return array();
	}

	$map = array(
		'solid'   => 'fa-solid',
		'regular' => 'fa-regular',
		'brands'  => 'fa-brands',
	);
	if ( isset( $icon['library'] ) && isset( $map[ $icon['library'] ] ) ) {
		$icon['library'] = $map[ $icon['library'] ];
	}

	return $icon;
}

/**
 * HTML tags allowed for card titles, as tag => label.
 *
 * @return array
 */
function dpce_get_title_tags() {
	return array(
		'h1'   => 'H1',
		'h2'   => 'H2',
		'h3'   => 'H3',
		'h4'   => 'H4',
		'h5'   => 'H5',
		'h6'   => 'H6',
		'div'  => 'div',
		'p'    => 'p',
		'span' => 'span',
	);
}

/**
 * Get the title tag from the settings, falling back to h3.
 *
 * @param array $settings Carousel settings.
 * @return string
 */
function dpce_get_title_tag( $settings ) {
	$tag = isset( $settings['title_tag'] ) ? (string) $settings['title_tag'] : 'h3';
	return array_key_exists( $tag, dpce_get_title_tags() ) ? $tag : 'h3';
}
