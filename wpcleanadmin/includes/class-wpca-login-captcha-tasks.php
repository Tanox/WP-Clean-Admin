<?php
/**
 * WPCleanAdmin Login CAPTCHA Tasks Trait
 *
 * 登录 CAPTCHA 组合 trait：聚合生成与渲染/校验两个子 trait，对外契约不变。
 * 业务方法已按职责抽离至 LoginCaptchaGenerateTasks / LoginCaptchaRenderVerifyTasks。
 *
 * @package WPCleanAdmin
 * @version 1.9.8
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的登录 CAPTCHA 子模块 trait
require_once __DIR__ . '/class-wpca-login-captcha-generate-tasks.php';
require_once __DIR__ . '/class-wpca-login-captcha-render-verify-tasks.php';

/**
 * 登录 CAPTCHA 任务 trait（组合）
 */
trait LoginCaptchaTasks {

    use LoginCaptchaGenerateTasks;
    use LoginCaptchaRenderVerifyTasks;
}
