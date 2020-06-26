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
class CommentMentionMain {

	/**
	 * Cunstructor for admin class.
	 */
	public function __construct() {

		// Enqueue script
		add_action( 'wp_enqueue_scripts', array( $this, 'cmt_mntn_enqueue_styles_script' ) );

		// Ger users.
		add_action( 'wp_ajax_cmt_mntn_get_users', array( $this, 'cmt_mntn_ajax_get_users' ) );

		// Modify comment content.
		add_filter( 'pre_comment_content', array( $this, 'cmt_mntn_at_name_filter' ) );

		// Process comment.
		add_action( 'comment_post', array( $this, 'cmt_mntn_preprocess_comment' ), 10, 3 );

		// Get plugin settings.
		$this->cmt_mntn_settings = get_option( 'cmt_mntn_settings' );

	}

	/**
	 * Add scripts and styles.
	 */
	public function cmt_mntn_enqueue_styles_script() {

		if ( ! is_single() ) {
			return;
		}

		$bbpress_post_types = array(
			'reply' => true,
			'topic' => true,
		);

		// If bbpress pages and mention is not enable, then don't include the scripts and styles.
		if ( isset( $bbpress_post_types[ get_post_type() ] ) &&
			 ! cmt_mntn_enable_bbp_user_mention() ) {
			return;
		}

		// Set ajax URL.
		wp_localize_script(
			'jquery',
			'ajax',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
			)
		);

		// Atwho CSS.
		// Ref: https://github.com/ichord/At.js/
		wp_enqueue_style(
			'cmt-mntn-atwho-css',
			CMT_MNTN_URL . 'app/assets/css/jquery.atwho.css',
			array(),
			CMT_MNTN_VERSION
		);

		// caret CSS.
		// Ref: https://github.com/ichord/At.js/
		wp_enqueue_script(
			'cmt-mntn-caret',
			CMT_MNTN_URL . 'app/assets/js/jquery.caret.js',
			array( 'jquery' ),
			CMT_MNTN_VERSION,
			true
		);

		// Atwho JS.
		// Ref: https://github.com/ichord/At.js/
		wp_enqueue_script(
			'cmt-mntn-atwho',
			CMT_MNTN_URL . 'app/assets/js/jquery.atwho.js',
			array( 'cmt-mntn-caret' ),
			CMT_MNTN_VERSION,
			true
		);

		// Plugin script.
		wp_enqueue_script(
			'cmt-mntn-mentions',
			CMT_MNTN_URL . 'app/assets/js/mentions.js',
			array( 'cmt-mntn-caret', 'cmt-mntn-atwho' ),
			CMT_MNTN_VERSION,
			true
		);

	}

	/**
	 * Get usernames.
	 */
	public function cmt_mntn_ajax_get_users() {

		// Set arguments.
		$args = array(
			'term' => sanitize_text_field( $_GET['term'] ),
		);

		// Get usernames.
		$results = $this->cmt_mntn_get_users( $args['term'] );

		// Send response as json.
		if ( is_wp_error( $results ) ) {
			wp_send_json_error( $results->get_error_message() );
			exit;
		}

		wp_send_json_success( $results );
	}

	/**
	 * Get usernames from DB.
	 *
	 * @param  string $username Username text.
	 * @return object           Object of usernames.
	 */
	public function cmt_mntn_get_users( $username ) {

		global $wpdb;

		// Get results from DB.
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_login as name FROM $wpdb->users WHERE user_login LIKE %s",
				$username . '%'
			)
		);

		return $results;

	}

	/**
	 * Find and link @-mentioned users in the contents of a given item.
	 */
	public function cmt_mntn_at_name_filter( $content ) {

		// Try to find mentions.
		$usernames = $this->cmt_mntn_find_mentions( $content );

		// If no mentions found, then halt the process.
		if ( empty( $usernames ) ) {
			return $content;
		}

		// We don't want to link @mentions that are inside of links, so we
		// temporarily remove them.
		$replace_count = 0;
		$replacements  = array();
		foreach ( $usernames as $username ) {
			// Prevent @ name linking inside <a> tags.
			preg_match_all( '/(<a.*?(?!<\/a>)@' . $username . '.*?<\/a>)/', $content, $content_matches );
			if ( ! empty( $content_matches[1] ) ) {
				foreach ( $content_matches[1] as $replacement ) {
					$replacements[ '#BPAN' . $replace_count ] = $replacement;
					$content                                  = str_replace( $replacement, '#BPAN' . $replace_count, $content );
					$replace_count++;
				}
			}
		}

		// Linkify the mentions with the username.
		foreach ( (array) $usernames as $user_id => $username ) {
			$author_url = get_author_posts_url( $user_id );
			$content    = preg_replace( '/(@' . $username . '\b)/', "<a class='comment-mention' href='$author_url' rel='nofollow'>@$username</a>", $content );
		}

		// Put everything back.
		if ( ! empty( $replacements ) ) {
			foreach ( $replacements as $placeholder => $original ) {
				$content = str_replace( $placeholder, $original, $content );
			}
		}

		// Return the content.
		return $content;
	}

	/**
	 * Locate usernames in an comment content string, as designated by an @ sign.
	 */
	public static function cmt_mntn_find_mentions( $content ) {

		$pattern = '/[@]+([A-Za-z0-9-_\.@]+)\b/';
		preg_match_all( $pattern, $content, $usernames );

		// Make sure there's only one instance of each username.
		$usernames = array_unique( $usernames[1] );

		// Bail if no usernames.
		if ( empty( $usernames ) ) {
			return false;
		}

		$mentioned_users = array();

		// We've found some mentions! Check to see if users exist.
		foreach ( (array) array_values( $usernames ) as $username ) {

			// Get user info by login.
			$user_obj = get_user_by( 'login', $username );

			if ( empty( $user_obj ) ) {
				continue;
			}

			$user_id = intval( $user_obj->ID );

			// The user ID exists, so let's add it to our array.
			if ( ! empty( $user_id ) ) {
				$mentioned_users[ $user_id ] = $username;
			}
		}

		if ( empty( $mentioned_users ) ) {
			return false;
		}

		return $mentioned_users;
	}

	/**
	 * Send email notifications to mentioned users.
	 *
	 * @param  int    $comment_ID        Current comment id.
	 * @param  string $comment_status Comment status.
	 * @param  obj    $comment_data      Comment data.
	 * @return void
	 */
	public function cmt_mntn_preprocess_comment( $comment_ID, $comment_status, $comment_data ) {

		$is_send_email_enabled = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_enable'] ) ? $this->cmt_mntn_settings['cmt_mntn_email_enable'] : false;

		if ( ! $is_send_email_enabled ) {
			return;
		}

		// Get content.
		$content = $comment_data['comment_content'];

		// Add data to array for post use.
		$comment_data['comment_ID'] = $comment_ID;

		// Get usernames from comment content.
		$usernames = $this->cmt_mntn_find_mentions( $content );

		// Iterate the username loop.
		foreach ( $usernames as $username ) {

			// Get user id.
			$uid = username_exists( $username );

			if ( $uid ) {

				// Get user data.
				$cmt_mntn_user_data                  = get_user_by( 'id', $uid );
				$comment_data['mentioned_user_data'] = $cmt_mntn_user_data;

				// Get email body.
				$cmt_mntn_mail_body = $this->cmt_mntn_mail_body( $comment_data );
				$cmt_mntn_mail_sub  = $this->cmt_mntn_mail_subject();

				// Set headers.
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				// Set mail as HTML format.
				add_filter(
					'wp_mail_content_type',
					function() {
						return 'text/html';
					}
				);

				// Send mail.
				wp_mail(
					esc_html( $cmt_mntn_user_data->user_email ),
					esc_html( $cmt_mntn_mail_sub ),
					wp_kses_post( $cmt_mntn_mail_body ),
					$headers
				);

			}
		}

	}

	/**
	 * Mail body for user mail
	 *
	 * @param  array $comment_data Array of comment data.
	 * @return strin               Mail body.
	 */
	function cmt_mntn_mail_body( $comment_data ) {

		// Get current comment link.
		$cmt_mntn_comment_link = get_permalink( $comment_data['comment_post_ID'] ) . '#comment-' . $comment_data['comment_ID'];

		// Get post name related to that comment.
		$post_name = get_the_title( $comment_data['comment_post_ID'] );

		// Get mentioned user's display name.
		$user_name = $comment_data['mentioned_user_data']->display_name;

		$mail_content = ! empty( $this->cmt_mntn_settings['cmt_mntn_mail_content'] ) ? $this->cmt_mntn_settings['cmt_mntn_mail_content'] : '';

		if ( empty( $mail_content ) ) {
			$mail_content = $this->cmt_mntn_default_mail_content();
		}

		$search = array(
			'#comment_link#',
			'#post_name#',
			'#user_name#',
		);

		$replace = array(
			esc_url( $cmt_mntn_comment_link ),
			esc_html( $post_name ),
			esc_html( $user_name ),
		);

		$mail_content = str_replace( $search, $replace, $mail_content );

		return $mail_content;
	}

	/**
	 * Get Email subject sent to mentioned User.
	 */
	public function cmt_mntn_mail_subject() {

		// Get subject from settings.
		$subject = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_subject'] ) ? $this->cmt_mntn_settings['cmt_mntn_email_subject'] : '';

		// If no subject is set, then return default.
		if ( empty( $subject ) ) {

			$subject = esc_html__( 'You were mentioned in a comment', 'comment-mention' );

		}

		// Return subject.
		return $subject;
	}

	/**
	 * Get default mail content.
	 *
	 * @return string Mail content.
	 */
	public static function cmt_mntn_default_mail_content() {

		$content = 'Hi, <strong>#user_name#,</strong>

Someone mentioned you in a post. See the details below:

<a href="#comment_link#"><strong>#post_name#</strong></a>';

		return $content;
	}


}

new CommentMentionMain();
