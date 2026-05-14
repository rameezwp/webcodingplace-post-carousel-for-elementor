<?php
/**
 * Template: Style 10.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-10 dpce-wrapper">
  <div class="image">
    <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <?php echo wp_kses_post( dpce_icon( 'link' ) ); ?>
    <div class="date">
      <span class="day"><?php echo get_the_date( 'd' ); ?></span>
      <span class="month"><?php echo get_the_date( 'M' ); ?></span>
    </div>
  </div>
  <div class="dpce-body">
    <?php dpce_render_title( $post_id, $carousel_settings ); ?>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
  </div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>