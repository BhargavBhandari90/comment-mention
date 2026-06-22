/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

/**
 * Local dependencies
 */
import App from './App';

if ( window.cmtMntnAdmin?.restNonce ) {
	apiFetch.use(
		apiFetch.createNonceMiddleware( window.cmtMntnAdmin.restNonce )
	);
}

domReady( () => {
	const rootElement = document.getElementById( 'cmt-mntn-admin-page' );

	if ( rootElement ) {
		const root = createRoot( rootElement );
		root.render( <App /> );
	}
} );
