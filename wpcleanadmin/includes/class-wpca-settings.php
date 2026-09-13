<?php
/**
 * WPCleanAdmin Settings Class
 *
 * 设置门面类：保留单例、初始化与子模块 require，设置注册与页面渲染
 * 按职责抽离至 Settings_*_Tasks trait，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.11
 * @update_date 2026-01-28
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include menu customization settings
if ( file_exists( dirname( __FILE__ ) . '/settings/menu-customization.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/menu-customization.php';
}

// Include settings fields classes
if ( file_exists( dirname( __FILE__ ) . '/settings/fields/class-wpca-general-settings-fields.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/fields/class-wpca-general-settings-fields.php';
}

if ( file_exists( dirname( __FILE__ ) . '/settings/fields/class-wpca-cleanup-settings-fields.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/fields/class-wpca-cleanup-settings-fields.php';
}

if ( file_exists( dirname( __FILE__ ) . '/settings/fields/class-wpca-performance-settings-fields.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/fields/class-wpca-performance-settings-fields.php';
}

if ( file_exists( dirname( __FILE__ ) . '/settings/fields/class-wpca-security-settings-fields.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/fields/class-wpca-security-settings-fields.php';
}

if ( file_exists( dirname( __FILE__ ) . '/settings/fields/class-wpca-diagnostics-settings-fields.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/fields/class-wpca-diagnostics-settings-fields.php';
}

// Include settings validation class
if ( file_exists( dirname( __FILE__ ) . '/settings/class-wpca-settings-validation.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/class-wpca-settings-validation.php';
}

// Include settings scripts class
if ( file_exists( dirname( __FILE__ ) . '/settings/class-wpca-settings-scripts.php' ) ) {
    require_once dirname( __FILE__ ) . '/settings/class-wpca-settings-scripts.php';
}

// WordPress stubs are loaded in autoload.php for IDE compatibility only

/**
 * Settings class
 */
class Settings {

    use SettingsRegistrationTasks;
    use SettingsPageRenderTasks;

    /**
     * 单例实例
     *
     * @var Settings|null
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Settings
     */
    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize the settings module
     */
    public function init() {
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
            \add_action( 'admin_init', array( $this, 'register_settings' ) );
            \add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }
    }

    /**
     * Register the settings page
     */
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

    /**
     * Enqueue settings scripts and styles
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_scripts( string $hook ) {
        \WPCleanAdmin\Settings\Settings_Scripts::enqueue_scripts( $hook );
    }
}
