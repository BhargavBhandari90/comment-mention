/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { Button } from "@wordpress/components";
import { Icon, check, close } from "@wordpress/icons";

/**
 * Internal dependencies
 */
import { useSettings } from "../context/SettingsContext";

const SaveButton = () => {
	const { saveSettings, isSaving, saveStatus } = useSettings();

	return (
		<div className="cmt-mntn-card__actions">
			{saveStatus === "success" && (
				<span className="cmt-mntn-save-notice cmt-mntn-save-notice--success is-visible">
					<Icon icon={check} />
					{__("Saved", "comment-mention")}
				</span>
			)}
			{saveStatus === "error" && (
				<span className="cmt-mntn-save-notice cmt-mntn-save-notice--error is-visible">
					<Icon icon={close} />
					{__("Save failed", "comment-mention")}
				</span>
			)}
			<Button
				variant="primary"
				isBusy={isSaving}
				disabled={isSaving}
				onClick={saveSettings}
				__next40pxDefaultSize
			>
				{isSaving
					? __("Saving…", "comment-mention")
					: __("Save settings", "comment-mention")}
			</Button>
		</div>
	);
};

export default SaveButton;
