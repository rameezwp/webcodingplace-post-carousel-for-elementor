<?php
/**
 * Template: Style 21.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-21 dpce-wrapper">
  <div class="dpce-body">
    <h2 class="dpce-title">
      <?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>
    </h2>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
    <div class="icons">
      <?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?> 
    </div>
  </div>
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="position dpce-date">
    <?php printf( _x( '%s ago', '%s = human-readable time difference', 'webcodingplace-post-carousel-for-elementor' ), human_time_diff( get_the_time( 'U' ), time() ) ); ?>
  </div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>