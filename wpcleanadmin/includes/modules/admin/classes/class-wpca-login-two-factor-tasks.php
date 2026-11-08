<?php
/**
 * WPCleanAdmin Login Two-Factor Tasks
 *
 * 双因素认证相关委托方法（转发至 `Login_TwoFactor`），公开 API 不变。
 * 主类 `Login` 通过 `use LoginTwoFactorTasks;` 组合。
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

/**
 * Login two-factor tasks
 */
trait LoginTwoFactorTasks {

    /**
     * Initialize two-factor auth (delegated)
     */
    public function init_two_factor_auth() {
        $this->two_factor->init();
    }

    /**
     * Generate two-factor secret (delegated)
     *
     * @param int $user_id
     */
    public function generate_two_factor_secret( $user_id ) {
        $this->two_factor->generate_two_factor_secret( $user_id );
    }

    /**
     * Update two-factor secret (delegated)
     *
     * @param int $user_id
     */
    public function update_two_factor_secret( $user_id ) {
        $this->two_factor->update_two_factor_secret( $user_id );
    }

    /**
     * Create random secret (delegated)
     *
     * @return string
     */
    public function create_random_secret() {
        return $this->two_factor->create_random_secret();
    }

    /**
     * Get QR code URL (delegated)
     *
     * @param int $user_id
     * @return string
     */
    public function get_qr_code_url( $user_id ) {
        return $this->two_factor->get_qr_code_url( $user_id );
    }

    /**
     * Render two-factor form (delegated)
     */
    public function render_two_factor_form() {
        $this->two_factor->render_two_factor_form();
    }

    /**
     * Check two-factor auth (delegated)
     *
     * @param object $user
     * @param string $password
     * @return object
     */
    public function check_two_factor_auth( $user, $password ) {
        return $this->two_factor->check_two_factor_auth( $user, $password );
    }

    /**
     * Handle two-factor failure (delegated)
     *
     * @param string $username
     */
    public function handle_two_factor_failure( $username ) {
        $this->two_factor->handle_two_factor_failure( $username );
    }
}
