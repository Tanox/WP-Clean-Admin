<?php
/**
 * WPCleanAdmin Settings AJAX Compatibility Proxy
 *
 * 原 3 个未挂载 action 的处理器已移除（OPT-002）；仅保留 sanitize_settings 静态代理，
 * 供既有调用方使用。统一响应见 WPCleanAdmin\AJAX\Response。
 *
 * @package WPCleanAdmin
 * @version 1.9.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\AJAX;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WPCleanAdmin\Modules\Utilities\Classes\Helpers;

/**
 * 设置消毒兼容代理
 */
class Settings {

    /**
     * 递归消毒设置数据
     *
     * @param mixed $value 待消毒值
     * @return mixed
     */
    public static function sanitize_settings( $value ) {
        return Helpers::getInstance()->sanitize_settings( $value );
    }
}
