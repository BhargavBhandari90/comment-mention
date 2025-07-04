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
 * Class for comment mention methods.
 *
 * @package Comment_Mention
 */
class CommentMentionBBPress {

	/**
	 * Plugin Settings.
	 *
	 * @var array
	 */
	public $cmt_mntn_settings;

	/**
	 * Cunstructor for bbpress class.
	 */
	public function __construct() {

		// If bbpress is not activate, then don't execute this class.
		if ( ! class_exists( 'bbPress' ) ) {
			return;
		}

		// Add setting for enable/disable user mention for bb-press.
		add_filter( 'bbp_admin_get_settings_fields', array( $this, 'cmt_mntn_enable_bbpress_mention' ) );

		// Set default option for enable user mention for bb-press.
		add_filter( 'bbp_get_default_options', array( $this, 'cmt_mntn_enable_bbpress_mention_option' ) );

		// If enabled user mention, then send email.
		if ( cmt_mntn_enable_bbp_user_mention() ) {

			// Send email to mentioned user in topic & reply.
			add_filter( 'bbp_new_topic', array( $this, 'cmt_mntn_bbpress_mention_user_email' ) );
			add_filter( 'bbp_new_reply', array( $this, 'cmt_mntn_bbpress_mention_user_email' ) );

		}

		add_filter( 'bbp_kses_allowed_tags', array( $this, 'cmt_mntn_bbp_kses_allowed_tags' ) );

		if ( ! class_exists( 'CommentMentionMainPro' ) ) {
			remove_filter( 'bbp_make_clickable', 'bbp_make_mentions_clickable', 8 );
			add_filter( 'bbp_make_clickable', array( $this, 'cmt_mntn_make_mentions_clickable_nopro' ), 8 );
		}

		// Get plugin settings.
		$this->cmt_mntn_settings = get_option( 'cmt_mntn_settings' );
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

	/**
	 * Send email to mentioned user.
	 *
	 * @param  integer $post_id Topic ID.
	 * @return void
	 */
	public function cmt_mntn_bbpress_mention_user_email( $post_id ) {

		// Bail, if anything goes wrong.
		if ( empty( $post_id ) ) {
			return;
		}

		// Get email enabled setting.
		$is_send_email_enabled = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_enable'] )
			? $this->cmt_mntn_settings['cmt_mntn_email_enable']
			: false;

		// If emails not enabled, then abort.
		if ( ! $is_send_email_enabled ) {
			return;
		}

		// Get topic content.
		$topic_content = apply_filters( 'cmt_mntn_bbp_post_content', get_post_field( 'post_content', $post_id ) );

		// Prevention.
		if ( empty( $topic_content ) ) {
			return;
		}

		// Check if there are mentions in the topic content.
		$usernames = CommentMentionMain::cmt_mntn_find_mentions( $topic_content );

		// If no user mention, then don't do anything.
		if ( empty( $usernames ) || ! is_array( $usernames ) ) {
			return;
		}

		// Iterate the username loop.
		foreach ( $usernames as $username ) {

			// Get user id.
			$uid = username_exists( $username );

			if ( $uid ) {

				// Get user data.
				$cmt_mntn_user_data    = get_user_by( 'id', $uid );
				$cmt_mntn_mail_setting = cmt_mntn_mail_setting( $uid, $post_id );

				// Get email body.
				$cmt_mntn_mail_body = $cmt_mntn_mail_setting['email_content'];
				$cmt_mntn_mail_sub  = $cmt_mntn_mail_setting['email_subject'];

				// Set headers.
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				// Set mail as HTML format.
				add_filter(
					'wp_mail_content_type',
					function () {
						return 'text/html';
					}
				);

				// Send mail.
				wp_mail(
					esc_html( $cmt_mntn_user_data->user_email ),
					stripslashes( html_entity_decode( esc_html( $cmt_mntn_mail_sub ), ENT_QUOTES, 'UTF-8' ) ),
					wp_kses_post( $cmt_mntn_mail_body ),
					$headers
				);

			}
		}
	}

	/**
	 * Allow html tag for mentioned user.
	 *
	 * @param array $allowed_tags Array of allowd tags.
	 * @return array              Updated array of allowed tags.
	 */
	public function cmt_mntn_bbp_kses_allowed_tags( $allowed_tags ) {

		$allowed_tags['span'] = array(
			'class'               => array(),
			'data-atwho-at-query' => array(),
		);

		return $allowed_tags;
	}

	/**
	 * Make mentiones clickabke.
	 *
	 * @param string $text Content text.
	 * @return string
	 */
	public function cmt_mntn_make_mentions_clickable_nopro( $text = '' ) {
		return preg_replace_callback( '/\B@[a-zA-Z0-9._@]+/', array( $this, 'cmt_mntn_make_mentions_clickable_nopro_callback' ), $text );
	}

	/**
	 * Callback to convert mention matches to HTML A tag.
	 *
	 * @param array $matches Regular expression matches in the current text blob.
	 *
	 * @return string Original text if no user exists, or link to user profile.
	 */
	public function cmt_mntn_make_mentions_clickable_nopro_callback( $matches = array() ) {

		// Bail if the match is empty malformed.
		if ( empty( $matches[0] ) || ! is_string( $matches[0] ) ) {
			return $matches[0];
		}

		$username = ltrim( $matches[0], '@' );

		// Get user; bail if not found.
		$user = get_user_by( 'login', $username );

		if ( empty( $user ) || bbp_is_user_inactive( $user->ID ) ) {
			return $matches[0];
		}

		// Default anchor classes.
		$classes = array(
			'bbp-user-mention',
			'bbp-user-id-' . absint( $user->ID ),
		);

		// Filter classes.
		$classes = (array) apply_filters( 'bbp_make_mentions_clickable_classes', $classes, $user );

		// Escape & implode if not empty, otherwise an empty string.
		$class_str = ! empty( $classes )
			? implode( ' ', array_map( 'sanitize_html_class', $classes ) )
			: '';

		// Setup as a variable to avoid a potentially empty class attribute.
		$class = ! empty( $class_str )
			? ' class="' . esc_attr( $class_str ) . '"'
			: '';

		// Create the link to the user's profile.
		$html   = '<a href="%1$s"' . $class . '>%2$s</a>';
		$url    = bbp_get_user_profile_url( $user->ID );
		$anchor = sprintf( $html, esc_url( $url ), esc_html( $matches[0] ) );

		// Prevent this link from being followed by bots.
		$link = bbp_rel_nofollow( $anchor );

		// Concatenate the matches into the return value.
		$retval = $link;

		// Return the link.
		return $retval;
	}
}

new CommentMentionBBPress();
