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
    <?php echo dpce_icon( 'link' ); ?>
    <div class="date">
      <span class="day"><?php echo get_the_date( 'd' ); ?></span>
      <span class="month"><?php echo get_the_date( 'M' ); ?></span>
    </div>
  </div>
  <div class="dpce-body">
    <h3 class="dpce-title"><?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?></h3>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
  </div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>