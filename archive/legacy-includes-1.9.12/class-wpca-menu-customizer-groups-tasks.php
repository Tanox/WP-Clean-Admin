<?php
/**
 * WPCleanAdmin Menu Customizer Groups Tasks
 *
 * 承载菜单分组的组织与应用、分组 CRUD 逻辑，从 Menu_Customizer 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.9
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.9
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 菜单分组任务（trait）。
 */
trait MenuCustomizerGroupsTasks {

    /**
     * Apply menu groups to admin menu
     *
     * This method organizes menu items into user-defined groups.
     * Menu groups are defined in the settings and applied to the admin menu.
     *
     * @param array $menu Admin menu array (passed by reference)
     * @param array $menu_groups Menu groups settings
     * @return void
     */
    private function apply_menu_groups( &$menu, $menu_groups ) {
        if ( empty( $menu_groups ) || ! is_array( $menu_groups ) ) {
            return;
        }

        foreach ( $menu_groups as $group_id => $group_settings ) {
            if ( ! isset( $group_settings['enabled'] ) || ! $group_settings['enabled'] ) {
                continue;
            }

            $group_name = isset( $group_settings['name'] ) ? $group_settings['name'] : __( 'Custom Group', WPCA_TEXT_DOMAIN );
            $group_menu_items = isset( $group_settings['menu_items'] ) ? $group_settings['menu_items'] : array();

            if ( empty( $group_menu_items ) ) {
                continue;
            }

            // Find and extract menu items for this group
            $grouped_items = array();
            $remaining_items = array();

            foreach ( $menu as $menu_item ) {
                $menu_slug = isset( $menu_item[2] ) ? $menu_item[2] : '';

                if ( in_array( $menu_slug, $group_menu_items ) ) {
                    $grouped_items[] = $menu_item;
                } else {
                    $remaining_items[] = $menu_item;
                }
            }

            // If we found items for this group, insert them as a separator with the group name
            if ( ! empty( $grouped_items ) ) {
                // Find insertion position (first grouped item position)
                $insert_position = count( $remaining_items );
                foreach ( $remaining_items as $index => $item ) {
                    $item_slug = isset( $item[2] ) ? $item[2] : '';
                    if ( in_array( $item_slug, $group_menu_items ) ) {
                        $insert_position = $index;
                        break;
                    }
                }

                // Insert group separator at the position of first grouped item
                $separator_item = array(
                    '',                                 // Menu title (empty for separator)
                    'read',                             // Capability
                    'separator-' . $group_id,           // Menu slug
                    '',                                 // Hook name
                    'wpca-menu-group wpca-menu-group-' . sanitize_html_class( $group_id ), // CSS class
                    $insert_position                    // Position
                );

                // Rebuild menu with group separator
                $new_menu = array_slice( $remaining_items, 0, $insert_position );
                $new_menu[] = $separator_item;
                $new_menu = array_merge( $new_menu, array_slice( $remaining_items, $insert_position ) );
                $menu = $new_menu;
            }
        }
    }

    /**
     * Get menu group settings
     *
     * @return array Menu groups settings
     */
    public function get_menu_groups() {
        $settings = $this->get_settings();
        return isset( $settings['menu_groups'] ) ? $settings['menu_groups'] : array();
    }

    /**
     * Create a new menu group
     *
     * @param string $group_id Group identifier
     * @param string $group_name Group display name
     * @param array $menu_items Array of menu item slugs to include in group
     * @return bool Success status
     */
    public function create_menu_group( $group_id, $group_name, $menu_items = array() ) {
        if ( empty( $group_id ) || empty( $group_name ) ) {
            return false;
        }

        $settings = $this->get_settings();

        if ( ! isset( $settings['menu_groups'] ) ) {
            $settings['menu_groups'] = array();
        }

        $settings['menu_groups'][ $group_id ] = array(
            'name' => $group_name,
            'menu_items' => $menu_items,
            'enabled' => true,
            'created_at' => date( 'Y-m-d H:i:s' )
        );

        return update_option( 'wpca_menu_customizer_settings', $settings );
    }

    /**
     * Delete a menu group
     *
     * @param string $group_id Group identifier
     * @return bool Success status
     */
    public function delete_menu_group( $group_id ) {
        if ( empty( $group_id ) ) {
            return false;
        }

        $settings = $this->get_settings();

        if ( isset( $settings['menu_groups'][ $group_id ] ) ) {
            unset( $settings['menu_groups'][ $group_id ] );
            return update_option( 'wpca_menu_customizer_settings', $settings );
        }

        return false;
    }

    /**
     * Update menu group settings
     *
     * @param string $group_id Group identifier
     * @param array $group_settings New group settings
     * @return bool Success status
     */
    public function update_menu_group( $group_id, $group_settings ) {
        if ( empty( $group_id ) || empty( $group_settings ) ) {
            return false;
        }

        $settings = $this->get_settings();

        if ( isset( $settings['menu_groups'][ $group_id ] ) ) {
            $settings['menu_groups'][ $group_id ] = array_merge(
                $settings['menu_groups'][ $group_id ],
                $group_settings
            );
            return update_option( 'wpca_menu_customizer_settings', $settings );
        }

        return false;
    }
}
