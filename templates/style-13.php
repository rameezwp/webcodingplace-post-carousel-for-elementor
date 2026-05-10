<figure class="rpc-style-13 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="icons">
    <?php do_action( 'rpc_social_share_icons', $post_id, $carousel_settings ); ?> 
  </div>
  <figcaption class="rpc_bg">
    <h3 class="rpc_title"><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></h3>
    <div class="price rpc_desc"><?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?></div>
  </figcaption>
</figure>