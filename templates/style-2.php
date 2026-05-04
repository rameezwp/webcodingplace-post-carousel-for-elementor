<?php
/**
 * Template: Style 2 — Below Image (Classic Card).
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="dpce-style-2 dpce-wrapper">
	<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
	<div class="dpce-body">
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
