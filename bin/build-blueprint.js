#!/usr/bin/env node
/**
 * Build .wordpress-org/blueprints/blueprint.json, the WordPress Playground
 * blueprint behind the "Live Preview" button on WordPress.org.
 *
 * The demo content lives in bin/blueprint-demo.php so it can be read and
 * edited as normal PHP. This script puts it into the blueprint's runPHP step.
 *
 * Usage: node bin/build-blueprint.js
 *
 * For local testing, these environment variables replace the download
 * sources (the committed blueprint always uses the defaults):
 *   DPCE_IMAGES_URL   Folder with demo-1.jpg ... demo-6.jpg.
 *   DPCE_PLUGIN_ZIP   URL of a zip of this plugin instead of WordPress.org.
 *   DPCE_ELEMENTOR_ZIP, DPCE_THEME_ZIP  Same for Elementor and Hello Elementor.
 *   DPCE_OUT          Output file.
 */
'use strict';

const fs = require( 'fs' );
const path = require( 'path' );

const root = path.resolve( __dirname, '..' );
const slug = 'webcodingplace-post-carousel-for-elementor';
const imagesUrl = process.env.DPCE_IMAGES_URL ||
	'https://raw.githubusercontent.com/rameezwp/' + slug + '/main/.wordpress-org/blueprints/images';
const out = process.env.DPCE_OUT || path.join( root, '.wordpress-org/blueprints/blueprint.json' );

function source( envName, wpOrgSlug, kind ) {
	if ( process.env[ envName ] ) {
		return { resource: 'url', url: process.env[ envName ] };
	}
	return { resource: 'wordpress.org/' + kind, slug: wpOrgSlug };
}

const steps = [
	{
		step: 'installTheme',
		themeData: source( 'DPCE_THEME_ZIP', 'hello-elementor', 'themes' ),
		options: { activate: true },
	},
	{
		step: 'installPlugin',
		pluginData: source( 'DPCE_ELEMENTOR_ZIP', 'elementor', 'plugins' ),
		options: { activate: true },
	},
	{
		step: 'installPlugin',
		pluginData: source( 'DPCE_PLUGIN_ZIP', slug, 'plugins' ),
		options: { activate: true },
	},
];

for ( let i = 1; i <= 6; i++ ) {
	steps.push( {
		step: 'writeFile',
		path: '/tmp/dpce-demo-' + i + '.jpg',
		data: { resource: 'url', url: imagesUrl + '/demo-' + i + '.jpg' },
	} );
}

steps.push( {
	step: 'runPHP',
	code: fs.readFileSync( path.join( __dirname, 'blueprint-demo.php' ), 'utf8' ),
} );

const blueprint = {
	$schema: 'https://playground.wordpress.net/blueprint-schema.json',
	meta: {
		title: 'Post Carousel & Grid for Elementor demo',
		description: 'Elementor with six sample posts and a page showing the Card carousel, template 21, a grid and a news ticker.',
		author: 'webcodingplace',
		categories: [ 'Elementor', 'Content' ],
	},
	landingPage: '/',
	preferredVersions: { php: '8.3', wp: 'latest' },
	features: { networking: true },
	login: true,
	steps,
};

fs.writeFileSync( out, JSON.stringify( blueprint, null, '\t' ) + '\n' );
console.log( 'Wrote ' + path.relative( process.cwd(), out ) );
