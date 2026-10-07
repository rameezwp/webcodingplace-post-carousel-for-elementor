<?php
/**
 * Card, meta row, badge, image and WooCommerce controls (added in 2.0).
 *
 * Options that change existing templates are off by default. Options for
 * the new Card template are on by default: no carousel saved before 2.0
 * uses that template.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Trait DPCE_Card_Controls
 */
trait DPCE_Card_Controls {

	/**
	 * Parts of the Card template, keyed by setting suffix.
	 *
	 * @return array<string, string>
	 */
	protected function get_card_parts() {
		return array(
			'image'       => esc_html__( 'Image', 'webcodingplace-post-carousel-for-elementor' ),
			'badge'       => esc_html__( 'Category Badge on Image', 'webcodingplace-post-carousel-for-elementor' ),
			'meta'        => esc_html__( 'Meta Row', 'webcodingplace-post-carousel-for-elementor' ),
			'excerpt'     => esc_html__( 'Excerpt', 'webcodingplace-post-carousel-for-elementor' ),
			'button'      => esc_html__( 'Button', 'webcodingplace-post-carousel-for-elementor' ),
			'rating'      => esc_html__( 'Product Rating', 'webcodingplace-post-carousel-for-elementor' ),
			'price'       => esc_html__( 'Product Price', 'webcodingplace-post-carousel-for-elementor' ),
			'sale_badge'  => esc_html__( 'Sale Badge', 'webcodingplace-post-carousel-for-elementor' ),
			'add_to_cart' => esc_html__( 'Add to Cart for Products', 'webcodingplace-post-carousel-for-elementor' ),
		);
	}

	/**
	 * Content tab: "Card Elements" section.
	 */
	protected function register_card_content_section() {
		$this->start_controls_section(
			'section_card_elements',
			array(
				'label' => esc_html__( 'Card Elements', 'webcodingplace-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'card_elements_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Choose the template in Style > Appearance. The Card template lets you switch each part on or off. Other templates can get a meta row, badges and product details too.', 'webcodingplace-post-carousel-for-elementor' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		// Card template parts: on by default.
		foreach ( $this->get_card_parts() as $part => $label ) {
			$this->add_control(
				'card_show_' . $part,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'style_id' => 'card' ),
				)
			);
		}

		// Other templates: extras, off by default so saved carousels do not change.
		$extras = array(
			'extra_meta'       => esc_html__( 'Add Meta Row Under Title', 'webcodingplace-post-carousel-for-elementor' ),
			'extra_badge'      => esc_html__( 'Add Category Badge on Image', 'webcodingplace-post-carousel-for-elementor' ),
			'extra_sale_badge' => esc_html__( 'Add Sale Badge on Product Images', 'webcodingplace-post-carousel-for-elementor' ),
			'extra_product'    => esc_html__( 'Add Product Rating, Price and Cart Button', 'webcodingplace-post-carousel-for-elementor' ),
		);
		foreach ( $extras as $id => $label ) {
			$this->add_control(
				$id,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'style_id!' => 'card' ),
				)
			);
		}

		$this->add_control(
			'meta_items',
			array(
				'label'       => esc_html__( 'Meta Row Shows', 'webcodingplace-post-carousel-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'date', 'author' ),
				'options'     => array(
					'date'         => esc_html__( 'Date', 'webcodingplace-post-carousel-for-elementor' ),
					'author'       => esc_html__( 'Author', 'webcodingplace-post-carousel-for-elementor' ),
					'avatar'       => esc_html__( 'Author Picture', 'webcodingplace-post-carousel-for-elementor' ),
					'terms'        => esc_html__( 'Categories', 'webcodingplace-post-carousel-for-elementor' ),
					'comments'     => esc_html__( 'Comments', 'webcodingplace-post-carousel-for-elementor' ),
					'reading_time' => esc_html__( 'Reading Time', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'separator'   => 'before',
				'conditions'  => $this->either_card_or_extra( 'meta' ),
			)
		);

		$this->add_control(
			'badge_taxonomy',
			array(
				'label'      => esc_html__( 'Badge and Categories From', 'webcodingplace-post-carousel-for-elementor' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'auto',
				'options'    => array( 'auto' => esc_html__( 'Main category of the post type', 'webcodingplace-post-carousel-for-elementor' ) ) + dpce_get_taxonomies(),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						$this->either_card_or_extra( 'badge' ),
						$this->either_card_or_extra( 'meta' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Conditions: "Card template with this part on" OR "another template with the extra on".
	 *
	 * @param string $part meta or badge.
	 * @return array
	 */
	private function either_card_or_extra( $part ) {
		return array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'style_id',
							'operator' => '==',
							'value'    => 'card',
						),
						array(
							'name'     => 'card_show_' . $part,
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
				array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'style_id',
							'operator' => '!==',
							'value'    => 'card',
						),
						array(
							'name'     => 'extra_' . $part,
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			),
		);
	}

	/**
	 * Style tab: "Image" and "Card Parts" sections.
	 */
	protected function register_card_style_sections() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => esc_html__( 'Image', 'webcodingplace-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_ratio',
			array(
				'label'     => esc_html__( 'Image Ratio', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''     => esc_html__( 'Original', 'webcodingplace-post-carousel-for-elementor' ),
					'1/1'  => '1:1',
					'4/3'  => '4:3',
					'3/2'  => '3:2',
					'16/9' => '16:9',
					'3/4'  => '3:4',
					'2/3'  => '2:3',
				),
				'selectors' => array(
					'{{WRAPPER}} .dpce-thumbnail img' => 'aspect-ratio: {{VALUE}}; width: 100%; height: auto; object-fit: cover;',
				),
			)
		);

		$this->add_control(
			'image_fit',
			array(
				'label'     => esc_html__( 'Image Fit', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''        => esc_html__( 'Fill (crop)', 'webcodingplace-post-carousel-for-elementor' ),
					'contain' => esc_html__( 'Fit (no crop)', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .dpce-thumbnail img' => 'object-fit: {{VALUE}};',
				),
				'condition' => array( 'image_ratio!' => '' ),
			)
		);

		$this->add_control(
			'image_hover',
			array(
				'label'        => esc_html__( 'Hover Effect', 'webcodingplace-post-carousel-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => '',
				'options'      => array(
					''          => esc_html__( 'None', 'webcodingplace-post-carousel-for-elementor' ),
					'zoom'      => esc_html__( 'Zoom image', 'webcodingplace-post-carousel-for-elementor' ),
					'lift'      => esc_html__( 'Lift card', 'webcodingplace-post-carousel-for-elementor' ),
					'darken'    => esc_html__( 'Darken image', 'webcodingplace-post-carousel-for-elementor' ),
					'grayscale' => esc_html__( 'Color on hover', 'webcodingplace-post-carousel-for-elementor' ),
				),
				'prefix_class' => 'dpce-hover-',
			)
		);

		$this->add_control(
			'image_radius',
			array(
				'label'      => esc_html__( 'Image Corner Radius', 'webcodingplace-post-carousel-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dpce-thumbnail, {{WRAPPER}} .dpce-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_parts_style',
			array(
				'label' => esc_html__( 'Card Parts', 'webcodingplace-post-carousel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'webcodingplace-post-carousel-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dpce-card__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array( 'style_id' => 'card' ),
			)
		);

		$this->add_responsive_control(
			'card_align',
			array(
				'label'     => esc_html__( 'Alignment', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'webcodingplace-post-carousel-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'webcodingplace-post-carousel-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'webcodingplace-post-carousel-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dpce-card__body' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'style_id' => 'card' ),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => esc_html__( 'Meta Row Color', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-meta' => 'color: {{VALUE}};' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'label'    => esc_html__( 'Meta Row Typography', 'webcodingplace-post-carousel-for-elementor' ),
				'selector' => '{{WRAPPER}} .dpce-meta',
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => esc_html__( 'Badge Background', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-badge--term' => 'background-color: {{VALUE}};' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => esc_html__( 'Badge Text', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-badge--term' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'sale_badge_bg',
			array(
				'label'     => esc_html__( 'Sale Badge Background', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-badge--sale' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'Price Color', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-price, {{WRAPPER}} .dpce-price ins' => 'color: {{VALUE}};' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => esc_html__( 'Price Typography', 'webcodingplace-post-carousel-for-elementor' ),
				'selector' => '{{WRAPPER}} .dpce-price',
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button Text', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-card__actions .dpce-button, {{WRAPPER}} .dpce-product-extras .dpce-button' => 'color: {{VALUE}};' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => esc_html__( 'Button Background', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-card__actions .dpce-button, {{WRAPPER}} .dpce-product-extras .dpce-button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => esc_html__( 'Button Background on Hover', 'webcodingplace-post-carousel-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .dpce-card__actions .dpce-button:hover, {{WRAPPER}} .dpce-product-extras .dpce-button:hover, {{WRAPPER}} .dpce-wrapper:hover .dpce-card__actions span.dpce-button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Button Typography', 'webcodingplace-post-carousel-for-elementor' ),
				'selector' => '{{WRAPPER}} .dpce-card__actions .dpce-button, {{WRAPPER}} .dpce-product-extras .dpce-button',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Card and extras settings passed to templates in $carousel_settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function build_card_settings( $settings ) {
		$is_card = isset( $settings['style_id'] ) && 'card' === $settings['style_id'];
		$on      = static function ( $key ) use ( $settings ) {
			return isset( $settings[ $key ] ) && 'yes' === $settings[ $key ];
		};

		$card = array();
		foreach ( array_keys( $this->get_card_parts() ) as $part ) {
			$card[ $part ] = $on( 'card_show_' . $part );
		}

		return array(
			'card'                => $card,
			'show_meta'           => $is_card ? $card['meta'] : $on( 'extra_meta' ),
			'show_badge'          => $is_card ? ( $card['image'] && $card['badge'] ) : $on( 'extra_badge' ),
			'show_sale_badge'     => $is_card ? ( $card['image'] && $card['sale_badge'] ) : $on( 'extra_sale_badge' ),
			'show_product_extras' => ! $is_card && $on( 'extra_product' ),
			'meta_items'          => isset( $settings['meta_items'] ) ? array_values( array_filter( (array) $settings['meta_items'], 'is_string' ) ) : array( 'date', 'author' ),
			'badge_taxonomy'      => isset( $settings['badge_taxonomy'] ) ? sanitize_key( (string) $settings['badge_taxonomy'] ) : 'auto',
		);
	}
}
