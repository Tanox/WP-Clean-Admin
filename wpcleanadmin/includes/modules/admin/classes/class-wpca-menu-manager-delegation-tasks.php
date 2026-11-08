<?php
/**
 * WPCleanAdmin Menu Manager Delegation Tasks
 *
 * 菜单数据/限制相关委托方法（转发至 `Menu_Manager_Data` / `Menu_Manager_Restrict`），
 * 公开 API 不变。主类 `Menu_Manager` 通过 `use MenuManagerDelegationTasks;` 组合。
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

/**
 * Menu manager delegation tasks
 */
trait MenuManagerDelegationTasks {

    /**
     * Get menu items (delegated to data handler)
     *
     * @return array
     */
    public function get_menu_items() {
        return $this->data->get_menu_items();
    }

    /**
     * Save menu items (delegated to data handler)
     *
     * @param array $menu_items
     * @return array
     */
    public function save_menu_items( $menu_items ) {
        return $this->data->save_menu_items( $menu_items );
    }

    /**
     * Remove dashboard widgets based on settings (delegated)
     */
    public function remove_dashboard_widgets_by_settings(): void {
        $this->restrict->remove_dashboard_widgets_by_settings();
    }

    /**
     * Apply role-based menu restrictions (delegated)
     */
    public function apply_role_based_menu_restrictions() {
        $this->restrict->apply_role_based_menu_restrictions();
    }

    /**
     * Clean up admin menu (delegated to restrict handler)
     */
    public function cleanup_admin_menu(): void {
        $this->restrict->cleanup();
    }
}
