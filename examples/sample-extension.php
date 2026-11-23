<?php
/**
 * WP Clean Admin 扩展示例（T016）
 *
 * 演示基于 Extension_API（规范阶段，见 openspec/changes/规范改进与功能增强计划/specs/extension-api/spec.md）
 * 与现有公开接口的第三方扩展写法：
 *  - 通过 wpca_update_settings 过滤器干预设置入库
 *  - 复用 Helpers 的错误响应结构与日志
 *  - 复用 Cache 的三级缓存入口
 *
 * 使用方式：作为 mu-plugin 放入 wp-content/mu-plugins/，或包装为普通插件。
 *
 * @package WPCleanAdmin_Example_Extension
 * @version 1.9.17
 */

namespace WPCleanAdminExample;

use WPCleanAdmin\Modules\Utilities\Classes\Cache;
use WPCleanAdmin\Modules\Utilities\Classes\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 示例扩展主体。
 */
final class Sample_Extension {

    /**
     * 启动扩展。
     *
     * @return void
     */
    public static function boot(): void {
        // 1) 设置入库前干预：强制关闭某个性能子项，防止被管理端误开。
        add_filter( 'wpca_update_settings', array( __CLASS__, 'guard_settings' ) );

        // 2) 演示缓存：对昂贵计算结果做 10 分钟内存缓存。
        add_action( 'admin_init', array( __CLASS__, 'warm_heavy_result' ) );
    }

    /**
     * 过滤插件设置（wpca_update_settings 过滤器）。
     *
     * @param array $settings 经插件校验后的完整设置数组。
     * @return array 修改后的设置数组。
     */
    public static function guard_settings( array $settings ): array {
        // 示例：禁用自动优化数据库，避免与站点自有维护计划冲突。
        if ( isset( $settings['performance']['optimize_database'] ) ) {
            $settings['performance']['optimize_database'] = 0;
        }

        return $settings;
    }

    /**
     * 演示经 Cache 统一入口使用内存缓存。
     *
     * @return void
     */
    public static function warm_heavy_result(): void {
        $cache = Cache::getInstance();

        $cached = $cache->get( 'example_heavy_result', null, 'memory' );
        if ( null !== $cached ) {
            return; // 缓存命中，直接复用。
        }

        try {
            $result = self::compute_heavy_result();
            // TTL 600 秒；type 为 'memory'，不落盘。
            $cache->set( 'example_heavy_result', $result, 600, 'memory' );
        } catch ( \Throwable $e ) {
            // 统一错误码与日志：不要散落裸 error_log。
            Helpers::getInstance()->log_error(
                'example heavy result failed',
                array( 'exception' => $e->getMessage() ),
                \WPCleanAdmin\Modules\Utilities\Classes\WPCA_Errors::ERROR_UNKNOWN
            );
        }
    }

    /**
     * 昂贵计算的占位实现。
     *
     * @return array
     */
    private static function compute_heavy_result(): array {
        return array(
            'computed_at' => gmdate( 'c' ),
            'note'        => 'replace with real workload',
        );
    }
}

Sample_Extension::boot();
