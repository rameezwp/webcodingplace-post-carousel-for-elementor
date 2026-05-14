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
  <div class="dpce-body">
    <h3 class="dpce-title"><?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?></h3>
    <p class="dpce-desc"><?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?></p>
    <div class="price rpc_price">
      <?php
        if (function_exists('wc_get_product')) {
          $product = wc_get_product($post_id);
          echo ($product) ? $product->get_price_html() : '' ;
        }
      ?>
    </div>
  </div>
  <i class="fa fa-cart-plus ajax_add_to_cart add_to_cart_button" data-product_id="<?php echo intval($post_id); ?>"></i>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>