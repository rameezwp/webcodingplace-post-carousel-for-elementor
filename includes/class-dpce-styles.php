<?php
/**
 * Template / style registry.
 *
 * Each style is an array describing one carousel template:
 *   id       (string, required) - matches the template file: templates/style-{id}.php
 *   name     (string, required) - human label shown in the picker.
 *   thumb    (string, optional) - URL to a preview image.
 *   settings (array,  optional) - per-style appearance settings.
 *
 * Each entry in `settings` is rendered as an Elementor control inside the
 * Appearance tab and conditionally bound to the chosen style. The `selectors`
 * key is used to write the resulting CSS rule.
 *
 *   array(
 *       'id'        => 'icon_color',                // unique within style
 *       'label'     => __( 'Icon Color', '...' ),
 *       'type'      => 'color' | 'text' | 'number' | 'slider',
 *       'default'   => '#1abc9c',
 *       'selectors' => array( '{{WRAPPER}} .fa' => 'color: {{VALUE}};' ),
 *   )
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Styles
 */
class DPCE_Styles {

	/**
	 * Cached styles array.
	 *
	 * @var array|null
	 */
	private static $styles = null;

	/**
	 * Get the full styles map.
	 *
	 * @return array
	 */
	public static function all() {
		if ( null !== self::$styles ) {
			return self::$styles;
		}

		$default_styles = array(
			'1' => array(
				'id'       => '1',
				'name'     => esc_html__( 'Style 1 - Overlay Card', 'webcodingplace-post-carousel-for-elementor' ),
				'thumb'    => DPCE_URL . 'assets/images/style-1.svg',
				'settings' => array(
					array(
						'id'        => 'icon_color',
						'label'     => esc_html__( 'Link Icon Color', 'webcodingplace-post-carousel-for-elementor' ),
						'type'      => 'color',
						'default'   => '#ffffff',
						'selectors' => array(
							'{{WRAPPER}} .dpce-style-1 .dpce-icon' => 'color: {{VALUE}};',
						),
					),
					array(
						'id'        => 'overlay_bg',
						'label'     => esc_html__( 'Overlay Background', 'webcodingplace-post-carousel-for-elementor' ),
						'type'      => 'color',
						'default'   => 'rgba(0,0,0,0.55)',
						'selectors' => array(
							'{{WRAPPER}} .dpce-style-1 .dpce-caption' => 'background-color: {{VALUE}};',
						),
					),
				),
			),
			'2' => array(
				'id'       => '2',
				'name'     => esc_html__( 'Style 2 - Below Image', 'webcodingplace-post-carousel-for-elementor' ),
				'thumb'    => DPCE_URL . 'assets/images/style-2.svg',
				'settings' => array(
					array(
						'id'        => 'card_radius',
						'label'     => esc_html__( 'Card Border Radius (px)', 'webcodingplace-post-carousel-for-elementor' ),
						'type'      => 'number',
						'default'   => 6,
						'min'       => 0,
						'max'       => 60,
						'selectors' => array(
							'{{WRAPPER}} .dpce-style-2' => 'border-radius: {{VALUE}}px; overflow: hidden;',
						),
					),
				),
			),
			'3' => array(
				'id'       => '3',
				'name'     => esc_html__( 'Style 3 - Side Meta', 'webcodingplace-post-carousel-for-elementor' ),
				'thumb'    => DPCE_URL . 'assets/images/style-3.svg',
				'settings' => array(
					array(
						'id'        => 'meta_color',
						'label'     => esc_html__( 'Meta Text Color', 'webcodingplace-post-carousel-for-elementor' ),
						'type'      => 'color',
						'default'   => '#888888',
						'selectors' => array(
							'{{WRAPPER}} .dpce-style-3 .dpce-meta' => 'color: {{VALUE}};',
						),
					),
				),
			),
		);

		/**
		 * Filter the registered carousel styles.
		 *
		 * Add new styles by pushing into the array, keyed by style id.
		 *
		 * @param array $styles Map of style id => style definition.
		 */
		self::$styles = apply_filters( 'dpce_styles', $default_styles );

		return self::$styles;
	}

	/**
	 * Get a single style by id.
	 *
	 * @param string $id Style id.
	 * @return array|null
	 */
	public static function get( $id ) {
		$styles = self::all();
		return isset( $styles[ $id ] ) ? $styles[ $id ] : null;
	}

	/**
	 * Get the styles formatted for an Elementor SELECT control.
	 *
	 * @return array id => name
	 */
	public static function get_choices() {
		$choices = array();
		foreach ( self::all() as $id => $style ) {
			$choices[ $id ] = isset( $style['name'] ) ? $style['name'] : $id;
		}
		return $choices;
	}

	/**
	 * Resolve the absolute path to a style template file.
	 *
	 * Allows themes/child-plugins to override templates by placing
	 * `webcodingplace-post-carousel-for-elementor/style-{id}.php` in their theme.
	 *
	 * @param string $id Style id.
	 * @return string|null
	 */
	public static function locate_template( $id ) {
		$id   = sanitize_file_name( $id );
		$file = 'style-' . $id . '.php';

		$located = locate_template( array( 'webcodingplace-post-carousel-for-elementor/' . $file ) );
		if ( $located && file_exists( $located ) ) {
			return $located;
		}

		$plugin_file = DPCE_TEMPLATES_PATH . $file;
		if ( file_exists( $plugin_file ) ) {
			return $plugin_file;
		}

		return null;
	}
}
