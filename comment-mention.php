<?php
/**
 * Plugin Name:     Comment Mention
 * Plugin URI:      https://bhargavb.wordpress.com
 * Description:     Mention user in comments. Mentioned user will get email notification
 * Author:          Bunty
 * Author URI:      https://bhargavb.wordpress.com
 * Text Domain:     comment-mention
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Comment_Mention
 */

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    Comment_Mention
 * @subpackage Main
 */
if ( ! defined( 'CM_VERSION' ) ) {
	/**
	 * The version of the plugin.
	 */
	define( 'CM_VERSION', '1.0.0' );
}
if ( ! defined( 'CM_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory.
	 */
	define( 'CM_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'CM_URL' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'CM_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'CM_BASE_NAME' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'CM_BASE_NAME', plugin_basename( __FILE__ ) );
}

// Include admin functions file.
require CM_PATH . 'app/main/class-comment-mention.php';
