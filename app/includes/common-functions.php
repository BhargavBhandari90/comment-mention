<?php
/**
 * Common functions of Comment Mention.
 *
 * @package Comment_Mention
 */

/**
 * Exit if accessed directly
 *
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
