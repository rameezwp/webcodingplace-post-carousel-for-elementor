<?php
/**
 * Template: Style 20.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-20 dpce-wrapper">
	<div class="dpce-body">
	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
	<p class="dpce-desc">
		<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
	</p>
	<div class="icons">
		<?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?> 
	</div>
	</div>
	<div class="image">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	</div>
	<div class="position dpce-date">
	<?php
	printf(
		/* translators: %s: Human-readable time difference. */
		esc_html_x(
			'%s ago',
			'%s = human-readable time difference',
			'webcodingplace-post-carousel-for-elementor'
		),
		esc_html(
			human_time_diff(
				get_the_time( 'U' ),
				current_time( 'timestamp' )
			)
		)
	);
	?>
	</div>
	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>