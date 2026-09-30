<?php
/**
 * Review request notice.
 *
 * Shown only to administrators, only on the Dashboard or the plugin's own
 * page, only after the plugin has been installed for 7 days and a carousel
 * has been used at least once. Each person can snooze it for 30 days or
 * close it for good.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Review_Notice
 */
class DPCE_Review_Notice {

	/**
	 * Nonce action.
	 */
	const NONCE = 'dpce_review';

	/**
	 * User meta key: "done" or a timestamp until which the notice is snoozed.
	 */
	const META = 'dpce_review_state';

	/**
	 * Hook up.
	 */
	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'render' ) );
		add_action( 'admin_post_dpce_review', array( __CLASS__, 'handle_link' ) );
		add_action( 'wp_ajax_dpce_review', array( __CLASS__, 'handle_ajax' ) );
	}

	/**
	 * Whether the notice should show for the current person on this screen.
	 *
	 * @return bool
	 */
	public static function should_show() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$id     = $screen ? (string) $screen->id : '';
		if ( 'dashboard' !== $id && false === strpos( $id, DPCE_Admin::PAGE ) ) {
			return false;
		}

		$installed = (int) get_option( 'dpce_installed_at', 0 );
		if ( ! $installed || ( time() - $installed ) < 7 * DAY_IN_SECONDS || ! get_option( 'dpce_first_use' ) ) {
			return false;
		}

		$state = get_user_meta( get_current_user_id(), self::META, true );
		if ( 'done' === $state ) {
			return false;
		}

		return ! ( is_numeric( $state ) && time() < (int) $state );
	}

	/**
	 * Print the notice.
	 */
	public static function render() {
		if ( ! self::should_show() ) {
			return;
		}

		$url = static function ( $choice ) {
			return wp_nonce_url( admin_url( 'admin-post.php?action=dpce_review&choice=' . $choice ), self::NONCE );
		};
		?>
		<div class="notice notice-info is-dismissible dpce-review-notice">
			<p>
				<?php esc_html_e( 'Thanks for using Post Carousel for Elementor! If it has been useful, would you leave a short review on WordPress.org? It helps other people find the plugin and keeps it free.', 'webcodingplace-post-carousel-for-elementor' ); ?>
			</p>
			<p class="dpce-review-notice__actions">
				<a class="button button-primary" href="<?php echo esc_url( $url( 'review' ) ); ?>" target="_blank" rel="noopener noreferrer" data-dpce-review="review"><?php esc_html_e( 'Leave a review', 'webcodingplace-post-carousel-for-elementor' ); ?></a>
				<a class="button" href="<?php echo esc_url( $url( 'later' ) ); ?>" data-dpce-review="later"><?php esc_html_e( 'Maybe later', 'webcodingplace-post-carousel-for-elementor' ); ?></a>
				<a class="button-link" href="<?php echo esc_url( $url( 'done' ) ); ?>" data-dpce-review="done"><?php esc_html_e( 'I already did', 'webcodingplace-post-carousel-for-elementor' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Save a choice for the current person.
	 *
	 * @param string $choice review, later or done.
	 */
	private static function save( $choice ) {
		$value = 'later' === $choice ? time() + 30 * DAY_IN_SECONDS : 'done';
		update_user_meta( get_current_user_id(), self::META, $value );
	}

	/**
	 * Clean the choice from the request.
	 *
	 * @return string
	 */
	private static function choice_from_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- Nonce is checked by the callers.
		$choice = isset( $_REQUEST['choice'] ) ? sanitize_key( wp_unslash( $_REQUEST['choice'] ) ) : '';
		return in_array( $choice, array( 'review', 'later', 'done' ), true ) ? $choice : 'later';
	}

	/**
	 * Links (work without JavaScript).
	 */
	public static function handle_link() {
		check_admin_referer( self::NONCE );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'webcodingplace-post-carousel-for-elementor' ) );
		}

		$choice = self::choice_from_request();
		self::save( $choice );

		if ( 'review' === $choice ) {
			add_filter(
				'allowed_redirect_hosts',
				static function ( $hosts ) {
					$hosts[] = 'wordpress.org';
					return $hosts;
				}
			);
			$links = DPCE_Admin::links();
			wp_safe_redirect( $links['review'] );
			exit;
		}

		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
		exit;
	}

	/**
	 * Buttons and the close icon (JavaScript).
	 */
	public static function handle_ajax() {
		check_ajax_referer( self::NONCE, 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( null, 403 );
		}
		self::save( self::choice_from_request() );
		wp_send_json_success();
	}
}
