<?php
/**
 * Template: Style 48.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-48 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="dpce-body">
    <?php dpce_render_title( $post_id, $carousel_settings ); ?>
    <p class="dpce-desc"><?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?></p>
    <div class="price dpce-price">
      <?php
        if (function_exists('wc_get_product')) {
          $product = wc_get_product($post_id);
          echo ($product) ? $product->get_price_html() : '' ;
        }
      ?>
    </div>
  </div>
  <?php echo dpce_icon( 'cart-plus' ); ?>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>