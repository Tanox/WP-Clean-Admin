<?php
/**
 * Cache API Tasks
 *
 * Public cache API (get/set/delete/clear) and cache configuration accessors.
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
 * Public cache API and accessor tasks for the Cache class.
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
