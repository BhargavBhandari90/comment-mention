/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/icons';

/**
 * Inline icons for pro features — not in @wordpress/icons.
 */
const iconUserSearch = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M10 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0-6.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM2 20a8 8 0 0 1 11.6-7.15l-1.1 1.05A6.5 6.5 0 0 0 3.5 20H2Zm18.56-2.5-2.27-2.27a3.5 3.5 0 1 0-1.06 1.06l2.27 2.27 1.06-1.06ZM16 17a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
	</svg>
);

const iconBadge = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M12 2a5 5 0 1 1 0 10A5 5 0 0 1 12 2Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7ZM4 20a8 8 0 0 1 16 0H4Z" />
	</svg>
);

const iconPage = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M7 5.5h10v1.5H7V5.5Zm0 4h10v1.5H7V9.5Zm0 4h6v1.5H7v-1.5ZM5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Zm0 1.5h14a.5.5 0 0 1 .5.5v14a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5Z" />
	</svg>
);

const iconReply = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M9.06 4.5 3.56 10l5.5 5.5 1.06-1.06L5.87 10.75H15.5a2.75 2.75 0 0 1 0 5.5H11v1.5h4.5a4.25 4.25 0 0 0 0-8.5H5.87l4.25-4.25L9.06 4.5Z" />
	</svg>
);

const PRO_FEATURES = [
	{
		key: 'first-last',
		icon: iconUserSearch,
		name: __( 'Search by first & last name', 'comment-mention' ),
		desc: __( 'Find users by their full name', 'comment-mention' ),
	},
	{
		key: 'display-name',
		icon: iconBadge,
		name: __( 'Search by display name', 'comment-mention' ),
		desc: __( 'Match mentions to display name', 'comment-mention' ),
	},
	{
		key: 'pages',
		icon: iconPage,
		name: __( 'Mentions on pages', 'comment-mention' ),
		desc: __( 'Enable @mention in page comments', 'comment-mention' ),
	},
	{
		key: 'reply-to',
		icon: iconReply,
		name: __( 'Auto reply-to', 'comment-mention' ),
		desc: __( 'Prefill @username on reply click', 'comment-mention' ),
	},
];

const crownIcon = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M3 17.25V19.5h18v-2.25L18 9l-4.5 6L12 9 10.5 15 6 9l-3 8.25Z" />
	</svg>
);

const ProUpsellCard = () => {
	return (
		<div className="cmt-mntn-sidebar-card">
			<div className="cmt-mntn-sidebar-card__header">
				<Icon icon={ crownIcon } />
				<span>{ __( 'Upgrade to Pro', 'comment-mention' ) }</span>
			</div>
			<div className="cmt-mntn-sidebar-card__body">
				{ PRO_FEATURES.map( ( feature ) => (
					<div key={ feature.key } className="cmt-mntn-pro-item">
						<div className="cmt-mntn-pro-item__icon">
							<Icon icon={ feature.icon } />
						</div>
						<div>
							<div className="cmt-mntn-pro-item__name">
								{ feature.name }
							</div>
							<div className="cmt-mntn-pro-item__desc">
								{ feature.desc }
							</div>
						</div>
					</div>
				) ) }
				<a
					href="https://biliplugins.com/comment-mention-pro-product/?ref=freeplugin"
					target="_blank"
					rel="noopener noreferrer"
					className="cmt-mntn-pro-cta"
				>
					{ __( 'Get Pro →', 'comment-mention' ) }
				</a>
			</div>
			<div className="cmt-mntn-sidebar-card__footer">
				{ __( 'Free version', 'comment-mention' ) }
				{ window.cmt_mntn_version &&
					` · v${ window.cmt_mntn_version }` }
			</div>
		</div>
	);
};

export default ProUpsellCard;
