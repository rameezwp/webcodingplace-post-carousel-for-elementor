<?php
/**
 * Default callbacks for the dpce_carousel_* action hooks.
 *
 * Templates fire these hooks; this class wires sensible defaults so a
 * template author can simply call do_action() in their markup and get a
 * thumbnail / title / description / read-more button rendered using the
 * widget's settings.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Renderer
 */
class DPCE_Renderer {

	/**
	 * Singleton.
	 *
	 * @var DPCE_Renderer|null
	 */
	private static $instance = null;

	/**
	 * Boot.
	 *
	 * @return DPCE_Renderer
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook callbacks.
	 */
	private function __construct() {
		add_action( 'dpce_carousel_thumbnail', array( $this, 'render_thumbnail' ), 10, 2 );
		add_action( 'dpce_carousel_title', array( $this, 'render_title' ), 10, 2 );
		add_action( 'dpce_carousel_desc', array( $this, 'render_desc' ), 10, 2 );
		add_action( 'dpce_carousel_read_more', array( $this, 'render_read_more' ), 10, 2 );
		add_action( 'dpce_carousel_meta', array( $this, 'render_meta' ), 10, 2 );
		add_action( 'dpce_carousel_share', array( $this, 'render_share' ), 10, 2 );
	}

	/**
	 * Render the thumbnail.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_thumbnail( $post_id, $settings ) {
		$size = isset( $settings['image_size'] ) && $settings['image_size'] ? $settings['image_size'] : 'medium_large';

		$attr = array(
			'class' => 'dpce-thumbnail-img',
		);

		if ( ! empty( $settings['lazy_load'] ) ) {
			$attr['loading'] = 'lazy';
		}

		if ( has_post_thumbnail( $post_id ) ) {
			echo '<div class="dpce-thumbnail">';
			echo get_the_post_thumbnail( $post_id, $size, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe HTML from core.
			echo '</div>';
			return;
		}

		if ( ! empty( $settings['placeholder_image'] ) ) {
			printf(
				'<div class="dpce-thumbnail"><img class="dpce-thumbnail-img" src="%s" alt="%s"%s /></div>',
				esc_url( $settings['placeholder_image'] ),
				esc_attr( get_the_title( $post_id ) ),
				! empty( $settings['lazy_load'] ) ? ' loading="lazy"' : ''
			);
		}
	}

	/**
	 * Render the title using the configured field/meta key.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_title( $post_id, $settings ) {
		$field = isset( $settings['heading_field'] ) ? $settings['heading_field'] : 'title';
		$meta  = isset( $settings['heading_meta_key'] ) ? $settings['heading_meta_key'] : '';
		$max   = isset( $settings['heading_max_words'] ) ? (int) $settings['heading_max_words'] : 0;
		$append = isset( $settings['trim_append'] ) ? $settings['trim_append'] : '...';

		$value = dpce_get_field_value( $post_id, $field, $meta );
		if ( '' === trim( wp_strip_all_tags( $value ) ) ) {
			return;
		}

		$value = dpce_trim_words( $value, $max, $append );
		echo esc_html( $value );
	}

	/**
	 * Render the description using the configured field/meta key.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_desc( $post_id, $settings ) {
		$field  = isset( $settings['desc_field'] ) ? $settings['desc_field'] : 'excerpt';
		$meta   = isset( $settings['desc_meta_key'] ) ? $settings['desc_meta_key'] : '';
		$max    = isset( $settings['desc_max_words'] ) ? (int) $settings['desc_max_words'] : 20;
		$append = isset( $settings['trim_append'] ) ? $settings['trim_append'] : '...';

		$value = dpce_get_field_value( $post_id, $field, $meta );
		if ( '' === trim( wp_strip_all_tags( $value ) ) ) {
			return;
		}

		if ( ! empty( $settings['desc_render_shortcodes'] ) ) {
			// Trim BEFORE running shortcodes so we don't break tag pairs.
			$value = dpce_trim_words( $value, $max, $append );
			echo do_shortcode( wp_kses_post( $value ) );
			return;
		}

		$value = dpce_trim_words( $value, $max, $append );
		echo esc_html( $value );
	}

	/**
	 * Render the read-more button.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_read_more( $post_id, $settings ) {
		$text = isset( $settings['read_more_txt'] ) ? trim( (string) $settings['read_more_txt'] ) : '';
		if ( '' === $text ) {
			return;
		}
		$classes = isset( $settings['read_more_classes'] ) ? $settings['read_more_classes'] : 'dpce-button';
		$target  = ! empty( $settings['read_more_target'] ) ? $settings['read_more_target'] : '_self';

		printf(
			'<a class="%1$s" href="%2$s" target="%3$s" rel="%4$s">%5$s</a>',
			esc_attr( $classes ),
			esc_url( get_permalink( $post_id ) ),
			esc_attr( $target ),
			'_blank' === $target ? 'noopener noreferrer' : 'follow',
			esc_html( $text )
		);
	}

	/**
	 * Render compact post meta (date + author) used by some templates.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_meta( $post_id, $settings ) {
		unset( $settings );
		$date   = get_the_date( '', $post_id );
		$author = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );

		echo '<span class="dpce-meta-date">' . esc_html( $date ) . '</span>';
		if ( $author ) {
			echo ' <span class="dpce-meta-author">' . esc_html( $author ) . '</span>';
		}
	}

	/**
	 * Render social share buttons.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_share( $post_id, $settings ) {
		if ( empty( $settings['enable_share'] ) ) {
			return;
		}
		$networks = isset( $settings['share_networks'] ) && is_array( $settings['share_networks'] )
			? $settings['share_networks']
			: array( 'facebook', 'twitter', 'linkedin' );

		$url   = rawurlencode( get_permalink( $post_id ) );
		$title = rawurlencode( get_the_title( $post_id ) );

		$urls = array(
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'twitter'  => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
			'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
			'whatsapp' => 'https://api.whatsapp.com/send?text=' . $title . '%20' . $url,
			'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title,
			'email'    => 'mailto:?subject=' . $title . '&body=' . $url,
		);

		$labels = array(
			'facebook'  => __( 'Share on Facebook', 'dynamic-post-carousel-for-elementor' ),
			'twitter'   => __( 'Share on Twitter', 'dynamic-post-carousel-for-elementor' ),
			'linkedin'  => __( 'Share on LinkedIn', 'dynamic-post-carousel-for-elementor' ),
			'whatsapp'  => __( 'Share on WhatsApp', 'dynamic-post-carousel-for-elementor' ),
			'pinterest' => __( 'Share on Pinterest', 'dynamic-post-carousel-for-elementor' ),
			'email'     => __( 'Share by Email', 'dynamic-post-carousel-for-elementor' ),
		);

		echo '<div class="dpce-share">';
		foreach ( $networks as $network ) {
			if ( ! isset( $urls[ $network ] ) ) {
				continue;
			}
			printf(
				'<a class="dpce-share-link dpce-share-%1$s" target="_blank" rel="noopener noreferrer" href="%2$s" aria-label="%3$s"><span>%4$s</span></a>',
				esc_attr( $network ),
				esc_url( $urls[ $network ] ),
				esc_attr( $labels[ $network ] ),
				esc_html( ucfirst( $network ) )
			);
		}
		echo '</div>';
	}
}
