<?php
/**
 * Template: Style 23.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-23 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="dpce-body">
    <h3 class="dpce-title">
    <?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>
    <span class="dpce-desc"><?php do_action( 'dpce_carousel_desc', $post_id,  $carousel_settings ); ?></span></h3>
  </div>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>