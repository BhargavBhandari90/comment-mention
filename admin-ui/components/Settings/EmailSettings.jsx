/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettings } from '../../context/SettingsContext';
import RichTextEditor from '../RichTextEditor';

const SUBJECT_SHORTCODES = [
	{ code: '#post_name#', desc: __( 'post title', 'comment-mention' ) },
	{ code: '#user_name#', desc: __( 'mentioned user', 'comment-mention' ) },
	{ code: '#commenter_name#', desc: __( 'commenter', 'comment-mention' ) },
];

const CONTENT_SHORTCODES = [
	{
		code: '#comment_link#',
		desc: __( 'link to comment', 'comment-mention' ),
	},
	{ code: '#post_name#', desc: __( 'post title', 'comment-mention' ) },
	{ code: '#user_name#', desc: __( 'mentioned user', 'comment-mention' ) },
	{ code: '#commenter_name#', desc: __( 'commenter', 'comment-mention' ) },
	{
		code: '#comment_content#',
		desc: __( 'comment text', 'comment-mention' ),
	},
];

const ShortcodePills = ( { shortcodes } ) => (
	<div className="cmt-mntn-shortcodes">
		{ shortcodes.map( ( { code, desc } ) => (
			<span key={ code } className="cmt-mntn-shortcode">
				<code>{ code }</code>
				<span className="cmt-mntn-shortcode__desc">{ desc }</span>
			</span>
		) ) }
	</div>
);

const EmailSettings = () => {
	const { settings, updateSetting } = useSettings();
	const emailEnabled = !! settings.cmt_mntn_email_enable;

	return (
		<>
			{ /* Enable toggle */ }
			<div className="cmt-mntn-setting-row">
				<ToggleControl
					label={ __(
						'Send email when mentioned',
						'comment-mention'
					) }
					help={ __(
						'Notifies the mentioned user with an email containing the comment context.',
						'comment-mention'
					) }
					name={ 'cmt_mntn_email_enable' }
					checked={ emailEnabled }
					onChange={ ( val ) =>
						updateSetting( 'cmt_mntn_email_enable', val ? 1 : 0 )
					}
					__nextHasNoMarginBottom
				/>
			</div>

			{ /* Collapsible fields — only shown when email is enabled */ }
			{ emailEnabled && (
				<div className="cmt-mntn-field-group">
					<div>
						<TextControl
							label={ __( 'Subject line', 'comment-mention' ) }
							value={ settings.cmt_mntn_email_subject || '' }
							onChange={ ( val ) =>
								updateSetting( 'cmt_mntn_email_subject', val )
							}
							__nextHasNoMarginBottom
							__next40pxDefaultSize
						/>
						<ShortcodePills shortcodes={ SUBJECT_SHORTCODES } />
					</div>

					<div>
						<RichTextEditor
							label={ __( 'Email body', 'comment-mention' ) }
							value={ settings.cmt_mntn_mail_content || '' }
							onChange={ ( val ) =>
								updateSetting( 'cmt_mntn_mail_content', val )
							}
							rows={ 10 }
						/>
						<ShortcodePills shortcodes={ CONTENT_SHORTCODES } />
					</div>
				</div>
			) }
		</>
	);
};

export default EmailSettings;
