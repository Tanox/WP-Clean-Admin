<?php
/**
 * WPCleanAdmin Login Two Factor
 *
 * 承载双因素认证的逻辑（密钥生成、OTP、QR、校验与表单），从 Login 主类抽取。
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

require_once __DIR__ . '/class-wpca-login-totp.php';
require_once __DIR__ . '/class-wpca-login-two-factor-secret-tasks.php';
require_once __DIR__ . '/class-wpca-login-two-factor-auth-tasks.php';

/**
 * 双因素认证类
 */
class Login_TwoFactor {

    use LoginTwoFactorSecretTasks;
    use LoginTwoFactorAuthTasks;

    /**
     * TOTP 工具实例
     *
     * @var Login_TOTP
     */
    private $totp;

    /**
     * 构造时初始化 TOTP 助手
     */
    public function __construct() {
        $this->totp = new Login_TOTP();
    }

    /**
     * Initialize two-factor authentication hooks
     */
    public function init(): void {
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) ) {
            \add_action( 'wp_authenticate_user', array( $this, 'check_two_factor_auth' ), 20, 2 );
            \add_action( 'login_form', array( $this, 'render_two_factor_form' ) );
            \add_action( 'wp_login_failed', array( $this, 'handle_two_factor_failure' ) );
            \add_action( 'user_register', array( $this, 'generate_two_factor_secret' ) );
            \add_action( 'profile_update', array( $this, 'update_two_factor_secret' ) );
        }
    }
}
