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
delete_option( 'dpce_installed_at' );
delete_option( 'dpce_first_use' );
delete_metadata( 'user', 0, 'dpce_review_state', '', true );
