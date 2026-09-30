<?php
/**
 * Template: Card (customizable).
 *
 * A clean card whose parts are switched on and off in the widget:
 * image with badges, meta row, title, excerpt, product rating and price,
 * and a button (read more, or add to cart for WooCommerce products).
 *
 * Available variables: $post_id, $carousel_settings.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dpce_card    = isset( $carousel_settings['card'] ) ? (array) $carousel_settings['card'] : array();
$dpce_show    = static function ( $key ) use ( $dpce_card ) {
	return ! isset( $dpce_card[ $key ] ) || ! empty( $dpce_card[ $key ] );
};
$dpce_product = DPCE_Woo::product( $post_id );
?>
<article class="dpce-style-card dpce-wrapper<?php echo $dpce_product ? ' dpce-card--product' : ''; ?>">
	<?php if ( $dpce_show( 'image' ) ) : ?>
		<div class="dpce-card__media">
			<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
		</div>
	<?php endif; ?>

	<div class="dpce-card__body dpce-bg">
		<?php dpce_render_title( $post_id, $carousel_settings ); ?>

		<?php if ( $dpce_product && $dpce_show( 'rating' ) ) : ?>
			<?php echo DPCE_Woo::rating( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in DPCE_Woo. ?>
		<?php endif; ?>

		<?php if ( $dpce_show( 'excerpt' ) ) : ?>
			<div class="dpce-desc"><?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?></div>
		<?php endif; ?>

		<?php if ( $dpce_product && $dpce_show( 'price' ) ) : ?>
			<?php echo DPCE_Woo::price( $post_id ) . DPCE_Woo::stock_label( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in DPCE_Woo. ?>
		<?php endif; ?>

		<?php if ( $dpce_show( 'button' ) ) : ?>
			<div class="dpce-card__actions">
				<?php
				if ( $dpce_product && $dpce_show( 'add_to_cart' ) ) {
					echo DPCE_Woo::add_to_cart( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in DPCE_Woo.
				} elseif ( isset( $carousel_settings['link_area'] ) && 'button' === $carousel_settings['link_area'] ) {
					do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings );
				} elseif ( ! empty( $carousel_settings['read_more_txt'] ) ) {
					// The whole card is the link, so the button is only a visual cue.
					echo '<span class="dpce-button" aria-hidden="true">' . esc_html( $carousel_settings['read_more_txt'] ) . '</span>';
				}
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'dpce_carousel_overlay', $post_id, $carousel_settings ); ?>
</article>
