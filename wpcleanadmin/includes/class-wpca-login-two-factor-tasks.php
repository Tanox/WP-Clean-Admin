<?php
/**
 * WPCleanAdmin Login Two-Factor Tasks Trait (composite)
 *
 * 组合 trait：聚合 LoginTwoFactorSecretTasks 与 LoginTwoFactorAuthTasks。
 * 消费类（class-wpca-login.php）仍 `use LoginTwoFactorTasks;`，零改动。
 *
 * @package WPCleanAdmin
 * @version 1.9.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-login-two-factor-secret-tasks.php';
require_once __DIR__ . '/class-wpca-login-two-factor-auth-tasks.php';

/**
 * 登录两步验证任务（组合 trait）。
 */
trait LoginTwoFactorTasks {
    use LoginTwoFactorSecretTasks;
    use LoginTwoFactorAuthTasks;
}
