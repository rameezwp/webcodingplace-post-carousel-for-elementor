<?php
/**
 * Plugin Name:       WebCodingPlace Post Carousel for Elementor
 * Description:       Show posts, WooCommerce products and custom post types in a responsive carousel widget for Elementor, with 51 ready made templates.
 * Plugin URI:        https://webcodingplace.com/post-carousel-for-elementor
 * Version:           1.4
 * Author:            WebCodingPlace
 * Author URI:        https://webcodingplace.com/
 * License:           GPL-2.0-or-later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webcodingplace-post-carousel-for-elementor
 * Domain Path:       /languages
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Elementor tested up to: 4.0
 * Elementor Pro tested up to: 3.27
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DPCE_VERSION' ) ) {
	define( 'DPCE_VERSION', '1.4' );
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
	define( 'DPCE_MIN_ELEMENTOR_VERSION', '3.18.0' );
}
if ( ! defined( 'DPCE_MIN_PHP_VERSION' ) ) {
	define( 'DPCE_MIN_PHP_VERSION', '7.4' );
}

/*
 * Optional deactivation feedback. Empty by default: no feedback form is
 * shown and nothing is sent. Set an HTTPS URL here (or through the
 * dpce_feedback_endpoint filter) to turn it on, and describe it in the
 * readme's External services section when you do.
 */
if ( ! defined( 'DPCE_FEEDBACK_ENDPOINT' ) ) {
	define( 'DPCE_FEEDBACK_ENDPOINT', '' );
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
		if ( version_compare( PHP_VERSION, DPCE_MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php' ) );
			return;
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
			return;
		}

		if ( ! defined( 'ELEMENTOR_VERSION' ) || ! version_compare( ELEMENTOR_VERSION, DPCE_MIN_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor' ) );
			return;
		}

		$this->includes();

		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );
	}

	/**
	 * Load plugin includes.
	 */
	private function includes() {
		require_once DPCE_PATH . 'includes/helpers.php';
		require_once DPCE_PATH . 'includes/class-dpce-styles.php';
		require_once DPCE_PATH . 'includes/class-dpce-renderer.php';
		require_once DPCE_PATH . 'includes/class-dpce-query.php';
		require_once DPCE_PATH . 'includes/class-dpce-assets.php';
		require_once DPCE_PATH . 'includes/class-dpce-rest.php';
		require_once DPCE_PATH . 'includes/controls/class-dpce-query-control.php';
		require_once DPCE_PATH . 'includes/widget/trait-dpce-query-controls.php';
		require_once DPCE_PATH . 'includes/widget/trait-dpce-card-controls.php';
		require_once DPCE_PATH . 'includes/widget/trait-dpce-layout-controls.php';
		require_once DPCE_PATH . 'includes/class-dpce-woo.php';

		// Boot renderer hooks.
		DPCE_Renderer::instance();
		DPCE_Assets::init();
		DPCE_Rest::init();

		if ( is_admin() ) {
			require_once DPCE_PATH . 'includes/admin/class-dpce-admin.php';
			require_once DPCE_PATH . 'includes/admin/class-dpce-review-notice.php';
			require_once DPCE_PATH . 'includes/admin/class-dpce-feedback.php';
			DPCE_Admin::init();
			DPCE_Review_Notice::init();
			DPCE_Feedback::init();
		}
	}

	/**
	 * Register custom Elementor controls.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Controls manager.
	 */
	public function register_controls( $controls_manager ) {
		$controls_manager->register( new DPCE_Query_Control() );
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
	 * Admin notice: Elementor missing.
	 */
	public function admin_notice_missing_elementor() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

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
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

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
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

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

register_activation_hook(
	__FILE__,
	static function ( $network_wide = false ) {
		require_once DPCE_PATH . 'includes/admin/class-dpce-admin.php';
		DPCE_Admin::on_activation( (bool) $network_wide );
	}
);
