<?php
/**
 * WPCleanAdmin Helpers Format Tasks
 *
 * 承载格式化与环境信息读取逻辑，从 Helpers 主类抽取。
 *
 * @package WPCleanAdmin
 * @version  1.9.14
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.14
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 格式化与环境信息任务（trait）。
 */
trait HelpersFormatTasks {

    /**
     * Format bytes to human readable size
     *
     * @param int $bytes Bytes to format
     * @param int $precision Precision of the result
     * @return string Human readable size
     */
    public function format_bytes( $bytes, $precision = 2 ) {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

        $bytes = max( $bytes, 0 );
        $pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
        $pow = min( $pow, count( $units ) - 1 );

        $bytes /= pow( 1024, $pow );

        return round( $bytes, $precision ) . ' ' . $units[ $pow ];
    }

    /**
     * Format seconds to human readable time
     *
     * @param int $seconds Seconds to format
     * @return string Human readable time
     */
    public function format_seconds( int $seconds ): string {
        $days = floor( $seconds / 86400 );
        $hours = floor( ( $seconds % 86400 ) / 3600 );
        $minutes = floor( ( $seconds % 3600 ) / 60 );
        $seconds = $seconds % 60;

        $result = array();

        if ( $days > 0 ) {
            $result[] = sprintf( \_n( '%d day', '%d days', $days, \WPCA_TEXT_DOMAIN ), $days );
        }
        if ( $hours > 0 ) {
            $result[] = sprintf( \_n( '%d hour', '%d hours', $hours, \WPCA_TEXT_DOMAIN ), $hours );
        }
        if ( $minutes > 0 ) {
            $result[] = sprintf( \_n( '%d minute', '%d minutes', $minutes, \WPCA_TEXT_DOMAIN ), $minutes );
        }
        if ( $seconds > 0 ) {
            $result[] = sprintf( \_n( '%d second', '%d seconds', $seconds, \WPCA_TEXT_DOMAIN ), $seconds );
        }

        return implode( ', ', $result );
    }

    /**
     * Get plugin information
     *
     * @param string $field Field to get
     * @return mixed Plugin information
     */
    public function get_plugin_info( $field = '' ) {
        if ( ! function_exists( '\get_plugin_data' ) ) {
            return '';
        }

        $plugin_data = \get_plugin_data( \WPCA_PLUGIN_DIR . 'wp-clean-admin.php' );

        if ( empty( $field ) ) {
            return $plugin_data;
        }

        return isset( $plugin_data[ $field ] ) ? $plugin_data[ $field ] : '';
    }

    /**
     * Get WordPress version
     *
     * @return string WordPress version
     */
    public function get_wp_version(): string {
        global $wp_version;
        return $wp_version;
    }

    /**
     * Get PHP version
     *
     * @return string PHP version
     */
    public function get_php_version(): string {
        return PHP_VERSION;
    }

    /**
     * Get database version
     *
     * @return string Database version
     */
    public function get_db_version() {
        global $wpdb;
        return $wpdb->db_version();
    }

    /**
     * Get server information
     *
     * @return string Server information
     */
    public function get_server_info(): string {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    /**
     * Check if plugin is network activated
     *
     * @return bool True if network activated, false otherwise
     */
    public function is_network_activated() {
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            if ( defined( '\ABSPATH' ) ) {
                require_once \ABSPATH . '/wp-admin/includes/plugin.php';
            } else {
                return false;
            }
        }

        if ( function_exists( 'plugin_basename' ) ) {
            return \is_plugin_active_for_network( \plugin_basename( \WPCA_PLUGIN_DIR . 'wp-clean-admin.php' ) );
        }

        return false;
    }

    /**
     * Get current admin page URL
     *
     * @return string Current admin page URL
     */
    public function get_current_admin_url() {
        return \admin_url( \add_query_arg( array(), $_SERVER['REQUEST_URI'] ) );
    }

    /**
     * Get plugin settings page URL
     *
     * @param string $tab Tab to open
     * @return string Settings page URL
     */
    public function get_settings_url( $tab = '' ) {
        $url = \admin_url( 'admin.php?page=wp-clean-admin' );

        if ( ! empty( $tab ) ) {
            $url .= '&tab=' . $tab;
        }

        return $url;
    }
}
