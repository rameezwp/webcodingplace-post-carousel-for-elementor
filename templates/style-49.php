<?php
/**
 * Template: Style 49.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-49 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<div class="dpce-body dpce-bg">
	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
	<p class="dpce-desc"><?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?></p>
	<div class="price dpce-price">
		<?php
		if ( function_exists( 'wc_get_product' ) ) {
			$dpce_product = wc_get_product( $post_id );
			echo ( $dpce_product ) ? wp_kses_post( $dpce_product->get_price_html() ) : '';
		}
		?>
	</div>
	</div>
	<?php
	list( $dpce_cart_open, $dpce_cart_close ) = dpce_cart_tags(
		$post_id,
		'span',
		array(
			'class'           => 'ajax_add_to_cart add_to_cart_button',
			'data-product_id' => (string) intval( $post_id ),
		)
	);
	?>
	<?php echo $dpce_cart_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in dpce_cart_tags(). ?>
	<?php do_action( 'dpce_carousel_icon', $post_id, $carousel_settings ); ?>
	<?php echo $dpce_cart_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static tag. ?>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>