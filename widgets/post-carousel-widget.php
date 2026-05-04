<?php
/**
 * Elementor widget: Dynamic Post Carousel.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Class DPCE_Post_Carousel_Widget
 */
class DPCE_Post_Carousel_Widget extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dpce_post_carousel';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dynamic Post Carousel', 'dynamic-post-carousel-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * Widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Search keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'carousel', 'slider', 'posts', 'slick', 'taxonomy', 'cpt' );
	}

	/**
	 * Style dependencies.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'dpce-slick', 'dpce-frontend', 'dpce-slick-theme' );
	}

	/**
	 * Script dependencies.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'dpce-slick', 'dpce-frontend' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->register_post_section();
		$this->register_slider_section();
		$this->register_appearance_section();
		$this->register_advanced_section();
	}

	/* =====================================================
	 * SECTION 1 - POST / CONTENT
	 * ===================================================== */
	private function register_post_section() {
		$this->start_controls_section(
			'section_post',
			array(
				'label' => esc_html__( 'Post / Content', 'dynamic-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'display_by',
			array(
				'label'   => esc_html__( 'Display By', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_type',
				'options' => array(
					'post_type' => esc_html__( 'Post Type', 'dynamic-post-carousel-for-elementor' ),
					'taxonomy'  => esc_html__( 'Taxonomy', 'dynamic-post-carousel-for-elementor' ),
				),
			)
		);

		// 2. Post type picker.
		$this->add_control(
			'post_type',
			array(
				'label'     => esc_html__( 'Choose Post Type', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post',
				'options'   => dpce_get_post_types(),
				'condition' => array( 'display_by' => 'post_type' ),
			)
		);

		// 4. Posts multiselect — one control per post type, conditionally shown.
		foreach ( dpce_get_post_types() as $pt_slug => $pt_label ) {
			$this->add_control(
				'posts__' . $pt_slug,
				array(
					/* translators: %s: post type label */
					'label'       => sprintf( esc_html__( 'Select %s', 'dynamic-post-carousel-for-elementor' ), $pt_label ),
					'description' => esc_html__( 'Leave empty to include all.', 'dynamic-post-carousel-for-elementor' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => dpce_get_posts_for_select( $pt_slug ),
					'condition'   => array(
						'display_by' => 'post_type',
						'post_type'  => $pt_slug,
					),
				)
			);
		}

		// 3. Taxonomy picker.
		$this->add_control(
			'taxonomy',
			array(
				'label'     => esc_html__( 'Choose Taxonomy', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => dpce_get_taxonomies(),
				'condition' => array( 'display_by' => 'taxonomy' ),
			)
		);

		// 5. Terms multiselect — one per taxonomy.
		foreach ( dpce_get_taxonomies() as $tax_slug => $tax_label ) {
			$this->add_control(
				'terms__' . $tax_slug,
				array(
					/* translators: %s: taxonomy label */
					'label'       => sprintf( esc_html__( 'Select %s Terms', 'dynamic-post-carousel-for-elementor' ), $tax_label ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => dpce_get_terms_for_select( $tax_slug ),
					'condition'   => array(
						'display_by' => 'taxonomy',
						'taxonomy'   => $tax_slug,
					),
				)
			);
		}

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'Number of Posts', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => -1,
				'max'     => 100,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'Date', 'dynamic-post-carousel-for-elementor' ),
					'title'      => esc_html__( 'Title', 'dynamic-post-carousel-for-elementor' ),
					'menu_order' => esc_html__( 'Menu Order', 'dynamic-post-carousel-for-elementor' ),
					'rand'       => esc_html__( 'Random', 'dynamic-post-carousel-for-elementor' ),
					'comment_count' => esc_html__( 'Comment Count', 'dynamic-post-carousel-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'Descending', 'dynamic-post-carousel-for-elementor' ),
					'ASC'  => esc_html__( 'Ascending', 'dynamic-post-carousel-for-elementor' ),
				),
			)
		);

		// 6. Exclude posts by ID.
		$this->add_control(
			'exclude_ids',
			array(
				'label'       => esc_html__( 'Exclude Post IDs', 'dynamic-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Comma-separated list of post IDs to exclude.', 'dynamic-post-carousel-for-elementor' ),
				'placeholder' => '12, 34, 56',
			)
		);

		// 7-8. Heading.
		$this->add_control(
			'heading_field',
			array(
				'label'   => esc_html__( 'Heading Source', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => dpce_get_field_choices(),
			)
		);

		$this->add_control(
			'heading_meta_key',
			array(
				'label'       => esc_html__( 'Heading Meta Key', 'dynamic-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'my_meta_key',
				'condition'   => array( 'heading_field' => 'meta' ),
			)
		);

		$this->add_control(
			'heading_max_words',
			array(
				'label'   => esc_html__( 'Heading Max Words', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 100,
			)
		);

		// 9-10. Description.
		$this->add_control(
			'desc_field',
			array(
				'label'   => esc_html__( 'Description Source', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'excerpt',
				'options' => dpce_get_field_choices(),
			)
		);

		$this->add_control(
			'desc_meta_key',
			array(
				'label'       => esc_html__( 'Description Meta Key', 'dynamic-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'my_meta_key',
				'condition'   => array( 'desc_field' => 'meta' ),
			)
		);

		$this->add_control(
			'desc_max_words',
			array(
				'label'   => esc_html__( 'Description Max Words', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 20,
				'min'     => 0,
				'max'     => 200,
			)
		);

		// 11. Trim append.
		$this->add_control(
			'trim_append',
			array(
				'label'   => esc_html__( 'Append to Trimmed Text', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '...',
			)
		);

		// 12. Shortcodes / page builder support.
		$this->add_control(
			'desc_render_shortcodes',
			array(
				'label'        => esc_html__( 'Render Shortcodes / HTML in Description', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dynamic-post-carousel-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dynamic-post-carousel-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'desc_field!' => array( 'title', 'date', 'author', 'none' ) ),
			)
		);

		// 13-15. Read more.
		$this->add_control(
			'read_more_txt',
			array(
				'label'   => esc_html__( 'Read More Button Text', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'dynamic-post-carousel-for-elementor' ),
			)
		);

		$this->add_control(
			'read_more_classes',
			array(
				'label'       => esc_html__( 'Read More Extra CSS Classes', 'dynamic-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'dpce-button',
				'placeholder' => 'dpce-button my-class',
			)
		);

		$this->add_control(
			'read_more_target',
			array(
				'label'   => esc_html__( 'Link Target', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '_self',
				'options' => array(
					'_self'  => esc_html__( 'Same Tab', 'dynamic-post-carousel-for-elementor' ),
					'_blank' => esc_html__( 'New Tab', 'dynamic-post-carousel-for-elementor' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================
	 * SECTION 2 - SLIDER
	 * ===================================================== */
	private function register_slider_section() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => esc_html__( 'Slider', 'dynamic-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'cols_desktop',
			array(
				'label'   => esc_html__( 'Columns - Desktop', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 8,
			)
		);
		$this->add_control(
			'cols_tablet',
			array(
				'label'   => esc_html__( 'Columns - Tablet', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2,
				'min'     => 1,
				'max'     => 6,
			)
		);
		$this->add_control(
			'cols_mobile',
			array(
				'label'   => esc_html__( 'Columns - Mobile', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
				'max'     => 4,
			)
		);
		$this->add_control(
			'slides_to_scroll',
			array(
				'label'   => esc_html__( 'Slides to Scroll', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
				'max'     => 8,
			)
		);
		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Animation Speed (ms)', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				'min'     => 50,
				'max'     => 5000,
			)
		);
		$this->add_control(
			'infinite',
			array(
				'label'        => esc_html__( 'Infinite Scroll', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'vertical',
			array(
				'label'        => esc_html__( 'Vertical Mode', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_responsive_control(
			'space_between',
			array(
				'label'      => esc_html__( 'Space Between Posts (px)', 'dynamic-post-carousel-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 15 ),
				'selectors'  => array(
					'{{WRAPPER}} .dpce-slide' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .dpce-track' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				),
			)
		);
		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => esc_html__( 'Autoplay Speed (ms)', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'min'       => 500,
				'max'       => 20000,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);
		$this->add_control(
			'dots',
			array(
				'label'        => esc_html__( 'Show Dots', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'dots_icon',
			array(
				'label'     => esc_html__( 'Dots Style', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'circle',
				'options'   => array(
					'circle' => esc_html__( 'Circle', 'dynamic-post-carousel-for-elementor' ),
					'square' => esc_html__( 'Square', 'dynamic-post-carousel-for-elementor' ),
					'dash'   => esc_html__( 'Dash', 'dynamic-post-carousel-for-elementor' ),
				),
				'condition' => array( 'dots' => 'yes' ),
			)
		);
		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Show Arrows', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'arrows_style',
			array(
				'label'     => esc_html__( 'Arrows Style', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'chevron',
				'options'   => array(
					'chevron' => esc_html__( 'Chevron', 'dynamic-post-carousel-for-elementor' ),
					'arrow'   => esc_html__( 'Arrow', 'dynamic-post-carousel-for-elementor' ),
					'circle'  => esc_html__( 'Circled Chevron', 'dynamic-post-carousel-for-elementor' ),
					'square'  => esc_html__( 'Square', 'dynamic-post-carousel-for-elementor' ),
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);
		$this->add_control(
			'rtl',
			array(
				'label'        => esc_html__( 'RTL Mode', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause on Hover', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================
	 * SECTION 3 - APPEARANCE
	 * ===================================================== */
	private function register_appearance_section() {
		$this->start_controls_section(
			'section_appearance',
			array(
				'label' => esc_html__( 'Appearance', 'dynamic-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'style_id',
			array(
				'label'   => esc_html__( 'Template Style', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => DPCE_Styles::get_choices(),
			)
		);

		$this->add_control(
			'post_bg_color',
			array(
				'label'     => esc_html__( 'Post Background Color', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dpce-slide-inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dpce-title, {{WRAPPER}} .dpce-title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'dynamic-post-carousel-for-elementor' ),
				'selector' => '{{WRAPPER}} .dpce-title, {{WRAPPER}} .dpce-title a',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'Description Color', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dpce-desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Description Typography', 'dynamic-post-carousel-for-elementor' ),
				'selector' => '{{WRAPPER}} .dpce-desc',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .dpce-slide-inner',
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dynamic-post-carousel-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dpce-slide-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .dpce-slide-inner',
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrows Color', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-prev:before, {{WRAPPER}} .slick-next:before' => 'color: {{VALUE}};',
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => esc_html__( 'Arrows Background', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => esc_html__( 'Dots Color', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'color: {{VALUE}};',
				),
				'condition' => array( 'dots' => 'yes' ),
			)
		);

		$this->add_control(
			'custom_css',
			array(
				'label'       => esc_html__( 'Custom CSS', 'dynamic-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::CODE,
				'language'    => 'css',
				'description' => esc_html__( 'Use {{WRAPPER}} as the widget selector.', 'dynamic-post-carousel-for-elementor' ),
				'rows'        => 8,
			)
		);

		// Per-style settings, conditionally rendered.
		$this->add_control(
			'_per_style_heading',
			array(
				'label' => esc_html__( 'Style Specific Settings', 'dynamic-post-carousel-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		foreach ( DPCE_Styles::all() as $style_id => $style ) {
			if ( empty( $style['settings'] ) || ! is_array( $style['settings'] ) ) {
				continue;
			}
			foreach ( $style['settings'] as $setting ) {
				if ( empty( $setting['id'] ) ) {
					continue;
				}
				$control_id = 'style_' . $style_id . '_' . $setting['id'];
				$args = array(
					'label'     => isset( $setting['label'] ) ? $setting['label'] : $setting['id'],
					'condition' => array( 'style_id' => (string) $style_id ),
				);

				if ( isset( $setting['default'] ) ) {
					$args['default'] = $setting['default'];
				}
				if ( ! empty( $setting['selectors'] ) ) {
					$args['selectors'] = $setting['selectors'];
				}
				if ( isset( $setting['min'] ) ) {
					$args['min'] = $setting['min'];
				}
				if ( isset( $setting['max'] ) ) {
					$args['max'] = $setting['max'];
				}

				switch ( isset( $setting['type'] ) ? $setting['type'] : 'text' ) {
					case 'color':
						$args['type'] = Controls_Manager::COLOR;
						break;
					case 'number':
						$args['type'] = Controls_Manager::NUMBER;
						break;
					case 'slider':
						$args['type'] = Controls_Manager::SLIDER;
						$args['size_units'] = array( 'px', '%' );
						break;
					case 'text':
					default:
						$args['type'] = Controls_Manager::TEXT;
						break;
				}

				$this->add_control( $control_id, $args );
			}
		}

		$this->end_controls_section();
	}

	/* =====================================================
	 * SECTION 4 - ADVANCED
	 * ===================================================== */
	private function register_advanced_section() {
		$this->start_controls_section(
			'section_advanced',
			array(
				'label' => esc_html__( 'Advanced', 'dynamic-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__( 'Image Size', 'dynamic-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium_large',
				'options' => dpce_get_image_sizes(),
			)
		);

		$this->add_control(
			'lazy_load',
			array(
				'label'        => esc_html__( 'Lazy Load Images', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'placeholder_image',
			array(
				'label' => esc_html__( 'Placeholder Image', 'dynamic-post-carousel-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'disable_current_post',
			array(
				'label'        => esc_html__( 'Hide Current Post', 'dynamic-post-carousel-for-elementor' ),
				'description'  => esc_html__( 'Skip the current post when the carousel is rendered on a singular page.', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'adaptive_height',
			array(
				'label'        => esc_html__( 'Adaptive Height', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'enable_share',
			array(
				'label'        => esc_html__( 'Enable Social Sharing', 'dynamic-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'share_networks',
			array(
				'label'       => esc_html__( 'Networks', 'dynamic-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(
					'facebook'  => 'Facebook',
					'twitter'   => 'Twitter / X',
					'linkedin'  => 'LinkedIn',
					'whatsapp'  => 'WhatsApp',
					'pinterest' => 'Pinterest',
					'email'     => esc_html__( 'Email', 'dynamic-post-carousel-for-elementor' ),
				),
				'default'     => array( 'facebook', 'twitter', 'linkedin' ),
				'condition'   => array( 'enable_share' => 'yes' ),
			)
		);

		$this->add_control(
			'share_color',
			array(
				'label'     => esc_html__( 'Share Icon Color', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dpce-share .dpce-share-link' => 'color: {{VALUE}};',
				),
				'condition' => array( 'enable_share' => 'yes' ),
			)
		);

		$this->add_control(
			'share_bg',
			array(
				'label'     => esc_html__( 'Share Icon Background', 'dynamic-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dpce-share .dpce-share-link' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'enable_share' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* =====================================================
	 * RENDER
	 * ===================================================== */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$raw_settings = $this->get_settings();

		// Normalize relevant fields the renderer hooks expect.
		$carousel_settings = $this->build_carousel_settings( $settings, $raw_settings );

		// Build query.
		$query = DPCE_Query::run( $this->build_query_settings( $settings ) );

		if ( ! $query->have_posts() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'No posts match the current settings.', 'dynamic-post-carousel-for-elementor' ) . '</p>';
			}
			return;
		}

		$style_id = isset( $settings['style_id'] ) ? $settings['style_id'] : '1';
		$template = DPCE_Styles::locate_template( $style_id );

		// Build slick options.
		$slick_options = array(
			'slidesToShow'   => max( 1, (int) ( isset( $settings['cols_desktop'] ) ? $settings['cols_desktop'] : 3 ) ),
			'slidesToScroll' => max( 1, (int) ( isset( $settings['slides_to_scroll'] ) ? $settings['slides_to_scroll'] : 1 ) ),
			'speed'          => (int) ( isset( $settings['speed'] ) ? $settings['speed'] : 500 ),
			'infinite'       => 'yes' === ( isset( $settings['infinite'] ) ? $settings['infinite'] : 'yes' ),
			'vertical'       => 'yes' === ( isset( $settings['vertical'] ) ? $settings['vertical'] : '' ),
			'autoplay'       => 'yes' === ( isset( $settings['autoplay'] ) ? $settings['autoplay'] : '' ),
			'autoplaySpeed'  => (int) ( isset( $settings['autoplay_speed'] ) ? $settings['autoplay_speed'] : 3000 ),
			'pauseOnHover'   => 'yes' === ( isset( $settings['pause_on_hover'] ) ? $settings['pause_on_hover'] : 'yes' ),
			'dots'           => 'yes' === ( isset( $settings['dots'] ) ? $settings['dots'] : 'yes' ),
			'arrows'         => 'yes' === ( isset( $settings['arrows'] ) ? $settings['arrows'] : 'yes' ),
			'rtl'            => 'yes' === ( isset( $settings['rtl'] ) ? $settings['rtl'] : '' ),
			'adaptiveHeight' => 'yes' === ( isset( $settings['adaptive_height'] ) ? $settings['adaptive_height'] : '' ),
			'responsive'     => array(
				array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow' => max( 1, (int) ( isset( $settings['cols_tablet'] ) ? $settings['cols_tablet'] : 2 ) ),
					),
				),
				array(
					'breakpoint' => 600,
					'settings'   => array(
						'slidesToShow' => max( 1, (int) ( isset( $settings['cols_mobile'] ) ? $settings['cols_mobile'] : 1 ) ),
					),
				),
			),
		);

		$wrapper_classes = array(
			'dpce-carousel',
			'dpce-style-' . sanitize_html_class( $style_id ),
			'dpce-arrows-' . sanitize_html_class( isset( $settings['arrows_style'] ) ? $settings['arrows_style'] : 'chevron' ),
			'dpce-dots-' . sanitize_html_class( isset( $settings['dots_icon'] ) ? $settings['dots_icon'] : 'circle' ),
		);

		// Inline custom CSS (with {{WRAPPER}} replaced).
		if ( ! empty( $settings['custom_css'] ) ) {
			$widget_id = 'elementor-element-' . $this->get_id();
			$css = str_replace( '{{WRAPPER}}', '.' . $widget_id, $settings['custom_css'] );
			// Remove any tag-like sequences to prevent breaking out of <style>.
			$css = preg_replace( '#</?[a-z][^>]*>#i', '', $css );
			echo '<style>' . wp_strip_all_tags( $css ) . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>"
			data-slick="<?php echo esc_attr( wp_json_encode( $slick_options ) ); ?>">
			<div class="dpce-track">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = get_the_ID();
					echo '<div class="dpce-slide"><div class="dpce-slide-inner">';
					if ( $template ) {
						include $template;
					} else {
						$this->render_fallback_slide( $post_id, $carousel_settings );
					}
					echo '</div></div>';
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Compose the settings array exposed to template files via $carousel_settings.
	 *
	 * @param array $settings    Display settings.
	 * @param array $raw         Raw settings.
	 * @return array
	 */
	private function build_carousel_settings( $settings, $raw ) {
		unset( $raw );

		$placeholder = '';
		if ( isset( $settings['placeholder_image'] ) && is_array( $settings['placeholder_image'] ) && ! empty( $settings['placeholder_image']['url'] ) ) {
			$placeholder = $settings['placeholder_image']['url'];
		}

		return array(
			'heading_field'         => isset( $settings['heading_field'] ) ? $settings['heading_field'] : 'title',
			'heading_meta_key'      => isset( $settings['heading_meta_key'] ) ? $settings['heading_meta_key'] : '',
			'heading_max_words'     => isset( $settings['heading_max_words'] ) ? (int) $settings['heading_max_words'] : 0,
			'desc_field'            => isset( $settings['desc_field'] ) ? $settings['desc_field'] : 'excerpt',
			'desc_meta_key'         => isset( $settings['desc_meta_key'] ) ? $settings['desc_meta_key'] : '',
			'desc_max_words'        => isset( $settings['desc_max_words'] ) ? (int) $settings['desc_max_words'] : 20,
			'desc_render_shortcodes' => 'yes' === ( isset( $settings['desc_render_shortcodes'] ) ? $settings['desc_render_shortcodes'] : '' ),
			'trim_append'           => isset( $settings['trim_append'] ) ? $settings['trim_append'] : '...',
			'read_more_txt'         => isset( $settings['read_more_txt'] ) ? $settings['read_more_txt'] : '',
			'read_more_classes'     => isset( $settings['read_more_classes'] ) ? $settings['read_more_classes'] : 'dpce-button',
			'read_more_target'      => isset( $settings['read_more_target'] ) ? $settings['read_more_target'] : '_self',
			'image_size'            => isset( $settings['image_size'] ) ? $settings['image_size'] : 'medium_large',
			'lazy_load'             => 'yes' === ( isset( $settings['lazy_load'] ) ? $settings['lazy_load'] : 'yes' ),
			'placeholder_image'     => $placeholder,
			'enable_share'          => 'yes' === ( isset( $settings['enable_share'] ) ? $settings['enable_share'] : '' ),
			'share_networks'        => isset( $settings['share_networks'] ) ? (array) $settings['share_networks'] : array(),
			'style_id'              => isset( $settings['style_id'] ) ? $settings['style_id'] : '1',
		);
	}

	/**
	 * Compose query-only settings.
	 *
	 * @param array $settings Settings.
	 * @return array
	 */
	private function build_query_settings( $settings ) {
		$display_by = isset( $settings['display_by'] ) ? $settings['display_by'] : 'post_type';
		$post_type  = isset( $settings['post_type'] ) ? $settings['post_type'] : 'post';
		$taxonomy   = isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : '';
		$terms      = isset( $settings[ 'terms__' . $taxonomy ] ) ? (array) $settings[ 'terms__' . $taxonomy ] : array();

		$query_settings = array(
			'display_by'           => $display_by,
			'post_type'            => $post_type,
			'taxonomy'             => $taxonomy,
			'terms'                => $terms,
			'posts_per_page'       => isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 8,
			'orderby'              => isset( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'                => isset( $settings['order'] ) ? $settings['order'] : 'DESC',
			'exclude_ids'          => isset( $settings['exclude_ids'] ) ? $settings['exclude_ids'] : '',
			'disable_current_post' => 'yes' === ( isset( $settings['disable_current_post'] ) ? $settings['disable_current_post'] : '' ),
		);

		// Pass through the per-post-type "selected posts" key.
		if ( isset( $settings[ 'posts__' . $post_type ] ) ) {
			$query_settings[ 'posts__' . $post_type ] = (array) $settings[ 'posts__' . $post_type ];
		}

		return $query_settings;
	}

	/**
	 * Fallback markup when no template is found.
	 *
	 * @param int   $post_id           Post ID.
	 * @param array $carousel_settings Carousel settings.
	 */
	private function render_fallback_slide( $post_id, $carousel_settings ) {
		?>
		<figure class="dpce-fallback">
			<?php do_action( 'dpce_carousel_thumbnail', $post_id, $carousel_settings ); ?>
			<figcaption>
				<h3 class="dpce-title">
					<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" target="<?php echo esc_attr( $carousel_settings['read_more_target'] ); ?>">
						<?php do_action( 'dpce_carousel_title', $post_id, $carousel_settings ); ?>
					</a>
				</h3>
				<div class="dpce-desc">
					<?php do_action( 'dpce_carousel_desc', $post_id, $carousel_settings ); ?>
				</div>
				<?php do_action( 'dpce_carousel_read_more', $post_id, $carousel_settings ); ?>
			</figcaption>
		</figure>
		<?php
	}
}
