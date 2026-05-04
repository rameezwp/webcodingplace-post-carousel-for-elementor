<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Post Slider Widget.
 *
 * @since 1.0.0
 */
class Elementor_Post_Carousel_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'dps_post_slider';
	}

	public function get_title() {
		return esc_html__( 'Dynamic Post Carousel', 'dynamic-post-carousel-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_categories() {
		return [ 'general' ];
	}
    
    public function get_style_depends() {
        return [ 'slick-css', 'dps-main-css' ];
    }
    
    public function get_script_depends() {
		return [ 'slick-js', 'dps-main-js' ];
	}

	protected function register_controls() {

		// Content Tab: Query Section
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Post Settings', 'dynamic-post-carousel-for-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->end_controls_section();

		// Content Tab: Slider Settings
		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'dynamic-post-carousel-for-elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->end_controls_section();

	}

	protected function render() {
		
	}
}