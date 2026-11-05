<?php
/**
 * WPCleanAdmin Core Security Tasks
 *
 * 承载安全 HTTP 响应头相关逻辑，从 Core 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.8
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 *
 * @noinspection PhpUndefinedFunctionInspection WordPress functions are available in WP environment
 */

namespace WPCleanAdmin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 安全响应头相关任务（trait）。
 */
trait CoreSecurityTasks {

    /**
     * Add security HTTP headers
     */
    private function add_security_headers() {
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'send_headers', array( $this, 'send_security_headers' ) );
        }
    }

    /**
     * Send security HTTP headers
     */
    public function send_security_headers() {
        // X-Frame-Options: Prevent clickjacking
        if ( ! \headers_sent() ) {
            \header( 'X-Frame-Options: SAMEORIGIN' );
        }

        // X-XSS-Protection: Enable browser XSS filter
        if ( ! \headers_sent() ) {
            \header( 'X-XSS-Protection: 1; mode=block' );
        }

        // X-Content-Type-Options: Prevent MIME type sniffing
        if ( ! \headers_sent() ) {
            \header( 'X-Content-Type-Options: nosniff' );
        }

        // Referrer-Policy: Control referrer information
        if ( ! \headers_sent() ) {
            \header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        }

        // Content-Security-Policy: Restrict resource loading (basic configuration, admin only)
        if ( ! \headers_sent() && \is_admin() ) {
            \header( "Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self';" );
        }
    }
}
