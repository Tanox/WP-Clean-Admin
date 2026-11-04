<?php
/**
 * WPCleanAdmin Database AJAX Class
 *
 * @package WPCleanAdmin
 * @version 1.9.6
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的数据库 AJAX 模块 trait
require_once __DIR__ . '/class-wpca-database-ajax-info-tasks.php';
require_once __DIR__ . '/class-wpca-database-ajax-backup-tasks.php';
require_once __DIR__ . '/class-wpca-database-ajax-settings-tasks.php';

/**
 * WPCleanAdmin Database AJAX Handler
 *
 * This class handles all database related AJAX requests for the WPCleanAdmin plugin.
 *
 * @package WPCleanAdmin
 * @subpackage AJAX
 * @since 1.7.15
 */

class Database {

    use DatabaseAjaxInfoTasks;
    use DatabaseAjaxBackupTasks;
    use DatabaseAjaxSettingsTasks;

    /**
     * Verify AJAX nonce and check user capabilities.
     *
     * @since 1.7.15
     * @param string $action The AJAX action name.
     * @return bool True if the nonce is valid and user has capabilities, false otherwise.
     */
    private static function verify_ajax_request( string $action ): bool {
        if ( ! function_exists( '\wp_verify_nonce' ) || ! isset( $_POST['_wpnonce'] ) || ! \wp_verify_nonce( $_POST['_wpnonce'], 'wpca_ajax_nonce' ) ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( \__( 'Invalid nonce', 'wp-clean-admin' ) );
            }
            return false;
        }

        if ( ! function_exists( '\current_user_can' ) || ! \current_user_can( 'manage_options' ) ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( \__( 'Insufficient permissions', 'wp-clean-admin' ) );
            }
            return false;
        }

        return true;
    }
}
