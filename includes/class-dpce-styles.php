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

		$default_styles = array();

		// Auto-register styles 1-51. Each ships with an empty `settings` array;
		// add per-style controls later by pushing into $default_styles[$id]['settings']
		// or by hooking into the `dpce_styles` filter below.
		for ( $i = 1; $i <= 51; $i++ ) {
			$default_styles[ (string) $i ] = array(
				'id'       => (string) $i,
				/* translators: %d: template style number. */
				'name'     => sprintf( esc_html__( 'Style %d', 'webcodingplace-post-carousel-for-elementor' ), $i ),
				'thumb'    => DPCE_URL . 'assets/images/style-' . $i . '.svg',
				'settings' => array(),
			);
		}

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
		// Only registered styles can be loaded.
		if ( null === self::get( (string) $id ) ) {
			return null;
		}

		$id   = sanitize_file_name( $id );
		$file = 'style-' . $id . '.php';

		$located = locate_template( array( 'dpce/' . $file ) );
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
