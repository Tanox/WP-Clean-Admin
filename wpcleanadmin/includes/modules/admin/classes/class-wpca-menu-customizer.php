<?php
/**
 * WPCleanAdmin Menu Customizer Class
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-menu-customizer-options.php';
require_once __DIR__ . '/class-wpca-menu-customizer-tree.php';
require_once __DIR__ . '/class-wpca-menu-customizer-render.php';
require_once __DIR__ . '/class-wpca-menu-customizer-delegation-tasks.php';
require_once __DIR__ . '/class-wpca-menu-customizer-ajax-tasks.php';

/**
 * Menu_Customizer class
 */
class Menu_Customizer {

    use MenuCustomizerDelegationTasks;
    use MenuCustomizerAjaxTasks;

    /**
     * Singleton instance
     *
     * @var Menu_Customizer
     */
    private static $instance = null;

    /**
     * 设置操作处理器
     *
     * @var Menu_Customizer_Options
     */
    private $options;

    /**
     * 菜单结构构建处理器
     *
     * @var Menu_Customizer_Tree
     */
    private $tree;

    /**
     * 菜单渲染/导出处理器
     *
     * @var Menu_Customizer_Render
     */
    private $render;

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
        $this->options = new Menu_Customizer_Options();
        $this->tree    = new Menu_Customizer_Tree();
        $this->render  = new Menu_Customizer_Render();
        $this->init();
    }

    /**
     * Initialize the menu customizer module
     */
    public function init() {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['menu_customizer'] ) && function_exists( 'add_action' ) ) {
            // Add admin menu customization
            \add_action( 'admin_menu', array( $this, 'customize_admin_menu' ), 999 );
            \add_action( 'admin_bar_menu', array( $this, 'customize_admin_bar' ), 999 );

            // Add AJAX handlers (wrapped with nonce + capability verification)
            \add_action( 'wp_ajax_wpca_save_menu_customizer', array( $this, 'ajax_save_settings' ) );
            \add_action( 'wp_ajax_wpca_reset_menu_customizer', array( $this, 'ajax_reset_settings' ) );

            // Export/Import handlers
            \add_action( 'wp_ajax_wpca_export_menu_customizer', array( $this, 'ajax_export_settings' ) );
            \add_action( 'wp_ajax_wpca_import_menu_customizer', array( $this, 'ajax_import_settings' ) );
        }
    }
}
