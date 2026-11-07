<?php
/**
 * WPCleanAdmin Cache API Tasks
 *
 * 承载缓存的统一读写删清分发入口，从 Cache 主类抽取。
 *
 * @package WPCleanAdmin
 * @version  1.9.14
 * @author Tanox
 * @since 1.9.14
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 缓存分发 API 任务（trait）。
 */
trait CacheApiTasks {

    /**
     * Get cache
     *
     * @param string $key Cache key
     * @param mixed $default Default value if cache not found
     * @param string $type Cache type (memory, database, file)
     * @return mixed
     */
    public function get( string $key, $default = null, string $type = 'memory' ) {
        if ( ! $this->cache_enabled ) {
            return $default;
        }

        switch ( $type ) {
            case 'memory':
                return $this->get_memory_cache( $key, $default );
            case 'database':
                return $this->get_database_cache( $key, $default );
            case 'file':
                return $this->get_file_cache( $key, $default );
            default:
                return $this->get_memory_cache( $key, $default );
        }
    }

    /**
     * Set cache
     *
     * @param string $key Cache key
     * @param mixed $value Cache value
     * @param int $expiration Expiration time in seconds
     * @param string $type Cache type (memory, database, file)
     * @return bool
     */
    public function set( string $key, $value, int $expiration = 0, string $type = 'memory' ): bool {
        if ( ! $this->cache_enabled ) {
            return false;
        }

        if ( $expiration <= 0 ) {
            $expiration = $this->cache_expiration;
        }

        switch ( $type ) {
            case 'memory':
                return $this->set_memory_cache( $key, $value, $expiration );
            case 'database':
                return $this->set_database_cache( $key, $value, $expiration );
            case 'file':
                return $this->set_file_cache( $key, $value, $expiration );
            default:
                return $this->set_memory_cache( $key, $value, $expiration );
        }
    }

    /**
     * Delete cache
     *
     * @param string $key Cache key
     * @param string $type Cache type (memory, database, file)
     * @return bool
     */
    public function delete( string $key, string $type = 'memory' ): bool {
        switch ( $type ) {
            case 'memory':
                return $this->delete_memory_cache( $key );
            case 'database':
                return $this->delete_database_cache( $key );
            case 'file':
                return $this->delete_file_cache( $key );
            default:
                return $this->delete_memory_cache( $key );
        }
    }

    /**
     * Clear all cache
     *
     * @param string $type Cache type (memory, database, file, all)
     * @return bool
     */
    public function clear( string $type = 'all' ): bool {
        switch ( $type ) {
            case 'memory':
                return $this->clear_memory_cache();
            case 'database':
                return $this->clear_database_cache();
            case 'file':
                return $this->clear_file_cache();
            case 'all':
                return $this->clear_memory_cache() &&
                       $this->clear_database_cache() &&
                       $this->clear_file_cache();
            default:
                return false;
        }
    }
}
