<?php
/**
 * Template: Style 5.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-5 dpce-wrapper">
  <div class="image">
    <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  </div>
  <div class="dpce-body dpce-bg">
    <?php dpce_render_title( $post_id, $carousel_settings ); ?>
    <h3 class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </h3>
    <footer class="dpce-footer">
      <div class="dpce-post-date"><?php echo get_the_date(); ?></div>
      <div class="icons">
        <div class="views">
          <?php do_action( 'dpce_carousel_icon', $post_id, $carousel_settings, 'far fa-comments' ); ?>
          <span>
            <?php
              $comments = wp_count_comments(get_the_id());
              echo esc_attr( $comments->total_comments );
            ?>
          </span>
        </div>
      </div>
    </footer>
  </div>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>