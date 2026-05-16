<?php
/**
 * Template: Style 15.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-15 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="dpce-body">
    <div class="icon"><span>
      <?php do_action( 'dpce_carousel_icon', $post_id, $carousel_settings); ?>
    </span></div>
    <div class="caption">
      <?php dpce_render_title( $post_id, $carousel_settings ); ?>
    </div>
  </div>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>