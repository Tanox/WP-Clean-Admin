<?php
/**
 * WPCleanAdmin Settings Scripts Class
 *
 * @package WPCleanAdmin
 * @version 1.9.12
 * @update_date 2026-11-05
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Settings_Scripts {

    /**
     * Enqueue scripts and styles
     *
     * @param string $hook Current admin page hook
     */
    public static function enqueue_scripts( $hook ) {
        if ( \strpos( $hook, 'wp-clean-admin' ) === false ) {
            return;
        }

        $plugin_url = defined( 'WPCA_PLUGIN_URL' ) ? WPCA_PLUGIN_URL : '';
        $plugin_version = defined( 'WPCA_VERSION' ) ? WPCA_VERSION : '1.8.0';
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';

        if ( function_exists( 'wp_enqueue_style' ) ) {
            \wp_enqueue_style(
                'wpca-admin',
                $plugin_url . 'assets/css/wpca-admin.css',
                array(),
                $plugin_version
            );
        }

        if ( function_exists( 'wp_enqueue_script' ) ) {
            \wp_enqueue_script(
                'wpca-main',
                $plugin_url . 'assets/js/wpca-main.js',
                array( 'jquery' ),
                $plugin_version,
                true
            );
        }

        // 1.9.12：由设置页渲染 trait / 菜单定制字段抽离出的内联资源，统一在此 enqueue
        if ( function_exists( 'wp_enqueue_style' ) ) {
            \wp_enqueue_style(
                'wpca-settings-page',
                $plugin_url . 'assets/css/wpca-settings-page.css',
                array( 'wpca-admin' ),
                $plugin_version
            );
            \wp_enqueue_style(
                'wpca-menu-customization',
                $plugin_url . 'assets/css/wpca-menu-customization.css',
                array( 'wpca-admin' ),
                $plugin_version
            );
        }

        if ( function_exists( 'wp_enqueue_script' ) ) {
            \wp_enqueue_script(
                'wpca-settings-page',
                $plugin_url . 'assets/js/wpca-settings-page.js',
                array( 'jquery' ),
                $plugin_version,
                true
            );
            // 'Saving...' 等本地化字符串注入供 JS 使用
            if ( function_exists( 'wp_localize_script' ) ) {
                \wp_localize_script(
                    'wpca-settings-page',
                    'wpcaSettings',
                    array( 'saveMessage' => \__( 'Saving...', $text_domain ) )
                );
            }

            \wp_enqueue_script(
                'wpca-menu-customization',
                $plugin_url . 'assets/js/wpca-menu-customization.js',
                array( 'jquery', 'jquery-ui-sortable' ),
                $plugin_version,
                true
            );
        }
    }
}
