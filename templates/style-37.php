<?php
/**
 * Template: Style 37.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-37 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <span class="dpce-title">
    <?php do_action( 'dpce_carousel_icon', $post_id, $carousel_settings ); ?>
    <?php dpce_render_title( $post_id, $carousel_settings ); ?>
  </span>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>