<?php
/**
 * Template: Style 18.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-18 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="icons">
    <?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?> 
  </div>
  <div class="dpce-body dpce-bg">
    <?php dpce_render_title( $post_id, $carousel_settings, array( 'inner_wrap' => 'span' ) ); ?>
    <p class="dpce-desc">
      <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
  </div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>