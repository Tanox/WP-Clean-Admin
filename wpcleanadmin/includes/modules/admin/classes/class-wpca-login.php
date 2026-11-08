<?php
/**
 * WPCleanAdmin Login Class
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

// 依赖的子类文件——放在 ABSPATH 守卫之后，避免在非 WP 环境（如 CLI）下意外加载
require_once __DIR__ . '/class-wpca-login-captcha.php';
require_once __DIR__ . '/class-wpca-login-two-factor.php';
require_once __DIR__ . '/class-wpca-login-style.php';
require_once __DIR__ . '/class-wpca-login-attempts.php';
require_once __DIR__ . '/class-wpca-login-style-tasks.php';
require_once __DIR__ . '/class-wpca-login-attempts-tasks.php';
require_once __DIR__ . '/class-wpca-login-captcha-tasks.php';
require_once __DIR__ . '/class-wpca-login-two-factor-tasks.php';

/**
 * Login class
 */
class Login {

    use LoginStyleTasks;
    use LoginAttemptsTasks;
    use LoginCaptchaTasks;
    use LoginTwoFactorTasks;

    /**
     * Singleton instance
     *
     * @var Login
     */
    private static $instance;

    /**
     * CAPTCHA 处理器
     *
     * @var Login_Captcha
     */
    private $captcha;

    /**
     * 双因素认证处理器
     *
     * @var Login_TwoFactor
     */
    private $two_factor;

    /**
     * 登录页样式处理器
     *
     * @var Login_Style
     */
    private $style;

    /**
     * 登录尝试限制处理器
     *
     * @var Login_Attempts
     */
    private $attempts;

    /**
     * Get singleton instance
     *
     * @return Login
     */
    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->captcha     = new Login_Captcha();
        $this->two_factor  = new Login_TwoFactor();
        $this->style       = new Login_Style();
        $this->attempts    = new Login_Attempts();
        $this->init();
    }

    /**
     * Initialize the login module
     */
    public function init() {
        // Handle CAPTCHA image request
        if ( isset( $_GET['wpca_captcha'] ) && $_GET['wpca_captcha'] === '1' ) {
            $this->captcha->generate_captcha_image();
        }

        // Add login hooks
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) ) {
            \add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_scripts' ) );
            \add_filter( 'login_headerurl', array( $this, 'filter_login_header_url' ) );
            \add_filter( 'login_headertitle', array( $this, 'filter_login_header_title' ) );
            \add_action( 'login_footer', array( $this, 'add_login_footer_content' ) );
            \add_filter( 'login_body_class', array( $this, 'filter_login_body_class' ) );

            // Register sub-module hooks
            $this->two_factor->init();
            $this->captcha->init();
            $this->attempts->init();
        }
    }
}
