<?php
/**
 * WPCleanAdmin Core Modules Tasks
 *
 * 承载模块加载相关逻辑（legacy 与模块化两套），从 Core 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.8
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 *
 * @noinspection PhpUndefinedFunctionInspection WordPress functions are available in WP environment
 */

namespace WPCleanAdmin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 模块加载相关任务（trait）。
 */
trait CoreModulesTasks {

    /**
     * Initialize all plugin modules
     *
     * Legacy Core 不再自动加载子模块，请使用 Modules\Core\Classes\Core
     */
    private function init_modules() {
        // Legacy Core 不再自动加载子模块，请使用 Modules\Core\Classes\Core
    }

    /**
     * Load legacy modules for backward compatibility
     */
    private function load_legacy_modules() {
        // Module initialization order matters - load core modules first
        $modules = array(
            'Settings',
            'Dashboard',
            'Database',
            'Performance',
            'Menu_Manager',
            'Menu_Customizer',
            'Permissions',
            'User_Roles',
            'Login',
            'Cleanup',
            'Resources',
            'Reset',
            'AJAX',
            'i18n',
            'Error_Handler',
            'Cache',
            'Extension_API'
        );

        foreach ( $modules as $module ) {
            $class_name = 'WPCleanAdmin\\' . $module;

            try {
                if ( class_exists( $class_name ) ) {
                    // Check if getInstance method exists
                    if ( method_exists( $class_name, 'getInstance' ) ) {
                        $class_name::getInstance();
                    }
                }
            } catch ( \Exception $e ) {
                // Silently catch exceptions during module initialization
                // This prevents the entire plugin from failing if one module has an issue
                // Log error for debugging if needed
                if ( function_exists( 'error_log' ) ) {
                    \error_log( 'WPCA Legacy Module Init Error: ' . $e->getMessage() );
                }
            }
        }
    }

    /**
     * Load modules from new modular structure
     */
    private function load_modular_modules() {
        // Load admin settings module
        try {
            $class_name = 'WPCleanAdmin\\Modules\\Admin\\Settings\\Settings';
            if ( class_exists( $class_name ) ) {
                if ( method_exists( $class_name, 'getInstance' ) ) {
                    $class_name::getInstance();
                }
            }
        } catch ( \Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                \error_log( 'WPCA Modular Module Init Error: ' . $e->getMessage() );
            }
        }
    }
}
