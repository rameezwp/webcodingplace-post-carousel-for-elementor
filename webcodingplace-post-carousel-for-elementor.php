<?php
/**
 * Plugin Name:       WebCodingPlace Post Carousel for Elementor
 * Description:       Display posts, custom post types or taxonomy terms in a beautiful, fully responsive Slick-powered carousel widget for Elementor with 50+ ready-made templates.
 * Plugin URI:        https://webcodingplace.com/webcodingplace-post-carousel-for-elementor
 * Version:           1.0
 * Author:            WebCodingPlace
 * Author URI:        https://webcodingplace.com/
 * License:           GPL-2.0-or-later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webcodingplace-post-carousel-for-elementor
 * Domain Path:       /languages
 * Requires at least: 5.6
 * Requires PHP:      7.0
 * Requires Plugins:  elementor
 * Elementor tested up to: 3.32
 * Elementor Pro tested up to: 3.27
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DPCE_VERSION' ) ) {
	define( 'DPCE_VERSION', '1.0' );
}
if ( ! defined( 'DPCE_FILE' ) ) {
	define( 'DPCE_FILE', __FILE__ );
}
if ( ! defined( 'DPCE_PATH' ) ) {
	define( 'DPCE_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'DPCE_URL' ) ) {
	define( 'DPCE_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'DPCE_TEMPLATES_PATH' ) ) {
	define( 'DPCE_TEMPLATES_PATH', DPCE_PATH . 'templates/' );
}
if ( ! defined( 'DPCE_MIN_ELEMENTOR_VERSION' ) ) {
	define( 'DPCE_MIN_ELEMENTOR_VERSION', '3.0.0' );
}
if ( ! defined( 'DPCE_MIN_PHP_VERSION' ) ) {
	define( 'DPCE_MIN_PHP_VERSION', '7.0' );
}

/**
 * Main plugin bootstrap.
 *
 * @since 1.0.0
 */
final class DPCE_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var DPCE_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return DPCE_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
			return;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, DPCE_MIN_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor' ) );
			return;
		}

		if ( version_compare( PHP_VERSION, DPCE_MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php' ) );
			return;
		}

		$this->includes();

		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ) );
	}

	/**
	 * Load plugin includes.
	 */
	private function includes() {
		require_once DPCE_PATH . 'includes/helpers.php';
		require_once DPCE_PATH . 'includes/class-dpce-styles.php';
		require_once DPCE_PATH . 'includes/class-dpce-renderer.php';
		require_once DPCE_PATH . 'includes/class-dpce-query.php';

		// Boot renderer hooks.
		DPCE_Renderer::instance();
	}

	/**
	 * Register the Elementor widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once DPCE_PATH . 'widgets/post-carousel-widget.php';
		$widgets_manager->register( new \DPCE_Post_Carousel_Widget() );
	}

	/**
	 * Register frontend scripts.
	 */
	public function register_scripts() {
		$slick_js = DPCE_PATH . 'assets/vendor/slick/slick.min.js';
		$slick_url = DPCE_URL . 'assets/vendor/slick/slick.min.js';

		// Only register slick if the vendor file is present (required for wordpress.org).
		if ( file_exists( $slick_js ) ) {
			wp_register_script( 'dpce-slick', $slick_url, array( 'jquery' ), '1.8.1', true );
		}

		wp_register_script(
			'dpce-frontend',
			DPCE_URL . 'assets/js/main.js',
			array( 'jquery', 'dpce-slick' ),
			DPCE_VERSION,
			true
		);
	}

	/**
	 * Register frontend styles.
	 */
	public function register_styles() {
		$slick_css = DPCE_PATH . 'assets/vendor/slick/slick.css';
		$slick_url = DPCE_URL . 'assets/vendor/slick/slick.css';

		if ( file_exists( $slick_css ) ) {
			wp_register_style( 'dpce-slick', $slick_url, array(), '1.8.1' );
		}

		$slick_theme = DPCE_PATH . 'assets/vendor/slick/slick-theme.css';
		$slick_theme_url = DPCE_URL . 'assets/vendor/slick/slick-theme.css';

		if ( file_exists( $slick_theme ) ) {
			wp_register_style( 'dpce-slick-theme', $slick_theme_url, array( 'dpce-slick' ), '1.8.1' );
		}

		wp_register_style(
			'dpce-frontend',
			DPCE_URL . 'assets/css/main.css',
			array( 'dpce-slick' ),
			DPCE_VERSION
		);
	}

	/**
	 * Admin notice: Elementor missing.
	 */
	public function admin_notice_missing_elementor() {
		$message = sprintf(
			/* translators: 1: plugin name, 2: required plugin name */
			esc_html__( '"%1$s" requires "%2$s" to be installed and active.', 'webcodingplace-post-carousel-for-elementor' ),
			'<strong>' . esc_html__( 'WebCodingPlace Post Carousel for Elementor', 'webcodingplace-post-carousel-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'webcodingplace-post-carousel-for-elementor' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice: Elementor below minimum version.
	 */
	public function admin_notice_minimum_elementor() {
		$message = sprintf(
			/* translators: 1: plugin name, 2: required plugin name, 3: minimum version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'webcodingplace-post-carousel-for-elementor' ),
			'<strong>' . esc_html__( 'WebCodingPlace Post Carousel for Elementor', 'webcodingplace-post-carousel-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'webcodingplace-post-carousel-for-elementor' ) . '</strong>',
			DPCE_MIN_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice: PHP below minimum version.
	 */
	public function admin_notice_minimum_php() {
		$message = sprintf(
			/* translators: 1: plugin name, 2: required PHP version */
			esc_html__( '"%1$s" requires PHP version %2$s or greater.', 'webcodingplace-post-carousel-for-elementor' ),
			'<strong>' . esc_html__( 'WebCodingPlace Post Carousel for Elementor', 'webcodingplace-post-carousel-for-elementor' ) . '</strong>',
			DPCE_MIN_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}
}

DPCE_Plugin::instance();
