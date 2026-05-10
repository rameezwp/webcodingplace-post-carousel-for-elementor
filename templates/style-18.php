<figure class="rpc-style-18 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="icons">
    <?php do_action( 'rpc_social_share_icons', $post_id, $carousel_settings ); ?> 
  </div>
  <figcaption class="rpc_bg">
    <h3 class="rpc_title"><span><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></span></h3>
    <p class="rpc_desc">
      <?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
  </figcaption>
</figure>