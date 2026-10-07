<?php
/**
 * Admin: Getting Started page, activation redirect and plugin links.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Admin
 */
class DPCE_Admin {

	/**
	 * Admin page slug.
	 */
	const PAGE = 'dpce-getting-started';

	/**
	 * Transient set on activation to trigger the one time redirect.
	 */
	const REDIRECT_TRANSIENT = 'dpce_activation_redirect';

	/**
	 * Links used across the admin screens.
	 *
	 * @return array<string, string>
	 */
	public static function links() {
		return array(
			'demos'   => 'https://classicaddons.com/elementor/posts-carousel-slider/',
			'docs'    => 'https://webcodingplace.com/post-carousel-for-elementor',
			'support' => 'https://wordpress.org/support/plugin/webcodingplace-post-carousel-for-elementor/',
			'review'  => 'https://wordpress.org/support/plugin/webcodingplace-post-carousel-for-elementor/reviews/#new-post',
		);
	}

	/**
	 * Hook up.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ), 100 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_redirect' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( DPCE_FILE ), array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	/**
	 * Called on activation: remember the install date and ask for a
	 * one time redirect to the Getting Started page.
	 *
	 * @param bool $network_wide Whether the plugin is network activated.
	 */
	public static function on_activation( $network_wide = false ) {
		add_option( 'dpce_installed_at', time(), '', false );

		if ( ! $network_wide ) {
			set_transient( self::REDIRECT_TRANSIENT, 1, MINUTE_IN_SECONDS );
		}
	}

	/**
	 * Redirect once after a single activation. Never after bulk or network
	 * activation, never during AJAX, and only for people who can use the page.
	 */
	public static function maybe_redirect() {
		if ( ! get_transient( self::REDIRECT_TRANSIENT ) ) {
			return;
		}
		delete_transient( self::REDIRECT_TRANSIENT );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checks whether this was a bulk activation.
		$bulk = isset( $_GET['activate-multi'] );

		if ( $bulk || wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_safe_redirect( self::page_url() );
		exit;
	}

	/**
	 * URL of the Getting Started page.
	 *
	 * @return string
	 */
	public static function page_url() {
		return admin_url( 'admin.php?page=' . self::PAGE );
	}

	/**
	 * Add the page under the Elementor menu.
	 */
	public static function add_page() {
		add_submenu_page(
			'elementor',
			esc_html__( 'Post Carousel: Getting Started', 'webcodingplace-post-carousel-for-elementor' ),
			esc_html__( 'Post Carousel', 'webcodingplace-post-carousel-for-elementor' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Load admin styles and scripts where they are needed.
	 *
	 * @param string $hook_suffix Current admin page.
	 */
	public static function enqueue( $hook_suffix ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$ours   = false !== strpos( (string) $hook_suffix, self::PAGE );

		if ( $ours || 'dashboard' === ( $screen ? $screen->id : '' ) || 'plugins' === ( $screen ? $screen->id : '' ) ) {
			wp_enqueue_style( 'dpce-admin', DPCE_URL . 'assets/css/admin.css', array(), DPCE_VERSION );
			wp_enqueue_script( 'dpce-admin', DPCE_URL . 'assets/js/admin.js', array(), DPCE_VERSION, true );
			wp_localize_script(
				'dpce-admin',
				'dpceAdmin',
				array(
					'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
					'reviewNonce'   => wp_create_nonce( DPCE_Review_Notice::NONCE ),
					'feedbackNonce' => wp_create_nonce( DPCE_Feedback::NONCE ),
					'feedbackOn'    => DPCE_Feedback::is_enabled(),
					'pluginFile'    => plugin_basename( DPCE_FILE ),
				)
			);
		}
	}

	/**
	 * "Getting started" link in the plugin row.
	 *
	 * @param array $links Action links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( self::page_url() ) . '">' . esc_html__( 'Getting started', 'webcodingplace-post-carousel-for-elementor' ) . '</a>' );
		return $links;
	}

	/**
	 * Docs and support links under the plugin description.
	 *
	 * @param array  $meta Row meta.
	 * @param string $file Plugin file.
	 * @return array
	 */
	public static function row_meta( $meta, $file ) {
		if ( plugin_basename( DPCE_FILE ) !== $file ) {
			return $meta;
		}
		$links  = self::links();
		$meta[] = '<a href="' . esc_url( $links['docs'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Docs', 'webcodingplace-post-carousel-for-elementor' ) . '</a>';
		$meta[] = '<a href="' . esc_url( $links['support'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Support', 'webcodingplace-post-carousel-for-elementor' ) . '</a>';
		return $meta;
	}

	/**
	 * Template thumbnails for the gallery.
	 *
	 * @return array<string, array{name: string, image: string}>
	 */
	private static function gallery() {
		$items = array();
		foreach ( DPCE_Styles::all() as $id => $style ) {
			$file = 'assets/images/templates/style-' . sanitize_file_name( (string) $id ) . '.webp';
			if ( ! file_exists( DPCE_PATH . $file ) ) {
				continue;
			}
			$items[ (string) $id ] = array(
				'name'  => isset( $style['name'] ) ? (string) $style['name'] : (string) $id,
				'image' => DPCE_URL . $file,
			);
		}
		return $items;
	}

	/**
	 * Print the Getting Started page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$links   = self::links();
		$used    = (bool) get_option( 'dpce_first_use' );
		$pages   = admin_url( 'edit.php?post_type=page' );
		$is_woo  = class_exists( 'WooCommerce' );
		$gallery = self::gallery();
		?>
		<div class="wrap dpce-admin">
			<header class="dpce-admin__header">
				<h1><?php esc_html_e( 'Post Carousel for Elementor', 'webcodingplace-post-carousel-for-elementor' ); ?></h1>
				<p class="dpce-admin__lead">
					<?php esc_html_e( 'Show your posts, products and custom post types as a carousel, grid or list in Elementor. Everything works with the free version of Elementor.', 'webcodingplace-post-carousel-for-elementor' ); ?>
				</p>
			</header>

			<div class="dpce-admin__grid">
				<section class="dpce-admin__card">
					<h2><?php esc_html_e( 'Getting started', 'webcodingplace-post-carousel-for-elementor' ); ?></h2>
					<ol class="dpce-checklist">
						<li class="is-done"><?php esc_html_e( 'Elementor is active.', 'webcodingplace-post-carousel-for-elementor' ); ?></li>
						<li class="<?php echo $used ? 'is-done' : ''; ?>">
							<?php
							printf(
								/* translators: %s: link to the Pages screen. */
								esc_html__( 'Open a page with Elementor, search the widget panel for "Post Carousel" and drag it in. %s', 'webcodingplace-post-carousel-for-elementor' ),
								'<a href="' . esc_url( $pages ) . '">' . esc_html__( 'Go to Pages', 'webcodingplace-post-carousel-for-elementor' ) . '</a>'
							);
							?>
						</li>
						<li><?php esc_html_e( 'Pick what to show under Post / Content: latest posts, hand picked posts, related posts, or the posts of the current archive.', 'webcodingplace-post-carousel-for-elementor' ); ?></li>
						<li><?php esc_html_e( 'Choose a design in Style > Appearance. The Card template lets you switch each part on or off; 51 more templates are ready to use.', 'webcodingplace-post-carousel-for-elementor' ); ?></li>
						<li><?php esc_html_e( 'Prefer a static layout? Switch Layout to Grid or List in the Slider section.', 'webcodingplace-post-carousel-for-elementor' ); ?></li>
						<?php if ( $is_woo ) : ?>
							<li><?php esc_html_e( 'Selling with WooCommerce? Choose Products as the post type to get prices, ratings, sale badges and add to cart buttons.', 'webcodingplace-post-carousel-for-elementor' ); ?></li>
						<?php endif; ?>
					</ol>
				</section>

				<section class="dpce-admin__card">
					<h2><?php esc_html_e( 'Help and resources', 'webcodingplace-post-carousel-for-elementor' ); ?></h2>
					<ul class="dpce-links">
						<li><a href="<?php echo esc_url( $links['demos'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'See live demos', 'webcodingplace-post-carousel-for-elementor' ); ?></a></li>
						<li><a href="<?php echo esc_url( $links['docs'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read the documentation', 'webcodingplace-post-carousel-for-elementor' ); ?></a></li>
						<li><a href="<?php echo esc_url( $links['support'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Ask a question in the support forum', 'webcodingplace-post-carousel-for-elementor' ); ?></a></li>
					</ul>
					<p class="dpce-admin__muted">
						<?php
						printf(
							/* translators: %s: link to the review form. */
							esc_html__( 'Enjoying the plugin? %s helps other people find it.', 'webcodingplace-post-carousel-for-elementor' ),
							'<a href="' . esc_url( $links['review'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'A short review', 'webcodingplace-post-carousel-for-elementor' ) . '</a>'
						);
						?>
					</p>
				</section>
			</div>

			<?php if ( $gallery ) : ?>
				<section class="dpce-admin__card dpce-gallery-wrap">
					<h2><?php esc_html_e( 'Templates', 'webcodingplace-post-carousel-for-elementor' ); ?></h2>
					<p class="dpce-admin__muted"><?php esc_html_e( 'Pick any of these in Style > Appearance > Template Style. Colors, fonts and spacing can be changed in the widget.', 'webcodingplace-post-carousel-for-elementor' ); ?></p>
					<ul class="dpce-gallery">
						<?php foreach ( $gallery as $item ) : ?>
							<li>
								<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" width="480" height="300" />
								<span><?php echo esc_html( $item['name'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>
		</div>
		<?php
	}
}
