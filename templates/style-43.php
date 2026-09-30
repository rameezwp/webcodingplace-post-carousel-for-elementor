<?php
/**
 * Template: Style 43.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-43 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<div class="dpce-body">
		<div>
			<?php dpce_render_title( $post_id, $carousel_settings ); ?>
		</div>
		<div>
			<p class="dpce-desc dpce-bg">
				<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
			</p>
			<div class="curl"></div>
		</div>
		<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
	</div>           
</article>