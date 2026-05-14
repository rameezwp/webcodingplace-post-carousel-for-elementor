<?php
/**
 * Template: Style 22.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-22 dpce-wrapper">
    <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <div class="dpce-body">
        <div class="image">
            <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
        </div>
        <h2 class="dpce-title"><?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?></h2>
    </div>
    <?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>