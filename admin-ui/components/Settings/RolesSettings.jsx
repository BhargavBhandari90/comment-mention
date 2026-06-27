/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";

/**
 * Internal dependencies
 */
import { useSettings } from "../../context/SettingsContext";

function toggleRole(current = [], role, checked) {
	const arr = Array.isArray(current) ? [...current] : [];
	if (checked) {
		return arr.includes(role) ? arr : [...arr, role];
	}
	return arr.filter((r) => r !== role);
}

const RoleChip = ({ role, label, checked, onChange }) => (
	<label className={`cmt-mntn-role-chip${checked ? " is-checked" : ""}`}>
		<input
			type="checkbox"
			checked={checked}
			onChange={(e) => onChange(role, e.target.checked)}
		/>
		<span className="cmt-mntn-role-chip__label">{label}</span>
	</label>
);

const RolesSettings = () => {
	const { settings, updateSetting } = useSettings();

	// cmtMntn.editable_roles is raw get_editable_roles() output:
	// { slug: { name: 'Subscriber', capabilities: {...} }, ... }
	const editableRoles = cmtMntn.editable_roles || {};

	const enabledRoles = settings.cmt_mntn_enabled_user_roles || [];
	const disabledRoles = settings.cmt_mntn_disabled_mention_user_roles || [];

	// All roles except administrator for "who can mention".
	const mentionableRoles = Object.entries(editableRoles).filter(
		([slug]) => slug !== "administrator",
	);

	// All roles for "hide from suggestions" (admin can be hidden too).
	const allRoles = Object.entries(editableRoles);

	return (
		<>
			{/* Who can mention */}
			<div className="cmt-mntn-setting-row">
				<div className="cmt-mntn-setting-row__info">
					<div className="cmt-mntn-setting-row__label">
						{__("Who can mention", "comment-mention")}
					</div>
					<div className="cmt-mntn-setting-row__desc">
						{__(
							"Select which roles can @mention others in comments. Administrators can always mention.",
							"comment-mention",
						)}
					</div>
					<div className="cmt-mntn-role-grid" style={{ marginTop: "10px" }}>
						{mentionableRoles.map(([slug, details]) => (
							<RoleChip
								key={slug}
								role={slug}
								label={details.name}
								checked={enabledRoles.includes(slug)}
								onChange={(role, checked) =>
									updateSetting(
										"cmt_mntn_enabled_user_roles",
										toggleRole(enabledRoles, role, checked),
									)
								}
							/>
						))}
					</div>
				</div>
			</div>

			{/* Hide from suggestions */}
			<div className="cmt-mntn-setting-row">
				<div className="cmt-mntn-setting-row__info">
					<div className="cmt-mntn-setting-row__label">
						{__("Hide from suggestions", "comment-mention")}
					</div>
					<div className="cmt-mntn-setting-row__desc">
						{__(
							"Users in these roles will not appear in the @mention autocomplete dropdown.",
							"comment-mention",
						)}
					</div>
					<div className="cmt-mntn-role-grid" style={{ marginTop: "10px" }}>
						{allRoles.map(([slug, details]) => (
							<RoleChip
								key={slug}
								role={slug}
								label={details.name}
								checked={disabledRoles.includes(slug)}
								onChange={(role, checked) =>
									updateSetting(
										"cmt_mntn_disabled_mention_user_roles",
										toggleRole(disabledRoles, role, checked),
									)
								}
							/>
						))}
					</div>
				</div>
			</div>
		</>
	);
};

export default RolesSettings;
