<?php
/**
 * Template: Style 44.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-44 dpce-wrapper">
    <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <div>
        <h3 class="dpce-title">
        	<?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>   
        </h3>
        <p class="dpce-desc">
        	<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
        </p>
        <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
    </div>          
</article>