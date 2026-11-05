<?php
/**
 * WPCleanAdmin Database AJAX Settings Tasks
 *
 * 承载数据库设置保存/重置类 AJAX 处理器，从 AJAX\Database 主类抽取。
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
 * 数据库设置保存/重置类 AJAX 任务（trait）。
 */
trait DatabaseAjaxSettingsTasks {

    /**
     * Save database settings.
     *
     * @since 1.7.15
     */
    public static function save_database_settings() {
        if ( ! self::verify_ajax_request( 'wpca_save_database_settings' ) ) {
            return;
        }

        $settings = isset( $_POST['settings'] ) ? ( function_exists( '\wp_unslash' ) ? \wp_unslash( $_POST['settings'] ) : $_POST['settings'] ) : array();
        $settings = \WPCleanAdmin\AJAX\Settings::sanitize_settings( $settings );

        $current_settings = function_exists( '\get_option' ) ? \get_option( 'wpca_settings', array() ) : array();

        $current_settings['database'] = $settings;
        $result = function_exists( '\update_option' ) ? \update_option( 'wpca_settings', $current_settings ) : false;

        if ( $result ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( array( 'message' => \__( 'Database settings saved successfully', 'wp-clean-admin' ) ) );
            }
        } else {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( \__( 'Failed to save database settings', 'wp-clean-admin' ) );
            }
        }
    }

    /**
     * Reset database settings.
     *
     * @since 1.7.15
     */
    public static function reset_database_settings() {
        if ( ! self::verify_ajax_request( 'wpca_reset_database_settings' ) ) {
            return;
        }

        $current_settings = function_exists( '\get_option' ) ? \get_option( 'wpca_settings', array() ) : array();

        unset( $current_settings['database'] );
        $result = function_exists( '\update_option' ) ? \update_option( 'wpca_settings', $current_settings ) : false;

        if ( $result ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( array( 'message' => \__( 'Database settings reset to default', 'wp-clean-admin' ) ) );
            }
        } else {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( \__( 'Failed to reset database settings', 'wp-clean-admin' ) );
            }
        }
    }
}
