<?php
/**
 * WPCleanAdmin Login Two-Factor Secret Tasks
 *
 * 双因素认证密钥生成、QR 码地址与随机数工具，公开 API 不变。
 * 主类 `Login_TwoFactor` 通过 `use LoginTwoFactorSecretTasks;` 组合。
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
 * Login two-factor secret tasks
 */
trait LoginTwoFactorSecretTasks {

    /**
     * Generate two-factor authentication secret for user
     *
     * @param int $user_id
     */
    public function generate_two_factor_secret( $user_id ): void {
        // Generate random secret
        $secret = $this->create_random_secret();

        // Save secret to user meta
        if ( function_exists( '\update_user_meta' ) ) {
            \update_user_meta( $user_id, 'wpca_two_factor_secret', $secret );
            \update_user_meta( $user_id, 'wpca_two_factor_enabled', 1 );
        }
    }

    /**
     * Update two-factor authentication secret
     *
     * @param int $user_id
     */
    public function update_two_factor_secret( $user_id ): void {
        // Check if secret already exists
        if ( function_exists( '\get_user_meta' ) && ! \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) ) {
            // Generate new secret if it doesn't exist
            $this->generate_two_factor_secret( $user_id );
        }
    }

    /**
     * Create random secret for two-factor authentication
     *
     * @return string
     * @noinspection PhpUnusedPrivateMethodInspection Used by test validation script
     */
    public function create_random_secret(): string {
        $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ( $i = 0; $i < 16; $i++ ) {
            $secret .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
        }

        return $secret;
    }

    /**
     * Generate QR code URL for two-factor authentication
     *
     * @param int $user_id
     * @return string
     */
    public function get_qr_code_url( $user_id ): string {
        if ( function_exists( '\get_userdata' ) ) {
            $user = \get_userdata( $user_id );
        } else {
            $user = false;
        }
        if ( ! $user ) {
            return '';
        }

        // Get secret
        $secret = function_exists( '\get_user_meta' ) ? \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) : '';
        if ( ! $secret ) {
            return '';
        }

        // Get site name
        $site_name = function_exists( '\get_bloginfo' ) ? \get_bloginfo( 'name' ) : 'WordPress';

        // Generate otpauth URL
        $otpauth_url = 'otpauth://totp/' . urlencode( $site_name . ':' . $user->user_email ) . '?secret=' . $secret . '&issuer=' . urlencode( $site_name );

        // Return QR code URL
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode( $otpauth_url );
    }
}
