<figure class="rpc-style-10 rpc-wrapper">
  <div class="image">
    <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <i class="fa fa-link"></i>
    <div class="date">
      <span class="day"><?php echo get_the_date( 'd' ); ?></span>
      <span class="month"><?php echo get_the_date( 'M' ); ?></span>
    </div>
  </div>
  <figcaption class="rpc_bg">
    <h3 class="rpc_title"><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></h3>
    <p class="rpc_desc">
      <?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <a class="read-more <?php echo esc_attr($carousel_settings['read_more_classes']); ?>" target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>">
    <?php echo esc_attr($carousel_settings['read_more_txt']); ?></a>
  </figcaption>
</figure>