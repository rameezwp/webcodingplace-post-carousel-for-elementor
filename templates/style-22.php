<figure class="rpc-style-22 rpc-wrapper">
    <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
    <figcaption>
        <div class="image">
            <?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
        </div>
        <h2 class="rpc_title"><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></h2>
    </figcaption>
    <a target="<?php echo esc_attr($carousel_settings['read_more_target']); ?>" href="<?php the_permalink(); ?>"></a>
</figure>