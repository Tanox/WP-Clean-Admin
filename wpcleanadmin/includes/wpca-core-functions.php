<?php
/**
 * WPCleanAdmin Core Functions
 *
 * 纯函数库入口：按职责拆分为设置、路径、后台与工具四组子文件，
 * 统一在此引入，对外仍是同一组全局函数（公开 API 不变）。
 *
 * @package WPCleanAdmin
 * @version 1.9.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 按职责引入拆分后的函数模块
require_once __DIR__ . '/wpca-core-settings-functions.php';
require_once __DIR__ . '/wpca-core-path-functions.php';
require_once __DIR__ . '/wpca-core-admin-functions.php';
require_once __DIR__ . '/wpca-core-util-functions.php';
