<?php
/**
 * WPCleanAdmin Menu Customizer Tree
 *
 * 承载菜单结构构建、排序与分组逻辑，从 Menu_Customizer 主类抽取。
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

require_once __DIR__ . '/class-wpca-menu-customizer-tree-customize-tasks.php';
require_once __DIR__ . '/class-wpca-menu-customizer-tree-order-tasks.php';

if ( ! function_exists( 'sanitize_html_class' ) ) {
    function sanitize_html_class() {}
}

/**
 * 菜单结构构建类
 */
class Menu_Customizer_Tree {

    use MenuCustomizerTreeCustomizeTasks;
    use MenuCustomizerTreeOrderTasks;
}
