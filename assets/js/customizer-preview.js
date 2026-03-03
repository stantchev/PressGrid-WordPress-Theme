/**
 * PressGrid Customizer Live Preview
 *
 * Uses postMessage transport for instant CSS variable updates.
 *
 * @package PressGrid
 */

( function ( wp ) {
	'use strict';

	var colorSettings = {
		pressgrid_primary_color:    '--pg-primary',
		pressgrid_secondary_color:  '--pg-secondary',
		pressgrid_accent_color:     '--pg-accent',
		pressgrid_bg_color:         '--pg-bg',
		pressgrid_text_color:       '--pg-text',
		pressgrid_link_hover_color: '--pg-link-hover',
	};

	Object.keys( colorSettings ).forEach( function ( settingId ) {
		var cssVar = colorSettings[ settingId ];
		wp.customize( settingId, function ( value ) {
			value.bind( function ( newVal ) {
				if ( newVal ) {
					document.documentElement.style.setProperty( cssVar, newVal );
				}
			} );
		} );
	} );

} ( wp ) );
