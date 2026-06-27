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
class CommentMentionAdmin {

	/**
	 * Settings array.
	 *
	 * @var array
	 */
	public $cmt_mntn_settings = array();

	/**
	 * Object for comments mention class.
	 *
	 * @var object
	 */
	public $comment_mention;

	/**
	 * Object for main class.
	 *
	 * @var object
	 */
	public $comment_mention_main;

	/**
	 * Cunstructor for admin class.
	 */
	public function __construct() {

		// Allow for backend only.
		if ( ! is_admin() ) {
			return;
		}

		// Add admin menu.
		add_action( 'admin_menu', array( $this, 'cmt_mntn_plugin_setup_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'cmt_mntn_admin_enqueue_scripts' ) );

		// Main class object for future use.
		$this->comment_mention_main = new CommentMentionMain();

		// Get settings.
		$this->cmt_mntn_settings = get_option( 'cmt_mntn_settings' );
	}

	/**
	 * Add admin page for plugin settings.
	 */
	public function cmt_mntn_plugin_setup_menu() {

		$icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="black"><text x="10" y="15" text-anchor="middle" font-size="20" font-family="Arial, sans-serif">&#64;</text></svg>';
		$icon_url = 'data:image/svg+xml;base64,' . base64_encode( $icon_svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		add_menu_page(
			esc_html__( 'Comment Mention Settings', 'comment-mention' ),
			esc_html__( 'Comment Mention', 'comment-mention' ),
			'manage_options',
			'comment-mention',
			array(
				$this,
				'cmt_mntn_admin_settings',
			),
			$icon_url
		);
	}

	/**
	 * Call back for admin settings.
	 */
	public function cmt_mntn_admin_settings() {
		?>
		<div id="cmt-mntn-admin-page">
			<div class="cmt-mntn-skeleton" aria-hidden="true">
				<div class="cmt-mntn-skeleton__header">
					<div class="cmt-mntn-skeleton__brand">
						<div class="cmt-mntn-skeleton__circle"></div>
						<div class="cmt-mntn-skeleton__line" style="width:160px;height:20px;"></div>
					</div>
					<div class="cmt-mntn-skeleton__line" style="width:200px;height:14px;"></div>
				</div>

				<div class="cmt-mntn-skeleton__tabs">
					<div class="cmt-mntn-skeleton__tab cmt-mntn-skeleton__tab--active"></div>
					<div class="cmt-mntn-skeleton__tab"></div>
				</div>
				<div class="cmt-mntn-skeleton__body">

					<div class="cmt-mntn-skeleton__card">
						<div class="cmt-mntn-skeleton__card-header">
							<div class="cmt-mntn-skeleton__line" style="width:120px;height:16px;"></div>
							<div class="cmt-mntn-skeleton__line" style="width:100px;height:32px;border-radius:3px;"></div>
						</div>
						<?php for ( $i = 0; $i < 3; $i++ ) : ?>
						<div class="cmt-mntn-skeleton__section">
							<div class="cmt-mntn-skeleton__line" style="width:140px;height:13px;"></div>
							<div class="cmt-mntn-skeleton__line" style="width:90%;height:12px;margin-top:6px;"></div>
							<div class="cmt-mntn-skeleton__line" style="width:100%;height:40px;margin-top:12px;border-radius:3px;"></div>
						</div>
						<?php endfor; ?>
					</div>

					<div class="cmt-mntn-skeleton__sidebar">
						<div class="cmt-mntn-skeleton__card">
							<div class="cmt-mntn-skeleton__card-header">
								<div class="cmt-mntn-skeleton__line" style="width:80px;height:14px;"></div>
							</div>
							<div class="cmt-mntn-skeleton__section">
								<div class="cmt-mntn-skeleton__line" style="width:100%;height:12px;"></div>
								<div class="cmt-mntn-skeleton__line" style="width:80%;height:12px;margin-top:6px;"></div>
								<div class="cmt-mntn-skeleton__line" style="width:100px;height:14px;margin-top:10px;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook The current admin page.
	 */
	public function cmt_mntn_admin_enqueue_scripts( $hook ) {

		if ( 'toplevel_page_comment-mention' !== $hook ) {
			return;
		}

		$asset_file = include trailingslashit( CMT_MNTN_PATH ) . 'build/admin-ui.asset.php';

		// Enqueue wp_editor.
		wp_enqueue_editor();

		wp_enqueue_script(
			'cmt-mntn-admin-page-script',
			CMT_MNTN_URL . 'build/admin-ui.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$asset_file = include trailingslashit( CMT_MNTN_PATH ) . 'build/admin-ui.asset.php';

		wp_enqueue_style(
			'cmt-mntn-admin-page-style',
			CMT_MNTN_URL . 'build/admin-ui.css',
			array(),
			$asset_file['version']
		);

		// All editable roles (excluding administrator) as { slug: label }.
		$roles          = array();
		$editable_roles = array_reverse( get_editable_roles() );
		foreach ( $editable_roles as $slug => $details ) {
			$roles[ $slug ] = translate_user_role( $details['name'] );
		}

		$data = array(
			'settings'       => $this->cmt_mntn_settings,
			'apiUrl'         => rest_url( 'cmt-mntn/v1' ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'userId'         => get_current_user_id(),
			'editable_roles' => $editable_roles,
			'hasPro'         => class_exists( 'CommentMentionMainPro' ),
			'defaults'       => array(
				'cmt_mntn_email_subject' => $this->comment_mention_main->cmt_mntn_mail_subject(),
				'cmt_mntn_mail_content'  => $this->comment_mention_main->cmt_mntn_default_mail_content(),
			),
		);

		wp_add_inline_script(
			'cmt-mntn-admin-page-script',
			'const cmtMntn = ' . wp_json_encode( $data ) . ';',
		);
	}
}

new CommentMentionAdmin();
