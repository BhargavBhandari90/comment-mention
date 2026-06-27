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

		// Save plugin settings.
		add_action( 'init', array( $this, 'cmt_mntn_save_plugin_data' ) );

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

		// Get status.
		$cmt_mntn_email_enable = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_enable'] ) ? $this->cmt_mntn_settings['cmt_mntn_email_enable'] : false;

		// Get subject.
		$cmt_mntn_subject = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_subject'] ) ? $this->cmt_mntn_settings['cmt_mntn_email_subject'] : $this->comment_mention_main->cmt_mntn_mail_subject();

		// Get content.
		$cmt_mntn_mail_content = ! empty( $this->cmt_mntn_settings['cmt_mntn_mail_content'] ) ? $this->cmt_mntn_settings['cmt_mntn_mail_content'] : $this->comment_mention_main->cmt_mntn_default_mail_content();

		// Get selected users roles who can mention.
		$cmt_mntn_enabled_user_roles = ! empty( $this->cmt_mntn_settings['cmt_mntn_enabled_user_roles'] ) ? $this->cmt_mntn_settings['cmt_mntn_enabled_user_roles'] : array();

		// Get selected users roles that can not be mentioned.
		$cmt_mntn_disabled_mention_user_roles = ! empty( $this->cmt_mntn_settings['cmt_mntn_disabled_mention_user_roles'] ) ? $this->cmt_mntn_settings['cmt_mntn_disabled_mention_user_roles'] : array();

		$cmt_mntn_enable_avatar = ! empty( $this->cmt_mntn_settings['cmt_mntn_enable_avatar'] )
			? $this->cmt_mntn_settings['cmt_mntn_enable_avatar']
			: false;

		?>
		<!-- <div class="wrap"> -->
		<!-- <h1><?php // esc_html_e( 'Comment Mention Settings', 'comment-mention' ); ?></h1> -->

		<form method="post" action="" style="display: none;">
			<?php wp_nonce_field( 'cmt_mntn_save_data_action', 'cmt_mntn_save_data_field' ); ?>
			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enabled Roles', 'comment-mention' ); ?></th>
					<td>
						<fieldset class="metabox-prefs">
							<?php
							$editable_roles = array_reverse( get_editable_roles() );

							if ( ! empty( $editable_roles ) && array_key_exists( 'administrator', $editable_roles ) ) {
								unset( $editable_roles['administrator'] );
							}

							if ( ! empty( $editable_roles ) ) {
								foreach ( $editable_roles as $role => $details ) {
									$name     = translate_user_role( $details['name'] );
									$selected = in_array( $role, $cmt_mntn_enabled_user_roles, true ) ? 'checked' : '';
									printf(
										'<label><input type="checkbox" name="cmt_mntn_enabled_user_roles[]" value="%1$s" %2$s>%3$s</label>',
										esc_attr( $role ),
										esc_attr( $selected ),
										esc_html( $name )
									);
								}
							}
							?>
						</fieldset>
						<p class="description"><?php esc_html_e( 'Choose which user roles should be able to mention in Comments.', 'comment-mention' ); ?><br/>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<fieldset class="metabox-prefs">
							<?php
							$editable_roles = array_reverse( get_editable_roles() );

							if ( ! empty( $editable_roles ) ) {
								foreach ( $editable_roles as $role => $details ) {
									$name     = translate_user_role( $details['name'] );
									$selected = in_array( $role, $cmt_mntn_disabled_mention_user_roles, true ) ? 'checked' : '';
									printf(
										'<label><input type="checkbox" name="cmt_mntn_disabled_mention_user_roles[]" value="%1$s" %2$s>%3$s</label>',
										esc_attr( $role ),
										esc_attr( $selected ),
										esc_html( $name )
									);
								}
							}
							?>
						</fieldset>
						<p class="description"><?php esc_html_e( 'Hide selected user roles while mentioning in Comments.', 'comment-mention' ); ?><br/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Emails', 'comment-mention' ); ?></th>
					<td>
						<input type="checkbox" name="cmt_mntn_email_enable" value="1" <?php checked( $cmt_mntn_email_enable, '1' ); ?> />
						<p class="description"><?php esc_html_e( 'Whether to send email to mentioned user or not.', 'comment-mention' ); ?><br/>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Email Subject', 'comment-mention' ); ?></th>
					<td>
						<input type="text" class="regular-text" name="cmt_mntn_email_subject" value="<?php echo esc_attr( stripslashes( $cmt_mntn_subject ) ); ?>" />
						<p class="description"><?php esc_html_e( 'Subject for mentioned user email. Available shortcodes:', 'comment-mention' ); ?><br/>
						<strong>#post_name#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Post title where user is mentioned.', 'comment-mention' ); ?><br/>
						<strong>#user_name#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Username who is mentioned.', 'comment-mention' ); ?><br/>
						<strong>#commenter_name#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Commenter name.', 'comment-mention' ); ?><br/>
						</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Email Content', 'comment-mention' ); ?></th>
					<td>
						<?php

							$editor_id = 'cmt_mntn_mail_content';
							$settings  = array(
								'media_buttons' => false,
							);

							wp_editor( wp_kses_post( $cmt_mntn_mail_content ), $editor_id, $settings );
							?>
						<p class="description"><?php esc_html_e( 'Mail content for mentioned user email. Available shortcodes:', 'comment-mention' ); ?><br/>
						<strong>#comment_link#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Link where user is mentioned.', 'comment-mention' ); ?><br/>
						<strong>#post_name#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Post title where user is mentioned.', 'comment-mention' ); ?><br/>
						<strong>#user_name#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Username who is mentioned.', 'comment-mention' ); ?><br/>
						<strong>#commenter_name#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Commenter name.', 'comment-mention' ); ?><br/>
						<strong>#comment_content#</strong>&nbsp;-&nbsp;<?php esc_html_e( 'Comment content.', 'comment-mention' ); ?><br/>
						</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Avatar', 'comment-mention' ); ?></th>
					<td>
						<input type="checkbox" name="cmt_mntn_enable_avatar" value="1" <?php checked( $cmt_mntn_enable_avatar, '1' ); ?> />
						<p class="description"><?php esc_html_e( 'Enable Avatar for Mentioned user.', 'comment-mention' ); ?><br/>
					</td>
				</tr>

				<?php if ( ! class_exists( 'CommentMentionMainPro' ) ) : ?>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Search user by First/Last name - PRO', 'comment-mention-pro' ); ?></th>
					<td>
						<a target="_blank" href="https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin">Get PRO</a>
						<p class="description"><?php esc_html_e( 'Enable mention search from First/Last name.', 'comment-mention-pro' ); ?><br/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Search user by Display name - PRO', 'comment-mention-pro' ); ?></th>
					<td>
						<a target="_blank" href="https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin">Get PRO</a>
						<p class="description"><?php esc_html_e( 'Enable mention search from Display name.', 'comment-mention-pro' ); ?><br/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable mention for WordPress Pages - PRO', 'comment-mention-pro' ); ?></th>
					<td>
						<a target="_blank" href="https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin">Get PRO</a>
						<p class="description"><?php esc_html_e( 'Enable mention on page comments.', 'comment-mention-pro' ); ?><br/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable "Reply-to" - PRO', 'comment-mention-pro' ); ?></th>
					<td>
						<a target="_blank" href="https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin">Get PRO</a>
						<p class="description"><?php esc_html_e( 'Automatically adds username when you reply.', 'comment-mention-pro' ); ?><br/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Mention by First Name & Last Name - PRO', 'comment-mention-pro' ); ?></th>
					<td>
						<a target="_blank" href="https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin">Get PRO</a>
						<p class="description"><?php esc_html_e( 'Mention by First Name & Last Name.', 'comment-mention-pro' ); ?><br/>
					</td>
				</tr>

				<?php endif; ?>

				<?php do_action( 'cmt_mntn_more_options' ); ?>

			</table>

			<?php submit_button(); ?>

		</form>
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
		<!-- </div> -->
		<?php
	}

	/**
	 * Save plugin settings to option.
	 */
	public function cmt_mntn_save_plugin_data() {

		// Verify nonce for security.
		if (
			isset( $_POST['cmt_mntn_save_data_field'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cmt_mntn_save_data_field'] ) ), 'cmt_mntn_save_data_action' )
		) {

			$cmt_mntn_settings = array();

			// Get data from form.
			if ( isset( $_POST['cmt_mntn_email_enable'] ) && ! empty( $_POST['cmt_mntn_email_enable'] ) ) {
				$cmt_mntn_settings['cmt_mntn_email_enable'] = intval( $_POST['cmt_mntn_email_enable'] );
			}

			if ( isset( $_POST['cmt_mntn_email_subject'] ) && ! empty( $_POST['cmt_mntn_email_subject'] ) ) {
				$cmt_mntn_settings['cmt_mntn_email_subject'] = sanitize_text_field( wp_unslash( $_POST['cmt_mntn_email_subject'] ) );
			}

			if ( isset( $_POST['cmt_mntn_mail_content'] ) && ! empty( $_POST['cmt_mntn_mail_content'] ) ) {
				$cmt_mntn_settings['cmt_mntn_mail_content'] = wp_kses_post( wp_kses_stripslashes( $_POST['cmt_mntn_mail_content'] ) ); // phpcs:ignore
			}

			$cmt_mntn_settings['cmt_mntn_enabled_user_roles']          = filter_input( INPUT_POST, 'cmt_mntn_enabled_user_roles', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$cmt_mntn_settings['cmt_mntn_disabled_mention_user_roles'] = filter_input( INPUT_POST, 'cmt_mntn_disabled_mention_user_roles', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			if ( isset( $_POST['cmt_mntn_enable_avatar'] ) && ! empty( $_POST['cmt_mntn_enable_avatar'] ) ) {
				$cmt_mntn_settings['cmt_mntn_enable_avatar'] = intval( $_POST['cmt_mntn_enable_avatar'] );
			}

			$cmt_mntn_settings = apply_filters( 'cmt_mntn_settings', $cmt_mntn_settings );

			// Save to option table.
			update_option( 'cmt_mntn_settings', $cmt_mntn_settings );

			add_action( 'admin_notices', array( $this, 'cmt_mntn_admin_notice' ) );

		}
	}

	/**
	 * Admin notice for saved data.
	 *
	 * @return void
	 */
	public function cmt_mntn_admin_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings Saved', 'comment-mention' ); ?></p>
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
