import { Icon } from '@wordpress/icons';

/**
 * Renders a section within the settings layout.
 */
const Section = ( { icon, title, description, children } ) => (
	<div className="cmt-mntn-section">
		<div className="cmt-mntn-section__heading">
			{ icon && <Icon icon={ icon } /> }
			<h2 className="cmt-mntn-section__title">{ title }</h2>
		</div>
		{ description && (
			<p className="cmt-mntn-section__description">{ description }</p>
		) }
		{ children }
	</div>
);

export default Section;
