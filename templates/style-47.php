<div class="rpc-style-47 rpc-wrapper">
    <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>">
    	<h3 class="rpc_title">
    		<?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?>
    	</h3>
    </a>
    <p class="rpc_desc">
    	<?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    </p>
</div>