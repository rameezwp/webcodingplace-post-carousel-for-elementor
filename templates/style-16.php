<figure class="rpc-style-16 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <h3 class="rpc_title"><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></h3>
  <figcaption class="rpc_bg">
    <p class="rpc_desc">
      <?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <div class="icons">
      <?php do_action( 'rpc_social_share_icons', $post_id, $carousel_settings ); ?> 
    </div>
  </figcaption>
</figure>