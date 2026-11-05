<?php
/**
 * WPCleanAdmin Core Class
 *
 * @package WPCleanAdmin
 * @version 1.9.8
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的核心模块 trait
require_once __DIR__ . '/class-wpca-core-security-tasks.php';
require_once __DIR__ . '/class-wpca-core-modules-tasks.php';

/**
 * Core class
 */
class Core {

    use CoreSecurityTasks;
    use CoreModulesTasks;

    /**
     * Singleton instance
     *
     * @var Core
     */
    private static $instance = null;

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

        // Add security headers
        $this->add_security_headers();

        // Initialize modules
        $this->init_modules();
    }

    /**
     * Plugin activation callback
     */
    public function activate() {
        // Set default settings
        $this->set_default_settings();

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

    /**
     * Set default plugin settings
     */
    private function set_default_settings() {
        $default_settings = array(
            'general' => array(
                'clean_admin_bar' => 1,
                'clean_dashboard' => 1,
                'remove_wp_logo' => 1,
            ),
            'performance' => array(
                'optimize_database' => 1,
                'clean_transients' => 1,
                'disable_emojis' => 1,
            ),
            'menu' => array(
                'remove_dashboard_widgets' => 1,
                'simplify_admin_menu' => 1,
            ),
        );

        // Update settings if they don't exist
        $current_settings = ( function_exists( 'get_option' ) ? \get_option( 'wpca_settings', array() ) : array() );
        // wp_parse_args( $args, $defaults )：以 defaults 为基底，$args 覆盖 defaults
        $updated_settings = ( function_exists( 'wp_parse_args' ) ? \wp_parse_args( $current_settings, $default_settings ) : $current_settings );

        if ( function_exists( 'update_option' ) ) {
            \update_option( 'wpca_settings', $updated_settings );
        }
    }
}
