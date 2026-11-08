<?php
/**
 * WPCleanAdmin Login Style
 *
 * 承载登录页样式/脚本注入与头部定制逻辑，从 Login 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-login-style-enqueue-tasks.php';
require_once __DIR__ . '/class-wpca-login-style-customize-tasks.php';

if ( ! function_exists( 'esc_url' ) ) {
    function esc_url() {}
}
if ( ! function_exists( 'esc_html' ) ) {
    function esc_html() {}
}
if ( ! function_exists( 'esc_attr' ) ) {
    function esc_attr() {}
}
if ( ! function_exists( 'sanitize_html_class' ) ) {
    function sanitize_html_class() {}
}
if ( ! function_exists( 'wp_kses_post' ) ) {
    function wp_kses_post() {}
}
if ( ! function_exists( 'add_action' ) ) {
    function add_action() {}
}

/**
 * 登录页样式类
 */
class Login_Style {

    use LoginStyleEnqueueTasks;
    use LoginStyleCustomizeTasks;
}
