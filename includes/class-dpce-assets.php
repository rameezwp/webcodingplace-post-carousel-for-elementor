<?php
/**
 * Script and style registration.
 *
 * Everything is registered on every request but only enqueued by the widget
 * that needs it, so pages without a carousel load nothing from this plugin.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Assets
 */
class DPCE_Assets {

	/**
	 * Option that stores the plugin version the site last ran.
	 */
	const VERSION_OPTION = 'dpce_version';

	/**
	 * Hook everything up.
	 */
	public static function init() {
		add_action( 'elementor/frontend/after_register_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'elementor/frontend/after_register_styles', array( __CLASS__, 'register_styles' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_editor_scripts' ) );
		add_action( 'init', array( __CLASS__, 'maybe_upgrade' ), 20 );
	}

	/**
	 * Register front end scripts.
	 */
	public static function register_scripts() {
		// Classic engine (Slick). Kept for carousels created before 2.0.
		wp_register_script( 'dpce-slick', DPCE_URL . 'assets/vendor/slick/slick.min.js', array( 'jquery' ), '1.8.1', true );
		wp_register_script( 'dpce-frontend', DPCE_URL . 'assets/js/main.js', array( 'jquery', 'dpce-slick' ), DPCE_VERSION, true );

		// Modern engine: Elementor's own Swiper plus a small script without jQuery.
		// Some Elementor versions do not register a "swiper" script and load
		// it on demand instead; the script then asks Elementor to load it.
		$swiper_deps = wp_script_is( 'swiper', 'registered' ) ? array( 'swiper' ) : array();
		wp_register_script( 'dpce-swiper', DPCE_URL . 'assets/js/swiper-init.js', $swiper_deps, DPCE_VERSION, true );
	}

	/**
	 * Register front end styles.
	 */
	public static function register_styles() {
		wp_register_style( 'dpce-slick', DPCE_URL . 'assets/vendor/slick/slick.css', array(), '1.8.1' );
		wp_register_style( 'dpce-slick-theme', DPCE_URL . 'assets/vendor/slick/slick-theme.css', array( 'dpce-slick' ), '1.8.1' );

		// Full stylesheet with every template. Used in the editor, where the
		// template can change without a page reload.
		wp_register_style( 'dpce-frontend', DPCE_URL . 'assets/css/main.css', array( 'dpce-slick' ), DPCE_VERSION );

		// Front end: a small base file plus one file per template.
		wp_register_style( 'dpce-base', DPCE_URL . 'assets/css/dist/base.min.css', array(), DPCE_VERSION );
		wp_register_style( 'dpce-swiper', DPCE_URL . 'assets/css/swiper.css', array(), DPCE_VERSION );
		foreach ( array_keys( DPCE_Styles::all() ) as $style_id ) {
			if ( file_exists( self::style_file( $style_id ) ) ) {
				wp_register_style( self::style_handle( $style_id ), DPCE_URL . self::style_file( $style_id, false ), array( 'dpce-base' ), DPCE_VERSION );
			}
		}
	}

	/**
	 * Handle name of a template stylesheet.
	 *
	 * @param string|int $style_id Template id.
	 * @return string
	 */
	public static function style_handle( $style_id ) {
		return 'dpce-style-' . sanitize_key( (string) $style_id );
	}

	/**
	 * Path of a template stylesheet.
	 *
	 * @param string|int $style_id Template id.
	 * @param bool       $absolute Absolute path (true) or path relative to the plugin (false).
	 * @return string
	 */
	public static function style_file( $style_id, $absolute = true ) {
		$file = 'assets/css/dist/style-' . sanitize_file_name( (string) $style_id ) . '.min.css';
		return $absolute ? DPCE_PATH . $file : $file;
	}

	/**
	 * Styles a carousel needs.
	 *
	 * @param string|null $style_id Template id, or null when unknown (editor, preview).
	 * @param string      $engine   "slick" or "swiper".
	 * @return string[] Style handles in load order.
	 */
	public static function get_style_handles( $style_id = null, $engine = 'slick' ) {
		if ( null === $style_id ) {
			return array( 'dpce-slick', 'dpce-frontend', 'dpce-slick-theme', 'swiper', 'dpce-swiper' );
		}

		if ( 'swiper' === $engine ) {
			$handles = array( 'swiper', 'dpce-base', 'dpce-swiper' );
		} else {
			// Same order 1.4 printed: Slick, Slick theme, then the plugin styles.
			$handles = array( 'dpce-slick', 'dpce-slick-theme', 'dpce-base' );
		}

		// Elementor can ask before styles are registered, so check the file.
		if ( file_exists( self::style_file( $style_id ) ) ) {
			$handles[] = self::style_handle( $style_id );
		}

		return $handles;
	}

	/**
	 * Scripts a carousel needs.
	 *
	 * @param string|null $engine "slick", "swiper", or null for both (editor, preview).
	 * @return string[] Script handles.
	 */
	public static function get_script_handles( $engine = null ) {
		if ( 'swiper' === $engine ) {
			return array( 'dpce-swiper' );
		}
		if ( 'slick' === $engine ) {
			return array( 'dpce-slick', 'dpce-frontend' );
		}
		return array( 'dpce-slick', 'dpce-frontend', 'dpce-swiper' );
	}

	/**
	 * Editor only scripts (custom controls).
	 */
	public static function enqueue_editor_scripts() {
		wp_enqueue_script(
			'dpce-editor',
			DPCE_URL . 'assets/js/editor.js',
			array( 'jquery', 'wp-api-fetch', 'wp-url' ),
			DPCE_VERSION,
			true
		);
	}

	/**
	 * Run once after the plugin is updated.
	 *
	 * Elementor caches the list of assets each page needs. Asset handles
	 * changed in 2.0, so clear that cache once, the same way Elementor does
	 * after its own updates.
	 */
	public static function maybe_upgrade() {
		$stored = get_option( self::VERSION_OPTION );
		if ( DPCE_VERSION === $stored ) {
			return;
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		update_option( self::VERSION_OPTION, DPCE_VERSION, false );
	}
}
