<?php
/**
 * Query controls added in 2.0: query mode, related posts and filters.
 *
 * Every control defaults to the 1.4 behaviour.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/**
 * Trait DPCE_Query_Controls
 */
trait DPCE_Query_Controls {

	/**
	 * "Source" control, printed at the top of the Post / Content section.
	 */
	protected function register_query_mode_controls() {
		$this->add_control(
			'query_mode',
			array(
				'label'   => esc_html__( 'Source', 'webcodingplace-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => array(
					'custom'  => esc_html__( 'Posts I choose', 'webcodingplace-post-carousel-for-elementor' ),
					'current' => esc_html__( 'Current query (archive and search pages)', 'webcodingplace-post-carousel-for-elementor' ),
					'related' => esc_html__( 'Related posts (single post pages)', 'webcodingplace-post-carousel-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'query_mode_current_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Shows the posts of the archive, category or search page the carousel is on. In the editor you see your latest posts instead.', 'webcodingplace-post-carousel-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array( 'query_mode' => 'current' ),
			)
		);

		$related_taxonomies = array( 'any' => esc_html__( 'Any shared category, tag or term', 'webcodingplace-post-carousel-for-elementor' ) ) + dpce_get_taxonomies();

		$this->add_control(
			'related_by',
			array(
				'label'       => esc_html__( 'Related By', 'webcodingplace-post-carousel-for-elementor' ),
				'description' => esc_html__( 'Shows posts of the same type that share these terms with the post being viewed. The post itself is left out.', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'any',
				'options'     => $related_taxonomies,
				'condition'   => array( 'query_mode' => 'related' ),
			)
		);

		$this->add_control(
			'related_fallback',
			array(
				'label'        => esc_html__( 'Show Latest Posts When Nothing Is Related', 'webcodingplace-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'query_mode' => 'related' ),
			)
		);
	}

	/**
	 * Extra order options, printed after the Order control.
	 */
	protected function register_order_extra_controls() {
		$this->add_control(
			'orderby_meta_key',
			array(
				'label'       => esc_html__( 'Custom Field Key', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'my_field',
				'condition'   => array(
					'orderby'     => array( 'meta_value', 'meta_value_num' ),
					'query_mode!' => 'current',
				),
			)
		);
	}

	/**
	 * The "Filters" section.
	 */
	protected function register_filters_section() {
		$this->start_controls_section(
			'section_query_filters',
			array(
				'label'     => esc_html__( 'Filters', 'webcodingplace-post-carousel-for-elementor' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'query_mode!' => 'current' ),
			)
		);

		$this->add_control(
			'include_terms',
			array(
				'label'       => esc_html__( 'Only Posts With These Terms', 'webcodingplace-post-carousel-for-elementor' ),
				'description' => esc_html__( 'Categories, tags or any other terms. Type to search.', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => DPCE_Query_Control::TYPE,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(),
				'query'       => array(
					'kind'   => 'term',
					'source' => 'any',
				),
			)
		);

		$this->add_control(
			'terms_relation',
			array(
				'label'     => esc_html__( 'Match', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'OR',
				'options'   => array(
					'OR'  => esc_html__( 'Any of these terms', 'webcodingplace-post-carousel-for-elementor' ),
					'AND' => esc_html__( 'All of these terms', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'condition' => array( 'include_terms!' => '' ),
			)
		);

		$this->add_control(
			'exclude_terms',
			array(
				'label'       => esc_html__( 'Leave Out Posts With These Terms', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => DPCE_Query_Control::TYPE,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(),
				'query'       => array(
					'kind'   => 'term',
					'source' => 'any',
				),
			)
		);

		$this->add_control(
			'include_authors',
			array(
				'label'       => esc_html__( 'Only These Authors', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => DPCE_Query_Control::TYPE,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(),
				'query'       => array( 'kind' => 'author' ),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'exclude_authors',
			array(
				'label'       => esc_html__( 'Leave Out These Authors', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => DPCE_Query_Control::TYPE,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array(),
				'query'       => array( 'kind' => 'author' ),
			)
		);

		$this->add_control(
			'date_range',
			array(
				'label'     => esc_html__( 'Date', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'anytime',
				'options'   => array(
					'anytime' => esc_html__( 'Any time', 'webcodingplace-post-carousel-for-elementor' ),
					'day'     => esc_html__( 'Past day', 'webcodingplace-post-carousel-for-elementor' ),
					'week'    => esc_html__( 'Past week', 'webcodingplace-post-carousel-for-elementor' ),
					'month'   => esc_html__( 'Past month', 'webcodingplace-post-carousel-for-elementor' ),
					'quarter' => esc_html__( 'Past 3 months', 'webcodingplace-post-carousel-for-elementor' ),
					'year'    => esc_html__( 'Past year', 'webcodingplace-post-carousel-for-elementor' ),
					'custom'  => esc_html__( 'Custom range', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'date_after',
			array(
				'label'          => esc_html__( 'From', 'webcodingplace-post-carousel-for-elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array( 'enableTime' => false ),
				'condition'      => array( 'date_range' => 'custom' ),
			)
		);

		$this->add_control(
			'date_before',
			array(
				'label'          => esc_html__( 'To', 'webcodingplace-post-carousel-for-elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array( 'enableTime' => false ),
				'condition'      => array( 'date_range' => 'custom' ),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => esc_html__( 'Skip First Posts', 'webcodingplace-post-carousel-for-elementor' ),
				'description' => esc_html__( 'Useful when another widget already shows the latest posts.', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 100,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'sticky_posts',
			array(
				'label'   => esc_html__( 'Sticky Posts', 'webcodingplace-post-carousel-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ignore',
				'options' => array(
					'ignore'  => esc_html__( 'Treat like other posts', 'webcodingplace-post-carousel-for-elementor' ),
					'exclude' => esc_html__( 'Leave out', 'webcodingplace-post-carousel-for-elementor' ),
					'only'    => esc_html__( 'Show only sticky posts', 'webcodingplace-post-carousel-for-elementor' ),
				),
			)
		);

		$product_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'query_mode',
							'operator' => '==',
							'value'    => 'custom',
						),
						array(
							'name'     => 'display_by',
							'operator' => '==',
							'value'    => 'post_type',
						),
						array(
							'name'     => 'post_type',
							'operator' => '==',
							'value'    => 'product',
						),
					),
				),
				array(
					'name'     => 'query_mode',
					'operator' => '==',
					'value'    => 'related',
				),
			),
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$this->add_control(
				'product_filter',
				array(
					'label'      => esc_html__( 'Products', 'webcodingplace-post-carousel-for-elementor' ),
					'type'       => Controls_Manager::SELECT,
					'default'    => 'none',
					'options'    => array(
						'none'         => esc_html__( 'All products', 'webcodingplace-post-carousel-for-elementor' ),
						'featured'     => esc_html__( 'Featured', 'webcodingplace-post-carousel-for-elementor' ),
						'on_sale'      => esc_html__( 'On sale', 'webcodingplace-post-carousel-for-elementor' ),
						'best_selling' => esc_html__( 'Best selling', 'webcodingplace-post-carousel-for-elementor' ),
						'top_rated'    => esc_html__( 'Top rated', 'webcodingplace-post-carousel-for-elementor' ),
					),
					'separator'  => 'before',
					'conditions' => $product_conditions,
				)
			);

			$this->add_control(
				'hide_out_of_stock',
				array(
					'label'        => esc_html__( 'Hide Out of Stock Products', 'webcodingplace-post-carousel-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => $product_conditions,
				)
			);
		}

		$this->end_controls_section();
	}
}
