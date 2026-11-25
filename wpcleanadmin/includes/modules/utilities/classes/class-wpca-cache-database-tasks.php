<?php
/**
 * WPCleanAdmin Cache Database Tasks
 *
 * 承载数据库（options 表）缓存后端的读写删清逻辑，从 Cache 主类抽取。
 *
 * @package WPCleanAdmin
 * @version  1.9.18
 * @author Tanox
 * @since 1.9.14
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 数据库缓存后端任务（trait）。
 */
trait CacheDatabaseTasks {

    /**
     * Get database cache
     *
     * @param string $key Cache key
     * @param mixed $default Default value if cache not found
     * @return mixed
     */
    private function get_database_cache( string $key, $default = null ) {
        if ( ! function_exists( 'get_option' ) ) {
            return $default;
        }

        $cache_key = 'wpca_cache_' . $key;
        $cache = \get_option( $cache_key, false );

        if ( $cache !== false ) {
            // Check if cache has expired
            if ( isset( $cache['expiration'] ) && $cache['expiration'] < \time() ) {
                \delete_option( $cache_key );
                return $default;
            }

            return $cache['value'];
        }

        return $default;
    }

    /**
     * Set database cache
     *
     * @param string $key Cache key
     * @param mixed $value Cache value
     * @param int $expiration Expiration time in seconds
     * @return bool
     */
    private function set_database_cache( string $key, $value, int $expiration ): bool {
        if ( ! function_exists( 'update_option' ) ) {
            return false;
        }

        $cache_key = 'wpca_cache_' . $key;
        $cache = array(
            'value' => $value,
            'expiration' => \time() + $expiration,
            'created_at' => \time()
        );

        return \update_option( $cache_key, $cache, false );
    }

    /**
     * Delete database cache
     *
     * @param string $key Cache key
     * @return bool
     */
    private function delete_database_cache( string $key ): bool {
        if ( ! function_exists( 'delete_option' ) ) {
            return false;
        }

        $cache_key = 'wpca_cache_' . $key;
        return \delete_option( $cache_key );
    }

    /**
     * Clear database cache
     *
     * @return bool
     */
    private function clear_database_cache(): bool {
        if ( ! function_exists( 'delete_option' ) ) {
            return false;
        }

        // 分批查询并删除，避免一次无界扫描
        global $wpdb;
        if ( ! isset( $wpdb ) || ! is_object( $wpdb ) || ! method_exists( $wpdb, 'get_col' ) ) {
            return true;
        }

        $like = $wpdb->esc_like( 'wpca_cache_' ) . '%';

        do {
            $cache_keys = $wpdb->get_col(
                $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT 500", $like )
            );
            $cache_keys = is_array( $cache_keys ) ? $cache_keys : array();

            foreach ( $cache_keys as $cache_key ) {
                \delete_option( $cache_key );
            }
        } while ( count( $cache_keys ) === 500 );

        return true;
    }

    /**
     * 清理过期的数据库缓存条目
     *
     * @return int 删除条目数
     */
    private function clean_expired_database_cache(): int {
        global $wpdb;
        if ( ! isset( $wpdb ) || ! is_object( $wpdb ) || ! method_exists( $wpdb, 'get_results' ) ) {
            return 0;
        }

        $like  = $wpdb->esc_like( 'wpca_cache_' ) . '%';
        $removed = 0;

        do {
            $rows = $wpdb->get_results(
                $wpdb->prepare( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT 500", $like )
            );
            $rows = is_array( $rows ) ? $rows : array();

            foreach ( $rows as $row ) {
                $value = function_exists( 'maybe_unserialize' ) ? \maybe_unserialize( $row->option_value ) : $row->option_value;

                if ( is_array( $value ) && isset( $value['expiration'] ) && $value['expiration'] < \time() ) {
                    \delete_option( $row->option_name );
                    $removed++;
                }
            }
        } while ( count( $rows ) === 500 );

        return $removed;
    }
}
