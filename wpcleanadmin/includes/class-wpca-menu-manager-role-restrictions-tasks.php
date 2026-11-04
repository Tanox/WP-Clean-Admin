<?php
/**
 * WPCleanAdmin Menu Manager Role Restrictions Tasks
 *
 * 承载基于角色的菜单限制逻辑，从 MenuManagerRestrictionsTasks 抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.10
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 菜单管理器基于角色的菜单限制任务（trait）。
 */
trait MenuManagerRoleRestrictionsTasks {

    /**
     * Apply role-based menu restrictions
     */
    public function apply_role_based_menu_restrictions() {
        global $menu, $submenu;

        // Get current user
        if ( ! function_exists( '\wp_get_current_user' ) ) {
            return;
        }

        $current_user = \wp_get_current_user();
        if ( ! $current_user || ! isset( $current_user->roles ) ) {
            return;
        }

        $user_roles = $current_user->roles;

        // Load settings
        $settings = \wpca_get_settings();

        // Check if role-based menu restrictions are enabled
        if ( ! isset( $settings['menu']['role_based_restrictions'] ) || ! $settings['menu']['role_based_restrictions'] ) {
            return;
        }

        // Get role-based menu restrictions
        $role_restrictions = isset( $settings['menu']['role_menu_restrictions'] ) ? $settings['menu']['role_menu_restrictions'] : array();

        // Menu items to remove for current user
        $menu_items_to_remove = array();

        // Check each role restriction
        foreach ( $role_restrictions as $role => $restrictions ) {
            if ( in_array( $role, $user_roles ) && isset( $restrictions['menu_items'] ) ) {
                $menu_items_to_remove = array_merge( $menu_items_to_remove, array_keys( array_filter( $restrictions['menu_items'] ) ) );
            }
        }

        // Remove duplicates
        $menu_items_to_remove = array_unique( $menu_items_to_remove );

        // Remove top-level menu items
        foreach ( $menu as $key => $menu_item ) {
            if ( isset( $menu_item[2] ) && in_array( $menu_item[2], $menu_items_to_remove ) ) {
                unset( $menu[$key] );
            }
        }

        // Remove submenu items
        foreach ( $submenu as $parent_slug => $submenu_items ) {
            foreach ( $submenu_items as $key => $submenu_item ) {
                if ( isset( $submenu_item[2] ) && in_array( $submenu_item[2], $menu_items_to_remove ) ) {
                    unset( $submenu[$parent_slug][$key] );
                }
            }
        }
    }
}
