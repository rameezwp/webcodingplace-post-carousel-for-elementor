<?php
/**
 * Template: Style 13.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-13 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<div class="icons">
	<?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?> 
	</div>
	<div class="dpce-body">
	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
	<div class="price dpce-desc">
		<p><?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?></p>
		<?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
		</div>
	</div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>