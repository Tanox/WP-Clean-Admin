<?php
/**
 * WPCleanAdmin Settings Registration Tasks Trait
 *
 * 设置注册（section/field/setting）与 section 说明渲染，
 * 从 class-wpca-settings.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置注册任务 trait
 */
trait SettingsRegistrationTasks {

    /**
     * Register all plugin settings
     */
    public function register_settings() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        // Register general settings section
        if ( function_exists( 'add_settings_section' ) ) {
            \add_settings_section(
                'wpca_general_settings',
                \__( 'General Settings', $text_domain ),
                array( $this, 'render_general_settings_section' ),
                'wp-clean-admin'
            );
        }

        // Register cleanup settings section
        if ( function_exists( 'add_settings_section' ) ) {
            \add_settings_section(
                'wpca_cleanup_settings',
                \__( 'Cleanup Settings', $text_domain ),
                array( $this, 'render_cleanup_settings_section' ),
                'wp-clean-admin'
            );
        }

        // Register performance settings section
        if ( function_exists( 'add_settings_section' ) ) {
            \add_settings_section(
                'wpca_performance_settings',
                \__( 'Performance Settings', $text_domain ),
                array( $this, 'render_performance_settings_section' ),
                'wp-clean-admin'
            );
        }

        // Register security settings section
        if ( function_exists( 'add_settings_section' ) ) {
            \add_settings_section(
                'wpca_security_settings',
                \__( 'Security Settings', $text_domain ),
                array( $this, 'render_security_settings_section' ),
                'wp-clean-admin'
            );
        }

        // Register diagnostics settings section
        if ( function_exists( 'add_settings_section' ) ) {
            \add_settings_section(
                'wpca_diagnostics_settings',
                \__( 'Diagnostics Settings', $text_domain ),
                array( $this, 'render_diagnostics_settings_section' ),
                'wp-clean-admin'
            );
        }

        // Register general settings fields
        if ( function_exists( 'add_settings_field' ) ) {
            \add_settings_field(
                'wpca_clean_admin_bar',
                \__( 'Clean Admin Bar', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\General_Settings_Fields', 'render_clean_admin_bar_field' ),
                'wp-clean-admin',
                'wpca_general_settings'
            );

            \add_settings_field(
                'wpca_remove_wp_logo',
                \__( 'Remove WordPress Logo', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\General_Settings_Fields', 'render_remove_wp_logo_field' ),
                'wp-clean-admin',
                'wpca_general_settings'
            );
        }

        // Register cleanup settings fields
        if ( function_exists( 'add_settings_field' ) ) {
            \add_settings_field(
                'wpca_remove_dashboard_widgets',
                \__( 'Remove Dashboard Widgets', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Cleanup_Settings_Fields', 'render_remove_dashboard_widgets_field' ),
                'wp-clean-admin',
                'wpca_cleanup_settings'
            );

            \add_settings_field(
                'wpca_simplify_admin_menu',
                \__( 'Simplify Admin Menu', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Cleanup_Settings_Fields', 'render_simplify_admin_menu_field' ),
                'wp-clean-admin',
                'wpca_cleanup_settings'
            );

            \add_settings_field(
                'wpca_menu_customization',
                \__( 'Menu Customization', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Cleanup_Settings_Fields', 'render_menu_customization_field' ),
                'wp-clean-admin',
                'wpca_cleanup_settings'
            );
        }

        // Register performance settings fields
        if ( function_exists( 'add_settings_field' ) ) {
            \add_settings_field(
                'wpca_optimize_database',
                \__( 'Optimize Database', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Performance_Settings_Fields', 'render_optimize_database_field' ),
                'wp-clean-admin',
                'wpca_performance_settings'
            );

            \add_settings_field(
                'wpca_clean_transients',
                \__( 'Clean Transients', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Performance_Settings_Fields', 'render_clean_transients_field' ),
                'wp-clean-admin',
                'wpca_performance_settings'
            );
        }

        // Register security settings fields
        if ( function_exists( 'add_settings_field' ) ) {
            \add_settings_field(
                'wpca_hide_wp_version',
                \__( 'Hide WordPress Version', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Security_Settings_Fields', 'render_hide_wp_version_field' ),
                'wp-clean-admin',
                'wpca_security_settings'
            );

            \add_settings_field(
                'wpca_disable_xmlrpc',
                \__( 'Disable XML-RPC', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Security_Settings_Fields', 'render_disable_xmlrpc_field' ),
                'wp-clean-admin',
                'wpca_security_settings'
            );

            \add_settings_field(
                'wpca_restrict_rest_api',
                \__( 'Restrict REST API Access', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Security_Settings_Fields', 'render_restrict_rest_api_field' ),
                'wp-clean-admin',
                'wpca_security_settings'
            );

            \add_settings_field(
                'wpca_restrict_admin_access',
                \__( 'Restrict Admin Access', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Security_Settings_Fields', 'render_restrict_admin_access_field' ),
                'wp-clean-admin',
                'wpca_security_settings'
            );
        }

        // Register diagnostics settings fields
        if ( function_exists( 'add_settings_field' ) ) {
            \add_settings_field(
                'wpca_enable_diagnostics',
                \__( 'Enable Diagnostics', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Diagnostics_Settings_Fields', 'render_enable_diagnostics_field' ),
                'wp-clean-admin',
                'wpca_diagnostics_settings'
            );

            \add_settings_field(
                'wpca_auto_run_diagnostics',
                \__( 'Auto Run Diagnostics', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Diagnostics_Settings_Fields', 'render_auto_run_diagnostics_field' ),
                'wp-clean-admin',
                'wpca_diagnostics_settings'
            );

            \add_settings_field(
                'wpca_show_warnings',
                \__( 'Show Warnings', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Diagnostics_Settings_Fields', 'render_show_warnings_field' ),
                'wp-clean-admin',
                'wpca_diagnostics_settings'
            );

            \add_settings_field(
                'wpca_severity_filter',
                \__( 'Severity Filter', $text_domain ),
                array( '\WPCleanAdmin\Settings\Fields\Diagnostics_Settings_Fields', 'render_severity_filter_field' ),
                'wp-clean-admin',
                'wpca_diagnostics_settings'
            );
        }

        // Register setting
        if ( function_exists( 'register_setting' ) ) {
            \register_setting( 'wp-clean-admin', 'wpca_settings', array( '\WPCleanAdmin\Settings\Settings_Validation', 'validate_settings' ) );
        }
    }

    /**
     * Render general settings section description
     */
    public function render_general_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure general settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    /**
     * Render cleanup settings section description
     */
    public function render_cleanup_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure cleanup settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    /**
     * Render performance settings section description
     */
    public function render_performance_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure performance optimization settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    /**
     * Render security settings section description
     */
    public function render_security_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure security settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }

    /**
     * Render diagnostics settings section description
     */
    public function render_diagnostics_settings_section() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        echo '<p>' . \__( 'Configure site health diagnostics settings for WP Clean Admin plugin.', $text_domain ) . '</p>';
    }
}
