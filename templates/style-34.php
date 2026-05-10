<figure class="rpc-style-34 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <figcaption>
    <p class="rpc_desc">
    	<?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <div class="plus1"></div>
    <div class="plus2"></div>
  </figcaption>
  <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
</figure>