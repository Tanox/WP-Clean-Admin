<?php
/**
 * WPCleanAdmin Settings Registration Sections Tasks
 *
 * 设置 section 注册与 section 说明渲染，从 SettingsRegistrationTasks 抽离。
 *
 * @package WPCleanAdmin
 * @version 1.9.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置注册（sections）任务（trait）。
 */
trait SettingsRegistrationSectionsTasks {

    /**
     * Register all plugin settings
     */
    public function register_settings() {
        $this->register_settings_sections();
        $this->register_settings_fields();
    }

    /**
     * Register settings sections
     */
    public function register_settings_sections() {
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
