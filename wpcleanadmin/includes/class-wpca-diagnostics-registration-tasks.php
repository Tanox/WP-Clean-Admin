<?php
/**
 * Diagnostics Registration Tasks
 *
 * Registers the list of diagnostic checks for the Diagnostics class.
 *
 * @package WPCleanAdmin
 * @version 1.8.15
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.0
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check-registration tasks for the Diagnostics class.
 */
trait DiagnosticsRegistrationTasks {

    private function register_checks(): void {
        $this->checks = array(
            'php_version' => array(
                'name' => __( 'PHP Version', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_php_version' ),
                'category' => 'server',
                'severity' => 'critical'
            ),
            'wp_version' => array(
                'name' => __( 'WordPress Version', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_wp_version' ),
                'category' => 'core',
                'severity' => 'critical'
            ),
            'mysql_version' => array(
                'name' => __( 'MySQL Version', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_mysql_version' ),
                'category' => 'server',
                'severity' => 'critical'
            ),
            'memory_limit' => array(
                'name' => __( 'Memory Limit', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_memory_limit' ),
                'category' => 'server',
                'severity' => 'high'
            ),
            'wp_debug' => array(
                'name' => __( 'Debug Mode', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_wp_debug' ),
                'category' => 'core',
                'severity' => 'medium'
            ),
            'plugin_conflicts' => array(
                'name' => __( 'Plugin Conflicts', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_plugin_conflicts' ),
                'category' => 'plugins',
                'severity' => 'high'
            ),
            'theme_conflicts' => array(
                'name' => __( 'Theme Conflicts', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_theme_conflicts' ),
                'category' => 'themes',
                'severity' => 'high'
            ),
            'file_permissions' => array(
                'name' => __( 'File Permissions', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_file_permissions' ),
                'category' => 'security',
                'severity' => 'critical'
            ),
            'database_tables' => array(
                'name' => __( 'Database Tables', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_database_tables' ),
                'category' => 'database',
                'severity' => 'high'
            ),
            'ssl_status' => array(
                'name' => __( 'SSL Status', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_ssl_status' ),
                'category' => 'security',
                'severity' => 'critical'
            ),
            'cache_status' => array(
                'name' => __( 'Cache Status', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_cache_status' ),
                'category' => 'performance',
                'severity' => 'medium'
            ),
            'rest_api' => array(
                'name' => __( 'REST API', WPCA_TEXT_DOMAIN ),
                'callback' => array( $this, 'check_rest_api' ),
                'category' => 'core',
                'severity' => 'medium'
            )
        );
    }
}
