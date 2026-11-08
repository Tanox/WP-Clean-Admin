<?php
/**
 * WPCleanAdmin Cache Memory Tasks
 *
 * 承载内存缓存后端的读写删清逻辑，从 Cache 主类抽取。
 *
 * @package WPCleanAdmin
 * @version  1.9.16
 * @author Tanox
 * @since 1.9.14
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 内存缓存后端任务（trait）。
 */
trait CacheMemoryTasks {

    /**
     * Get memory cache
     *
     * @param string $key Cache key
     * @param mixed $default Default value if cache not found
     * @return mixed
     */
    private function get_memory_cache( string $key, $default = null ) {
        if ( isset( $this->memory_cache[ $key ] ) ) {
            $cache = $this->memory_cache[ $key ];

            // Check if cache has expired
            if ( isset( $cache['expiration'] ) && $cache['expiration'] < \time() ) {
                unset( $this->memory_cache[ $key ] );
                return $default;
            }

            return $cache['value'];
        }

        return $default;
    }

    /**
     * Set memory cache
     *
     * @param string $key Cache key
     * @param mixed $value Cache value
     * @param int $expiration Expiration time in seconds
     * @return bool
     */
    private function set_memory_cache( string $key, $value, int $expiration ): bool {
        $this->memory_cache[ $key ] = array(
            'value' => $value,
            'expiration' => \time() + $expiration
        );

        return true;
    }

    /**
     * Delete memory cache
     *
     * @param string $key Cache key
     * @return bool
     */
    private function delete_memory_cache( string $key ): bool {
        if ( isset( $this->memory_cache[ $key ] ) ) {
            unset( $this->memory_cache[ $key ] );
            return true;
        }

        return false;
    }

    /**
     * Clear memory cache
     *
     * @return bool
     */
    private function clear_memory_cache(): bool {
        $this->memory_cache = array();
        return true;
    }
}
