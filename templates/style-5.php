<figure class="rpc-style-5 rpc-wrapper">
  <div class="image">
    <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  </div>
  <figcaption class="rpc_bg">
    <h5 class="rpc_title">
      <?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?>
    </h5>
    <h3 class="rpc_desc">
      <?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </h3>
    <footer class="rpc_footer">
      <div class="date"><?php echo get_the_date(); ?></div>
      <div class="icons">
        <div class="views">
          <i class="fa fa-comments"></i>
        <?php
          $comments = wp_count_comments(get_the_id());
          echo esc_attr( $comments->total_comments );
        ?>
        </div>
      </div>
    </footer>
  </figcaption>
  <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
</figure>