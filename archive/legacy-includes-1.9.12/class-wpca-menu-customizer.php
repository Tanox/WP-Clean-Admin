<?php
/**
 * WPCleanAdmin Menu Customizer Class
 *
 * @package WPCleanAdmin
 * @version 1.9.9
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的菜单定制 trait
require_once __DIR__ . '/class-wpca-menu-customizer-apply-tasks.php';
require_once __DIR__ . '/class-wpca-menu-customizer-groups-tasks.php';
require_once __DIR__ . '/class-wpca-menu-customizer-structure-tasks.php';

/**
 * Menu_Customizer class
 */
class Menu_Customizer {

    use MenuCustomizerApplyTasks;
    use MenuCustomizerGroupsTasks;
    use MenuCustomizerStructureTasks;

    /**
     * Singleton instance
     *
     * @var Menu_Customizer
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return Menu_Customizer
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
     * Initialize the menu customizer module
     */
    public function init() {
        // Load settings
        $settings = $this->get_settings();

        // Apply menu customizations based on settings
        if ( isset( $settings['enabled'] ) && $settings['enabled'] ) {
            // Add menu customization hooks
            if ( function_exists( 'add_action' ) ) {
                \add_action( 'admin_menu', array( $this, 'customize_admin_menu' ), 999 );
                \add_action( 'admin_bar_menu', array( $this, 'customize_admin_bar' ), 999 );
            }
        }
    }

    /**
     * Get menu customizer settings
     *
     * @return array Menu customizer settings
     */
    public function get_settings() {
        // Get settings from options
        $settings = ( function_exists( 'get_option' ) ? \get_option( 'wpca_menu_customizer_settings', array() ) : array() );

        // Set default settings
        $default_settings = array(
            'enabled' => false,
            'menu_items' => array(),
            'submenu_items' => array(),
            'admin_bar_items' => array()
        );

        return ( function_exists( 'wp_parse_args' ) ? \wp_parse_args( $settings, $default_settings ) : array_merge( $default_settings, $settings ) );
    }

    /**
     * Save menu customizer settings
     *
     * @param array $settings Settings to save
     * @return bool Save result
     */
    public function save_settings( $settings ) {
        // Save settings to options
        return ( function_exists( 'update_option' ) ? \update_option( 'wpca_menu_customizer_settings', $settings ) : false );
    }

    /**
     * Reset menu customizer settings
     *
     * @return bool Reset result
     */
    public function reset_settings() {
        // Delete settings from options
        return ( function_exists( 'delete_option' ) ? \delete_option( 'wpca_menu_customizer_settings' ) : false );
    }
}
