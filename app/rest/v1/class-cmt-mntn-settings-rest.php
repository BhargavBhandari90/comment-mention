<?php // phpcs:ignore
/**
 * Functions of Comment Mention functions.
 *
 * @package Comment_Mention
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Controller for Settings.
 *
 * @package Comment_Mention
 */
class CmtMentionSettingsRest extends WP_REST_Controller {

	/**
	 * Cunstructor for admin class.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'cmt_mntn_register_rest_routes' ) );
	}

	/**
	 * Register the settings REST endpoint.
	 */
	public function cmt_mntn_register_rest_routes() {

		register_rest_route(
			'cmt-mntn/v1',
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'rest_get_settings' ),
					'permission_callback' => array( $this, 'rest_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_save_settings' ),
					'permission_callback' => array( $this, 'rest_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Permission check — admin only.
	 *
	 * @return bool|WP_Error
	 */
	public function rest_permissions_check() {
		return current_user_can( 'manage_options' ) ? true : new WP_Error(
			'rest_forbidden',
			esc_html__( 'You do not have permission to manage these settings.', 'comment-mention' ),
			array( 'status' => 403 )
		);
	}

	/**
	 * GET /cmt-mntn/v1/settings
	 *
	 * @return WP_REST_Response
	 */
	public function rest_get_settings() {
		return rest_ensure_response( get_option( 'cmt_mntn_settings', array() ) );
	}

	/**
	 * POST /cmt-mntn/v1/settings
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public function rest_save_settings( WP_REST_Request $request ) {

		$raw = $request->get_json_params();

		$sanitized = $this->cmt_mntn_sanitize_settings( $raw );

		/**
		 * Filter: let Pro (and other add-ons) sanitize their own keys.
		 * Each add-on should sanitize only the keys it owns and return
		 * the full $settings array.
		 *
		 * @param array $sanitized  Sanitized settings array.
		 * @param array $raw        Raw input from the request.
		 */
		$sanitized = apply_filters( 'cmt_mntn_sanitize_settings', $sanitized, $raw );

		update_option( 'cmt_mntn_settings', $sanitized );

		return rest_ensure_response( $sanitized );
	}

	/**
	 * Sanitize known free-plugin settings keys.
	 *
	 * @param array $raw Raw input.
	 * @return array
	 */
	private function cmt_mntn_sanitize_settings( array $raw ) {

		$sanitized = array();

		if ( isset( $raw['cmt_mntn_email_enable'] ) ) {
			$sanitized['cmt_mntn_email_enable'] = (int) $raw['cmt_mntn_email_enable'];
		}

		if ( ! empty( $raw['cmt_mntn_email_subject'] ) ) {
			$sanitized['cmt_mntn_email_subject'] = sanitize_text_field( $raw['cmt_mntn_email_subject'] );
		}

		if ( ! empty( $raw['cmt_mntn_mail_content'] ) ) {
			$sanitized['cmt_mntn_mail_content'] = wp_kses_post( $raw['cmt_mntn_mail_content'] );
		}

		if ( isset( $raw['cmt_mntn_enabled_user_roles'] ) && is_array( $raw['cmt_mntn_enabled_user_roles'] ) ) {
			$sanitized['cmt_mntn_enabled_user_roles'] = array_map( 'sanitize_key', $raw['cmt_mntn_enabled_user_roles'] );
		}

		if ( isset( $raw['cmt_mntn_disabled_mention_user_roles'] ) && is_array( $raw['cmt_mntn_disabled_mention_user_roles'] ) ) {
			$sanitized['cmt_mntn_disabled_mention_user_roles'] = array_map( 'sanitize_key', $raw['cmt_mntn_disabled_mention_user_roles'] );
		}

		if ( isset( $raw['cmt_mntn_enable_avatar'] ) ) {
			$sanitized['cmt_mntn_enable_avatar'] = (int) $raw['cmt_mntn_enable_avatar'];
		}

		return $sanitized;
	}
}

new CmtMentionSettingsRest();
