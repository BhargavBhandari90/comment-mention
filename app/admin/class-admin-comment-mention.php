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
class CommentMentionAdmin {

	/**
	 * Cunstructor for admin class.
	 */
	public function __construct() {

		// Add admin menu.
		add_action( 'admin_menu', array( $this, 'cmt_mntn_plugin_setup_menu' ) );

		// Save plugin settings.
		add_action( 'init', array( $this, 'cmt_mntn_save_plugin_data' ) );

		// Main class object for future use.
		$this->_comment_mention = new CommentMentionMain();

	}

	/**
	 * Add admin page for plugin settings.
	 */
	public function cmt_mntn_plugin_setup_menu() {

		add_menu_page( 'Comment Mention Settings', 'Comment Mention', 'manage_options', 'comment-mention', array( $this, 'cmt_mntn_admin_settings' ) );

	}

	/**
	 * Call back for admin settings.
	 */
	public function cmt_mntn_admin_settings() {

		// Get settings.
		$this->cmt_mntn_settings = get_option( 'cmt_mntn_settings' );

		// Get status.
		$cmt_mntn_email_enable = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_enable'] ) ? $this->cmt_mntn_settings['cmt_mntn_email_enable'] : false;

		// Get subject
		$cmt_mntn_subject = ! empty( $this->cmt_mntn_settings['cmt_mntn_email_subject'] ) ? $this->cmt_mntn_settings['cmt_mntn_email_subject'] : $this->_comment_mention->cmt_mntn_mail_subject();

		// Get content
		$cmt_mntn_mail_content = ! empty( $this->cmt_mntn_settings['cmt_mntn_mail_content'] ) ? $this->cmt_mntn_settings['cmt_mntn_mail_content'] : $this->_comment_mention->cmt_mntn_default_mail_content();

		?>
		<div class="wrap">
		<h1>Comment Mention Settings</h1>

		<form method="post" action="">
			<?php wp_nonce_field( 'cmt_mntn_save_data_action', 'cmt_mntn_save_data_field' ); ?>
			<table class="form-table">

				<tr valign="top">
				<th scope="row"><?php _e( 'Enable Emails', 'comment-mention' ); ?></th>
				<td>
					<input type="checkbox" name="cmt_mntn_email_enable" value="1" <?php checked( $cmt_mntn_email_enable, '1' ); ?> />
					<p class="description"><?php _e( 'Whether to send email to mentioned user or not.', 'comment-mention' ) ?><br/>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e( 'Email Subject', 'comment-mention' ); ?></th>
				<td>
					<input type="text" name="cmt_mntn_email_subject" value="<?php echo esc_attr( $cmt_mntn_subject ); ?>" />
					<p class="description"><?php _e( 'Subject for mentioned user email.', 'comment-mention' ) ?><br/>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e( 'Email Content', 'comment-mention' ); ?></th>
				<td>
					<?php

						$editor_id = 'cmt_mntn_mail_content';
						$settings  = array(
							'media_buttons' => false,
						);

						wp_editor( wp_kses_post( $cmt_mntn_mail_content ), $editor_id, $settings );
					?>
					<p class="description"><?php _e( 'Mail content for mentioned user email. Available shortcodes:', 'comment-mention' ); ?><br/>
					<strong>#comment_link#</strong><br/>
					<strong>#post_name#</strong><br/>
					<strong>#user_name#</strong><br/>
					</p>
				</td>
				</tr>

			</table>

			<?php submit_button(); ?>

		</form>
		</div>
		<?php

	}

	/**
	 * Save plugin settings to option.
	 */
	public function cmt_mntn_save_plugin_data() {

		// Verify nounce for security.
		if (
		    isset( $_POST['cmt_mntn_save_data_field'] )
		    && wp_verify_nonce( $_POST['cmt_mntn_save_data_field'], 'cmt_mntn_save_data_action' )
		) {

			$cmt_mntn_settings = array();

			// Get data from form
			if ( isset( $_POST['cmt_mntn_email_enable'] ) && ! empty( $_POST['cmt_mntn_email_enable'] ) ) {
				$cmt_mntn_settings['cmt_mntn_email_enable'] = intval( $_POST['cmt_mntn_email_enable'] );
			}

			if ( isset( $_POST['cmt_mntn_email_subject'] ) && ! empty( $_POST['cmt_mntn_email_subject'] ) ) {
				$cmt_mntn_settings['cmt_mntn_email_subject'] = sanitize_text_field( $_POST['cmt_mntn_email_subject'] );
			}

			if ( isset( $_POST['cmt_mntn_mail_content'] ) && ! empty( $_POST['cmt_mntn_mail_content'] ) ) {
				$cmt_mntn_settings['cmt_mntn_mail_content'] = wp_kses_post( wp_kses_stripslashes( $_POST['cmt_mntn_mail_content'] ) );
			}

			// Save to option table.
			update_option( 'cmt_mntn_settings', $cmt_mntn_settings );

		}

	}

}

new CommentMentionAdmin();