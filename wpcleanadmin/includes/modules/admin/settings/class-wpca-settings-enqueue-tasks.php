<?php
/**
 * WPCleanAdmin Settings Enqueue Tasks
 *
 * 设置页脚本/样式入队，公开 API 不变。
 * 主类 `Settings` 通过 `use SettingsEnqueueTasks;` 组合。
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Settings enqueue tasks
 */
trait SettingsEnqueueTasks {

    public function enqueue_scripts( string $hook ) {
        if ( \strpos( $hook, 'wp-clean-admin' ) === false ) {
            return;
        }

        $plugin_url = defined( 'WPCA_PLUGIN_URL' ) ? WPCA_PLUGIN_URL : '';
        $plugin_version = defined( 'WPCA_VERSION' ) ? WPCA_VERSION : '1.8.0';

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

            // Enqueue settings-specific script from modular structure
            $settings_script_path = $plugin_url . 'includes/modules/admin/settings/assets/js/wpca-settings.js';
            \wp_enqueue_script(
                'wpca-settings',
                $settings_script_path,
                array( 'jquery', 'wpca-main' ),
                $plugin_version,
                true
            );

            // Localize script for translations
            if ( function_exists( 'wp_localize_script' ) ) {
                $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
                \wp_localize_script( 'wpca-settings', 'wpcaSettingsLocalize', array(
                    'savingText' => \__( 'Saving...', $text_domain ),
                    'successText' => \__( 'Settings saved successfully.', $text_domain ),
                    'errorText' => \__( 'Error saving settings.', $text_domain ),
                    'ajaxurl' => function_exists( 'admin_url' ) ? \admin_url( 'admin-ajax.php' ) : ''
                ) );
            }
        }
    }
}
