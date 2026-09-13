<?php
/**
 * WPCleanAdmin Performance Disable Tasks Trait
 *
 * 禁用类性能优化（emojis / XML-RPC / REST API / heartbeat），
 * 从 class-wpca-performance.php 按职责抽离，公开方法契约不变。
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
 * 禁用项性能任务 trait
 */
trait PerformanceDisableTasks {

    /**
     * Disable WordPress emojis
     *
     * @uses remove_action() To remove emoji detection actions
     * @uses remove_action() To remove emoji styles
     * @return void
     */
    public function disable_emojis() {
        // Remove emoji actions
        if ( function_exists( 'remove_action' ) ) {
            \remove_action( 'admin_print_styles', 'print_emoji_styles' );
            \remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            \remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            \remove_action( 'wp_print_styles', 'print_emoji_styles' );
        }

        if ( function_exists( 'remove_filter' ) ) {
            \remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
            \remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            \remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        }

        // Disable emoji TinyMCE plugin
        \add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
    }

    /**
     * Disable emojis in TinyMCE
     *
     * @param array $plugins TinyMCE plugins
     * @return array Modified TinyMCE plugins
     */
    public function disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        }
        return $plugins;
    }

    /**
     * Disable XML-RPC
     */
    public function disable_xmlrpc(): void {
        // Disable XML-RPC methods
        \add_filter( 'xmlrpc_enabled', '__return_false' );
        \add_filter( 'xmlrpc_methods', '__return_empty_array' );

        // Remove XML-RPC headers
        if ( function_exists( 'remove_action' ) ) {
            \remove_action( 'wp_head', 'rsd_link' );
            \remove_action( 'wp_head', 'wlwmanifest_link' );
        }
    }

    /**
     * Disable REST API
     */
    public function disable_rest_api() {
        // Disable REST API for non-authenticated users
        \add_filter( 'rest_authentication_errors', array( $this, 'disable_rest_api_authentication' ) );
    }

    /**
     * Disable REST API authentication
     *
     * @param mixed $result Authentication result
     * @return mixed Modified authentication result
     */
    public function disable_rest_api_authentication( $result ) {
        if ( function_exists( '\is_user_logged_in' ) && ! \is_user_logged_in() ) {
            if ( class_exists( '\WP_Error' ) ) {
                return new \WP_Error( 'rest_not_logged_in', \__( 'REST API is disabled for non-authenticated users', \WPCA_TEXT_DOMAIN ), array( 'status' => 401 ) );
            }
        }
        return $result;
    }

    /**
     * Disable heartbeat
     */
    public function disable_heartbeat() {
        // Remove heartbeat actions
        if ( function_exists( 'remove_action' ) ) {
            \remove_action( 'admin_enqueue_scripts', 'wp_enqueue_heartbeat' );
            \remove_action( 'wp_enqueue_scripts', 'wp_enqueue_heartbeat' );
        }
    }
}
