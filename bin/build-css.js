#!/usr/bin/env node
/**
 * Split assets/css/main.css into a small base file plus one file per template.
 *
 * main.css stays the single source of truth (and is still loaded in the
 * Elementor editor, where the template can change live). On the front end
 * the widget loads base.min.css and only the file for the template it uses.
 *
 * Usage: node bin/build-css.js
 * No dependencies. Rules are assigned to a template when every selector in
 * the rule mentions the same `.dpce-style-N` or `.dpce-wrapper-N` class.
 * `@keyframes dpce-style-N-*` goes with template N. Everything else is base.
 */
'use strict';

const fs = require( 'fs' );
const path = require( 'path' );

const root = path.resolve( __dirname, '..' );
const src = path.join( root, 'assets/css/main.css' );
const outDir = path.join( root, 'assets/css/dist' );

const stripComments = ( css ) => css.replace( /\/\*[\s\S]*?\*\//g, '' );

/**
 * Split CSS text into top level blocks: { prelude, body } where body includes braces.
 *
 * @param {string} css CSS without comments.
 * @return {Array<{prelude: string, body: string}>} Blocks.
 */
function topLevelBlocks( css ) {
	const blocks = [];
	let i = 0;
	while ( i < css.length ) {
		const open = css.indexOf( '{', i );
		if ( open < 0 ) {
			break;
		}
		let depth = 1;
		let k = open + 1;
		while ( depth > 0 && k < css.length ) {
			if ( '{' === css[ k ] ) {
				depth++;
			} else if ( '}' === css[ k ] ) {
				depth--;
			}
			k++;
		}
		blocks.push( { prelude: css.slice( i, open ).trim(), body: css.slice( open, k ) } );
		i = k;
	}
	return blocks;
}

const styleIdsIn = ( text ) => new Set( [ ...text.matchAll( /dpce-(?:style|wrapper)-(\d+)/g ) ].map( ( m ) => m[ 1 ] ) );

/**
 * Which template a plain rule belongs to, or null for base.
 *
 * @param {string} selectorList Selector list.
 * @return {string|null} Template id.
 */
function ownerOfSelector( selectorList ) {
	const parts = selectorList.split( ',' ).map( ( s ) => styleIdsIn( s ) );
	if ( parts.some( ( ids ) => 1 !== ids.size ) ) {
		return null;
	}
	const all = new Set( parts.flatMap( ( ids ) => [ ...ids ] ) );
	return 1 === all.size ? [ ...all ][ 0 ] : null;
}

function minify( css ) {
	return css
		.replace( /\s+/g, ' ' )
		.replace( /\s*([{}:;,>])\s*/g, '$1' )
		.replace( /;}/g, '}' )
		.replace( /\(\s+/g, '(' )
		.replace( /\s+\)/g, ')' )
		.trim() + '\n';
}

const files = { base: [] };
const add = ( key, text ) => {
	( files[ key ] = files[ key ] || [] ).push( text );
};

for ( const block of topLevelBlocks( stripComments( fs.readFileSync( src, 'utf8' ) ) ) ) {
	const { prelude, body } = block;

	if ( prelude.startsWith( '@keyframes' ) || prelude.startsWith( '@-webkit-keyframes' ) ) {
		const ids = styleIdsIn( prelude );
		add( 1 === ids.size ? [ ...ids ][ 0 ] : 'base', prelude + body );
		continue;
	}

	if ( prelude.startsWith( '@media' ) || prelude.startsWith( '@supports' ) ) {
		// Split the inner rules by owner and wrap each group in the same at-rule.
		const inner = topLevelBlocks( body.slice( 1, -1 ) );
		const groups = {};
		for ( const rule of inner ) {
			const owner = ownerOfSelector( rule.prelude ) || 'base';
			( groups[ owner ] = groups[ owner ] || [] ).push( rule.prelude + rule.body );
		}
		for ( const [ owner, rules ] of Object.entries( groups ) ) {
			add( owner, prelude + '{' + rules.join( '' ) + '}' );
		}
		continue;
	}

	if ( prelude.startsWith( '@' ) ) {
		add( 'base', prelude + body );
		continue;
	}

	add( ownerOfSelector( prelude ) || 'base', prelude + body );
}

fs.rmSync( outDir, { recursive: true, force: true } );
fs.mkdirSync( outDir, { recursive: true } );

const banner = '/* Generated from main.css by bin/build-css.js. Do not edit. */\n';
let total = 0;
for ( const [ key, parts ] of Object.entries( files ) ) {
	const name = 'base' === key ? 'base.min.css' : `style-${ key }.min.css`;
	const out = banner + minify( parts.join( '\n' ) );
	total += out.length;
	fs.writeFileSync( path.join( outDir, name ), out );
}
fs.writeFileSync( path.join( outDir, 'index.php' ), "<?php\n/**\n * Silence is golden.\n *\n * @package DPCE\n */\n" );

console.log( `Wrote ${ Object.keys( files ).length } files (${ total } bytes) to assets/css/dist/` );
