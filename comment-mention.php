<?php
/**
 * Plugin Name:     Comment Mention
 * Description:     Mention user in comments. Mentioned user will get email notification
 * Author:          Bunty
 * Author URI:      https://bhargavb.com
 * Text Domain:     comment-mention
 * Domain Path:     /languages
 * Version:         1.7.5
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
	define( 'CMT_MNTN_VERSION', '1.7.5' );
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
 * Function to identify if plugin is active.
 *
 * @return bool
 */
function cmt_mntn_plugin_active() {
	__return_true();
}

/**
 * Setting link for plugin.
 *
 * @param  array $links Array of plugin setting link.
 * @return array
 */
function cmt_mntn_setting_page_link( $links ) {

	$settings_link = sprintf(
		'<a href="%1$s">%2$s</a> | <a href="%3$s" target="_blank">%4$s</a>',
		esc_url( admin_url( 'admin.php?page=comment-mention' ) ),
		esc_html__( 'Settings', 'comment-mention' ),
		esc_url( 'https://checkout.freemius.com/mode/dialog/plugin/10495/plan/17738/' ),
		esc_html__( 'Go Pro', 'comment-mention' )
	);

	array_push( $links, $settings_link );
	return $links;
}

add_filter( 'plugin_action_links_' . CMT_MNTN_BASE_NAME, 'cmt_mntn_setting_page_link' );

/**
 * Show admin notice.
 *
 * @return void
 */
function sample_admin_notice__success() {

	// Check if notice is dismissed.
	$notice_dissmissed = get_option( 'dismiss-cm-notice' );

	// If notice is dismissed, then don't display it.
	if ( ! empty( $notice_dissmissed ) && 1 == $notice_dissmissed ) {
		return;
	}

	?>
	<div class="notice notice-success is-dismissible">
		<?php
		echo sprintf(
			'<p>%1$s</p><a href="%2$s" target="_blank">%3$s</a> | <a href="%4$s">%5$s</a>',
			esc_html__( 'Do you like Comment Mention plugin? Checkout our Pro plugin.', 'comment-mention' ),
			'https://biliplugins.com/comment-mention-pro-product/',
			esc_html__( 'Go Pro', 'comment-mention' ),
			esc_url( wp_nonce_url( add_query_arg( 'dismiss-cm-notice', '1' ), 'dismiss-cm-notice-' . get_current_user_id() ) ),
			esc_html__( 'Dismiss this notice', 'comment-mention' )
		);
		?>
	</div>
	<?php
}

add_action( 'admin_notices', 'sample_admin_notice__success' );

/**
 * Register dismissal of admin notices.
 */
function cmt_mntn_dismiss() {
	if ( isset( $_GET['dismiss-cm-notice'] ) && check_admin_referer( 'dismiss-cm-notice-' . get_current_user_id() ) ) {
		update_option( 'dismiss-cm-notice', 1 );
	}
}

add_action( 'admin_init', 'cmt_mntn_dismiss' );

/**
 * Reset admin notice on plugin upgrade.
 *
 * @param  object $upgrader_object Upgrade object.
 * @param  array  $options         Array of plugin related data.
 * @return void
 */
function cmt_mntn_upgrade_function( $upgrader_object, $options ) {
	$current_plugin_path_name = plugin_basename( __FILE__ );

	if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
		foreach ( $options['plugins'] as $each_plugin ) {
			if ( $each_plugin == $current_plugin_path_name ) {

				$notice_dissmissed = get_option( 'dismiss-cm-notice' );
				if ( ! empty( $notice_dissmissed ) ) {
					delete_option( 'dismiss-cm-notice' );
				}
			}
		}
	}
}

add_action( 'upgrader_process_complete', 'cmt_mntn_upgrade_function', 10, 2 );

/**
 * Reset option to show admin notice.
 */
function cmt_mntn_deactivation_callback() {

	// Check if notice is dismisses.
	$notice_dissmissed = get_option( 'dismiss-cm-notice' );

	// If dismissed, then reset to show when plugin is re-activate.
	if ( ! empty( $notice_dissmissed ) ) {
		delete_option( 'dismiss-cm-notice' );
	}
}

register_deactivation_hook( __FILE__, 'cmt_mntn_deactivation_callback' );
register_activation_hook( __FILE__, 'cmt_mntn_deactivation_callback' );

/**
 * Apply transaltion file as per WP language.
 */
function cmt_mntn_text_domain_loader() {

	// Get mo file as per current locale.
	$mofile = CMT_MNTN_PATH . 'languages/' . get_locale() . '.mo';

	// If file does not exists, then applu default mo.
	if ( ! file_exists( $mofile ) ) {
		$mofile = CMT_MNTN_PATH . 'languages/default.mo';
	}

	load_textdomain( 'comment-mention', $mofile );
}

add_action( 'plugins_loaded', 'cmt_mntn_text_domain_loader' );

// Include admin functions file.
require CMT_MNTN_PATH . 'app/includes/common-functions.php';
require CMT_MNTN_PATH . 'app/main/class-comment-mention.php';
require CMT_MNTN_PATH . 'app/main/class-bbpress-user-mention.php';
require CMT_MNTN_PATH . 'app/admin/class-admin-comment-mention.php';
