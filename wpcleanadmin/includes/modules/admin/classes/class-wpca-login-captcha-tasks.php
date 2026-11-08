<?php
/**
 * WPCleanAdmin Login Captcha Tasks
 *
 * CAPTCHA 相关委托方法（转发至 `Login_Captcha`），公开 API 不变。
 * 主类 `Login` 通过 `use LoginCaptchaTasks;` 组合。
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
 * Login captcha tasks
 */
trait LoginCaptchaTasks {

    /**
     * Initialize CAPTCHA (delegated)
     */
    public function init_captcha() {
        $this->captcha->init();
    }

    /**
     * Generate CAPTCHA image (delegated)
     */
    public function generate_captcha_image() {
        $this->captcha->generate_captcha_image();
    }

    /**
     * Render CAPTCHA form (delegated)
     */
    public function render_captcha() {
        $this->captcha->render_captcha();
    }

    /**
     * Verify CAPTCHA (delegated)
     *
     * @param object $user
     * @param string $username
     * @param string $password
     * @return object
     */
    public function verify_captcha( $user, $username, $password ) {
        return $this->captcha->verify_captcha( $user, $username, $password );
    }
}
