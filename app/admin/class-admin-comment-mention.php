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
		add_action( 'admin_menu', array( $this, 'cm_plugin_setup_menu' ) );

		// Save plugin settings.
		add_action( 'init', array( $this, 'cm_save_plugin_data' ) );

		// Main class object for future use.
		$this->_comment_mention = new CommentMentionMain();

	}

	/**
	 * Add admin page for plugin settings.
	 */
	public function cm_plugin_setup_menu() {

		add_menu_page( 'Comment Mention Settings', 'Comment Mention', 'manage_options', 'comment-mention', array( $this, 'cm_admin_settings' ) );

	}

	/**
	 * Call back for admin settings.
	 */
	public function cm_admin_settings() {

		// Get settings.
		$this->cm_settings = get_option( 'cm_settings' );

		// Get subject.
		$cm_subject = ! empty( $this->cm_settings['cm_email_subject'] ) ? $this->cm_settings['cm_email_subject'] : $this->_comment_mention->cm_mail_subject();

		// Get content
		$cm_mail_content = ! empty( $this->cm_settings['cm_mail_content'] ) ? $this->cm_settings['cm_mail_content'] : $this->_comment_mention->cm_default_mail_content();

		?>
		<div class="wrap">
		<h1>Comment Mention Settings</h1>

		<form method="post" action="">
			<?php wp_nonce_field( 'cm_save_data_action', 'cm_save_data_field' ); ?>
			<table class="form-table">

				<tr valign="top">
				<th scope="row"><?php _e( 'Email Subject', 'comment-mention' ); ?></th>
				<td>
					<input type="text" name="cm_email_subject" value="<?php echo esc_attr( $cm_subject ); ?>" />
					<p class="description"><?php _e( 'Subject for mentioned user email.', 'comment-mention' ) ?><br/>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e( 'Email Content', 'comment-mention' ); ?></th>
				<td>
					<?php

						$editor_id = 'cm_mail_content';
						$settings  = array(
							'media_buttons' => false,
						);

						wp_editor( $cm_mail_content, $editor_id, $settings );
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
	public function cm_save_plugin_data() {

		// Verify nounce for security.
		if (
		    isset( $_POST['cm_save_data_field'] )
		    && wp_verify_nonce( $_POST['cm_save_data_field'], 'cm_save_data_action' )
		) {

			$cm_settings = array();

			// Get data from form
			if ( isset( $_POST['cm_email_subject'] ) && ! empty( $_POST['cm_email_subject'] ) ) {
				$cm_settings['cm_email_subject'] = $_POST['cm_email_subject'];
			}

			if ( isset( $_POST['cm_mail_content'] ) && ! empty( $_POST['cm_mail_content'] ) ) {
				$cm_settings['cm_mail_content'] = wp_kses_stripslashes( $_POST['cm_mail_content'] );
			}

			// Save to option table.
			update_option( 'cm_settings', $cm_settings );

		}

	}

}

new CommentMentionAdmin();