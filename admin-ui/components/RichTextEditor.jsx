/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { BaseControl } from '@wordpress/components';
import { useEffect, useRef } from '@wordpress/element';

/**
 * RichTextEditor
 *
 * TinyMCE WYSIWYG editor initialized via wp.editor — the same
 * underlying engine as wp_editor() in PHP, now usable inside a
 * React component tree.
 *
 * Falls back to a plain <textarea> if wp.editor is unavailable
 * (e.g. on a page where TinyMCE assets weren't enqueued).
 *
 * @param {string}   value       Current HTML string value.
 * @param {Function} onChange    Called with new HTML string on change.
 * @param {string}   [label]     Field label.
 * @param {string}   [help]      Help text shown below the editor.
 * @param {string}   [id]        Editor instance ID. Must be unique per page.
 */
export default function RichTextEditor( {
	value,
	onChange,
	label,
	help,
	id = 'cmt-mntn-mail-content',
	rows = 8,
} ) {
	const textareaRef = useRef( null );
	const initializedRef = useRef( false );

	// Keep onChange in a ref so the TinyMCE event handler never
	// captures a stale closure — same pattern as the source example.
	const onChangeRef = useRef( onChange );
	onChangeRef.current = onChange;

	useEffect( () => {
		if ( initializedRef.current || ! textareaRef.current ) {
			return;
		}

		if ( typeof wp === 'undefined' || ! wp.editor?.initialize ) {
			return;
		}

		initializedRef.current = true;

		wp.editor.initialize( id, {
			tinymce: {
				wpautop: true,
				// Minimal toolbar — only what makes sense for an email template.
				toolbar1:
					'undo redo bold italic underline alignleft aligncenter alignright bullist numlist link',
				setup: ( editor ) => {
					editor.on( 'change keyup', () => {
						onChangeRef.current( editor.getContent() );
					} );
				},
			},
			quicktags: false,
			mediaButtons: false,
		} );

		return () => {
			if ( wp.editor?.remove ) {
				wp.editor.remove( id );
			}
			initializedRef.current = false;
		};
	}, [ id ] );

	return (
		<BaseControl label={ label } help={ help }>
			<div className="cmt-mntn-rte">
				<textarea
					ref={ textareaRef }
					id={ id }
					defaultValue={ value }
					rows={ rows || 8 }
					className="cmt-mntn-rte__textarea"
					style={ { width: '100%' } }
				/>
			</div>
		</BaseControl>
	);
}
