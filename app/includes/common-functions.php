<?php
/**
 * Common functions of Comment Mention.
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
 * Is user mention enabled for BBPress?
 *
 * @param bool $default Optional. Default value true.
 *
 * @return bool Is user mention enabled for BBPress?
 */
function cmt_mntn_enable_bbp_user_mention( $default = 1 ) {

	// Filter & return
	return (bool) apply_filters( 'cmt_mntn_enable_bbp_user_mention', (bool) get_option( '_bbp_enable_user_mention', $default ) );
}

/**
 * Get email content.
 *
 * @param  integer $uid     User ID.
 * @param  integer $post_id Mentioned post ID.
 * @return string           Email Content.
 */
function cmt_mntn_mail_setting( $uid, $post_id ) {

	// Bail, if anything goes wrong.
	if ( empty( $uid ) || empty( $post_id ) ) {
		return;
	}

	// Get post name related to that comment.
	$post_name = get_the_title( $post_id );

	// Get current comment link.
	$cmt_mntn_comment_link = trailingslashit( get_permalink( $post_id ) ) . '#post-' . intval( $post_id );

	// Get topic id related to reply.
	if ( 'reply' === get_post_type( $post_id ) && function_exists( 'bbp_get_reply_topic_id' ) ) {

		$topic_id = bbp_get_reply_topic_id( $post_id );

		// Get current comment link.
		$cmt_mntn_comment_link = trailingslashit( get_permalink( $topic_id ) ) . '#post-' . intval( $post_id );
	}

	// Get setting.
	$cmt_mntn_settings = get_option( 'cmt_mntn_settings' );

	// Get user.
	$cmt_mntn_user_data = get_user_by( 'id', $uid );

	// Get mentioned user's display name.
	$user_name = isset( $cmt_mntn_user_data->display_name )
		? $cmt_mntn_user_data->display_name
		: '';

	$mail_content = ! empty( $cmt_mntn_settings['cmt_mntn_mail_content'] ) ? $cmt_mntn_settings['cmt_mntn_mail_content'] : '';

	if ( empty( $mail_content ) ) {
		$mail_content = CommentMentionMain::cmt_mntn_default_mail_content();
	}

	$post_author_id  = get_post_field( 'post_author', $post_id );
	$author_obj      = get_user_by( 'ID', $post_author_id );
	$commenter_name  = isset( $author_obj->user_login ) ? $author_obj->user_login : 'Someone';
	$comment_content = get_post_field( 'post_content', $post_id );

	$search = apply_filters(
		'cmt_mtn_search_email_placeholders',
		array(
			'#comment_link#',
			'#post_name#',
			'#user_name#',
			'#commenter_name#',
			'#comment_content#',
		)
	);

	$replace = apply_filters(
		'cmt_mtn_replace_email_placeholders',
		array(
			esc_url( $cmt_mntn_comment_link ),
			esc_html( $post_name ),
			esc_html( $user_name ),
			esc_html( $commenter_name ),
			$comment_content,
		)
	);

	// Replace with actual values.
	$mail_setting['email_content'] = str_replace( $search, $replace, $mail_content );
	$mail_setting['email_subject'] = ! empty( $cmt_mntn_settings['cmt_mntn_email_subject'] )
		? $cmt_mntn_settings['cmt_mntn_email_subject']
		: esc_html__( 'You were mentioned in a comment', 'comment-mention' );

	return apply_filters( 'cmt_mntn_mail_setting', $mail_setting );
}

/**
 * Checks if current user'role is enabled for Comment Mention.
 */
function cmt_mntn_check_enabled_userroles() {

	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	$cmt_mntn_settings           = get_option( 'cmt_mntn_settings' );
	$cmt_mntn_enabled_user_roles = ! empty( $cmt_mntn_settings['cmt_mntn_enabled_user_roles'] ) ? $cmt_mntn_settings['cmt_mntn_enabled_user_roles'] : array();

	if ( empty( $cmt_mntn_enabled_user_roles ) ) {
		return false;
	}

	$user  = wp_get_current_user();
	$roles = (array) $user->roles;

	foreach ( $roles as $role ) {
		if ( in_array( $role, $cmt_mntn_enabled_user_roles, false ) ) {
			return true;
		}
	}
	return false;
}
