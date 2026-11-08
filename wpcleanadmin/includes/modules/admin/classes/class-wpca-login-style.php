<?php
/**
 * WPCleanAdmin Login Style
 *
 * 承载登录页样式/脚本注入与头部定制逻辑，从 Login 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.17
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


/**
 * 登录页样式类
 */
class Login_Style {

    use LoginStyleEnqueueTasks;
    use LoginStyleCustomizeTasks;
}
