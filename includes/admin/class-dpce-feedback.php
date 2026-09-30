<?php
/**
 * Optional deactivation feedback.
 *
 * Off unless a feedback address is configured: the DPCE_FEEDBACK_ENDPOINT
 * constant is empty by default, so out of the box no form is shown and
 * nothing is sent anywhere. When an address is set, the form appears when
 * the plugin is deactivated. It has a Skip button, and data is only sent
 * when the person presses Submit: the chosen reason, the optional comment
 * and the plugin version. Nothing else.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Feedback
 */
class DPCE_Feedback {

	/**
	 * Nonce action.
	 */
	const NONCE = 'dpce_feedback';

	/**
	 * Hook up.
	 */
	public static function init() {
		add_action( 'wp_ajax_dpce_deactivation_feedback', array( __CLASS__, 'handle' ) );
		add_action( 'admin_footer', array( __CLASS__, 'render_modal' ) );
	}

	/**
	 * Where feedback is sent. Empty means the feature is off.
	 *
	 * @return string
	 */
	public static function endpoint() {
		$endpoint = defined( 'DPCE_FEEDBACK_ENDPOINT' ) ? (string) DPCE_FEEDBACK_ENDPOINT : '';

		/**
		 * Filter the deactivation feedback address. Return an empty string to
		 * turn the feedback form off.
		 *
		 * @since 2.0.0
		 *
		 * @param string $endpoint HTTPS URL, or empty.
		 */
		$endpoint = (string) apply_filters( 'dpce_feedback_endpoint', $endpoint );

		return 0 === strpos( $endpoint, 'https://' ) ? $endpoint : '';
	}

	/**
	 * Whether the feedback form is on.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return '' !== self::endpoint();
	}

	/**
	 * Reasons offered in the form.
	 *
	 * @return array<string, string>
	 */
	public static function reasons() {
		return array(
			'temporary'   => __( 'I am only deactivating it for a moment', 'webcodingplace-post-carousel-for-elementor' ),
			'not_working' => __( 'It did not work on my site', 'webcodingplace-post-carousel-for-elementor' ),
			'missing'     => __( 'A feature I need is missing', 'webcodingplace-post-carousel-for-elementor' ),
			'other'       => __( 'I found another plugin', 'webcodingplace-post-carousel-for-elementor' ),
			'no_need'     => __( 'I no longer need a carousel', 'webcodingplace-post-carousel-for-elementor' ),
		);
	}

	/**
	 * Print the form on the Plugins screen (hidden until Deactivate is clicked).
	 */
	public static function render_modal() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'plugins' !== $screen->id || ! self::is_enabled() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		?>
		<div class="dpce-feedback" hidden>
			<div class="dpce-feedback__dialog" role="dialog" aria-modal="true" aria-labelledby="dpce-feedback-title">
				<h2 id="dpce-feedback-title"><?php esc_html_e( 'Quick question before you go', 'webcodingplace-post-carousel-for-elementor' ); ?></h2>
				<p><?php esc_html_e( 'Why are you deactivating Post Carousel for Elementor? This is optional. Nothing is sent if you skip.', 'webcodingplace-post-carousel-for-elementor' ); ?></p>
				<form>
					<fieldset>
						<legend class="screen-reader-text"><?php esc_html_e( 'Reason', 'webcodingplace-post-carousel-for-elementor' ); ?></legend>
						<?php foreach ( self::reasons() as $key => $label ) : ?>
							<label><input type="radio" name="reason" value="<?php echo esc_attr( $key ); ?>" /> <?php echo esc_html( $label ); ?></label>
						<?php endforeach; ?>
					</fieldset>
					<label for="dpce-feedback-details"><?php esc_html_e( 'Anything else? (optional)', 'webcodingplace-post-carousel-for-elementor' ); ?></label>
					<textarea id="dpce-feedback-details" name="details" rows="3" maxlength="1000"></textarea>
					<p class="dpce-feedback__note"><?php esc_html_e( 'If you submit, we receive your answer, your comment and the plugin version. No personal data, no site address.', 'webcodingplace-post-carousel-for-elementor' ); ?></p>
					<div class="dpce-feedback__actions">
						<button type="button" class="button" data-dpce-feedback="skip"><?php esc_html_e( 'Skip and deactivate', 'webcodingplace-post-carousel-for-elementor' ); ?></button>
						<button type="submit" class="button button-primary" data-dpce-feedback="submit"><?php esc_html_e( 'Submit and deactivate', 'webcodingplace-post-carousel-for-elementor' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Send one piece of feedback (only when the person pressed Submit).
	 */
	public static function handle() {
		check_ajax_referer( self::NONCE, 'nonce' );
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( null, 403 );
		}

		$endpoint = self::endpoint();
		if ( '' === $endpoint ) {
			wp_send_json_success();
		}

		$reason  = isset( $_POST['reason'] ) ? sanitize_key( wp_unslash( $_POST['reason'] ) ) : '';
		$details = isset( $_POST['details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['details'] ) ) : '';

		if ( ! array_key_exists( $reason, self::reasons() ) && '' === $details ) {
			wp_send_json_success();
		}

		wp_remote_post(
			$endpoint,
			array(
				'timeout'  => 3,
				'blocking' => false,
				'body'     => array(
					'plugin'  => 'webcodingplace-post-carousel-for-elementor',
					'version' => DPCE_VERSION,
					'reason'  => array_key_exists( $reason, self::reasons() ) ? $reason : '',
					'details' => function_exists( 'mb_substr' ) ? mb_substr( $details, 0, 1000 ) : substr( $details, 0, 1000 ),
				),
			)
		);

		wp_send_json_success();
	}
}
