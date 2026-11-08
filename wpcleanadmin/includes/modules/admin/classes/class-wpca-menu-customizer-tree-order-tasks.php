<?php
/**
 * WPCleanAdmin Menu Customizer Tree Order Tasks
 *
 * 后台菜单排序与分组逻辑，公开 API 不变。
 * 主类 `Menu_Customizer_Tree` 通过 `use MenuCustomizerTreeOrderTasks;` 组合。
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

/**
 * Menu customizer tree order tasks
 */
trait MenuCustomizerTreeOrderTasks {

    /**
     * Apply menu order to admin menu
     *
     * @param array $menu
     * @param array $menu_order
     */
    public function apply_menu_order( array &$menu, array $menu_order ): void {
        $new_menu         = array();
        $remaining_menu   = $menu;

        foreach ( $menu_order as $menu_slug ) {
            foreach ( $remaining_menu as $key => $menu_item ) {
                if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                    $new_menu[] = $menu_item;
                    unset( $remaining_menu[ $key ] );
                    break;
                }
            }
        }

        foreach ( $remaining_menu as $menu_item ) {
            $new_menu[] = $menu_item;
        }

        $menu = $new_menu;
    }

    /**
     * Apply menu groups to admin menu
     *
     * @param array $menu
     * @param array $menu_groups
     */
    public function apply_menu_groups( array &$menu, array $menu_groups ): void {
        if ( empty( $menu_groups ) || ! is_array( $menu_groups ) ) {
            return;
        }

        foreach ( $menu_groups as $group_id => $group_settings ) {
            if ( ! isset( $group_settings['enabled'] ) || ! $group_settings['enabled'] ) {
                continue;
            }

            $group_name      = isset( $group_settings['name'] ) ? $group_settings['name'] : __( 'Custom Group', WPCA_TEXT_DOMAIN );
            $group_menu_items = isset( $group_settings['menu_items'] ) ? $group_settings['menu_items'] : array();

            if ( empty( $group_menu_items ) ) {
                continue;
            }

            $grouped_items    = array();
            $remaining_items  = array();

            foreach ( $menu as $menu_item ) {
                $menu_slug = isset( $menu_item[2] ) ? $menu_item[2] : '';

                if ( in_array( $menu_slug, $group_menu_items, true ) ) {
                    $grouped_items[] = $menu_item;
                } else {
                    $remaining_items[] = $menu_item;
                }
            }

            if ( ! empty( $grouped_items ) ) {
                $insert_position = count( $remaining_items );
                foreach ( $remaining_items as $index => $item ) {
                    $item_slug = isset( $item[2] ) ? $item[2] : '';
                    if ( in_array( $item_slug, $group_menu_items, true ) ) {
                        $insert_position = $index;
                        break;
                    }
                }

                $separator_item = array(
                    '',
                    'read',
                    'separator-' . $group_id,
                    '',
                    'wpca-menu-group wpca-menu-group-' . ( function_exists( '\sanitize_html_class' ) ? \sanitize_html_class( $group_id ) : $group_id ),
                    $insert_position
                );

                $new_menu   = array_slice( $remaining_items, 0, $insert_position );
                $new_menu[] = $separator_item;
                $new_menu   = array_merge( $new_menu, array_slice( $remaining_items, $insert_position ) );
                $menu       = $new_menu;
            }
        }
    }
}
