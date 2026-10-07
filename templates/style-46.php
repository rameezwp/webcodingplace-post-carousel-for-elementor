<?php
/**
 * Template: Style 46.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-46 dpce-wrapper dpce-bg">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<div class="dpce-body">
	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
	<p class="dpce-desc">
		<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
	</p>
	</div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>