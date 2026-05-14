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
  <span class="dpce-title"><?php echo dpce_icon( 'share' ); ?><?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?></span>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>