<?php
/**
 * Template: Style 19.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-19 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="dpce-body">
    <p class="dpce-desc">
    	<span>
    		<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    	</span>
    </p>
    <h2 class="dpce-title"><span><?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?></span></h2>
    <div class="icons">
    	<?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?> 
    </div>
  </div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>