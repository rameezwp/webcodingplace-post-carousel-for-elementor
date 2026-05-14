<?php
/**
 * Template: Style 2.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-2 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<div class="date dpce-date">
		<span class="day"><?php echo get_the_date( 'd' ); ?></span>
		<span class="month"><?php echo get_the_date( 'M' ); ?></span>
	</div>
	<div class="dpce-body">
		<?php dpce_render_title( $post_id, $carousel_settings ); ?>
		<div class="dpce-desc">
			<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
		</div>
	</div>
	<div class="hover"><?php echo wp_kses_post( dpce_icon( 'link' ) ); ?></div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>
