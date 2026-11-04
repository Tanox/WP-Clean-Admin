<?php
/**
 * WPCleanAdmin Database AJAX Info Tasks
 *
 * 承载数据库信息/备份列表/设置查询类 AJAX 处理器，从 AJAX\Database 主类抽取。
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
 * 数据库信息查询类 AJAX 任务（trait）。
 */
trait DatabaseAjaxInfoTasks {

    /**
     * Get database information.
     *
     * @since 1.7.15
     */
    public static function get_database_info() {
        if ( ! self::verify_ajax_request( 'wpca_get_database_info' ) ) {
            return;
        }

        $database_handler = \WPCleanAdmin\Database::getInstance();
        $info = $database_handler->get_database_info();
        if ( function_exists( '\wp_send_json_success' ) ) {
            \wp_send_json_success( $info );
        }
    }

    /**
     * Get database backups.
     *
     * @since 1.7.15
     */
    public static function get_database_backups() {
        if ( ! self::verify_ajax_request( 'wpca_get_database_backups' ) ) {
            return;
        }

        $database_handler = \WPCleanAdmin\Database::getInstance();
        $backups = $database_handler->get_database_backups();
        if ( function_exists( '\wp_send_json_success' ) ) {
            \wp_send_json_success( $backups );
        }
    }

    /**
     * Get database settings.
     *
     * @since 1.7.15
     */
    public static function get_database_settings() {
        if ( ! self::verify_ajax_request( 'wpca_get_database_settings' ) ) {
            return;
        }

        $settings = function_exists( '\get_option' ) ? \get_option( 'wpca_settings', array() ) : array();
        $database_settings = isset( $settings['database'] ) ? $settings['database'] : array();

        if ( function_exists( '\wp_send_json_success' ) ) {
            \wp_send_json_success( $database_settings );
        }
    }
}
