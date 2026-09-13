<?php
/**
 * WPCleanAdmin Security Headers
 *
 * 承载安全 HTTP 头发送逻辑，从 Core 主类抽取。
 *
 * @package WPCleanAdmin\Modules\Core\Classes
 * @version  1.8.4
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Core\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 安全头发送类
 */
class Security_Headers {

    /**
     * Send security HTTP headers
     *
     * 仅在 admin 区域发送安全头 —— CSP / X-Frame-Options 等策略会影响前台正常业务
     *（如 CDN、第三方脚本、iframe 嵌入），admin 后台才是 WP 插件真正能掌控的区域。
     */
    public function send_security_headers(): void {
        // 仅在 WP 后台发送安全头，避免前台被 CSP 等策略误伤。
        if ( ! \is_admin() ) {
            return;
        }

        // 多个 header 共用一次 headers_sent() 检查。
        if ( \headers_sent() ) {
            return;
        }

        // X-Frame-Options: Prevent clickjacking
        \header( 'X-Frame-Options: SAMEORIGIN' );

        // X-XSS-Protection: Enable browser XSS filter
        \header( 'X-XSS-Protection: 1; mode=block' );

        // X-Content-Type-Options: Prevent MIME type sniffing
        \header( 'X-Content-Type-Options: nosniff' );

        // Referrer-Policy: Control referrer information
        \header( 'Referrer-Policy: strict-origin-when-cross-origin' );

        // Content-Security-Policy: Restrict resource loading (basic configuration)
        \header( "Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self';" );
    }

    /**
     * Register security headers hook
     */
    public function register(): void {
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'send_headers', array( $this, 'send_security_headers' ) );
        }
    }
}
