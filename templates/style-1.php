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

<article class="dpce-style-1 dpce-wrapper">
  <?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <div class="date">
  	<span class="day"><?php echo get_the_date( 'd' ); ?></span>
  	<span class="month"><?php echo get_the_date( 'M' ); ?></span>
  </div>
  <i class="fa fa-link"></i>
  <div class="dpce-body">
    <h3 class="dpce-title"><?php do_action( 'dpce_carousel_title', $post_id, $carousel_settings ); ?></h3>
    <p class="dpce-desc">
		<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
    </p>
  </div>
  <a class="dpce-overlay-link"
  	target="<?php echo esc_attr( $carousel_settings['read_more_target'] ); ?>"
  	href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"
  	aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"></a>
</article>
