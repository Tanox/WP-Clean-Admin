<?php
/**
 * WPCleanAdmin Core Class
 *
 * @package WPCleanAdmin\Modules\Core\Classes
 * @version 1.8.9
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Core\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 注：原文件在此处有 3 个 require_once（security-headers / module-loader / core-settings），
// 已移除 —— 由 Composer / WPCA 内部 autoloader 统一处理。

/**
 * Core class
 */
class Core {

    /**
     * Singleton instance
     *
     * @var Core
     */
    private static $instance = null;

    /**
     * 安全头发送器
     *
     * @var Security_Headers
     */
    private $security_headers;

    /**
     * 模块加载器
     *
     * @var Module_Loader
     */
    private $module_loader;

    /**
     * 默认设置管理器
     *
     * @var Core_Settings
     */
    private $core_settings;

    /**
     * Get singleton instance
     *
     * @return Core
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
     * Initialize the plugin
     */
    public function init() {
        // Load core functions
        if ( defined( 'WPCA_PLUGIN_DIR' ) ) {
            require_once WPCA_PLUGIN_DIR . 'includes/wpca-core-functions.php';
        } else {
            // Fallback if WPCA_PLUGIN_DIR not defined
            require_once dirname( __DIR__ ) . '/wpca-core-functions.php';
        }

        $this->security_headers = new Security_Headers();
        $this->module_loader    = new Module_Loader();
        $this->core_settings    = new Core_Settings();

        // Add security headers
        $this->security_headers->register();

        // Initialize modules
        // Legacy 加载已在 Module_Loader::load_legacy_modules() 内部通过常量守卫禁用，
        // 这里也直接注释掉，避免无效调用开销。
        // $this->module_loader->load_legacy_modules();
        $this->module_loader->load_modular_modules();
    }

    /**
     * Plugin activation callback
     */
    public function activate() {
        // Set default settings
        $this->core_settings->set_default_settings();

        // Flush rewrite rules
        if ( function_exists( 'flush_rewrite_rules' ) ) {
            \flush_rewrite_rules();
        }
    }

    /**
     * Plugin deactivation callback
     */
    public function deactivate() {
        // Flush rewrite rules
        if ( function_exists( 'flush_rewrite_rules' ) ) {
            \flush_rewrite_rules();
        }
    }
}
