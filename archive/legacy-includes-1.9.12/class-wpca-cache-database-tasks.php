<?php
/**
 * Cache Database Tasks
 *
 * Database (options-table) cache backend implementations.
 *
 * @package WPCleanAdmin
 * @version 1.9.0
 * @author Tanox
 * @since 1.8.0
 */
namespace WPCleanAdmin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Database cache backend tasks for the Cache class.
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
        if ( ! function_exists( 'get_options' ) && ! function_exists( 'delete_option' ) ) {
            return false;
        }

        // Get all cache options
        global $wpdb;
        if ( isset( $wpdb ) ) {
            $cache_keys = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'wpca_cache_%'" );

            foreach ( $cache_keys as $cache_key ) {
                \delete_option( $cache_key );
            }
        }

        return true;
    }
}
