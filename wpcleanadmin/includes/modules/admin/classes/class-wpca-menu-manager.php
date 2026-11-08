<?php
/**
 * WPCleanAdmin Menu Manager Class
 *
 * @package WPCleanAdmin
 * @version 1.9.17
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

require_once __DIR__ . '/class-wpca-menu-manager-data.php';
require_once __DIR__ . '/class-wpca-menu-manager-restrict.php';
require_once __DIR__ . '/class-wpca-menu-manager-simplification-tasks.php';
require_once __DIR__ . '/class-wpca-menu-manager-delegation-tasks.php';

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Declare WordPress functions for IDE compatibility

/**
 * Menu_Manager class
 */
class Menu_Manager {

    use MenuManagerSimplificationTasks;
    use MenuManagerDelegationTasks;

    /**
     * Singleton instance
     *
     * @var Menu_Manager
     */
    private static $instance = null;

    /**
     * 菜单数据处理器
     *
     * @var Menu_Manager_Data
     */
    private $data;

    /**
     * 菜单限制处理器
     *
     * @var Menu_Manager_Restrict
     */
    private $restrict;

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
        $this->data     = new Menu_Manager_Data();
        $this->restrict = new Menu_Manager_Restrict();
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
}
