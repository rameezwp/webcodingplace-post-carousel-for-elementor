<?php
/**
 * Template: Style 6.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-6 dpce-wrapper">
  <div class="image">
    <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  </div>
  <div class="dpce-body">
    <div class="date dpce-date">
      <span class="day"><?php echo get_the_date( 'd' ); ?></span>
      <span class="month"><?php echo get_the_date( 'M' ); ?></span>
    </div>
    <?php dpce_render_title( $post_id, $carousel_settings ); ?>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <footer>
      <div class="views"><?php echo wp_kses_post( dpce_icon( 'comments' )); ?>
        <?php
          $comments = wp_count_comments(get_the_id());
          echo esc_attr( $comments->total_comments );
        ?>
      </div>
      <div class="love"><?php echo wp_kses_post( dpce_icon( 'user' )); ?>
        <?php echo get_the_author(); ?>
      </div>
    </footer>
  </div>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>