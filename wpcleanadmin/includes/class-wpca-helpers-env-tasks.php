<?php
/**
 * Helpers Environment Tasks
 *
 * Environment and plugin-info helper methods for the Helpers class.
 *
 * @package WPCleanAdmin
 * @version 1.8.14
 * @author Tanox
 * @author URI: https://github.com/Tanox
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Environment and plugin-information helper tasks.
 */
trait HelpersEnvTasks {

    /**
     * Check if user has required capability
     *
     * @param string $capability Capability to check
     * @return bool True if user has capability, false otherwise
     */
    public function user_has_capability( $capability ) {
        if ( ! function_exists( 'current_user_can' ) ) {
            return false;
        }

        return \current_user_can( $capability );
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
