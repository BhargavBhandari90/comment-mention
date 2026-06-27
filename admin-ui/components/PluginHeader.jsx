/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon, starFilled, help, external } from '@wordpress/icons';

/**
 * Inline SVG for the @ symbol — not in @wordpress/icons.
 */
const atIcon = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10h3.5v-1.5H12c-4.687 0-8.5-3.813-8.5-8.5S7.313 3.5 12 3.5s8.5 3.813 8.5 8.5v1c0 .967-.783 1.75-1.75 1.75s-1.75-.783-1.75-1.75V12c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5c1.531 0 2.9-.695 3.841-1.786A3.24 3.24 0 0 0 18.75 16.5c1.793 0 3.25-1.457 3.25-3.25V12C22 6.477 17.523 2 12 2Zm0 13.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5Z" />
	</svg>
);

const bookIcon = (
	<svg
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M6.5 3.5h11V16h-11V3.5Zm-1.5 14V2H19v15.5H5Zm1.5 2h9V21h-9v-1.5ZM5 18h14v3H5v-3Z" />
	</svg>
);

const PluginHeader = () => {
	return (
		<header className="cmt-mntn-header">
			<div className="cmt-mntn-header__brand">
				<div className="cmt-mntn-header__icon" aria-hidden="true">
					<Icon icon={ atIcon } />
				</div>
				<h1 className="cmt-mntn-header__title">
					{ __( 'Comment Mention', 'comment-mention' ) }
				</h1>
			</div>

			<nav
				className="cmt-mntn-header__links"
				aria-label={ __( 'Plugin links', 'comment-mention' ) }
			>
				<a
					href="https://wordpress.org/support/plugin/comment-mention/reviews/#new-post"
					target="_blank"
					rel="noopener noreferrer"
					className="cmt-mntn-header__link"
				>
					<Icon icon={ starFilled } />
					<span>{ __( 'Review', 'comment-mention' ) }</span>
				</a>
				<a
					href="https://wordpress.org/support/plugin/comment-mention/"
					target="_blank"
					rel="noopener noreferrer"
					className="cmt-mntn-header__link"
				>
					<Icon icon={ help } />
					<span>{ __( 'Support', 'comment-mention' ) }</span>
				</a>
			</nav>
		</header>
	);
};

export default PluginHeader;
