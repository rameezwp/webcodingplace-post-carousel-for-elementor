<?php
/**
 * Template: Style 50.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-50 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="add-to-cart">
  	<?php do_action( 'dpce_carousel_icon', $post_id, $carousel_settings ); ?>
  	<span></span>
  </div>
  <div class="dpce-body dpce-bg">
    <?php dpce_render_title( $post_id, $carousel_settings ); ?>
    <p class="dpce-desc"><?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?></p>
    <div class="dpce-price">
      <?php
        if (function_exists('wc_get_product')) {
          $dpce_ = wc_get_product($post_id);
          echo ($dpce_) ? wp_kses_post($dpce_->get_price_html()) : '' ;
        }
      ?>
    </div>
  </div>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>