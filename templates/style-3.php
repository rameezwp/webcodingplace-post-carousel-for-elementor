<?php
/**
 * Template: Style 3 — Side Meta (image left, content right).
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-3 dpce-wrapper">
	<div class="dpce-side-image">
		<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	</div>
	<div class="dpce-side-content">
		<div class="dpce-meta">
			<?php do_action( 'dpce_carousel_meta', $post_id, $carousel_settings ); ?>
		</div>
		<h3 class="dpce-title">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"
				target="<?php echo esc_attr( $carousel_settings['read_more_target'] ); ?>">
				<?php do_action( 'dpce_carousel_title', $post_id, $carousel_settings ); ?>
			</a>
		</h3>
		<div class="dpce-desc">
			<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
		</div>
		<?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
		<?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?>
	</div>
</article>
