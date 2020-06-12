<?php

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
 * Class for comment mention methods.
 *
 * @package Comment_Mention
 */
class CommentMentionBBPress {

	/**
	 * Cunstructor for bbpress class.
	 */
	public function __construct() {

		// Add setting for enable/disable user mention for bb-press.
		add_filter( 'bbp_admin_get_settings_fields', array( $this, 'cmt_mntn_enable_bbpress_mention' ) );

		// Set default option for enable user mention for bb-press.
		add_filter( 'bbp_get_default_options', array( $this, 'cmt_mntn_enable_bbpress_mention_option' ) );
	}

	/**
	 * Add setting for enable/disable user mention for bbpress.
	 *
	 * @param  array $settings Array of settings.
	 * @return array           Updated array of settings.
	 */
	public function cmt_mntn_enable_bbpress_mention( $settings ) {

		$settings['bbp_settings_features']['_bbp_enable_user_mention'] = array(
			'title'             => esc_html__( 'Enable User Mention', 'comment-mention' ),
			'callback'          => array( $this, 'cmt_mntn_admin_setting_callback_enable_user_mention' ),
			'args'              => array(),
			'sanitize_callback' => 'intval',
		);

		return $settings;

	}

	/**
	 * Use the WordPress editor setting field
	 *
	 * @since 2.1.0 bbPress (r3586)
	 */
	public function cmt_mntn_admin_setting_callback_enable_user_mention() {

		// Bail, if anything goes wrong.
		if ( ! function_exists( 'bbp_maybe_admin_setting_disabled' ) ) {
			return;
		}

		?>

		<input name="_bbp_enable_user_mention" id="_bbp_enable_user_mention" type="checkbox" value="1"
		<?php
		checked( cmt_mntn_enable_bbp_user_mention( true ) );
		bbp_maybe_admin_setting_disabled( '_bbp_enable_user_mention' );
		?>
		 />
		<label for="_bbp_enable_user_mention"><?php esc_html_e( 'Enable User Mention in Topics & Replies Content', 'comment-mention' ); ?></label>

		<?php
	}

	/**
	 * Set default value for enable/disable user mention for bbpress.
	 *
	 * @param  array $options Default options.
	 *
	 * @return array          Updated array of default options.
	 */
	public function cmt_mntn_enable_bbpress_mention_option( $options ) {

		$options['_bbp_enable_user_mention'] = true;

		return $options;
	}

}

new CommentMentionBBPress();
