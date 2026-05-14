<?php
/**
 * Template: Style 45.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="dpce-style-45 dpce-wrapper">
	<div class="dpce-post-image">
		<a href="<?php echo esc_url(get_permalink($post_id)); ?>" target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>">
			<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
		</a>

		<span class="dpce-comment-box">
			<span class="dpce-post-comment">
				<?php
					$comments = wp_count_comments(get_the_id());
					echo esc_attr( $comments->total_comments );
				?>
			</span>
		</span>
	</div>

	<div class="dpce-post-category">
		<?php
		$dpce_categories = get_the_category();

		if ( ! empty( $dpce_categories ) ) {

			$dpce_limit = 0;

			foreach ( $dpce_categories as $dpce_category ) {

				if ( $dpce_limit >= 3 ) {
					break;
				}
				?>
				<a href="<?php echo esc_url( get_category_link( $dpce_category->term_id ) ); ?>">
					<?php echo esc_html( $dpce_category->name ); ?>
				</a>
				<?php

				$dpce_limit++;
			}
		}
		?>
	</div>

	<h3 class="dpce-post-title">
		<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" class="dpce-title">
			<?php do_action( 'dpce_carousel_title', $post_id,  $carousel_settings ); ?>
		</a>
	</h3>
	<span class="dpce-post-meta">
		<?php echo wp_kses_post( dpce_icon( 'user' )); ?>
		<?php the_author_posts_link(); ?>
	</span>
	<span class="dpce-post-date dpce-date">
		<?php echo wp_kses_post( dpce_icon( 'clock' )); ?>
		<?php echo get_the_date() ?>
	</span>

	<div class="clearfix"></div>
	<div class="dpce-post-para dpce-content dpce-desc">
        <?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings); ?>
	</div>
</div>