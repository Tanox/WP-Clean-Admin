<?php
/**
 * WPCleanAdmin Login CAPTCHA Generate Tasks
 *
 * 承载登录 CAPTCHA 生成与图像渲染逻辑，从 LoginCaptchaTasks 主 trait 抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.8
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 *
 * @noinspection PhpUndefinedFunctionInspection WordPress functions are available in WP environment
 */

namespace WPCleanAdmin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 登录 CAPTCHA 生成与图像渲染任务（trait）。
 */
trait LoginCaptchaGenerateTasks {

    /**
     * Generate CAPTCHA code
     *
     * @return string Generated CAPTCHA code
     */
    private function generate_captcha() {
        // Generate random code
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code       = '';
        $max_index  = strlen( $characters ) - 1;

        for ( $i = 0; $i < 6; $i++ ) {
            // 使用密码学安全的随机数生成验证码，避免 rand() 可预测导致验证码被破解
            $code .= $characters[ random_int( 0, $max_index ) ];
        }

        // Store in transient (wp_session doesn't exist in WordPress core)
        $user_ip = $this->get_captcha_user_key();
        if ( function_exists( '\set_transient' ) && '' !== $user_ip ) {
            \set_transient( 'wpca_captcha_' . $user_ip, $code, 300 ); // 5 minutes
        }

        return $code;
    }

    /**
     * Get a sanitized per-visitor key used to store the CAPTCHA code
     *
     * @return string Visitor key, or empty string when unavailable
     */
    private function get_captcha_user_key() {
        if ( ! isset( $_SERVER['REMOTE_ADDR'] ) ) {
            return '';
        }

        $raw = function_exists( '\wp_unslash' ) ? \wp_unslash( $_SERVER['REMOTE_ADDR'] ) : $_SERVER['REMOTE_ADDR'];

        return function_exists( '\sanitize_text_field' ) ? \sanitize_text_field( $raw ) : $raw;
    }

    /**
     * Generate CAPTCHA image
     */
    public function generate_captcha_image() {
        // GD 扩展缺失时直接返回 500，避免调用未定义函数导致致命错误
        if ( ! function_exists( 'imagecreatetruecolor' ) ) {
            if ( function_exists( '\status_header' ) ) {
                \status_header( 500 );
            }
            exit;
        }

        // Generate CAPTCHA code
        $code = $this->generate_captcha();

        // Create image
        $width = 120;
        $height = 40;

        $image = imagecreatetruecolor( $width, $height );

        // Set colors
        $bg_color = imagecolorallocate( $image, 240, 240, 240 );
        $text_color = imagecolorallocate( $image, 30, 30, 30 );
        $line_color = imagecolorallocate( $image, 150, 150, 150 );

        // Fill background
        imagefilledrectangle( $image, 0, 0, $width, $height, $bg_color );

        // Add noise lines
        for ( $i = 0; $i < 5; $i++ ) {
            imageline( $image, rand( 0, $width ), rand( 0, $height ), rand( 0, $width ), rand( 0, $height ), $line_color );
        }

        // Add text
        $font = 5;
        $text_width = imagefontwidth( $font ) * strlen( $code );
        $text_height = imagefontheight( $font );
        $x = ( $width - $text_width ) / 2;
        $y = ( $height - $text_height ) / 2;

        imagestring( $image, $font, $x, $y, $code, $text_color );

        // Output image
        header( 'Content-type: image/png' );
        imagepng( $image );
        imagedestroy( $image );
        exit;
    }
}
