<?php
/**
 * WPCleanAdmin Menu Manager Restrictions Tasks
 *
 * 承载管理菜单简化、排序与基于角色的菜单限制逻辑，从 Menu_Manager 主类抽取。
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
 * 菜单管理器菜单简化/限制任务（trait）。
 */
trait MenuManagerRestrictionsTasks {

    /**
     * Simplify admin menu
     */
    public function simplify_admin_menu() {
        global $menu, $submenu;

        // Menu items to remove
        $menu_items_to_remove = array(
            'edit-comments.php', // Comments
            'edit.php?post_type=page', // Pages
            'upload.php', // Media
            'themes.php', // Appearance
            'plugins.php', // Plugins
            'users.php', // Users
            'tools.php', // Tools
            'options-general.php', // Settings
        );

        // Remove menu items
        foreach ( $menu as $key => $menu_item ) {
            if ( isset( $menu_item[2] ) && in_array( $menu_item[2], $menu_items_to_remove ) ) {
                unset( $menu[$key] );
            }
        }

        // Remove submenu items
        if ( isset( $submenu['edit.php'] ) ) {
            // Remove Posts submenu items
            $post_submenu_to_remove = array(
                'edit.php?post_type=post', // All Posts
                'post-new.php', // Add New
                'edit-tags.php?taxonomy=category', // Categories
                'edit-tags.php?taxonomy=post_tag', // Tags
            );

            foreach ( $submenu['edit.php'] as $key => $submenu_item ) {
                if ( isset( $submenu_item[2] ) && in_array( $submenu_item[2], $post_submenu_to_remove ) ) {
                    unset( $submenu['edit.php'][$key] );
                }
            }
        }
    }

    /**
     * Simplify admin menu based on settings
     */
    public function simplify_admin_menu_by_settings() {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['menu'] ) && function_exists( 'add_action' ) ) {
            // Add action to customize menu
            \add_action( 'admin_menu', function() use ( $settings ) {
                global $menu, $submenu;

                // Apply menu order if specified
                if ( isset( $settings['menu']['menu_order'] ) && is_array( $settings['menu']['menu_order'] ) ) {
                    $this->reorder_menu( $menu, $settings['menu']['menu_order'] );
                }

                // Remove menu items if specified
                if ( isset( $settings['menu']['menu_items'] ) ) {
                    $menu_items_to_remove = $settings['menu']['menu_items'];

                    // Remove top-level menu items
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && isset( $menu_items_to_remove[$menu_item[2]] ) && $menu_items_to_remove[$menu_item[2]] ) {
                            unset( $menu[$key] );
                        }
                    }

                    // Remove submenu items
                    foreach ( $submenu as $parent_slug => $submenu_items ) {
                        foreach ( $submenu_items as $key => $submenu_item ) {
                            if ( isset( $submenu_item[2] ) && isset( $menu_items_to_remove[$submenu_item[2]] ) && $menu_items_to_remove[$submenu_item[2]] ) {
                                unset( $submenu[$parent_slug][$key] );
                            }
                        }
                    }
                }
            }, 999 );
        }
    }

    /**
     * Reorder menu items based on specified order
     *
     * @param array &$menu Menu array reference
     * @param array $menu_order Desired menu order
     */
    private function reorder_menu( &$menu, $menu_order ) {
        $new_menu = array();
        $menu_index = 5;

        // Add menu items in the specified order
        foreach ( $menu_order as $menu_slug ) {
            foreach ( $menu as $key => $menu_item ) {
                if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                    $menu_item[5] = $menu_index;
                    $new_menu[$key] = $menu_item;
                    $menu_index += 5;
                    break;
                }
            }
        }

        // Add remaining menu items
        foreach ( $menu as $key => $menu_item ) {
            $menu_slug = isset( $menu_item[2] ) ? $menu_item[2] : '';
            if ( ! in_array( $menu_slug, $menu_order ) ) {
                $menu_item[5] = $menu_index;
                $new_menu[$key] = $menu_item;
                $menu_index += 5;
            }
        }

        // Replace original menu with reordered menu
        $menu = $new_menu;
    }

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
