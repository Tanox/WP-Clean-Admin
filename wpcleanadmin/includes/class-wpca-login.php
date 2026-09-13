<?php
/**
 * WPCleanAdmin Login Class
 *
 * 登录优化门面类：保留单例、初始化与 WordPress IDE stub 声明，具体能力按职责
 * 拆入 Login_*_Tasks trait（CAPTCHA / 两步验证 / 登录页定制 / 登录限制），公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Declare WordPress functions for IDE compatibility
if ( ! function_exists( 'set_transient' ) ) {
    function set_transient() {}
}
if ( ! function_exists( 'get_transient' ) ) {
    function get_transient() {}
}
if ( ! function_exists( 'is_ssl' ) ) {
    function is_ssl() {}
}
if ( ! function_exists( 'is_wp_error' ) ) {
    function is_wp_error() {}
}
if ( ! function_exists( 'wp_kses_post' ) ) {
    function wp_kses_post() {}
}
if ( ! function_exists( 'sanitize_html_class' ) ) {
    function sanitize_html_class() {}
}
if ( ! function_exists( 'update_user_meta' ) ) {
    function update_user_meta() {}
}
if ( ! function_exists( 'get_user_meta' ) ) {
    function get_user_meta() {}
}
if ( ! function_exists( 'get_userdata' ) ) {
    function get_userdata() {}
}
if ( ! function_exists( 'wp_verify_nonce' ) ) {
    function wp_verify_nonce() {}
}
if ( ! function_exists( 'wp_login_url' ) ) {
    function wp_login_url() {}
}

/**
 * Login class
 */
class Login {

    use LoginCaptchaTasks;
    use LoginTwoFactorTasks;
    use LoginCustomizationTasks;
    use LoginAttemptRestrictionTasks;

    /**
     * Singleton instance
     *
     * @var Login
     */
    private static $instance;

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
        $this->init();
    }

    /**
     * Initialize the login module
     */
    public function init() {
        // Handle CAPTCHA image request
        if ( isset( $_GET['wpca_captcha'] ) && $_GET['wpca_captcha'] === '1' ) {
            $this->generate_captcha_image();
        }

        // Add login hooks
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) ) {
            \add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_scripts' ) );
            \add_filter( 'login_headerurl', array( $this, 'filter_login_header_url' ) );
            \add_filter( 'login_headertitle', array( $this, 'filter_login_header_title' ) );
            \add_action( 'login_footer', array( $this, 'add_login_footer_content' ) );
            \add_filter( 'login_body_class', array( $this, 'filter_login_body_class' ) );

            // Initialize two-factor authentication
            $this->init_two_factor_auth();

            // Initialize CAPTCHA
            $this->init_captcha();
        }
    }
}
