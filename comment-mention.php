<?php
/**
 * Plugin Name:     Comment Mention
 * Description:     Mention user in comments. Mentioned user will get email notification
 * Author:          Bunty
 * Author URI:      https://bhargavb.wordpress.com
 * Text Domain:     comment-mention
 * Domain Path:     /languages
 * Version:         1.1.0
 *
 * @package         Comment_Mention
 */

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    Comment_Mention
 * @subpackage Main
 */
if ( ! defined( 'CMT_MNTN_VERSION' ) ) {
	/**
	 * The version of the plugin.
	 */
	define( 'CMT_MNTN_VERSION', '1.1.0' );
}
if ( ! defined( 'CMT_MNTN_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory.
	 */
	define( 'CMT_MNTN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'CMT_MNTN_URL' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'CMT_MNTN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'CMT_MNTN_BASE_NAME' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'CMT_MNTN_BASE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * Apply transaltion file as per WP language.
 */
function cmt_mntn_text_domain_loader() {

	// Get mo file as per current locale.
	$mofile = CMT_MNTN_PATH . 'languages/' . get_locale() .'.mo';

	// If file does not exists, then applu default mo.
	if ( ! file_exists( $mofile ) ) {
		$mofile = CMT_MNTN_PATH . 'languages/default.mo';
	}

	load_textdomain( 'comment-mention', $mofile );
}

add_action( 'plugins_loaded', 'cmt_mntn_text_domain_loader' );

// Include admin functions file.
require CMT_MNTN_PATH . 'app/main/class-comment-mention.php';
require CMT_MNTN_PATH . 'app/main/class-bbpress-user-mention.php';
require CMT_MNTN_PATH . 'app/admin/class-admin-comment-mention.php';
require CMT_MNTN_PATH . 'app/includes/common-functions.php';
