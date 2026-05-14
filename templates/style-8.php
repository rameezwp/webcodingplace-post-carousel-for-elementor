<?php
/**
 * Template: Style 8.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-8 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="dpce-body">
    <h3 class="dpce-title">
    	<?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>
    </h3>
    <p class="dpce-desc">
    	<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
        <?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
  </div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>