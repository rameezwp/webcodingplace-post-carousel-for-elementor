<figure class="rpc-style-51 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="price rpc_price">
      <?php
        if (function_exists('wc_get_product')) {
          $product = wc_get_product($post_id);
          echo ($product) ? $product->get_price_html() : '' ;
        }
      ?>  	
  </div>
  <figcaption class="rpc_bg">
    <h3 class="rpc_title"><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></h3>
    <p class="rpc_desc"><?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?></p>
    <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
  </figcaption>
</figure>