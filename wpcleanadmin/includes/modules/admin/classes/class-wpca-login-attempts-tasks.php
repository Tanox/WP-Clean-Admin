<?php
/**
 * WPCleanAdmin Login Attempts Tasks
 *
 * 登录尝试限制相关委托方法（转发至 `Login_Attempts`），公开 API 不变。
 * 主类 `Login` 通过 `use LoginAttemptsTasks;` 组合。
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

/**
 * Login attempts tasks
 */
trait LoginAttemptsTasks {

    /**
     * Restrict login attempts (delegated)
     */
    public function restrict_login_attempts(): void {
        $this->attempts->init();
    }

    /**
     * Check login attempts (delegated)
     *
     * @param object $user
     * @param string $username
     * @param string $password
     * @return object
     */
    public function check_login_attempts( $user, $username, $password ) {
        return $this->attempts->check_login_attempts( $user, $username, $password );
    }

    /**
     * Log failed login (delegated)
     *
     * @param string $username
     */
    public function log_failed_login( $username ) {
        $this->attempts->log_failed_login( $username );
    }
}
