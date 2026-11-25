<?php
/**
 * WPCleanAdmin Cache File Cleanup Tasks
 *
 * 承载文件缓存的过期回收与临时文件清理逻辑，从 CacheFileTasks 抽取。
 *
 * @package WPCleanAdmin
 * @version  1.9.18
 * @author Tanox
 * @since 1.9.18
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 文件缓存清理任务（trait）。
 */
trait CacheFileCleanupTasks {

    /**
     * Clean expired cache
     */
    private function clean_expired_cache() {
        // Clean expired file cache
        $this->clean_expired_file_cache();

        // Clean expired database cache
        $this->clean_expired_database_cache();
    }

    /**
     * Clean expired file cache
     */
    private function clean_expired_file_cache() {
        $cache_dir = $this->get_cache_directory();

        if ( ! \is_dir( $cache_dir ) ) {
            return;
        }

        try {
            $iterator = new \FilesystemIterator( $cache_dir, \FilesystemIterator::SKIP_DOTS );
        } catch ( \Exception $e ) {
            return;
        }

        foreach ( $iterator as $file ) {
            if ( ! $file->isFile() ) {
                continue;
            }

            $path = $file->getPathname();

            // 清理写入中断留下的临时文件
            if ( '.part' === substr( $path, -5 ) ) {
                \unlink( $path );
                continue;
            }

            if ( '.cache' !== substr( $path, -6 ) ) {
                continue;
            }

            $cache_data = \file_get_contents( $path );
            $cache = \json_decode( $cache_data, true );

            // 无效（含旧序列化格式）文件直接清理
            if ( ! \is_array( $cache ) ) {
                \unlink( $path );
                continue;
            }

            if ( isset( $cache['expiration'] ) && $cache['expiration'] < \time() ) {
                \unlink( $path );
            }
        }
    }
}
