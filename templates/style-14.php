<figure class="rpc-style-14 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <figcaption class="rpc_bg">
    <h3 class="rpc_title"><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></h3>
    <h5 class="rpc_desc"><?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?></h5>
  </figcaption>
  <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
</figure>