<?php
/**
 * WPCleanAdmin AJAX Response
 *
 * 统一 AJAX JSON 响应契约：{success, message, data} / {success, error_code, message, data}
 *
 * @package WPCleanAdmin
 * @version 1.9.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.17
 */

namespace WPCleanAdmin\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// WPCA_Errors 与 Helpers 定义在同一文件，确保错误码常量可用
require_once dirname( __DIR__ ) . '/modules/utilities/classes/class-wpca-helpers.php';

use WPCleanAdmin\Modules\Utilities\Classes\WPCA_Errors;

/**
 * AJAX 响应工厂
 */
final class Response {

    /**
     * 输出成功响应
     *
     * @param array  $data    附加数据
     * @param string $message 成功消息
     * @return void
     */
    public static function success( $data = array(), $message = '' ): void {
        self::send(
            array(
                'success' => true,
                'message' => $message,
                'data'    => $data,
            )
        );
    }

    /**
     * 输出错误响应
     *
     * @param int    $error_code WPCA_Errors 错误码
     * @param string $message    错误消息
     * @param array  $data       附加数据
     * @return void
     */
    public static function error( $error_code = WPCA_Errors::ERROR_UNKNOWN, $message = '', $data = array() ): void {
        self::send(
            array(
                'success'    => false,
                'error_code' => (int) $error_code,
                'message'    => $message,
                'data'       => $data,
            )
        );
    }

    /**
     * 发送 JSON 并终止请求
     *
     * @param array $dto 响应结构
     * @return void
     */
    private static function send( array $dto ): void {
        if ( function_exists( '\wp_send_json' ) ) {
            \wp_send_json( $dto );
            return;
        }

        header( 'Content-Type: application/json' );
        echo json_encode( $dto );
        exit;
    }
}
