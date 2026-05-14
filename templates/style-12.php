<?php
/**
 * Template: Style 12.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-12 dpce-wrapper">
  <div class="image">
    <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  </div>
  <div class="dpce-body">
    <div class="date rpc_date">
      <span class="day"><?php echo get_the_date( 'd' ); ?></span>
      <span class="month"><?php echo get_the_date( 'M' ); ?></span></div>
    <h3 class="dpce-title">
    <?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>
    </h3>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
  </div>
  <footer>
    <div class="views"><i class="fa fa-comments"></i>
        <?php
          $comments = wp_count_comments(get_the_id());
          echo esc_attr( $comments->total_comments );
        ?>
    </div>
    <div class="love"><i class="fa fa-user"></i><?php echo get_the_author(); ?></div>
  </footer>
    <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>