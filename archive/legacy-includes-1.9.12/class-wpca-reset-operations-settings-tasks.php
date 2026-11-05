<?php
/**
 * WPCleanAdmin Reset Operations Settings Tasks
 *
 * 各模块设置项重置（选项增删），从 ResetOperationsTasks 抽离。
 *
 * @package WPCleanAdmin
 * @version 1.9.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 重置操作（设置项）任务（trait）。
 */
trait ResetOperationsSettingsTasks {

    /**
     * Reset plugin settings
     *
     * @return array Reset results
     */
    public function reset_settings() {
        $results = array(
            'success' => true,
            'message' => \__( 'Settings reset successfully', WPCA_TEXT_DOMAIN ),
        );

        // Delete all plugin options
        $options = array(
            'wpca_settings',
            'wpca_menu_customizer_settings',
            'wpca_menu_items',
            'wpca_login_attempts_*',
        );

        foreach ( $options as $option ) {
            if ( strpos( $option, '*' ) !== false ) {
                // Delete all options matching the pattern
                global $wpdb;
                $pattern = str_replace( '*', '%', $option );
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $pattern ) );
            } else {
                // Delete specific option
                \delete_option( $option );
            }
        }

        return $results;
    }

    /**
     * Reset specific module settings
     *
     * @param string $module Module name
     * @return array Reset results
     */
    public function reset_module_settings( string $module ): array {
        $results = array(
            'success' => true,
            'message' => sprintf( \__( '%s module settings reset successfully', WPCA_TEXT_DOMAIN ), ucfirst( $module ) ),
        );

        // Get all settings
        $settings = wpca_get_settings();

        // Reset specific module settings
        if ( isset( $settings[ $module ] ) ) {
            unset( $settings[ $module ] );
            \update_option( 'wpca_settings', $settings );
        } else {
            $results['success'] = false;
            $results['message'] = sprintf( \__( 'Invalid module: %s', WPCA_TEXT_DOMAIN ), $module );
        }

        return $results;
    }

    /**
     * Reset dashboard widgets
     *
     * @return array Reset results
     */
    public function reset_dashboard_widgets() {
        $results = array(
            'success' => true,
            'message' => \__( 'Dashboard widgets reset successfully', WPCA_TEXT_DOMAIN ),
        );

        // Delete dashboard widget settings
        \delete_option( 'wp_user_dashboard_widgets' );
        \delete_option( 'dashboard_widget_options' );

        return $results;
    }

    /**
     * Reset admin menu
     *
     * @return array Reset results
     */
    public function reset_admin_menu() {
        $results = array(
            'success' => true,
            'message' => \__( 'Admin menu reset successfully', WPCA_TEXT_DOMAIN ),
        );

        // Delete admin menu settings
        \delete_option( 'wpca_menu_items' );
        \delete_option( 'wpca_menu_customizer_settings' );

        return $results;
    }

    /**
     * Reset login settings
     *
     * @return array Reset results
     */
    public function reset_login_settings() {
        $results = array(
            'success' => true,
            'message' => \__( 'Login settings reset successfully', WPCA_TEXT_DOMAIN ),
        );

        // Get all settings
        $settings = wpca_get_settings();

        // Reset login settings
        if ( isset( $settings['login'] ) ) {
            unset( $settings['login'] );
            \update_option( 'wpca_settings', $settings );
        }

        // Delete login attempt transients
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'wpca_login_attempts_%' ) );

        return $results;
    }

    /**
     * Reset performance settings
     *
     * @return array Reset results
     */
    public function reset_performance_settings() {
        $results = array(
            'success' => true,
            'message' => \__( 'Performance settings reset successfully', WPCA_TEXT_DOMAIN ),
        );

        // Get all settings
        $settings = wpca_get_settings();

        // Reset performance settings
        if ( isset( $settings['performance'] ) ) {
            unset( $settings['performance'] );
            \update_option( 'wpca_settings', $settings );
        }

        // Remove scheduled performance events
        $events = array(
            'wpca_optimize_database',
            'wpca_clean_transients',
        );

        foreach ( $events as $event ) {
            $timestamp = $this->wp_next_scheduled( $event );
            if ( $timestamp ) {
                $this->wp_unschedule_event( $timestamp, $event );
            }
        }

        return $results;
    }
}
