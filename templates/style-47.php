<?php
/**
 * Template: Style 47.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="dpce-style-47 dpce-wrapper">
    <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>">
    	<?php dpce_render_title( $post_id, $carousel_settings ); ?>
    </a>
    <p class="dpce-desc">
    	<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
</div>