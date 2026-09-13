<?php
/**
 * WPCleanAdmin Extension API Class
 *
 * 扩展 API 门面类：保留单例、扩展集合属性与 WordPress stub 无关的全局 helper，
 * 具体能力按职责拆入 Extension_*_Tasks trait（注册/钩子/菜单/设置），公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.0
 * @description Extension API for third-party developers to extend WP Clean Admin functionality
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Extension API class for plugin extensibility
 *
 * Provides APIs for third-party developers to extend plugin functionality
 * including hooks, filters, and custom modules
 */
class Extension_API {

    use ExtensionRegistrationTasks;
    use ExtensionHookTasks;
    use ExtensionMenuTasks;
    use ExtensionSettingsTasks;

    /**
     * Singleton instance
     *
     * @var Extension_API
     */
    private static $instance = null;

    /**
     * Registered extensions
     *
     * @var array
     */
    private $extensions = array();

    /**
     * Extension hooks
     *
     * @var array
     */
    private $hooks = array();

    /**
     * Extension filters
     *
     * @var array
     */
    private $filters = array();

    /**
     * Get singleton instance
     *
     * @return Extension_API
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
     * Initialize the extension API
     *
     * @uses \add_action() To register initialization action
     * @return void
     */
    private function init() {
        \add_action( 'wpca_init', array( $this, 'load_extensions' ) );
    }
}

/**
 * Register extension with WP Clean Admin
 *
 * Helper function for third-party developers to register extensions
 *
 * @param array $extension Extension configuration
 * @return bool Success status
 */
function wpca_register_extension( $extension ) {
    $api = Extension_API::getInstance();
    return $api->register_extension( $extension );
}

/**
 * Get WP Clean Admin extension API instance
 *
 * @return Extension_API
 */
function wpca_get_extension_api() {
    return Extension_API::getInstance();
}

/**
 * Save extension settings
 *
 * Helper function for third-party developers to save extension settings
 *
 * @param string $extension_id Extension ID
 * @param array $settings Extension settings
 * @return bool Success status
 */
function wpca_save_extension_settings( $extension_id, $settings ) {
    $api = Extension_API::getInstance();
    return $api->save_extension_settings( $extension_id, $settings );
}

/**
 * Get extension settings
 *
 * Helper function for third-party developers to get extension settings
 *
 * @param string $extension_id Extension ID
 * @return array Extension settings
 */
function wpca_get_extension_settings( $extension_id ) {
    $api = Extension_API::getInstance();
    return $api->get_extension_settings( $extension_id );
}

/**
 * Reset extension settings
 *
 * Helper function for third-party developers to reset extension settings
 *
 * @param string $extension_id Extension ID
 * @return bool Success status
 */
function wpca_reset_extension_settings( $extension_id ) {
    $api = Extension_API::getInstance();
    return $api->reset_extension_settings( $extension_id );
}

/**
 * Install extension
 *
 * Helper function for third-party developers to install extensions
 *
 * @param string $extension_id Extension ID
 * @param array $extension_data Extension data
 * @return bool Success status
 */
function wpca_install_extension( $extension_id, $extension_data = array() ) {
    $api = Extension_API::getInstance();
    return $api->install_extension( $extension_id, $extension_data );
}

/**
 * Uninstall extension
 *
 * Helper function for third-party developers to uninstall extensions
 *
 * @param string $extension_id Extension ID
 * @return bool Success status
 */
function wpca_uninstall_extension( $extension_id ) {
    $api = Extension_API::getInstance();
    return $api->uninstall_extension( $extension_id );
}
