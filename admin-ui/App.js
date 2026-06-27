/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import {
	SlotFillProvider,
	Slot,
	withFilters,
	TabPanel,
} from '@wordpress/components';
import { Icon, people, seen, envelope } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { SettingsProvider, useSettings } from './context/SettingsContext';
import Section from './components/Section';
import PluginHeader from './components/PluginHeader';
import SaveButton from './components/SaveButton';
import SupportCard from './components/SupportCard';
import ProUpsellCard from './components/ProUpsellCard';
import RolesSettings from './components/Settings/RolesSettings';
import GeneralSettings from './components/Settings/GeneralSettings';
import EmailSettings from './components/Settings/EmailSettings';
import ProUpsell from './components/ProUpsell';

const SettingsLayout = () => {
	const hasPro = !! cmtMntn.hasPro;

	const tabs = [
		{
			name: 'general-settings',
			title: 'General',
			className: 'cmt-mntn-general-settings',
		},
		{
			name: 'pro-settings',
			title: 'Pro',
			className: 'cmt-mntn-pro-settings',
		},
	];

	// 1. Read initial tab from URL parameter, fallback to 'general-settings'
	const getTabFromUrl = () => {
		const searchParams = new URLSearchParams( window.location.search );
		console.log( searchParams );

		const tabParam = searchParams.get( 'tab' );
		// Ensure the tab in URL is valid
		return tabs.some( ( t ) => t.name === tabParam )
			? tabParam
			: 'general-settings';
	};

	const [ activeTabName, setActiveTabName ] = useState( getTabFromUrl );

	// 2. Keep the URL query string updated whenever activeTabName changes
	useEffect( () => {
		const url = new URL( window.location.href );

		url.searchParams.set( 'tab', activeTabName );

		// Update URL hash/history without triggering a page reload
		window.history.replaceState( null, '', url.toString() );
	}, [ activeTabName ] );

	// 3. Optional: Support browser back/forward buttons navigating tabs
	useEffect( () => {
		const handlePopState = () => {
			setActiveTabName( getTabFromUrl() );
		};
		window.addEventListener( 'popstate', handlePopState );
		return () => window.removeEventListener( 'popstate', handlePopState );
	}, [] );

	return (
		<div className="cmt-mntn-wrap">
			<PluginHeader />
			<TabPanel
				className="my-custom-tab-panel"
				activeClass="is-active"
				initialTabName={ activeTabName }
				onSelect={ ( tabName ) => setActiveTabName( tabName ) }
				tabs={ tabs }
			>
				{ ( activeTab ) => (
					<div className="cmt-mntn-body">
						{ /* Main settings card */ }
						<div className="cmt-mntn-card">
							<div className="cmt-mntn-card__header">
								<h2 className="cmt-mntn-card__title">
									{ /* DYNAMIC TITLE: Changes based on the active tab */ }
									{ activeTab.name === 'general-settings'
										? __(
												'General Settings',
												'comment-mention'
										  )
										: __(
												'Pro Settings',
												'comment-mention'
										  ) }
								</h2>
								<SaveButton />
							</div>

							<Slot name="SettingsPageTop" />

							{ /* CONDITIONAL CONTENT: Renders fields based on tab */ }
							{ activeTab.name === 'general-settings' && (
								<>
									<Section
										icon={ people }
										title={ __(
											'Mention permissions',
											'comment-mention'
										) }
										description={ __(
											'Control which users can @mention others and who appears in the autocomplete suggestions.',
											'comment-mention'
										) }
									>
										<RolesSettings />
									</Section>

									<Section
										icon={ seen }
										title={ __(
											'Display',
											'comment-mention'
										) }
										description={ __(
											'Adjust how the mention autocomplete dropdown looks to users.',
											'comment-mention'
										) }
									>
										<GeneralSettings />
									</Section>

									<Section
										icon={ envelope }
										title={ __(
											'Email notifications',
											'comment-mention'
										) }
										description={ __(
											'Configure whether and how users are emailed when they are mentioned in a comment.',
											'comment-mention'
										) }
									>
										<EmailSettings />
									</Section>
								</>
							) }

							{ activeTab.name === 'pro-settings' &&
								( hasPro ? (
									<Slot name="ProPluginSettings" />
								) : (
									<ProUpsell />
								) ) }

							<Slot name="SettingsPageBottom" />
						</div>

						{ /* Sidebar remains visible on all tabs, but you can also make it dynamic if needed */ }
						<div className="cmt-mntn-sidebar">
							{ activeTab.name !== 'pro-settings' && (
								<SupportCard />
							) }
							{ ! hasPro && <ProUpsellCard /> }
						</div>
					</div>
				) }
			</TabPanel>
		</div>
	);
};

/**
 * Main settings application.
 */
const App = () => {
	const AdditionalSettings = withFilters( 'cmtMntn.Settings' )( ( props ) => (
		<></>
	) );
	return (
		<SettingsProvider>
			<SlotFillProvider>
				<AdditionalSettings useSettings={ useSettings } />
				<SettingsLayout />
			</SlotFillProvider>
		</SettingsProvider>
	);
};

export default App;
