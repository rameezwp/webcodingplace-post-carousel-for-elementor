<?php
/**
 * Template: Style 1 — Overlay Card.
 *
 * Available variables:
 *  - $post_id           int    The current post ID.
 *  - $carousel_settings array  Settings forwarded by the widget.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<figure class="dpce-style-1 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<i class="dpce-icon dashicons dashicons-admin-links" aria-hidden="true"></i>
	<figcaption class="dpce-caption">
		<h3 class="dpce-title"><?php do_action( 'dpce_carousel_title', $post_id, $carousel_settings ); ?></h3>
		<div class="dpce-desc">
			<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
		</div>
		<?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
		<?php do_action( 'dpce_carousel_share', $post_id, $carousel_settings ); ?>
	</figcaption>
	<a class="dpce-overlay-link"
		target="<?php echo esc_attr( $carousel_settings['read_more_target'] ); ?>"
		href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"
		aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"></a>
</figure>
