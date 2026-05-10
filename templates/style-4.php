<figure class="rpc-style-4 rpc-wrapper">
  <figcaption>
  	<?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <h3 class="rpc_title">
    	<?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?>
    </h3>
    <p class="rpc_desc">
    	<?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
      <?php  if (isset($carousel_settings['read_more_txt']) && $carousel_settings['read_more_txt'] != '') { ?>
        <button class="button <?php echo esc_attr($carousel_settings['read_more_classes']); ?>"><?php echo esc_attr($carousel_settings['read_more_txt']); ?></button>
      <?php } ?>    
  </figcaption>
</figure>