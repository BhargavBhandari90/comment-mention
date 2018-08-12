<?php

/**
 * Functions of Comment Mention functions.
 *
 * @package Comment_Mention
 */

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
		add_action( 'wp_enqueue_scripts', array( $this, 'cm_enqueue_styles_script' ) );

		// Ger users.
		add_action( 'wp_ajax_cm_get_users', array( $this, 'cm_ajax_get_users' ) );

		// Modify comment content.
		add_filter( 'pre_comment_content', array( $this, 'cm_at_name_filter' ) );

		// add_filter( 'preprocess_comment', array( $this, 'cm_preprocess_comment' ) );
		add_action( 'comment_post', array( $this, 'cm_preprocess_comment' ), 10, 3 );

	}

	/**
	 * Add scripts and styles.
	 */
	public function cm_enqueue_styles_script() {

		// Set ajax URL.
		wp_localize_script( 'jquery', 'ajax', array(
		    'url' => admin_url( 'admin-ajax.php' )
		) );

		// Atwho CSS.
		// Ref: https://github.com/ichord/At.js/
		wp_enqueue_style(
			'cm-atwho-css',
			CM_URL . 'app/assets/css/jquery.atwho.css',
			array(),
			filemtime( CM_PATH . 'app/assets/css/jquery.atwho.css' )
		);

		// caret CSS.
		// Ref: https://github.com/ichord/At.js/
		wp_enqueue_script(
			'cm-caret',
			CM_URL . 'app/assets/js/jquery.caret.js',
			array( 'jquery' ),
			filemtime( CM_PATH . 'app/assets/js/jquery.caret.js'),
			true
		);

		// Atwho JS.
		// Ref: https://github.com/ichord/At.js/
		wp_enqueue_script(
			'cm-atwho',
			CM_URL . 'app/assets/js/jquery.atwho.js',
			array( 'cm-caret' ),
			filemtime( CM_PATH . 'app/assets/js/jquery.atwho.js'),
			true
		);

		// Plugin script.
		wp_enqueue_script(
			'cm-mentions',
			CM_URL . 'app/assets/js/mentions.js',
			array( 'cm-caret', 'cm-atwho' ),
			filemtime( CM_PATH . 'app/assets/js/mentions.js'),
			true
		);

	}

	/**
	 * Get usernames.
	 */
	public function cm_ajax_get_users() {

		// Set arguments.
		$args = array(
			'term' => sanitize_text_field( $_GET['term'] ),
		);

		// Get usernames.
		$results = $this->cm_get_users( $args['term'] );

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
	public function cm_get_users( $username ) {

		global $wpdb;

		// Get results from DB.
		$results = $wpdb->get_results(
			"SELECT user_login as name FROM $wpdb->users WHERE user_login LIKE '$username%'"
		);

		return $results;

	}

	/**
	 * Find and link @-mentioned users in the contents of a given item.
	 */
	public function cm_at_name_filter( $content ) {

		// Try to find mentions.
		$usernames = $this->cm_find_mentions( $content );

		// If no mentions found, then halt the process.
		if ( empty( $usernames ) )
			return $content;

		// We don't want to link @mentions that are inside of links, so we
		// temporarily remove them.
		$replace_count = 0;
		$replacements = array();
		foreach ( $usernames as $username ) {
			// Prevent @ name linking inside <a> tags.
			preg_match_all( '/(<a.*?(?!<\/a>)@' . $username . '.*?<\/a>)/', $content, $content_matches );
			if ( ! empty( $content_matches[1] ) ) {
				foreach ( $content_matches[1] as $replacement ) {
					$replacements[ '#BPAN' . $replace_count ] = $replacement;
					$content = str_replace( $replacement, '#BPAN' . $replace_count, $content );
					$replace_count++;
				}
			}
		}

		// Linkify the mentions with the username.
		foreach ( (array) $usernames as $user_id => $username ) {
			$author_url = get_author_posts_url( $user_id );
			$content = preg_replace( '/(@' . $username . '\b)/', "<a class='comment-mention' href='$author_url' rel='nofollow'>@$username</a>", $content );
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
	public function cm_find_mentions( $content ) {

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
		foreach( (array) array_values( $usernames ) as $username ) {

			// Get user info by login.
			$user_obj = get_user_by( 'login', $username );

			if ( empty( $user_obj ) ) {
				continue;
			}

			$user_id  = $user_obj->ID;

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
	 * @param  int $comment_ID        Current comment id.
	 * @param  string $comment_status Comment status.
	 * @param  obj $comment_data      Comment data.
	 * @return void
	 */
	public function cm_preprocess_comment( $comment_ID, $comment_status, $comment_data ) {

		// Get content.
		$content = $comment_data['comment_content'];

		// Add data to array for post use.
		$comment_data['comment_ID'] = $comment_ID;

		// Get usernames from comment content.
		$usernames = $this->cm_find_mentions( $content );

		// Iterate the username loop.
		foreach ( $usernames as $username ) {

			// Get user id.
			$uid = username_exists( $username );

			if ( $uid ) {

				// Get user data.
				$cm_user_data = get_user_by( 'id', $uid );

				// Get email body.
				$cm_mail_body = $this->cm_mail_body( $comment_data );

				// Set headers.
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				// Send mail.
				wp_mail(
					$cm_user_data->user_email,
					__('You were mentioned in a comment', 'comment-mention'),
					$cm_mail_body,
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
	function cm_mail_body( $comment_data ) {

		// Get current comment link.
		$cm_comment_link = get_permalink( $comment_data['comment_post_ID'] ) . '#comment-' . $comment_data['comment_ID'];

		// Get post name related to that comment.
		$post_name = get_the_title( $comment_data['comment_post_ID'] );

		// Set email body.
		$mail_body = __( 'You are mentioned on the post. Check the follwing details for that:' );
		$mail_body .= __( 'Comment Link:' ) . '<a href="' . $cm_comment_link . '">' . $post_name . '</a>' ;

		return $mail_body;
	}


}

new CommentMentionMain();
