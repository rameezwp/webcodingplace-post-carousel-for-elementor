<?php
/**
 * Plugin Name:       Dynamic Post Carousel for Elementor
 * Description:       Displays posts in a beautiful, responsive carousel sliders.
 * Plugin URI:        https://webcodingplace.com/dynamic-post-carousel-for-elementor
 * Version:           1.0
 * Author:            WebCodingPlace
 * Author URI:        https://webcodingplace.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dps-elementor
 * 
 * Requires Plugins: elementor
 * Elementor tested up to: 3.32*
 * Elementor Pro tested up to: 3.27* 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Dynamic Post Slider Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Dynamic_Post_Carousel_Elementor_Plugin {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var Dynamic_Post_Slider_Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return Dynamic_Post_Slider_Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'dps-elementor' );
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		// Check if Elementor is installed and active
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Register Widget
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		
		// Register Widget Scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
	}
	
	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'dps-elementor' ),
			'<strong>' . esc_html__( 'Dynamic Post Slider', 'dps-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'dps-elementor' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Register Widgets
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets($widgets_manager) {
		require_once( __DIR__ . '/widgets/post-carousel-widget.php' );
		$widgets_manager->register( new \Elementor_Post_Carousel_Widget() );
	}
	
	/**
	 * Register Widget Scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {
		// Enqueue Slick Slider CSS
		wp_register_style( 'slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css' );
		
		// Enqueue Slick Slider JS (depends on jQuery)
		wp_register_script( 'slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', [ 'jquery' ], false, true );
		
		// Enqueue our custom JS
		wp_register_script( 'dps-main-js', plugins_url( 'assets/js/main.js', __FILE__ ), [ 'jquery', 'slick-js', 'elementor-frontend' ], self::VERSION, true );
        
        // Enqueue our custom CSS
        wp_register_style('dps-main-css', plugins_url('assets/css/main.css', __FILE__), ['slick-css'], self::VERSION);
	}
}

Dynamic_Post_Carousel_Elementor_Plugin::instance();