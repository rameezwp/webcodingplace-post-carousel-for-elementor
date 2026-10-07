<?php
/**
 * Demo content for the WordPress Playground live preview.
 *
 * This is the source of the "runPHP" step in
 * .wordpress-org/blueprints/blueprint.json. After editing it, run
 * `node bin/build-blueprint.js` to rebuild the blueprint.
 *
 * The blueprint has already downloaded the six demo images to
 * /tmp/dpce-demo-{1..6}.jpg. This script creates six posts with those
 * images and a front page with three carousels and a grid.
 *
 * @package DPCE
 */

require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Run as the admin: Elementor checks permissions when site options change.
wp_set_current_user( 1 );

// Skip Elementor's own onboarding screen.
update_option( 'elementor_onboarded', 1 );
delete_transient( 'elementor_activation_redirect' );

$dpce_posts = array(
	array( 'A Slow Weekend in Lisbon', 'Travel', 'Tiled streets, warm bakeries and the best viewpoints to watch the sun go down over the river.' ),
	array( '10 Easy Weeknight Dinners', 'Food', 'Simple recipes that come together in thirty minutes, with ingredients you probably already have.' ),
	array( 'How We Redesigned Our Studio', 'Design', 'Soft colors, better light and a layout that finally makes room for focused work.' ),
	array( 'Beginner Guide to Trail Running', 'Outdoors', 'What to wear, how to pace yourself and why the first mile always feels the hardest.' ),
	array( 'Plants That Love Low Light', 'Home', 'Seven easy going plants that stay green in north facing rooms and busy lives.' ),
	array( 'Notes From a Small Coffee Roaster', 'Stories', 'We spent a morning with a two person team roasting beans in an old garage.' ),
);

// The default "Hello world!" post has no image; hide it from the demo.
wp_trash_post( 1 );

$dpce_upload = wp_upload_dir();
foreach ( $dpce_posts as $dpce_i => $dpce_post ) {
	$dpce_term = term_exists( $dpce_post[1], 'category' );
	if ( ! $dpce_term ) {
		$dpce_term = wp_insert_term( $dpce_post[1], 'category' );
	}

	$dpce_id = wp_insert_post(
		array(
			'post_title'    => $dpce_post[0],
			'post_excerpt'  => $dpce_post[2],
			'post_content'  => str_repeat( '<!-- wp:paragraph --><p>' . $dpce_post[2] . '</p><!-- /wp:paragraph -->', 6 ),
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_date'     => gmdate( 'Y-m-d H:i:s', time() - ( $dpce_i + 1 ) * DAY_IN_SECONDS ),
			'post_category' => array( (int) $dpce_term['term_id'] ),
		)
	);

	$dpce_source = '/tmp/dpce-demo-' . ( $dpce_i + 1 ) . '.jpg';
	if ( file_exists( $dpce_source ) ) {
		$dpce_file = $dpce_upload['path'] . '/dpce-demo-' . ( $dpce_i + 1 ) . '.jpg';
		copy( $dpce_source, $dpce_file );
		$dpce_att = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => $dpce_post[0],
				'post_status'    => 'inherit',
			),
			$dpce_file,
			$dpce_id
		);
		wp_update_attachment_metadata( $dpce_att, wp_generate_attachment_metadata( $dpce_att, $dpce_file ) );
		set_post_thumbnail( $dpce_id, $dpce_att );
	}
}

/**
 * One full width section with a heading and a widget.
 *
 * @param string $key      Unique key for element IDs.
 * @param string $title    Heading text.
 * @param array  $widget   Widget type and settings.
 * @param string $bg       Background color.
 * @return array Elementor section.
 */
function dpce_demo_section( $key, $title, $widget, $bg ) {
	$id = substr( md5( $key ), 0, 7 );
	return array(
		'id'       => $id . 's',
		'elType'   => 'section',
		'settings' => array(
			'background_background' => 'classic',
			'background_color'      => $bg,
			'padding'               => array(
				'unit'     => 'px',
				'top'      => '50',
				'right'    => '20',
				'bottom'   => '60',
				'left'     => '20',
				'isLinked' => false,
			),
		),
		'elements' => array(
			array(
				'id'       => $id . 'c',
				'elType'   => 'column',
				'settings' => array( '_column_size' => 100 ),
				'elements' => array(
					array(
						'id'         => $id . 'h',
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'       => $title,
							'header_size' => 'h2',
							'align'       => 'center',
							'_margin'     => array(
								'unit'     => 'px',
								'top'      => '0',
								'right'    => '0',
								'bottom'   => '24',
								'left'     => '0',
								'isLinked' => false,
							),
						),
						'elements'   => array(),
					),
					array(
						'id'         => $id . 'w',
						'elType'     => 'widget',
						'widgetType' => $widget[0],
						'settings'   => $widget[1],
						'elements'   => array(),
					),
				),
			),
		),
	);
}

$dpce_page = wp_insert_post(
	array(
		'post_type'   => 'page',
		'post_title'  => 'Post Carousel demo',
		'post_name'   => 'post-carousel-demo',
		'post_status' => 'publish',
	)
);

$dpce_modern = array(
	'slider_engine'  => 'swiper',
	'pause_button'   => 'yes',
	'posts_per_page' => 6,
);

$dpce_intro = sprintf(
	'<p style="text-align:center">These are your demo posts in the Post Carousel widget. <a href="%s">Edit this page with Elementor</a> and click any carousel to try the settings.</p>',
	esc_url( admin_url( 'post.php?post=' . $dpce_page . '&action=elementor' ) )
);

$dpce_data = array(
	dpce_demo_section(
		'intro',
		'Post Carousel & Grid for Elementor',
		array( 'text-editor', array( 'editor' => $dpce_intro ) ),
		'#ffffff'
	),
	dpce_demo_section(
		'card',
		'Card template with autoplay',
		array(
			'dpce_post_carousel',
			$dpce_modern + array(
				'style_id'    => 'card',
				'autoplay'    => 'yes',
				'meta_items'  => array( 'date', 'reading_time' ),
				'image_hover' => 'zoom',
			),
		),
		'#f3f4f6'
	),
	dpce_demo_section(
		'style21',
		'Template 21 with a progress bar',
		array(
			'dpce_post_carousel',
			$dpce_modern + array(
				'style_id'        => '21',
				'pagination_type' => 'progress',
			),
		),
		'#ffffff'
	),
	dpce_demo_section(
		'grid',
		'Grid layout, template 5',
		array(
			'dpce_post_carousel',
			$dpce_modern + array(
				'style_id' => '5',
				'layout'   => 'grid',
			),
		),
		'#f3f4f6'
	),
	dpce_demo_section(
		'ticker',
		'News ticker',
		array(
			'dpce_post_carousel',
			$dpce_modern + array(
				'style_id'     => 'card',
				'ticker'            => 'yes',
				'autoplay'          => 'yes',
				'dots'              => '',
				'arrows'            => '',
				'cols_desktop'      => 4,
				'card_show_excerpt' => '',
				'card_show_button'  => '',
			),
		),
		'#ffffff'
	),
);

update_post_meta( $dpce_page, '_wp_page_template', 'elementor_header_footer' );
update_post_meta( $dpce_page, '_elementor_edit_mode', 'builder' );
update_post_meta( $dpce_page, '_elementor_template_type', 'wp-page' );
update_post_meta( $dpce_page, '_elementor_version', ELEMENTOR_VERSION );
update_post_meta( $dpce_page, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
update_post_meta( $dpce_page, '_elementor_data', wp_slash( wp_json_encode( $dpce_data ) ) );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $dpce_page );
update_option( 'blogname', 'Post Carousel demo' );
