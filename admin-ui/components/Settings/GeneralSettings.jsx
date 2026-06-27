/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { ToggleControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettings } from '../../context/SettingsContext';

const GeneralSettings = () => {
	const { settings, updateSetting } = useSettings();

	return (
		<div className="cmt-mntn-setting-row">
			<ToggleControl
				label={ __( 'Show avatars in suggestions', 'comment-mention' ) }
				help={ __(
					"Displays the user's profile picture next to their name in the @mention autocomplete dropdown.",
					'comment-mention'
				) }
				checked={ !! settings.cmt_mntn_enable_avatar }
				onChange={ ( val ) =>
					updateSetting( 'cmt_mntn_enable_avatar', val ? 1 : 0 )
				}
				__nextHasNoMarginBottom
			/>
		</div>
	);
};

export default GeneralSettings;
