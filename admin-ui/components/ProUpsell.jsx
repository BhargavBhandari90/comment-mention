/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { ToggleControl, ExternalLink } from '@wordpress/components';
import Section from './Section';
import { people } from '@wordpress/icons';

const PRO_URL =
	'https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin';

const PRO_FEATURES = [
	{
		key: 'first-last',
		label: __( 'Search by first & last name', 'comment-mention' ),
		desc: __( 'Let users find mentions by full name.', 'comment-mention' ),
	},
	{
		key: 'display-name',
		label: __( 'Search by display name', 'comment-mention' ),
		desc: __(
			'Enable mention search from Display name.',
			'comment-mention'
		),
	},
	{
		key: 'pages',
		label: __( 'Enable mention for WordPress Pages', 'comment-mention' ),
		desc: __( 'Enable mention on page comments.', 'comment-mention' ),
	},
	{
		key: 'reply',
		label: __( 'Enable "Reply-to"', 'comment-mention' ),
		desc: __(
			'Automatically adds username when you reply.',
			'comment-mention'
		),
	},
	{
		key: 'full-name',
		label: __(
			'Enable Mention by First Name & Last Name',
			'comment-mention'
		),
		desc: __( 'Mention by First Name & Last Name.', 'comment-mention' ),
	},
];

const ProUpsell = () => (
	<Section
		icon={ people }
		title={ __( 'Advanced mentions', 'comment-mention-pro' ) }
		description={ __(
			'Pro features for mention behaviour.',
			'comment-mention-pro'
		) }
	>
		{ PRO_FEATURES.map( ( { key, label, desc } ) => (
			<div key={ key } className="cmt-mntn-setting-row">
				<ToggleControl
					label={ label }
					help={ desc }
					checked={ false }
					onChange={ () => {} }
					disabled
					__nextHasNoMarginBottom
				/>
			</div>
		) ) }
		<div className="cmt-mntn-section__footer">
			<a
				href={ PRO_URL }
				target="_blank"
				rel="noopener noreferrer"
				className="cmt-mntn-pro-cta"
			>
				{ __( 'Get Pro →', 'comment-mention' ) }
			</a>
		</div>
	</Section>
);

export default ProUpsell;
