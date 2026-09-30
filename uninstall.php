<?php
/**
 * Uninstall handler: remove the options this plugin stores.
 *
 * Carousels themselves live in Elementor's page data and are not touched.
 *
 * @package DPCE
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'dpce_version' );
