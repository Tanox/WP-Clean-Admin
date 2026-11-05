<?php
/**
 * WPCleanAdmin Database AJAX Backup Tasks
 *
 * 承载数据库备份/恢复/删除类 AJAX 处理器，从 AJAX\Database 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.6
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
 * 数据库备份/恢复/删除类 AJAX 任务（trait）。
 */
trait DatabaseAjaxBackupTasks {

    /**
     * Backup database.
     *
     * @since 1.7.15
     */
    public static function backup_database() {
        if ( ! self::verify_ajax_request( 'wpca_backup_database' ) ) {
            return;
        }

        $options = isset( $_POST['options'] ) ? ( function_exists( '\wp_unslash' ) ? \wp_unslash( $_POST['options'] ) : $_POST['options'] ) : array();

        $database_handler = \WPCleanAdmin\Database::getInstance();
        $result = $database_handler->backup_database( $options );

        if ( $result['success'] ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( $result );
            }
        } else {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( $result['message'] );
            }
        }
    }

    /**
     * Restore database.
     *
     * @since 1.7.15
     */
    public static function restore_database() {
        if ( ! self::verify_ajax_request( 'wpca_restore_database' ) ) {
            return;
        }

        $backup_file = isset( $_POST['backup_file'] ) ? \sanitize_text_field( $_POST['backup_file'] ) : '';

        $database_handler = \WPCleanAdmin\Database::getInstance();
        $result = $database_handler->restore_database( $backup_file );

        if ( $result['success'] ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( $result );
            }
        } else {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( $result['message'] );
            }
        }
    }

    /**
     * Delete database backup.
     *
     * @since 1.7.15
     */
    public static function delete_database_backup() {
        if ( ! self::verify_ajax_request( 'wpca_delete_database_backup' ) ) {
            return;
        }

        $backup_file = isset( $_POST['backup_file'] ) ? \sanitize_text_field( $_POST['backup_file'] ) : '';

        $database_handler = \WPCleanAdmin\Database::getInstance();
        $result = $database_handler->delete_database_backup( $backup_file );

        if ( $result['success'] ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( $result );
            }
        } else {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( $result['message'] );
            }
        }
    }
}
