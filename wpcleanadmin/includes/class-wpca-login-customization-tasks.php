<?php
/**
 * WPCleanAdmin Login Customization Tasks Trait
 *
 * 登录页定制（样式/Logo/背景/页脚/Header/Body class），
 * 从 class-wpca-login.php 按职责抽离，公开方法契约不变。
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
 * 登录页定制任务 trait
 */
trait LoginCustomizationTasks {

    /**
     * Enqueue login scripts and styles
     *
     * @uses wpca_get_settings() To retrieve plugin settings
     * @uses \wp_enqueue_style() To enqueue login styles
     * @uses \wp_enqueue_script() To enqueue login scripts
     * @return void
     */
    public function enqueue_login_scripts() {
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
     * @param string $url Login header URL
     * @return string Modified URL
     */
    public function filter_login_header_url( $url ) {
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
     * @param string $title Login header title
     * @return string Modified title
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
    public function add_login_footer_content() {
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
     * @param array $classes Body classes
     * @return array Modified classes
     */
    public function filter_login_body_class( $classes ) {
        // Load settings
        $settings = \wpca_get_settings();

        // Add custom body class if set
        if ( isset( $settings['login'] ) && isset( $settings['login']['login_body_class'] ) && ! empty( $settings['login']['login_body_class'] ) ) {
            $classes[] = ( function_exists( '\sanitize_html_class' ) ? \sanitize_html_class( $settings['login']['login_body_class'] ) : $settings['login']['login_body_class'] );
        }

        return $classes;
    }

    /**
     * Customize login page
     */
    public function customize_login_page(): void {
        // Load settings
        $settings = \wpca_get_settings();

        // Customize login page based on settings
        if ( isset( $settings['login'] ) && function_exists( 'add_action' ) ) {
            // Change login logo
            if ( isset( $settings['login']['custom_login_logo'] ) && $settings['login']['custom_login_logo'] && isset( $settings['login']['login_logo_url'] ) && ! empty( $settings['login']['login_logo_url'] ) ) {
                \add_action( 'login_head', array( $this, 'add_custom_login_logo' ) );
            }

            // Change login background
            if ( isset( $settings['login']['custom_login_background'] ) && $settings['login']['custom_login_background'] && isset( $settings['login']['login_background_url'] ) && ! empty( $settings['login']['login_background_url'] ) ) {
                \add_action( 'login_head', array( $this, 'add_custom_login_background' ) );
            }
        }
    }

    /**
     * Add custom login logo
     */
    public function add_custom_login_logo(): void {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['login'] ) && isset( $settings['login']['login_logo_url'] ) && ! empty( $settings['login']['login_logo_url'] ) ) {
            $logo_url = ( function_exists( 'esc_url' ) ? \esc_url( $settings['login']['login_logo_url'] ) : $settings['login']['login_logo_url'] );
            $logo_width = isset( $settings['login']['login_logo_width'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_logo_width'] ) : $settings['login']['login_logo_width'] ) : '200px';
            $logo_height = isset( $settings['login']['login_logo_height'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_logo_height'] ) : $settings['login']['login_logo_height'] ) : '80px';

            echo "<style type='text/css'>
                #login h1 a {
                    background-image: url('{$logo_url}');
                    background-size: contain;
                    width: {$logo_width};
                    height: {$logo_height};
                }
            </style>";
        }
    }

    /**
     * Add custom login background
     */
    public function add_custom_login_background() {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['login'] ) && isset( $settings['login']['login_background_url'] ) && ! empty( $settings['login']['login_background_url'] ) ) {
            $background_url = ( function_exists( 'esc_url' ) ? \esc_url( $settings['login']['login_background_url'] ) : $settings['login']['login_background_url'] );
            $background_repeat = isset( $settings['login']['login_background_repeat'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_background_repeat'] ) : $settings['login']['login_background_repeat'] ) : 'no-repeat';
            $background_position = isset( $settings['login']['login_background_position'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_background_position'] ) : $settings['login']['login_background_position'] ) : 'center center';
            $background_size = isset( $settings['login']['login_background_size'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_background_size'] ) : $settings['login']['login_background_size'] ) : 'cover';

            echo "<style type='text/css'>
                body.login {
                    background-image: url('{$background_url}');
                    background-repeat: {$background_repeat};
                    background-position: {$background_position};
                    background-size: {$background_size};
                }
            </style>";
        }
    }
}
