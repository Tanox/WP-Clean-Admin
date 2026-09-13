<?php
/**
 * WPCleanAdmin Performance Database Tasks Trait
 *
 * 数据库优化与 transient 清理（调度 + 执行），
 * 从 class-wpca-performance.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 数据库优化任务 trait
 */
trait PerformanceDatabaseTasks {

    /**
     * Optimize database
     */
    public function optimize_database() {
        // Schedule database optimization
        if ( function_exists( '\wp_next_scheduled' ) && function_exists( '\wp_schedule_event' ) ) {
            if ( ! \wp_next_scheduled( 'wpca_optimize_database' ) ) {
                \wp_schedule_event( time(), 'weekly', 'wpca_optimize_database' );
            }
        }
    }

    /**
     * Clean transients
     */
    public function clean_transients() {
        // Schedule transient cleanup
        if ( function_exists( '\wp_next_scheduled' ) && function_exists( '\wp_schedule_event' ) ) {
            if ( ! \wp_next_scheduled( 'wpca_clean_transients' ) ) {
                \wp_schedule_event( time(), 'daily', 'wpca_clean_transients' );
            }
        }

        // Add transient cleanup hook
        \add_action( 'wpca_clean_transients', array( $this, 'run_transient_cleanup' ) );
    }

    /**
     * Run transient cleanup
     */
    public function run_transient_cleanup() {
        global $wpdb;

        // Delete expired transients
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d", '%_transient_timeout_%', time() ) );
        $wpdb->query( $wpdb->prepare( "DELETE t1 FROM {$wpdb->options} t1 INNER JOIN {$wpdb->options} t2 ON t1.option_name = CONCAT( '_transient_', SUBSTRING( t2.option_name, 19 ) ) WHERE t2.option_name LIKE %s", '%_transient_timeout_%' ) );
    }
}
