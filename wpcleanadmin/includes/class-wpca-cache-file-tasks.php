<?php
/**
 * Cache File Tasks
 *
 * File cache backend, cache-directory resolution and expired-cache cleanup.
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
 * File cache backend tasks for the Cache class.
 */
trait CacheFileTasks {

    /**
     * Get file cache
     *
     * @param string $key Cache key
     * @param mixed $default Default value if cache not found
     * @return mixed
     */
    private function get_file_cache( string $key, $default = null ) {
        $cache_file = $this->get_cache_file_path( $key );

        if ( \file_exists( $cache_file ) ) {
            $cache_data = \file_get_contents( $cache_file );
            $cache = \unserialize( $cache_data );

            // Check if cache has expired
            if ( isset( $cache['expiration'] ) && $cache['expiration'] < \time() ) {
                \unlink( $cache_file );
                return $default;
            }

            return $cache['value'];
        }

        return $default;
    }

    /**
     * Set file cache
     *
     * @param string $key Cache key
     * @param mixed $value Cache value
     * @param int $expiration Expiration time in seconds
     * @return bool
     */
    private function set_file_cache( string $key, $value, int $expiration ): bool {
        $cache_file = $this->get_cache_file_path( $key );
        $cache_dir = \dirname( $cache_file );

        // Create cache directory if it doesn't exist
        if ( ! \is_dir( $cache_dir ) ) {
            \wp_mkdir_p( $cache_dir );
        }

        $cache = array(
            'value' => $value,
            'expiration' => \time() + $expiration,
            'created_at' => \time()
        );

        return \file_put_contents( $cache_file, \serialize( $cache ) ) !== false;
    }

    /**
     * Delete file cache
     *
     * @param string $key Cache key
     * @return bool
     */
    private function delete_file_cache( string $key ): bool {
        $cache_file = $this->get_cache_file_path( $key );

        if ( \file_exists( $cache_file ) ) {
            return \unlink( $cache_file );
        }

        return false;
    }

    /**
     * Clear file cache
     *
     * @return bool
     */
    private function clear_file_cache(): bool {
        $cache_dir = $this->get_cache_directory();

        if ( \is_dir( $cache_dir ) ) {
            $files = \glob( $cache_dir . '/*' );

            foreach ( $files as $file ) {
                if ( \is_file( $file ) ) {
                    \unlink( $file );
                }
            }
        }

        return true;
    }

    /**
     * Get cache file path
     *
     * @param string $key Cache key
     * @return string
     */
    private function get_cache_file_path( string $key ): string {
        $cache_dir = $this->get_cache_directory();
        $key_hash = \md5( $key );

        return $cache_dir . '/' . $key_hash . '.cache';
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    private function get_cache_directory(): string {
        $cache_dir = WPCA_PLUGIN_DIR . 'cache';

        // Create cache directory if it doesn't exist
        if ( ! \is_dir( $cache_dir ) ) {
            \wp_mkdir_p( $cache_dir );
        }

        return $cache_dir;
    }

    /**
     * Clean expired cache
     */
    private function clean_expired_cache() {
        // Clean expired file cache
        $this->clean_expired_file_cache();

        // Clean expired database cache (done via cron)
    }

    /**
     * Clean expired file cache
     */
    private function clean_expired_file_cache() {
        $cache_dir = $this->get_cache_directory();

        if ( \is_dir( $cache_dir ) ) {
            $files = \glob( $cache_dir . '/*' );

            foreach ( $files as $file ) {
                if ( \is_file( $file ) ) {
                    $cache_data = \file_get_contents( $file );
                    $cache = \unserialize( $cache_data );

                    if ( isset( $cache['expiration'] ) && $cache['expiration'] < \time() ) {
                        \unlink( $file );
                    }
                }
            }
        }
    }
}
