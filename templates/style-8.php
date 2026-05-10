<figure class="rpc-style-8 rpc-wrapper">
  <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <figcaption class="rpc_bg">
    <h3 class="rpc_title">
    	<?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?>
    </h3>
    <p class="rpc_desc">
    	<?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
        <a class="read-more <?php echo esc_attr($carousel_settings['read_more_classes']); ?>" target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>">
    <?php echo esc_attr($carousel_settings['read_more_txt']); ?></a>
  </figcaption>
</figure>