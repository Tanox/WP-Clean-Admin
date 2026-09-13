<?php
/**
 * WPCleanAdmin Performance Cache Tasks Trait
 *
 * 缓存清理与性能统计，从 class-wpca-performance.php 按职责抽离，公开方法契约不变。
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
 * 缓存任务 trait
 */
trait PerformanceCacheTasks {

    /**
     * Clear cache
     *
     * @return array Cache clearing results
     */
    public function clear_cache() {
        $results = array(
            'success' => true,
            'message' => \__( 'Cache cleared successfully', \WPCA_TEXT_DOMAIN ),
            'caches' => array()
        );

        // Clear WordPress object cache
        if ( function_exists( '\wp_cache_flush' ) ) {
            \wp_cache_flush();
            $results['caches'][] = array(
                'name' => \__( 'WordPress Object Cache', \WPCA_TEXT_DOMAIN ),
                'cleared' => true
            );
        }

        // Clear transients
        $this->run_transient_cleanup();
        $results['caches'][] = array(
            'name' => \__( 'Transients', \WPCA_TEXT_DOMAIN ),
            'cleared' => true
        );

        // Clear opcode cache if available
        if ( function_exists( '\opcache_reset' ) ) {
            \opcache_reset();
            $results['caches'][] = array(
                'name' => \__( 'OPcache', \WPCA_TEXT_DOMAIN ),
                'cleared' => true
            );
        }

        return $results;
    }

    /**
     * Get performance statistics
     *
     * @return array Performance statistics
     */
    public function get_performance_stats() {
        global $wpdb;

        $stats = array();

        // Get PHP memory usage
        $stats['memory_usage'] = array(
            'current' => \size_format( \memory_get_usage() ),
            'peak' => \size_format( \memory_get_peak_usage() ),
            'limit' => \ini_get( 'memory_limit' )
        );

        // Get database query count
        $stats['query_count'] = function_exists( '\get_num_queries' ) ? \get_num_queries() : 0;

        // Get page load time
        $stats['load_time'] = function_exists( '\timer_stop' ) ? \timer_stop( 0, 3 ) . 's' : '0.000s';

        // Get transients count
        $stats['transients_count'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%transient%'" );

        // Get cache status
        $stats['cache_status'] = array(
            'object_cache' => function_exists( '\wp_cache_get' ) ? \__( 'Enabled', \WPCA_TEXT_DOMAIN ) : \__( 'Disabled', \WPCA_TEXT_DOMAIN ),
            'opcache' => function_exists( '\opcache_get_status' ) ? \__( 'Enabled', \WPCA_TEXT_DOMAIN ) : \__( 'Disabled', \WPCA_TEXT_DOMAIN )
        );

        return $stats;
    }
}
