<?php
/**
 * Template: Style 41.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-41 dpce-wrapper">
  <div class="image">
  	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <div class="icons">
    	<?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?> 
    </div>
  </div>
  <div class="dpce-body">
    <h3 class="dpce-title">
      <?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>
    </h3>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
  </div>
  <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>