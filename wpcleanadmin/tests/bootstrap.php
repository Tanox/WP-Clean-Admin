<?php
/**
 * PHPUnit bootstrap file
 *
 * 在非 WordPress 运行时提供最小 WP 函数 polyfill，并加载插件过程式
 * 核心函数，使单元测试（无需完整 WP 环境）可独立运行并验证插件逻辑。
 *
 * @package WPCleanAdmin
 */

// 提前定义 ABSPATH：阻止 autoloader 加载空白 IDE stub，并确保核心函数文件不 exit
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

// 定义插件核心常量（主插件文件在测试环境中不加载，故在此补齐）
if ( ! defined( 'WPCA_TEXT_DOMAIN' ) ) {
    define( 'WPCA_TEXT_DOMAIN', 'wp-clean-admin' );
}

// 最小 WordPress 函数 polyfill（仅覆盖测试所需，行为等价于 WP 核心）
if ( ! function_exists( 'sanitize_text_field' ) ) {
    function sanitize_text_field( $value ) {
        if ( ! is_string( $value ) ) {
            return $value;
        }
        // 去除 script/style 标签及其内容（等价 WP 行为）
        $value = preg_replace( '@<(script|style)[^>]*?>.*?</\1>@si', '', $value );
        $value = strip_tags( $value );
        return trim( $value );
    }
}

if ( ! function_exists( 'get_option' ) ) {
    function get_option( $key = '', $default = false ) {
        return $default;
    }
}

if ( ! function_exists( 'update_option' ) ) {
    function update_option( $key = '', $value = '' ) {
        return false;
    }
}

if ( ! function_exists( 'sanitize_key' ) ) {
    function sanitize_key( $key ) {
        return is_string( $key ) ? strtolower( preg_replace( '/[^a-z0-9_\-]/', '', $key ) ) : $key;
    }
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
    function wp_verify_nonce( $nonce, $action = '' ) {
        return false;
    }
}

if ( ! function_exists( 'current_user_can' ) ) {
    function current_user_can( $capability ) {
        return true;
    }
}

if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( $hook_name, $value ) {
        return $value;
    }
}

if ( ! function_exists( 'wp_unslash' ) ) {
    function wp_unslash( $value ) {
        return is_string( $value ) ? stripslashes( $value ) : $value;
    }
}

if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( $text ) {
        return $text;
    }
}

if ( ! function_exists( '__' ) ) {
    function __( $text, $domain = '' ) {
        return $text;
    }
}

if ( ! function_exists( 'esc_html__' ) ) {
    function esc_html__( $text, $domain = '' ) {
        return $text;
    }
}

// 加载 PSR-4 autoloader（ABSPATH 已定义，不会加载空白 IDE stub）
require_once dirname( __DIR__ ) . '/includes/autoload.php';

// 加载过程式核心函数（定义 wpca_get_settings / wpca_get_version 等）
require_once dirname( __DIR__ ) . '/includes/wpca-core-functions.php';
