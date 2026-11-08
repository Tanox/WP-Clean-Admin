<?php
/**
 * WPCleanAdmin Settings Registration Tasks
 *
 * 设置注册（菜单页、设置区段/字段、register_setting）与验证回调，公开 API 不变。
 * 主类 `Settings` 通过 `use SettingsRegistrationTasks;` 组合。
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
 * Settings registration tasks
 */
trait SettingsRegistrationTasks {

    public function register_settings_page() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        if ( function_exists( 'add_options_page' ) ) {
            \add_options_page(
                \__( 'WP Clean Admin', $text_domain ),
                \__( 'Clean Admin', $text_domain ),
                'manage_options',
                'wp-clean-admin',
                array( $this, 'render_settings_page' )
            );
        }
    }

    public function register_settings() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';

        // Register settings sections
        $sections = array(
            'general' => array(
                'id' => 'wpca_general_settings',
                'title' => \__( 'General Settings', $text_domain ),
                'callback' => array( $this, 'render_general_settings_section' )
            ),
            'cleanup' => array(
                'id' => 'wpca_cleanup_settings',
                'title' => \__( 'Cleanup Settings', $text_domain ),
                'callback' => array( $this, 'render_cleanup_settings_section' )
            ),
            'performance' => array(
                'id' => 'wpca_performance_settings',
                'title' => \__( 'Performance Settings', $text_domain ),
                'callback' => array( $this, 'render_performance_settings_section' )
            ),
            'security' => array(
                'id' => 'wpca_security_settings',
                'title' => \__( 'Security Settings', $text_domain ),
                'callback' => array( $this, 'render_security_settings_section' )
            )
        );

        foreach ( $sections as $section ) {
            if ( function_exists( 'add_settings_section' ) ) {
                \add_settings_section(
                    $section['id'],
                    $section['title'],
                    $section['callback'],
                    'wp-clean-admin'
                );
            }
        }

        // Register settings fields
        $this->register_settings_fields();

        // Register setting
        if ( function_exists( 'register_setting' ) ) {
            \register_setting( 'wp-clean-admin', 'wpca_settings', array( $this, 'validate_settings' ) );
        }
    }

    private function register_settings_fields() {
        // Include settings fields
        if ( file_exists( dirname( __FILE__ ) . '/class-wpca-settings-fields.php' ) ) {
            require_once dirname( __FILE__ ) . '/class-wpca-settings-fields.php';
        }

        // Register fields using the fields class
        if ( class_exists( 'WPCleanAdmin\Modules\Admin\Settings\Settings_Fields' ) ) {
            $fields = \WPCleanAdmin\Modules\Admin\Settings\Settings_Fields::getInstance();
            $fields->register_fields();
        }
    }

    /**
     * 验证设置
     *
     * @param array|mixed $input 输入的设置数据
     * @return array|mixed 验证后的设置数据
     */
    public function validate_settings( $input ) {
        // Include validation
        if ( file_exists( dirname( __FILE__ ) . '/class-wpca-settings-validation.php' ) ) {
            require_once dirname( __FILE__ ) . '/class-wpca-settings-validation.php';
        }

        // Validate using the validation class
        if ( class_exists( 'WPCleanAdmin\Modules\Admin\Settings\Settings_Validation' ) ) {
            $validation = \WPCleanAdmin\Modules\Admin\Settings\Settings_Validation::getInstance();
            return $validation->validate( $input );
        }

        return $input;
    }
}
