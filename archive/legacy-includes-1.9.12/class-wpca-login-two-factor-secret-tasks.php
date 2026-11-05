<?php
/**
 * WPCleanAdmin Login Two-Factor Secret Tasks
 *
 * 两步验证密钥生成 / OTP 校验 / QR 与 Base32 编解码，从 LoginTwoFactorTasks 抽离。
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

/**
 * 两步验证密钥与 OTP 任务（trait）。
 */
trait LoginTwoFactorSecretTasks {

    /**
     * Generate two-factor authentication secret for user
     *
     * @param int $user_id User ID
     */
    public function generate_two_factor_secret( $user_id ) {
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
     * @param int $user_id User ID
     */
    public function update_two_factor_secret( $user_id ) {
        // Check if secret already exists
        if ( function_exists( '\get_user_meta' ) && ! \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) ) {
            // Generate new secret if it doesn't exist
            $this->generate_two_factor_secret( $user_id );
        }
    }

    /**
     * Create random secret for two-factor authentication
     *
     * @return string Random secret
     * @noinspection PhpUnusedPrivateMethodInspection Used by test validation script
     */
    public function create_random_secret() {
        $chars     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret    = '';
        $max_index = strlen( $chars ) - 1;

        for ( $i = 0; $i < 16; $i++ ) {
            // 使用密码学安全的随机数生成 2FA 密钥，避免 rand() 可预测
            $secret .= $chars[ random_int( 0, $max_index ) ];
        }

        return $secret;
    }

    /**
     * Generate QR code URL for two-factor authentication
     *
     * @param int $user_id User ID
     * @return string QR code URL
     */
    public function get_qr_code_url( $user_id ) {
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

    /**
     * Check two-factor authentication code
     *
     * @param string $code Authentication code
     * @param int $user_id User ID
     * @return bool Whether the code is valid
     */
    private function verify_two_factor_code( $code, $user_id ) {
        // Get secret
        $secret = function_exists( '\get_user_meta' ) ? \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) : '';
        if ( ! $secret ) {
            return false;
        }

        // Get current timestamp
        $timestamp = time();

        // Check code with time tolerance
        for ( $offset = -1; $offset <= 1; $offset++ ) {
            $time = floor( ( $timestamp + ( $offset * 30 ) ) / 30 );
            $otp = $this->generate_otp( $secret, $time );

            if ( $code === $otp ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate OTP code from secret and counter
     *
     * @param string $secret Secret key
     * @param int $counter Counter
     * @return string OTP code
     */
    private function generate_otp( $secret, $counter ) {
        // Convert secret to binary
        $secret_bin = $this->base32_decode( $secret );

        // Pack counter into binary
        $counter_bin = str_pad( pack( 'N', $counter ), 8, chr( 0 ), STR_PAD_LEFT );

        // Calculate HMAC-SHA1
        $hash = hash_hmac( 'sha1', $counter_bin, $secret_bin, true );

        // Get offset
        $offset = ord( substr( $hash, -1 ) ) & 0x0F;

        // Get 4 bytes from hash
        $code = unpack( 'N', substr( $hash, $offset, 4 ) )[1];
        $code &= 0x7FFFFFFF;
        $code %= 1000000;

        // Format code to 6 digits
        return str_pad( $code, 6, '0', STR_PAD_LEFT );
    }

    /**
     * Base32 decode function
     *
     * @param string $str Base32 encoded string
     * @return string Decoded binary string
     */
    private function base32_decode( $str ) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $str = strtoupper( $str );
        $str = str_replace( array( ' ', "\r", "\n", "\t" ), '', $str );

        $length = strlen( $str );
        $result = '';
        $buffer = 0;
        $buffer_length = 0;

        for ( $i = 0; $i < $length; $i++ ) {
            $char = $str[$i];
            $index = strpos( $chars, $char );

            if ( $index === false ) {
                continue;
            }

            $buffer = ( $buffer << 5 ) | $index;
            $buffer_length += 5;

            if ( $buffer_length >= 8 ) {
                $result .= chr( ( $buffer >> ( $buffer_length - 8 ) ) & 0xFF );
                $buffer_length -= 8;
            }
        }

        return $result;
    }
}
