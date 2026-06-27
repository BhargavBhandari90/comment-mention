/**
 * WordPress dependencies
 */
import {
	createContext,
	useContext,
	useState,
	useCallback,
} from '@wordpress/element';

const SettingsContext = createContext();

export function SettingsProvider( { children } ) {
	const [ settings, setSettings ] = useState( () => {
		const saved = cmtMntn.settings || {};
		const defaults = cmtMntn.defaults || {};

		return {
			...defaults,
			...saved,
		};
	} );

	const [ isSaving, setIsSaving ] = useState( false );
	const [ saveStatus, setSaveStatus ] = useState( null );
	const [ saveRevision, setSaveRevision ] = useState( 0 );

	const defaults = cmtMntn.defaults || {};

	function updateSetting( key, value ) {
		setSettings( ( prev ) => ( { ...prev, [ key ]: value } ) );
	}

	const saveSettings = useCallback( async () => {
		setIsSaving( true );
		setSaveStatus( null );

		try {
			const response = await fetch( `${ cmtMntn.apiUrl }/settings`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': cmtMntn.nonce,
				},
				body: JSON.stringify( settings ),
			} );

			if ( ! response.ok ) {
				throw new Error( `HTTP ${ response.status }` );
			}

			const saved = await response.json();

			// Sync state with what the server actually saved (post-sanitization).
			const defaults = cmtMntn.defaults || {};
			setSettings( { ...defaults, ...saved } );
			// setSettings( saved );
			setSaveRevision( ( revision ) => revision + 1 );
			setSaveStatus( 'success' );
		} catch ( err ) {
			console.error( '[Comment Mention] Save failed:', err );
			setSaveStatus( 'error' );
		} finally {
			setIsSaving( false );
			// Auto-clear the status badge after 3s.
			setTimeout( () => setSaveStatus( null ), 3000 );
		}
	}, [ settings ] );

	const value = {
		settings,
		updateSetting,
		saveSettings,
		isSaving,
		saveStatus,
		saveRevision,
	};

	return (
		<SettingsContext.Provider value={ value }>
			{ children }
		</SettingsContext.Provider>
	);
}

export const useSettings = () => useContext( SettingsContext );
