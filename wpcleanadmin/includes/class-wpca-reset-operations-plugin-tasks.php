<?php
/**
 * WPCleanAdmin Reset Operations Plugin Tasks
 *
 * 整站重置编排与 WP 调度/角色清理封装，从 ResetOperationsTasks 抽离。
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
 * 重置操作（整站编排）任务（trait）。
 */
trait ResetOperationsPluginTasks {

    /**
     * Reset plugin completely
     *
     * @return array Reset results
     */
    public function reset_plugin() {
        $results = array(
            'success' => true,
            'message' => \__( 'Plugin reset successfully', WPCA_TEXT_DOMAIN ),
        );

        // Reset settings
        $settings_result = $this->reset_settings();

        if ( ! $settings_result['success'] ) {
            $results['success'] = false;
            $results['message'] = $settings_result['message'];
            return $results;
        }

        // Remove scheduled events
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

        // Remove custom roles
        $settings = wpca_get_settings();

        if ( isset( $settings['user_roles'] ) && isset( $settings['user_roles']['custom_roles'] ) ) {
            foreach ( $settings['user_roles']['custom_roles'] as $role_slug => $role_data ) {
                $this->remove_role( $role_slug );
            }
        }

        return $results;
    }

    /**
     * Wrapper for wp_next_scheduled function
     *
     * @param string $event Event hook name
     * @return int|false Timestamp or false
     */
    private function wp_next_scheduled( $event ) {
        if ( function_exists( 'wp_next_scheduled' ) ) {
            return wp_next_scheduled( $event );
        }
        return false;
    }

    /**
     * Wrapper for wp_unschedule_event function
     *
     * @param int $timestamp Timestamp
     * @param string $event Event hook name
     * @param array $args Event arguments
     */
    private function wp_unschedule_event( $timestamp, $event, $args = array() ) {
        if ( function_exists( 'wp_unschedule_event' ) ) {
            wp_unschedule_event( $timestamp, $event, $args );
        }
    }

    /**
     * Wrapper for remove_role function
     *
     * @param string $role Role slug
     */
    private function remove_role( $role ) {
        if ( function_exists( 'remove_role' ) ) {
            remove_role( $role );
        }
    }
}
