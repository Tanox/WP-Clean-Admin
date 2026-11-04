<?php
/**
 * WPCleanAdmin Core Utility Functions
 *
 * 承载版本、调试与日志等通用工具，从 wpca-core-functions.php 按职责抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.10
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get plugin version
 *
 * @return string Plugin version
 */
function wpca_get_version() {
    return defined( 'WPCA_VERSION' ) ? WPCA_VERSION : '1.8.0';
}

/**
 * Check if plugin is in debug mode
 *
 * @return bool True if debug mode is enabled, false otherwise
 */
function wpca_is_debug() {
    return defined( 'WP_DEBUG' ) && WP_DEBUG;
}

/**
 * Log debug message
 *
 * @param mixed $message Message to log
 * @param string $context Context of the log
 */
function wpca_log( $message, $context = 'general' ) {
    if ( ! wpca_is_debug() ) {
        return;
    }
    
    $log_message = sprintf( '[WPCleanAdmin] [%s] %s', $context, $message );
    
    if ( is_array( $message ) || is_object( $message ) ) {
        $log_message = sprintf( '[WPCleanAdmin] [%s] %s', $context, print_r( $message, true ) );
    }
    
    error_log( $log_message );
}

/**
 * Check if WordPress version is greater than or equal to specified version
 *
 * @param string $version Version to check against
 * @return bool True if WordPress version is greater than or equal to specified version, false otherwise
 */
function wpca_is_wp_version_gte( $version ) {
    global $wp_version;
    return version_compare( $wp_version, $version, '>=' );
}
