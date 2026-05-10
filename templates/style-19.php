<figure class="rpc-style-19 rpc-wrapper">
	<?php do_action( 'rpc_carousel_thumbnail', $post_id, $carousel_settings ); ?>
  <figcaption>
    <p class="rpc_desc">
    	<span>
    		<?php do_action( 'rpc_carousel_desc', $post_id, $carousel_settings); ?>
    	</span>
    </p>
    <h2 class="rpc_title"><span><?php do_action( 'rpc_carousel_title', $post_id,  $carousel_settings ); ?></span></h2>
    <div class="icons">
    	<?php do_action( 'rpc_social_share_icons', $post_id, $carousel_settings ); ?> 
    </div>
  </figcaption>
</figure>