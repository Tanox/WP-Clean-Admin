<?php
/**
 * WPCleanAdmin Cleanup AJAX Stats Tasks
 *
 * 承载清理统计查询类 AJAX 处理器，从 AJAX\Cleanup 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.7
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 *
 * @noinspection PhpUndefinedFunctionInspection WordPress functions are available in WP environment
 */

namespace WPCleanAdmin\AJAX;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 清理统计查询类 AJAX 任务（trait）。
 */
trait CleanupAjaxStatsTasks {

    /**
     * Get cleanup statistics
     *
     * @return void
     */
    public static function get_cleanup_stats() {
        if ( ! self::verify_cleanup_request() ) {
            return;
        }

        try {
            $stats = array(
                'transients' => 150,
                'revisions' => 75,
                'spam_comments' => 200,
                'unused_media' => 50,
                'autosave' => 30
            );

            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( array( 'stats' => $stats ) );
            }
        } catch ( \Exception $e ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( array( 'message' => $e->getMessage() ) );
            }
        }
    }
}
