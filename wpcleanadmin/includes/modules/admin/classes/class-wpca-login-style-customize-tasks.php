<?php
/**
 * WPCleanAdmin Login Style Customize Tasks
 *
 * 登录页 Logo/背景定制（含内联样式输出），公开 API 不变。
 * 主类 `Login_Style` 通过 `use LoginStyleCustomizeTasks;` 组合。
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
 * Login style customize tasks
 */
trait LoginStyleCustomizeTasks {

    /**
     * Customize login page (register logo/background hooks)
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
            $logo_url    = ( function_exists( 'esc_url' ) ? \esc_url( $settings['login']['login_logo_url'] ) : $settings['login']['login_logo_url'] );
            $logo_width  = isset( $settings['login']['login_logo_width'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_logo_width'] ) : $settings['login']['login_logo_width'] ) : '200px';
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
    public function add_custom_login_background(): void {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['login'] ) && isset( $settings['login']['login_background_url'] ) && ! empty( $settings['login']['login_background_url'] ) ) {
            $background_url       = ( function_exists( 'esc_url' ) ? \esc_url( $settings['login']['login_background_url'] ) : $settings['login']['login_background_url'] );
            $background_repeat    = isset( $settings['login']['login_background_repeat'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_background_repeat'] ) : $settings['login']['login_background_repeat'] ) : 'no-repeat';
            $background_position  = isset( $settings['login']['login_background_position'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_background_position'] ) : $settings['login']['login_background_position'] ) : 'center center';
            $background_size      = isset( $settings['login']['login_background_size'] ) ? ( function_exists( 'esc_attr' ) ? \esc_attr( $settings['login']['login_background_size'] ) : $settings['login']['login_background_size'] ) : 'cover';

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
