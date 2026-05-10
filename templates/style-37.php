<figure class="rpc-style-37 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <span class="rpc_title"><i class="fa fa-share"></i><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></span>
  <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
</figure>