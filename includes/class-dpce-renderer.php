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
		add_action( 'dpce_carousel_overlay', array( $this, 'render_overlay' ), 10, 2 );
		add_action( 'dpce_carousel_meta', array( $this, 'render_meta' ), 10, 2 );
		add_action( 'dpce_carousel_share', array( $this, 'render_share' ), 10, 2 );
		add_action( 'dpce_carousel_icon', array( $this, 'render_icon' ), 10, 4 );
		add_action( 'dpce_carousel_after_title', array( $this, 'render_meta_row' ), 10, 2 );
		add_action( 'dpce_carousel_overlay', array( $this, 'render_product_extras' ), 5, 2 );
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

		if ( ! empty( $settings['image_sizes_attr'] ) ) {
			$attr['sizes'] = $settings['image_sizes_attr'];
		}

		// Lazy load only slides that start outside the visible area, so the
		// first visible images are not delayed (better Largest Contentful Paint).
		$index   = isset( $settings['slide_index'] ) ? (int) $settings['slide_index'] : 0;
		$visible = isset( $settings['visible_slides'] ) ? (int) $settings['visible_slides'] : 0;
		if ( ! empty( $settings['lazy_load'] ) && $index >= $visible ) {
			$attr['loading'] = 'lazy';
		}

		$badges = $this->get_badges( $post_id, $settings );

		if ( has_post_thumbnail( $post_id ) ) {
			echo '<div class="dpce-thumbnail' . ( '' !== $badges ? ' dpce-has-badge' : '' ) . '">';
			echo $badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in get_badges().
			echo get_the_post_thumbnail( $post_id, $size, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe HTML from core.
			echo '</div>';
			return;
		}

		if ( ! empty( $settings['placeholder_image_id'] ) && wp_attachment_is_image( (int) $settings['placeholder_image_id'] ) ) {
			// The placeholder is decorative: the post title is printed right after it.
			$attr['alt'] = '';
			echo '<div class="dpce-thumbnail">';
			echo wp_get_attachment_image( (int) $settings['placeholder_image_id'], $size, false, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe HTML from core.
			echo '</div>';
			return;
		}

		if ( ! empty( $settings['placeholder_image'] ) ) {
			// The placeholder is decorative: the post title is printed right after it.
			printf(
				'<div class="dpce-thumbnail"><img class="dpce-thumbnail-img" src="%s" alt=""%s /></div>',
				esc_url( $settings['placeholder_image'] ),
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
		$field  = isset( $settings['heading_field'] ) ? $settings['heading_field'] : 'title';
		$meta   = isset( $settings['heading_meta_key'] ) ? $settings['heading_meta_key'] : '';
		$max    = isset( $settings['heading_max_words'] ) ? (int) $settings['heading_max_words'] : 0;
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
			// Trim before running shortcodes so tag pairs are not cut in half.
			// HTML is only kept when no word limit is set, because trimming strips tags.
			$value = dpce_trim_words( $value, $max, $append );
			echo do_shortcode( wp_kses_post( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Input is filtered by wp_kses_post(); shortcode output is trusted, as in the_content.
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
		// When the entire card links to the post, the read-more button is
		// suppressed so we do not render two overlapping anchors.
		$link_area = isset( $settings['link_area'] ) ? $settings['link_area'] : 'card';
		if ( 'button' !== $link_area ) {
			return;
		}

		$text = isset( $settings['read_more_txt'] ) ? trim( (string) $settings['read_more_txt'] ) : '';
		if ( '' === $text ) {
			return;
		}
		$classes = isset( $settings['read_more_classes'] ) ? $settings['read_more_classes'] : 'dpce-button';

		printf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $classes ),
			esc_url( (string) get_permalink( $post_id ) ),
			dpce_link_target_attrs( $settings ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from an allowlist.
			esc_html( $text )
		);
	}

	/**
	 * Render the absolute overlay anchor that turns the whole card into a
	 * link to the post. Suppressed when "Link Area" is set to button-only.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_overlay( $post_id, $settings ) {
		$link_area = isset( $settings['link_area'] ) ? $settings['link_area'] : 'card';
		if ( 'card' !== $link_area ) {
			return;
		}
		printf(
			'<a class="dpce-overlay-link" href="%1$s"%2$s aria-label="%3$s"></a>',
			esc_url( (string) get_permalink( $post_id ) ),
			dpce_link_target_attrs( $settings ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from an allowlist.
			esc_attr( wp_strip_all_tags( get_the_title( $post_id ) ) )
		);
	}

	/**
	 * Render an icon through Elementor's icon manager.
	 *
	 * Templates pass a Font Awesome class in $icon for their fixed icons
	 * (comments, clock and so on). The widget's own "Icon" control is
	 * rendered after it when set.
	 *
	 * @param int    $post_id   Post ID.
	 * @param array  $settings  Carousel settings.
	 * @param string $icon      Optional Font Awesome class, for example "far fa-clock".
	 * @param string $css_class CSS class for the fixed icon.
	 */
	public function render_icon( $post_id, $settings, $icon = '', $css_class = 'dpce-custom-icon' ) {
		unset( $post_id );

		if ( $icon ) {
			\Elementor\Icons_Manager::render_icon(
				array(
					'library' => 'fa-regular',
					'value'   => (string) $icon,
				),
				array(
					'aria-hidden' => 'true',
					'class'       => (string) $css_class,
				)
			);
		}

		$style_icon = isset( $settings['style_icon'] ) ? dpce_normalize_icon( $settings['style_icon'] ) : array();
		if ( ! empty( $style_icon['value'] ) ) {
			\Elementor\Icons_Manager::render_icon(
				$style_icon,
				array(
					'aria-hidden' => 'true',
					'class'       => 'dpce-icon',
				)
			);
		}
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
		$author = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );

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

		$url   = rawurlencode( (string) get_permalink( $post_id ) );
		$title = rawurlencode( wp_strip_all_tags( get_the_title( $post_id ) ) );

		$urls = array(
			'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'twitter'   => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
			'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
			'whatsapp'  => 'https://api.whatsapp.com/send?text=' . $title . '%20' . $url,
			'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title,
			'email'     => 'mailto:?subject=' . $title . '&body=' . $url,
		);

		$icons = array(
			'facebook'  => array(
				'value'   => 'fab fa-facebook-f',
				'library' => 'fa-brands',
			),
			'twitter'   => array(
				// The X logo ships with Elementor 3.20 and newer.
				'value'   => version_compare( ELEMENTOR_VERSION, '3.20.0', '>=' ) ? 'fab fa-x-twitter' : 'fab fa-twitter',
				'library' => 'fa-brands',
			),
			'linkedin'  => array(
				'value'   => 'fab fa-linkedin-in',
				'library' => 'fa-brands',
			),
			'whatsapp'  => array(
				'value'   => 'fab fa-whatsapp',
				'library' => 'fa-brands',
			),
			'pinterest' => array(
				'value'   => 'fab fa-pinterest-p',
				'library' => 'fa-brands',
			),
			'email'     => array(
				'value'   => 'fas fa-envelope',
				'library' => 'fa-solid',
			),
		);

		$labels = array(
			'facebook'  => __( 'Share on Facebook', 'webcodingplace-post-carousel-for-elementor' ),
			'twitter'   => __( 'Share on Twitter', 'webcodingplace-post-carousel-for-elementor' ),
			'linkedin'  => __( 'Share on LinkedIn', 'webcodingplace-post-carousel-for-elementor' ),
			'whatsapp'  => __( 'Share on WhatsApp', 'webcodingplace-post-carousel-for-elementor' ),
			'pinterest' => __( 'Share on Pinterest', 'webcodingplace-post-carousel-for-elementor' ),
			'email'     => __( 'Share by Email', 'webcodingplace-post-carousel-for-elementor' ),
		);

		echo '<div class="dpce-share">';

		foreach ( $networks as $network ) {
			if ( ! is_string( $network ) || ! isset( $urls[ $network ] ) ) {
				continue;
			}

			printf(
				'<a class="dpce-share-link dpce-share-%1$s" target="_blank" rel="noopener noreferrer" href="%2$s" aria-label="%3$s">',
				esc_attr( $network ),
				esc_url( $urls[ $network ] ),
				esc_attr( $labels[ $network ] )
			);

			\Elementor\Icons_Manager::render_icon(
				$icons[ $network ],
				array(
					'aria-hidden' => 'true',
					'class'       => 'dpce-social-icon',
				)
			);

			echo '</a>';
		}

		echo '</div>';
	}

	/**
	 * Badges shown on the image: the first term and, for products, "Sale".
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 * @return string Safe HTML, or an empty string.
	 */
	public function get_badges( $post_id, $settings ) {
		$html = '';

		if ( ! empty( $settings['show_badge'] ) ) {
			$terms = dpce_get_post_terms_for_display( $post_id, isset( $settings['badge_taxonomy'] ) ? $settings['badge_taxonomy'] : 'auto', 1 );
			foreach ( $terms as $term ) {
				$html .= '<span class="dpce-badge dpce-badge--term">' . esc_html( $term->name ) . '</span>';
			}
		}

		if ( ! empty( $settings['show_sale_badge'] ) ) {
			$html .= DPCE_Woo::sale_badge( $post_id );
		}

		if ( '' === $html ) {
			return '';
		}

		/**
		 * Filter the badges printed on a carousel image.
		 *
		 * @since 2.0.0
		 *
		 * @param string $html     Badge HTML.
		 * @param int    $post_id  Post ID.
		 * @param array  $settings Carousel settings.
		 */
		return (string) apply_filters( 'dpce_badges_html', '<div class="dpce-badges">' . $html . '</div>', $post_id, $settings );
	}

	/**
	 * Meta row (date, author, terms, comments, reading time).
	 *
	 * Printed right after the title when the Meta Row setting is on.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_meta_row( $post_id, $settings ) {
		if ( empty( $settings['show_meta'] ) ) {
			return;
		}

		$items = isset( $settings['meta_items'] ) && is_array( $settings['meta_items'] ) ? $settings['meta_items'] : array( 'date', 'author' );
		$parts = array();

		foreach ( $items as $item ) {
			switch ( $item ) {
				case 'date':
					$parts[] = sprintf(
						'<span class="dpce-meta__item dpce-meta__date"><time datetime="%1$s">%2$s</time></span>',
						esc_attr( (string) get_the_date( 'c', $post_id ) ),
						esc_html( (string) get_the_date( '', $post_id ) )
					);
					break;

				case 'author':
				case 'avatar':
					if ( 'avatar' === $item && in_array( 'author', $items, true ) ) {
						break;
					}
					$author_id = (int) get_post_field( 'post_author', $post_id );
					$avatar    = in_array( 'avatar', $items, true ) ? get_avatar( $author_id, 24, '', '', array( 'class' => 'dpce-meta__avatar' ) ) : '';
					$parts[]   = '<span class="dpce-meta__item dpce-meta__author">' . wp_kses_post( (string) $avatar ) . esc_html( get_the_author_meta( 'display_name', $author_id ) ) . '</span>';
					break;

				case 'terms':
					$terms = dpce_get_post_terms_for_display( $post_id, isset( $settings['badge_taxonomy'] ) ? $settings['badge_taxonomy'] : 'auto', 2 );
					if ( $terms ) {
						$parts[] = '<span class="dpce-meta__item dpce-meta__terms">' . esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) ) . '</span>';
					}
					break;

				case 'comments':
					$count   = (int) get_comments_number( $post_id );
					$parts[] = '<span class="dpce-meta__item dpce-meta__comments">' . esc_html(
						sprintf(
							/* translators: %s: number of comments. */
							_n( '%s comment', '%s comments', $count, 'webcodingplace-post-carousel-for-elementor' ),
							number_format_i18n( $count )
						)
					) . '</span>';
					break;

				case 'reading_time':
					$minutes = dpce_get_reading_time( $post_id );
					$parts[] = '<span class="dpce-meta__item dpce-meta__reading">' . esc_html(
						sprintf(
							/* translators: %s: number of minutes. */
							_n( '%s min read', '%s min read', $minutes, 'webcodingplace-post-carousel-for-elementor' ),
							number_format_i18n( $minutes )
						)
					) . '</span>';
					break;
			}
		}

		if ( ! $parts ) {
			return;
		}

		/**
		 * Filter the meta row items before they are printed.
		 *
		 * @since 2.0.0
		 *
		 * @param string[] $parts    HTML of each item (already escaped).
		 * @param int      $post_id  Post ID.
		 * @param array    $settings Carousel settings.
		 */
		$parts = (array) apply_filters( 'dpce_meta_row_items', $parts, $post_id, $settings );

		echo '<div class="dpce-meta">' . implode( '', array_map( 'wp_kses_post', $parts ) ) . '</div>';
	}

	/**
	 * WooCommerce block (rating, price, stock, add to cart) for templates
	 * other than Card, when "Show Product Details" is on.
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $settings Carousel settings.
	 */
	public function render_product_extras( $post_id, $settings ) {
		if ( empty( $settings['show_product_extras'] ) || ! DPCE_Woo::product( $post_id ) ) {
			return;
		}

		// Templates 48 to 51 already print the price.
		$has_price = isset( $settings['style_id'] ) && in_array( (string) $settings['style_id'], array( '48', '49', '50', '51' ), true );

		echo '<div class="dpce-product-extras">';
		echo DPCE_Woo::rating( $post_id ) . ( $has_price ? '' : DPCE_Woo::price( $post_id ) ) . DPCE_Woo::stock_label( $post_id ) . DPCE_Woo::add_to_cart( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Each part is escaped in DPCE_Woo.
		echo '</div>';
	}
}
