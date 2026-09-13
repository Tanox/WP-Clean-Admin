<?php
/**
 * WPCleanAdmin Performance Class
 *
 * 性能优化门面类：保留单例、初始化与 WordPress stub 加载，具体优化任务
 * 按职责拆入 Performance_*_Tasks trait（禁用项/数据库/缓存/资源/预加载），
 * 公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

// Load WordPress stubs for IDE compatibility
require_once __DIR__ . '/wpca-wordpress-stubs.php';

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Performance class
 */
class Performance {

    use PerformanceDisableTasks;
    use PerformanceDatabaseTasks;
    use PerformanceCacheTasks;
    use PerformanceResourceTasks;
    use PerformancePreloadTasks;

    /**
     * Singleton instance
     *
     * @var Performance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Performance
     */
    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize the performance module
     */
    public function init(): void {
        // Load settings
        $settings = \wpca_get_settings();

        // Apply performance optimizations based on settings
        if ( isset( $settings['performance'] ) ) {
            // Disable emojis
            if ( isset( $settings['performance']['disable_emojis'] ) && $settings['performance']['disable_emojis'] ) {
                $this->disable_emojis();
            }

            // Disable XML-RPC
            if ( isset( $settings['performance']['disable_xmlrpc'] ) && $settings['performance']['disable_xmlrpc'] ) {
                $this->disable_xmlrpc();
            }

            // Disable REST API
            if ( isset( $settings['performance']['disable_rest_api'] ) && $settings['performance']['disable_rest_api'] ) {
                $this->disable_rest_api();
            }

            // Disable heartbeat
            if ( isset( $settings['performance']['disable_heartbeat'] ) && $settings['performance']['disable_heartbeat'] ) {
                $this->disable_heartbeat();
            }

            // Optimize database
            if ( isset( $settings['performance']['optimize_database'] ) && $settings['performance']['optimize_database'] ) {
                $this->optimize_database();
            }

            // Clean transients
            if ( isset( $settings['performance']['clean_transients'] ) && $settings['performance']['clean_transients'] ) {
                $this->clean_transients();
            }
        }

        // Add performance hooks
        \add_action( 'wpca_clear_cache', array( $this, 'clear_cache' ) );
    }
}
