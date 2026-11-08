<?php
/**
 * WPCleanAdmin Login Style Enqueue Tasks
 *
 * 登录页脚本/样式入队与头部/页脚/Body 过滤器，公开 API 不变。
 * 主类 `Login_Style` 通过 `use LoginStyleEnqueueTasks;` 组合。
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
 * Login style enqueue tasks
 */
trait LoginStyleEnqueueTasks {

    /**
     * Enqueue login scripts and styles
     */
    public function enqueue_login_scripts(): void {
        // Load settings
        $settings = \wpca_get_settings();

        // Enqueue custom login styles if enabled
        if ( isset( $settings['login'] ) && isset( $settings['login']['custom_login_styles'] ) && $settings['login']['custom_login_styles'] ) {
            // Enqueue login CSS
            if ( function_exists( '\wp_enqueue_style' ) ) {
                \wp_enqueue_style(
                    'wpca-login',
                    WPCA_PLUGIN_URL . 'assets/css/wpca-login.css',
                    array(),
                    WPCA_VERSION
                );
            }

            // Enqueue login JS
            if ( function_exists( '\wp_enqueue_script' ) ) {
                \wp_enqueue_script(
                    'wpca-login',
                    WPCA_PLUGIN_URL . 'assets/js/wpca-login.js',
                    array( 'jquery' ),
                    WPCA_VERSION,
                    true
                );
            }
        }
    }

    /**
     * Filter login header URL
     *
     * @param string $url
     * @return string
     */
    public function filter_login_header_url( $url ): string {
        // Load settings
        $settings = \wpca_get_settings();

        // Change login header URL if custom URL is set
        if ( isset( $settings['login'] ) && isset( $settings['login']['login_header_url'] ) && ! empty( $settings['login']['login_header_url'] ) ) {
            return ( function_exists( 'esc_url' ) ? \esc_url( $settings['login']['login_header_url'] ) : $settings['login']['login_header_url'] );
        }

        return $url;
    }

    /**
     * Filter login header title
     *
     * @param string $title
     * @return string
     */
    public function filter_login_header_title( string $title ): string {
        // Load settings
        $settings = \wpca_get_settings();

        // Change login header title if custom title is set
        if ( isset( $settings['login'] ) && isset( $settings['login']['login_header_title'] ) && ! empty( $settings['login']['login_header_title'] ) ) {
            return ( function_exists( 'esc_html' ) ? \esc_html( $settings['login']['login_header_title'] ) : $settings['login']['login_header_title'] );
        }

        return $title;
    }

    /**
     * Add login footer content
     */
    public function add_login_footer_content(): void {
        // Load settings
        $settings = \wpca_get_settings();

        // Add custom footer content if set
        if ( isset( $settings['login'] ) && isset( $settings['login']['login_footer_content'] ) && ! empty( $settings['login']['login_footer_content'] ) ) {
            echo ( function_exists( '\wp_kses_post' ) ? \wp_kses_post( $settings['login']['login_footer_content'] ) : $settings['login']['login_footer_content'] );
        }
    }

    /**
     * Filter login body class
     *
     * @param array $classes
     * @return array
     */
    public function filter_login_body_class( $classes ): array {
        // Load settings
        $settings = \wpca_get_settings();

        // Add custom body class if set
        if ( isset( $settings['login'] ) && isset( $settings['login']['login_body_class'] ) && ! empty( $settings['login']['login_body_class'] ) ) {
            $classes[] = ( function_exists( '\sanitize_html_class' ) ? \sanitize_html_class( $settings['login']['login_body_class'] ) : $settings['login']['login_body_class'] );
        }

        return $classes;
    }
}
