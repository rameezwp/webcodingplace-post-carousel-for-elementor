<?php
/**
 * Template: Style 3.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-3 dpce-wrapper">
	<div class="image">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	</div>
	<div class="dpce-body">
	<div class="date dpce-date">
		<span class="day"><?php echo get_the_date( 'd' ); ?></span>
		<span class="month"><?php echo get_the_date( 'M' ); ?></span>
	</div>
	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
	<p class="dpce-desc">
		<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
	</p>
	</div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>