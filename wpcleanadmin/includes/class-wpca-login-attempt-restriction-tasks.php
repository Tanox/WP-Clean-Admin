<?php
/**
 * WPCleanAdmin Login Attempt Restriction Tasks Trait
 *
 * 登录失败次数限制与锁定，从 class-wpca-login.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 登录尝试限制任务 trait
 */
trait LoginAttemptRestrictionTasks {

    /**
     * Restrict login attempts
     */
    public function restrict_login_attempts(): void {
        // Load settings
        $settings = \wpca_get_settings();

        // Restrict login attempts if enabled
        if ( isset( $settings['login'] ) && isset( $settings['login']['restrict_login_attempts'] ) && $settings['login']['restrict_login_attempts'] ) {
            // Add login attempt restriction hooks
            if ( function_exists( 'add_filter' ) && function_exists( 'add_action' ) ) {
                \add_filter( 'authenticate', array( $this, 'check_login_attempts' ), 30, 3 );
                \add_action( 'wp_login_failed', array( $this, 'log_failed_login' ) );
            }
        }
    }

    /**
     * Check login attempts
     *
     * @param object $user User object or error
     * @param string $username Username
     * @param string $password Password
     * @return object Modified user or error
     */
    public function check_login_attempts( $user, $username, $password ) {
        // Load settings
        $settings = \wpca_get_settings();

        // Get max login attempts
        $max_attempts = isset( $settings['login']['max_login_attempts'] ) ? intval( $settings['login']['max_login_attempts'] ) : 5;

        // Get lockout duration
        $lockout_duration = isset( $settings['login']['lockout_duration'] ) ? intval( $settings['login']['lockout_duration'] ) : 300;

        // Get user IP
        $user_ip = $_SERVER['REMOTE_ADDR'];

        // Get login attempts
        $login_attempts = ( function_exists( '\get_transient' ) ? \get_transient( 'wpca_login_attempts_' . $user_ip ) : 0 );

        // Check if user is locked out
        if ( $login_attempts >= $max_attempts ) {
            return new \WP_Error( 'too_many_attempts', \__( 'Too many login attempts. Please try again later.', \WPCA_TEXT_DOMAIN ) );
        }

        return $user;
    }

    /**
     * Log failed login attempts
     *
     * @param string $username Username
     */
    public function log_failed_login( $username ) {
        // Get user IP
        $user_ip = $_SERVER['REMOTE_ADDR'];

        // Get login attempts
        $login_attempts = ( function_exists( '\get_transient' ) ? \get_transient( 'wpca_login_attempts_' . $user_ip ) : 0 );

        // Increment login attempts
        $login_attempts = $login_attempts ? $login_attempts + 1 : 1;

        // Set transient
        if ( function_exists( '\set_transient' ) ) {
            \set_transient( 'wpca_login_attempts_' . $user_ip, $login_attempts, 300 ); // 5 minutes
        }
    }
}
