/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { Icon, help, external } from "@wordpress/icons";

const SupportCard = () => {
	return (
		<div className="cmt-mntn-sidebar-card">
			<div className="cmt-mntn-sidebar-card__header">
				<Icon icon={help} />
				<span>{__("Need help?", "comment-mention")}</span>
			</div>
			<div className="cmt-mntn-sidebar-card__body">
				<p className="cmt-mntn-support-desc">
					{__(
						"Check the documentation or open a support thread on WordPress.org.",
						"comment-mention",
					)}
				</p>
				<a
					href="https://wordpress.org/support/plugin/comment-mention/"
					target="_blank"
					rel="noopener noreferrer"
					className="cmt-mntn-support-link"
				>
					<Icon icon={external} />
					{__("Support forum", "comment-mention")}
				</a>
				<a
					href="https://wordpress.org/plugins/comment-mention/#description"
					target="_blank"
					rel="noopener noreferrer"
					className="cmt-mntn-support-link"
				>
					<Icon icon={external} />
					{__("Documentation", "comment-mention")}
				</a>
			</div>
		</div>
	);
};

export default SupportCard;
