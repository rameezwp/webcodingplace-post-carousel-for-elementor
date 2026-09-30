<?php
/**
 * Layout (carousel, grid, list) and modern engine options (added in 2.0).
 *
 * Defaults match 1.4: a carousel, sliding one set of slides at a time.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/**
 * Trait DPCE_Layout_Controls
 */
trait DPCE_Layout_Controls {

	/**
	 * Layout control, printed at the top of the Slider section.
	 */
	protected function register_layout_controls() {
		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'webcodingplace-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => array(
					'carousel' => esc_html__( 'Carousel', 'webcodingplace-post-carousel-for-elementor' ),
					'grid'     => esc_html__( 'Grid', 'webcodingplace-post-carousel-for-elementor' ),
					'list'     => esc_html__( 'List', 'webcodingplace-post-carousel-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'grid_columns',
			array(
				'label'          => esc_html__( 'Columns', 'webcodingplace-post-carousel-for-elementor' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => array(
					'{{WRAPPER}} .dpce-layout-grid .dpce-track' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
				'condition'      => array( 'layout' => 'grid' ),
			)
		);

		$this->add_responsive_control(
			'grid_row_gap',
			array(
				'label'      => esc_html__( 'Space Between Rows (px)', 'webcodingplace-post-carousel-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dpce-layout-grid .dpce-track, {{WRAPPER}} .dpce-layout-list .dpce-track' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'layout!' => 'carousel' ),
			)
		);
	}

	/**
	 * Modern engine options, printed at the end of the Slider section.
	 */
	protected function register_swiper_extra_controls() {
		$modern = array(
			'layout'        => 'carousel',
			'slider_engine' => 'swiper',
		);

		$this->add_control(
			'swiper_extras_heading',
			array(
				'label'     => esc_html__( 'Modern Engine Options', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $modern,
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'     => esc_html__( 'Effect', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'options'   => array(
					'slide'     => esc_html__( 'Slide', 'webcodingplace-post-carousel-for-elementor' ),
					'fade'      => esc_html__( 'Fade (one slide at a time)', 'webcodingplace-post-carousel-for-elementor' ),
					'coverflow' => esc_html__( 'Coverflow', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'condition' => $modern + array( 'ticker!' => 'yes' ),
			)
		);

		$this->add_control(
			'centered_slides',
			array(
				'label'        => esc_html__( 'Center the Active Slide', 'webcodingplace-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => $modern + array(
					'effect'  => 'slide',
					'ticker!' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => esc_html__( 'Pagination', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dots',
				'options'   => array(
					'dots'     => esc_html__( 'Dots', 'webcodingplace-post-carousel-for-elementor' ),
					'fraction' => esc_html__( 'Numbers (2 / 8)', 'webcodingplace-post-carousel-for-elementor' ),
					'progress' => esc_html__( 'Progress bar', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'condition' => $modern + array(
					'dots'    => 'yes',
					'ticker!' => 'yes',
				),
			)
		);

		$this->add_control(
			'ticker',
			array(
				'label'        => esc_html__( 'News Ticker (Continuous Scroll)', 'webcodingplace-post-carousel-for-elementor' ),
				'description'  => esc_html__( 'Slides move without stopping, like a news ticker. Pauses on hover and keyboard focus.', 'webcodingplace-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => $modern,
			)
		);

		$this->add_control(
			'ticker_speed',
			array(
				'label'     => esc_html__( 'Ticker Speed (seconds per slide)', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 60,
				'step'      => 0.5,
				'condition' => $modern + array( 'ticker' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_prev_icon',
			array(
				'label'       => esc_html__( 'Previous Arrow Icon', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => $modern + array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_next_icon',
			array(
				'label'       => esc_html__( 'Next Arrow Icon', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => $modern + array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'classic_engine_options_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Fade, coverflow, news ticker, numbered or progress bar pagination and custom arrow icons need the Modern slider engine.', 'webcodingplace-post-carousel-for-elementor' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array(
					'layout'        => 'carousel',
					'slider_engine' => 'slick',
				),
			)
		);
	}
}
