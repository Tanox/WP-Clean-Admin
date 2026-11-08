<?php
/**
 * WordPress minimal polyfill for PHPUnit (global scope)
 *
 * 与 tests/bootstrap.php 同属测试环境支撑：以守卫化全局函数兜底
 * 插件代码在 CLI 测试环境中可能调用到的 WordPress 函数（原各模块
 * 类文件内的命名空间级空壳 stub 已移除，见 git 历史）。
 * 真实 WP 环境下这些函数已由 WP 核心提供，本文件全部守卫跳过，不生效。
 *
 * @package WPCleanAdmin
 * @version 1.9.17
 */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! function_exists( 'add_action' ) ) {
    function add_action( $tag, $cb = null, $priority = 10, $accepted_args = 1 ) {
        return true;
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( $tag, $cb = null, $priority = 10, $accepted_args = 1 ) {
        return true;
    }
}

if ( ! function_exists( 'remove_action' ) ) {
    function remove_action( $tag, $cb = null, $priority = 10 ) {
        return true;
    }
}

if ( ! function_exists( 'remove_filter' ) ) {
    function remove_filter( $tag, $cb = null, $priority = 10 ) {
        return true;
    }
}

if ( ! function_exists( 'remove_meta_box' ) ) {
    function remove_meta_box( $id, $screen, $context ) {
        return;
    }
}

if ( ! function_exists( 'get_transient' ) ) {
    function get_transient( $transient ) {
        return false;
    }
}

if ( ! function_exists( 'set_transient' ) ) {
    function set_transient( $transient, $value, $expiration = 0 ) {
        return true;
    }
}

if ( ! function_exists( 'delete_transient' ) ) {
    function delete_transient( $transient ) {
        return true;
    }
}

if ( ! function_exists( 'delete_option' ) ) {
    function delete_option( $option ) {
        return true;
    }
}

if ( ! function_exists( 'esc_attr' ) ) {
    function esc_attr( $text ) {
        return htmlspecialchars( (string) $text, ENT_QUOTES );
    }
}

if ( ! function_exists( 'sanitize_html_class' ) ) {
    function sanitize_html_class( $class, $fallback = '' ) {
        $sanitized = preg_replace( '/[^A-Za-z0-9\-_]/', '', (string) $class );
        return $sanitized !== '' ? $sanitized : $fallback;
    }
}

if ( ! function_exists( 'wp_kses_post' ) ) {
    function wp_kses_post( $data ) {
        return $data;
    }
}

if ( ! function_exists( 'wp_get_current_user' ) ) {
    function wp_get_current_user() {
        $user = new \stdClass();
        $user->ID = 0;
        $user->roles = array( 'administrator' );
        $user->display_name = 'Test User';
        return $user;
    }
}

if ( ! function_exists( 'wp_json_encode' ) ) {
    function wp_json_encode( $data, $flags = 0 ) {
        return json_encode( $data, $flags );
    }
}

if ( ! function_exists( 'wp_parse_args' ) ) {
    function wp_parse_args( $args, $defaults = array() ) {
        if ( is_object( $args ) ) {
            $args = get_object_vars( $args );
        }
        if ( ! is_array( $args ) ) {
            wp_parse_str( (string) $args, $args );
        }
        return array_merge( (array) $defaults, (array) $args );
    }
}

if ( ! function_exists( 'wp_parse_str' ) ) {
    function wp_parse_str( $string, &$array ) {
        parse_str( (string) $string, $out );
        $array = $out;
        return;
    }
}

if ( ! function_exists( 'get_user_by' ) ) {
    function get_user_by( $field, $value ) {
        return false;
    }
}

if ( ! function_exists( 'user_can' ) ) {
    function user_can( $user, $capability ) {
        return true;
    }
}

if ( ! function_exists( 'wp_redirect' ) ) {
    function wp_redirect( $location, $status = 302 ) {
        return false;
    }
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
    function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
        return;
    }
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
    function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
        return;
    }
}

if ( ! function_exists( 'wp_localize_script' ) ) {
    function wp_localize_script( $handle, $object_name, $l10n ) {
        return true;
    }
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
    function wp_create_nonce( $action = -1 ) {
        return 'test-nonce';
    }
}

if ( ! function_exists( 'wp_send_json' ) ) {
    function wp_send_json( $response, $status_code = null ) {
        return;
    }
}

if ( ! function_exists( 'wp_send_json_error' ) ) {
    function wp_send_json_error( $data = null, $status_code = null ) {
        return;
    }
}

if ( ! function_exists( 'wp_send_json_success' ) ) {
    function wp_send_json_success( $data = null, $status_code = null ) {
        return;
    }
}
