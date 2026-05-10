<figure class="rpc-style-30 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <figcaption>
    <h3 class="rpc_title">
    	<?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?>
    </h3>
    <p class="rpc_desc">
    	<?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
  </figcaption>
  <i class="fa fa-plus"></i>
  <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
</figure>