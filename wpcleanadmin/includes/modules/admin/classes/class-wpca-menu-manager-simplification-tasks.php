<?php
/**
 * WPCleanAdmin Menu Manager Simplification Tasks
 *
 * 菜单简化/后台小工具清理/管理栏清理等逻辑方法，公开 API 不变。
 * 主类 `Menu_Manager` 通过 `use MenuManagerSimplificationTasks;` 组合。
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
 * Menu manager simplification tasks
 */
trait MenuManagerSimplificationTasks {

    /**
     * Remove dashboard widgets
     */
    public function remove_dashboard_widgets() {
        // Remove default WordPress dashboard widgets
        if ( function_exists( '\remove_meta_box' ) ) {
            \remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
            \remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
            \remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
            \remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
            \remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
            \remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
            \remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
            \remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
        }

        // Remove WordPress welcome panel
        if ( function_exists( '\remove_action' ) ) {
            \remove_action( 'welcome_panel', 'wp_welcome_panel' );
        }
    }

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
            if ( isset( $menu_item[2] ) && in_array( $menu_item[2], $menu_items_to_remove, true ) ) {
                unset( $menu[ $key ] );
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
                if ( isset( $submenu_item[2] ) && in_array( $submenu_item[2], $post_submenu_to_remove, true ) ) {
                    unset( $submenu['edit.php'][ $key ] );
                }
            }
        }
    }

    /**
     * Clean admin bar
     *
     * @param \WP_Admin_Bar $wp_admin_bar Admin bar object
     */
    public function clean_admin_bar( \WP_Admin_Bar $wp_admin_bar ): void {
        // Remove default WordPress admin bar items
        $wp_admin_bar->remove_node( 'wp-logo' );
        $wp_admin_bar->remove_node( 'about' );
        $wp_admin_bar->remove_node( 'wporg' );
        $wp_admin_bar->remove_node( 'documentation' );
        $wp_admin_bar->remove_node( 'support-forums' );
        $wp_admin_bar->remove_node( 'feedback' );
        $wp_admin_bar->remove_node( 'site-name' );
        $wp_admin_bar->remove_node( 'view-site' );
        $wp_admin_bar->remove_node( 'comments' );
        $wp_admin_bar->remove_node( 'new-content' );
        $wp_admin_bar->remove_node( 'w3tc' ); // W3 Total Cache
        $wp_admin_bar->remove_node( 'wpseo-menu' ); // Yoast SEO
    }

    /**
     * Simplify admin menu based on settings
     */
    public function simplify_admin_menu_by_settings() {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['menu'] ) && function_exists( 'add_action' ) ) {
            // Add action to customize menu
            \add_action(
                'admin_menu',
                function () use ( $settings ) {
                    global $menu, $submenu;

                    // Apply menu order if specified
                    if ( isset( $settings['menu']['menu_order'] ) && is_array( $settings['menu']['menu_order'] ) ) {
                        $this->data->reorder_menu( $menu, $settings['menu']['menu_order'] );
                    }

                    // Remove menu items if specified
                    if ( isset( $settings['menu']['menu_items'] ) ) {
                        $menu_items_to_remove = $settings['menu']['menu_items'];

                        // Remove top-level menu items
                        foreach ( $menu as $key => $menu_item ) {
                            if ( isset( $menu_item[2] ) && isset( $menu_items_to_remove[ $menu_item[2] ] ) && $menu_items_to_remove[ $menu_item[2] ] ) {
                                unset( $menu[ $key ] );
                            }
                        }

                        // Remove submenu items
                        foreach ( $submenu as $parent_slug => $submenu_items ) {
                            foreach ( $submenu_items as $key => $submenu_item ) {
                                if ( isset( $submenu_item[2] ) && isset( $menu_items_to_remove[ $submenu_item[2] ] ) && $menu_items_to_remove[ $submenu_item[2] ] ) {
                                    unset( $submenu[ $parent_slug ][ $key ] );
                                }
                            }
                        }
                    }
                },
                999
            );
        }
    }
}
