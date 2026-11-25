<?php
/**
 * WPCleanAdmin Menu Customizer Class
 *
 * @package WPCleanAdmin
 * @version 1.9.17
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
        $this->tree    = new Menu_Customizer_Tree( $this->options );
        $this->render  = new Menu_Customizer_Render( $this->options );
        $this->init();
    }

    /**
     * Initialize the menu customizer module
     */
    public function init() {
        $this->options->get_settings();

        if ( ! function_exists( '\add_action' ) ) {
            return;
        }

        // 设置缺省为空数组时定制方法天然 no-op，无需旧版 menu_customizer 门禁
        \add_action( 'admin_menu', array( $this, 'customize_admin_menu' ), 999 );
        \add_action( 'admin_bar_menu', array( $this, 'customize_admin_bar' ), 999 );

        // 4 个 AJAX 动作无条件注册；nonce + 能力校验保留在 AJAX trait 内
        \add_action( 'wp_ajax_wpca_save_menu_customizer', array( $this, 'ajax_save_settings' ) );
        \add_action( 'wp_ajax_wpca_reset_menu_customizer', array( $this, 'ajax_reset_settings' ) );
        \add_action( 'wp_ajax_wpca_export_menu_customizer', array( $this, 'ajax_export_settings' ) );
        \add_action( 'wp_ajax_wpca_import_menu_customizer', array( $this, 'ajax_import_settings' ) );
    }
}
