<?php
/**
 * WP Clean Admin uninstall routine
 *
 * Removes all plugin data (options, transients, user meta) when the plugin
 * is deleted through the WordPress admin. Triggered by WordPress core only.
 *
 * @package WPCleanAdmin
 * @version 1.9.0
 */

// Exit if not uninstalling from WordPress admin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Known plugin options.
$wpca_options = array(
	'wpca_settings',
	'wpca_database_settings',
	'wpca_performance_settings',
	'wpca_menu_customizer_settings',
	'wpca_menu_items',
	'wpca_disable_rest_api',
);

foreach ( $wpca_options as $wpca_option ) {
	delete_option( $wpca_option );
}

// Remove any remaining plugin options, transients and their timeouts.
global $wpdb;

$wpca_like = $wpdb->esc_like( 'wpca_' ) . '%';

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$wpca_like}'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_wpca\\_%' OR option_name LIKE '\\_transient\\_timeout\\_wpca\\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// Remove plugin user meta (2FA secret, enabled flag, captcha prefs, etc.).
$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '{$wpca_like}'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
