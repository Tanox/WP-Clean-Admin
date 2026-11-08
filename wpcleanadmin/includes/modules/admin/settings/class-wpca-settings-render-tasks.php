<?php
/**
 * WPCleanAdmin Settings Render Tasks
 *
 * 设置区段说明与设置页渲染，公开 API 不变。
 * 主类 `Settings` 通过 `use SettingsRenderTasks;` 组合。
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
 * Settings render tasks
 */
trait SettingsRenderTasks {

    public function render_general_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure general settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    public function render_cleanup_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure cleanup settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    public function render_performance_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure performance optimization settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    public function render_security_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure security settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    public function render_settings_page() {
        $page = new Settings_Page();
        $page->render();
    }
}
