<?php
/**
 * WPCleanAdmin Cleanup AJAX Handler
 *
 * @package WPCleanAdmin
 * @version 1.9.7
 * @update_date 2026-01-30
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的清理 AJAX 模块 trait
require_once __DIR__ . '/class-wpca-cleanup-ajax-database-media-tasks.php';
require_once __DIR__ . '/class-wpca-cleanup-ajax-comments-content-tasks.php';
require_once __DIR__ . '/class-wpca-cleanup-ajax-stats-tasks.php';

/**
 * Cleanup AJAX Handler Class
 */
class Cleanup {

    use CleanupAjaxDatabaseMediaTasks;
    use CleanupAjaxCommentsContentTasks;
    use CleanupAjaxStatsTasks;

    /**
     * Verify AJAX nonce and check user capabilities.
     *
     * @since 1.7.15
     * @return bool True if the nonce is valid and user has capabilities, false otherwise.
     */
    private static function verify_cleanup_request(): bool {
        if ( ! isset( $_POST['_wpnonce'] ) || ! function_exists( '\wp_verify_nonce' ) || ! \wp_verify_nonce( $_POST['_wpnonce'], 'wpca_ajax_nonce' ) ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( array( 'message' => \__( 'Nonce verification failed', WPCA_TEXT_DOMAIN ) ) );
            }
            return false;
        }

        if ( ! function_exists( '\current_user_can' ) || ! \current_user_can( 'manage_options' ) ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( array( 'message' => \__( 'Insufficient permissions', WPCA_TEXT_DOMAIN ) ) );
            }
            return false;
        }

        return true;
    }
}
