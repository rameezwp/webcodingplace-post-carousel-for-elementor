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
	<div class="dpce-body dpce-bg">
	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
	<p class="dpce-desc">
		<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
	</p>
		<?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
	</div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>