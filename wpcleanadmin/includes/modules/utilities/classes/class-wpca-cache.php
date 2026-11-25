<?php
/**
 * WPCleanAdmin Cache Manager
 *
 * @package WPCleanAdmin
 * @version  1.9.16
 * @author Tanox
 * @since 1.8.0
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的 Cache trait
require_once __DIR__ . '/class-wpca-cache-api-tasks.php';
require_once __DIR__ . '/class-wpca-cache-memory-tasks.php';
require_once __DIR__ . '/class-wpca-cache-database-tasks.php';
require_once __DIR__ . '/class-wpca-cache-file-tasks.php';
require_once __DIR__ . '/class-wpca-cache-file-cleanup-tasks.php';

/**
 * Cache manager class
 */
class Cache {

    use CacheApiTasks;
    use CacheMemoryTasks;
    use CacheDatabaseTasks;
    use CacheFileTasks;
    use CacheFileCleanupTasks;

    /**
     * Singleton instance
     *
     * @var Cache
     */
    private static $instance = null;

    /**
     * Memory cache
     *
     * @var array
     */
    private $memory_cache = array();

    /**
     * Cache enabled status
     *
     * @var bool
     */
    private $cache_enabled = true;

    /**
     * Cache expiration time (in seconds)
     *
     * @var int
     */
    private $cache_expiration = 3600;

    /**
     * Get singleton instance
     *
     * @return Cache
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
     * Initialize cache manager
     */
    public function init() {
        // Load cache settings
        $this->load_cache_settings();

        // 过期清理由定时任务承担，避免每请求全目录扫描
        $this->register_cache_cleanup_cron();
    }

    /**
     * 注册缓存清理定时任务
     *
     * @return void
     */
    private function register_cache_cleanup_cron(): void {
        if ( ! function_exists( '\add_action' ) || ! function_exists( '\wp_schedule_event' ) || ! function_exists( '\wp_next_scheduled' ) ) {
            return;
        }

        \add_action( 'wpca_cache_cleanup', array( $this, 'run_cache_cleanup' ) );

        if ( ! \wp_next_scheduled( 'wpca_cache_cleanup' ) ) {
            \wp_schedule_event( \time() + 3600, 'hourly', 'wpca_cache_cleanup' );
        }
    }

    /**
     * 执行缓存清理（定时任务入口）
     *
     * @return void
     */
    public function run_cache_cleanup(): void {
        $this->clean_expired_cache();
    }

    /**
     * Load cache settings
     */
    private function load_cache_settings() {
        $settings = ( function_exists( 'get_option' ) ? \get_option( 'wpca_settings', array() ) : array() );

        if ( isset( $settings['performance'] ) ) {
            if ( isset( $settings['performance']['cache_enabled'] ) ) {
                $this->cache_enabled = (bool) $settings['performance']['cache_enabled'];
            }

            if ( isset( $settings['performance']['cache_expiration'] ) ) {
                $this->cache_expiration = (int) $settings['performance']['cache_expiration'];
            }
        }

        // Set default expiration if not set
        if ( $this->cache_expiration <= 0 ) {
            $this->cache_expiration = 3600;
        }
    }

    /**
     * Set cache enabled status
     *
     * @param bool $enabled Enabled status
     */
    public function set_cache_enabled( bool $enabled ) {
        $this->cache_enabled = $enabled;
    }

    /**
     * Get cache enabled status
     *
     * @return bool
     */
    public function get_cache_enabled(): bool {
        return $this->cache_enabled;
    }

    /**
     * Set cache expiration time
     *
     * @param int $expiration Expiration time in seconds
     */
    public function set_cache_expiration( int $expiration ) {
        $this->cache_expiration = $expiration;
    }

    /**
     * Get cache expiration time
     *
     * @return int
     */
    public function get_cache_expiration(): int {
        return $this->cache_expiration;
    }
}
