<?php
/**
 * WooCommerce helpers: price, rating, sale and stock labels, add to cart.
 *
 * Every function returns an empty string when WooCommerce is not active or
 * the post is not a product, so templates can call them safely.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Woo
 */
class DPCE_Woo {

	/**
	 * Get the product object for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return WC_Product|null
	 */
	public static function product( $post_id ) {
		if ( ! function_exists( 'wc_get_product' ) || 'product' !== get_post_type( $post_id ) ) {
			return null;
		}
		$product = wc_get_product( $post_id );
		return $product instanceof WC_Product ? $product : null;
	}

	/**
	 * Price HTML (with the regular price struck through when on sale).
	 *
	 * @param int $post_id Post ID.
	 * @return string Safe HTML.
	 */
	public static function price( $post_id ) {
		$product = self::product( $post_id );
		if ( ! $product ) {
			return '';
		}
		$html = $product->get_price_html();
		return '' === $html ? '' : '<div class="dpce-price">' . wp_kses_post( $html ) . '</div>';
	}

	/**
	 * Star rating. Empty for products without a rating.
	 *
	 * @param int $post_id Post ID.
	 * @return string Safe HTML.
	 */
	public static function rating( $post_id ) {
		$product = self::product( $post_id );
		if ( ! $product || ! function_exists( 'wc_review_ratings_enabled' ) || ! wc_review_ratings_enabled() ) {
			return '';
		}

		$average = (float) $product->get_average_rating();
		if ( $average <= 0 ) {
			return '';
		}

		// Drawn with CSS so it does not depend on WooCommerce's star font.
		return sprintf(
			'<div class="dpce-rating"><span class="dpce-stars" style="--dpce-rating: %1$s%%" role="img" aria-label="%2$s">&#9733;&#9733;&#9733;&#9733;&#9733;</span></div>',
			esc_attr( (string) round( $average / 5 * 100, 1 ) ),
			esc_attr(
				sprintf(
					/* translators: %s: average rating, for example 4.5. */
					__( 'Rated %s out of 5', 'webcodingplace-post-carousel-for-elementor' ),
					number_format_i18n( $average, 1 )
				)
			)
		);
	}

	/**
	 * "Sale" badge for products on sale.
	 *
	 * @param int $post_id Post ID.
	 * @return string Safe HTML.
	 */
	public static function sale_badge( $post_id ) {
		$product = self::product( $post_id );
		if ( ! $product || ! $product->is_on_sale() ) {
			return '';
		}
		return '<span class="dpce-badge dpce-badge--sale">' . esc_html__( 'Sale', 'webcodingplace-post-carousel-for-elementor' ) . '</span>';
	}

	/**
	 * "Out of stock" label.
	 *
	 * @param int $post_id Post ID.
	 * @return string Safe HTML.
	 */
	public static function stock_label( $post_id ) {
		$product = self::product( $post_id );
		if ( ! $product || $product->is_in_stock() ) {
			return '';
		}
		return '<span class="dpce-stock dpce-stock--out">' . esc_html__( 'Out of stock', 'webcodingplace-post-carousel-for-elementor' ) . '</span>';
	}

	/**
	 * Attributes for an add to cart link.
	 *
	 * Simple products are added with AJAX (WooCommerce's own script);
	 * other product types link to the product page ("Select options").
	 *
	 * @param WC_Product $product Product.
	 * @return array<string, string>
	 */
	public static function add_to_cart_attributes( $product ) {
		$ajax = $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock();

		$classes = array( 'dpce-button', 'dpce-add-to-cart', 'add_to_cart_button', 'product_type_' . $product->get_type() );
		if ( $ajax ) {
			$classes[] = 'ajax_add_to_cart';
		}

		return array(
			'href'             => $product->add_to_cart_url(),
			'data-quantity'    => '1',
			'data-product_id'  => (string) $product->get_id(),
			'data-product_sku' => (string) $product->get_sku(),
			'class'            => implode( ' ', $classes ),
			'aria-label'       => wp_strip_all_tags( $product->add_to_cart_description() ),
			'rel'              => 'nofollow',
		);
	}

	/**
	 * Add to cart button.
	 *
	 * @param int $post_id Post ID.
	 * @return string Safe HTML.
	 */
	public static function add_to_cart( $post_id ) {
		$product = self::product( $post_id );
		if ( ! $product ) {
			return '';
		}

		self::enqueue_cart_script();

		$html = '<a';
		foreach ( self::add_to_cart_attributes( $product ) as $name => $value ) {
			$html .= ' ' . $name . '="' . ( 'href' === $name ? esc_url( $value ) : esc_attr( $value ) ) . '"';
		}
		$html .= '>' . esc_html( $product->add_to_cart_text() ) . '</a>';

		return $html;
	}

	/**
	 * Load WooCommerce's AJAX add to cart script on this page.
	 */
	public static function enqueue_cart_script() {
		if ( wp_script_is( 'wc-add-to-cart', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart' );
		}
	}
}
