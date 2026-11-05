<?php
/**
 * WPCleanAdmin Menu Manager Class
 *
 * @package WPCleanAdmin
 * @version 1.9.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Declare WordPress functions for IDE compatibility
if ( ! function_exists( 'wp_get_current_user' ) ) {
    function wp_get_current_user() {}
}
if ( ! function_exists( 'add_action' ) ) {
    function add_action() {}
}
if ( ! function_exists( 'remove_meta_box' ) ) {
    function remove_meta_box() {}
}
if ( ! function_exists( 'remove_action' ) ) {
    function remove_action() {}
}
if ( ! function_exists( 'update_option' ) ) {
    function update_option() {}
}

// 引入拆分出的菜单管理 trait
require_once __DIR__ . '/class-wpca-menu-manager-display-tasks.php';
require_once __DIR__ . '/class-wpca-menu-manager-restrictions-tasks.php';
require_once __DIR__ . '/class-wpca-menu-manager-role-restrictions-tasks.php';

/**
 * Menu_Manager class
 */
class Menu_Manager {

    use MenuManagerDisplayTasks;
    use MenuManagerRestrictionsTasks;
    use MenuManagerRoleRestrictionsTasks;

    /**
     * Singleton instance
     *
     * @var Menu_Manager
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Menu_Manager
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
     * Initialize the menu manager module
     */
    public function init() {
        // Load settings
        $settings = \wpca_get_settings();

        // Apply menu optimizations based on settings
        if ( isset( $settings['menu'] ) && function_exists( 'add_action' ) ) {
            // Remove dashboard widgets
            if ( isset( $settings['menu']['remove_dashboard_widgets'] ) && $settings['menu']['remove_dashboard_widgets'] ) {
                \add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
            }

            // Apply menu customizations
            $this->simplify_admin_menu_by_settings();

            // Clean admin bar
            if ( isset( $settings['general']['clean_admin_bar'] ) && $settings['general']['clean_admin_bar'] ) {
                \add_action( 'admin_bar_menu', array( $this, 'clean_admin_bar' ), 999 );
            }

            // Apply role-based menu restrictions
            \add_action( 'admin_menu', array( $this, 'apply_role_based_menu_restrictions' ), 999 );
        }
    }

    /**
     * Get menu items
     *
     * @return array Menu items
     */
    public function get_menu_items() {
        global $menu, $submenu;

        $menu_items = array();

        // Get top-level menu items
        foreach ( $menu as $key => $menu_item ) {
            if ( ! empty( $menu_item[0] ) && $menu_item[0] !== '-' ) {
                $menu_item_data = array(
                    'id' => $key,
                    'title' => $menu_item[0],
                    'slug' => $menu_item[2],
                    'capability' => $menu_item[1],
                    'icon' => $menu_item[6],
                    'position' => $menu_item[5],
                    'submenu' => array()
                );

                // Get submenu items
                if ( isset( $submenu[$menu_item[2]] ) ) {
                    foreach ( $submenu[$menu_item[2]] as $submenu_key => $submenu_item ) {
                        $menu_item_data['submenu'][] = array(
                            'id' => $submenu_key,
                            'title' => $submenu_item[0],
                            'slug' => $submenu_item[2],
                            'capability' => $submenu_item[1]
                        );
                    }
                }

                $menu_items[] = $menu_item_data;
            }
        }

        return $menu_items;
    }

    /**
     * Save menu items
     *
     * @param array $menu_items Menu items to save
     * @return array Save results
     */
    public function save_menu_items( $menu_items ) {
        $results = array(
            'success' => true,
            'message' => \__( 'Menu items saved successfully', \WPCA_TEXT_DOMAIN )
        );

        // Save menu items to options
        if ( function_exists( 'update_option' ) ) {
            \update_option( 'wpca_menu_items', $menu_items );
        }

        return $results;
    }
}
