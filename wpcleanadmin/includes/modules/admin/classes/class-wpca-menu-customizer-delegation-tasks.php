<?php
/**
 * WPCleanAdmin Menu Customizer Delegation Tasks
 *
 * 菜单定制器的委托转发方法（转发至 Options/Tree/Render 处理器），公开 API 不变。
 * 主类 `Menu_Customizer` 通过 `use MenuCustomizerDelegationTasks;` 组合。
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
 * Menu customizer delegation tasks
 */
trait MenuCustomizerDelegationTasks {

    /**
     * Customize admin menu (delegated)
     */
    public function customize_admin_menu(): void {
        $this->tree->customize_admin_menu();
    }

    /**
     * Customize admin bar (delegated)
     */
    public function customize_admin_bar(): void {
        $this->render->customize_admin_bar();
    }

    /**
     * Get menu customizer settings (delegated)
     *
     * @return array
     */
    public function get_settings(): array {
        return $this->options->get_settings();
    }

    /**
     * Save menu customizer settings (delegated)
     *
     * @param array $settings
     * @return bool
     */
    public function save_settings( array $settings ): bool {
        return $this->options->save_settings( $settings );
    }

    /**
     * Reset menu customizer settings (delegated)
     *
     * @return bool
     */
    public function reset_settings(): bool {
        return $this->options->reset_settings();
    }

    /**
     * Create menu group (delegated)
     *
     * @param string $group_id
     * @param string $group_name
     * @param array  $menu_items
     * @return bool
     */
    public function create_menu_group( $group_id, $group_name, $menu_items = array() ): bool {
        return $this->options->create_menu_group( $group_id, $group_name, $menu_items );
    }

    /**
     * Delete menu group (delegated)
     *
     * @param string $group_id
     * @return bool
     */
    public function delete_menu_group( $group_id ): bool {
        return $this->options->delete_menu_group( $group_id );
    }

    /**
     * Update menu group (delegated)
     *
     * @param string $group_id
     * @param array  $group_settings
     * @return bool
     */
    public function update_menu_group( $group_id, $group_settings ): bool {
        return $this->options->update_menu_group( $group_id, $group_settings );
    }

    /**
     * Get menu group settings (delegated)
     *
     * @return array
     */
    public function get_menu_groups(): array {
        return $this->options->get_menu_groups();
    }

    /**
     * Get admin menu structure (delegated)
     *
     * @return array
     */
    public function get_admin_menu_structure(): array {
        return $this->render->get_admin_menu_structure();
    }

    /**
     * Get admin bar structure (delegated)
     *
     * @return array
     */
    public function get_admin_bar_structure(): array {
        return $this->render->get_admin_bar_structure();
    }

    /**
     * Export menu customizer settings (delegated)
     *
     * @return array
     */
    public function export_settings(): array {
        return $this->render->export_settings();
    }

    /**
     * Import menu customizer settings (delegated)
     *
     * @param array $import_data
     * @return bool
     */
    public function import_settings( array $import_data ): bool {
        return $this->render->import_settings( $import_data );
    }

    /**
     * Apply menu order (delegated)
     *
     * @param array $menu
     * @param array $menu_order
     */
    public function apply_menu_order( array &$menu, array $menu_order ): void {
        $this->tree->apply_menu_order( $menu, $menu_order );
    }

    /**
     * Apply menu groups (delegated)
     *
     * @param array $menu
     * @param array $menu_groups
     */
    public function apply_menu_groups( array &$menu, array $menu_groups ): void {
        $this->tree->apply_menu_groups( $menu, $menu_groups );
    }
}
