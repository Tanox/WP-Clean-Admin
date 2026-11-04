<?php
/**
 * WPCleanAdmin Menu Customizer Apply Tasks
 *
 * 承载管理菜单与管理栏的定制应用逻辑，从 Menu_Customizer 主类抽取。
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
 * 菜单定制应用任务（trait）。
 */
trait MenuCustomizerApplyTasks {

    /**
     * Customize admin menu
     */
    public function customize_admin_menu(): void {
        global $menu, $submenu;

        // Load settings
        $settings = $this->get_settings();

        // Customize menu items based on settings
        if ( isset( $settings['menu_items'] ) ) {
            foreach ( $settings['menu_items'] as $menu_slug => $menu_settings ) {
                // Hide menu item
                if ( isset( $menu_settings['hidden'] ) && $menu_settings['hidden'] ) {
                    // Find and remove top-level menu
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            unset( $menu[$key] );
                            break;
                        }
                    }
                }

                // Customize menu title
                if ( isset( $menu_settings['title'] ) && ! empty( $menu_settings['title'] ) ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            $menu[$key][0] = $menu_settings['title'];
                            break;
                        }
                    }
                }

                // Customize menu icon
                if ( isset( $menu_settings['icon'] ) && ! empty( $menu_settings['icon'] ) ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            $menu[$key][6] = $menu_settings['icon'];
                            break;
                        }
                    }
                }

                // Customize menu position
                if ( isset( $menu_settings['position'] ) && is_numeric( $menu_settings['position'] ) ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            $menu[$key][5] = $menu_settings['position'];
                            break;
                        }
                    }
                }
            }
        }

        // Customize submenu items
        if ( isset( $settings['submenu_items'] ) ) {
            foreach ( $settings['submenu_items'] as $parent_slug => $submenu_items ) {
                if ( isset( $submenu[$parent_slug] ) ) {
                    foreach ( $submenu_items as $submenu_slug => $submenu_settings ) {
                        // Hide submenu item
                        if ( isset( $submenu_settings['hidden'] ) && $submenu_settings['hidden'] ) {
                            foreach ( $submenu[$parent_slug] as $key => $submenu_item ) {
                                if ( isset( $submenu_item[2] ) && $submenu_item[2] === $submenu_slug ) {
                                    unset( $submenu[$parent_slug][$key] );
                                    break;
                                }
                            }
                        }

                        // Customize submenu title
                        if ( isset( $submenu_settings['title'] ) && ! empty( $submenu_settings['title'] ) ) {
                            foreach ( $submenu[$parent_slug] as $key => $submenu_item ) {
                                if ( isset( $submenu_item[2] ) && $submenu_item[2] === $submenu_slug ) {
                                    $submenu[$parent_slug][$key][0] = $submenu_settings['title'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Apply menu order
        if ( isset( $settings['menu_order'] ) && ! empty( $settings['menu_order'] ) ) {
            $this->apply_menu_order( $menu, $settings['menu_order'] );
        }

        // Apply menu groups
        if ( isset( $settings['menu_groups'] ) && ! empty( $settings['menu_groups'] ) ) {
            $this->apply_menu_groups( $menu, $settings['menu_groups'] );
        }
    }

    /**
     * Apply menu order to admin menu
     *
     * @param array $menu Admin menu array
     * @param array $menu_order Menu order settings
     */
    private function apply_menu_order( &$menu, $menu_order ) {
        // Create a new menu array with the desired order
        $new_menu = array();
        $remaining_menu = $menu;

        // Add menu items in the specified order
        foreach ( $menu_order as $menu_slug ) {
            foreach ( $remaining_menu as $key => $menu_item ) {
                if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                    $new_menu[] = $menu_item;
                    unset( $remaining_menu[$key] );
                    break;
                }
            }
        }

        // Add remaining menu items at the end
        foreach ( $remaining_menu as $menu_item ) {
            $new_menu[] = $menu_item;
        }

        // Update the menu with the new order
        $menu = $new_menu;
    }

    /**
     * Customize admin bar
     *
     * @param WP_Admin_Bar $wp_admin_bar Admin bar object
     */
    public function customize_admin_bar( $wp_admin_bar ) {
        // Load settings
        $settings = $this->get_settings();

        // Customize admin bar items based on settings
        if ( isset( $settings['admin_bar_items'] ) ) {
            foreach ( $settings['admin_bar_items'] as $node_id => $node_settings ) {
                // Hide admin bar item
                if ( isset( $node_settings['hidden'] ) && $node_settings['hidden'] ) {
                    $wp_admin_bar->remove_node( $node_id );
                }

                // Customize admin bar item
                if ( isset( $node_settings['title'] ) && ! empty( $node_settings['title'] ) ) {
                    $node = $wp_admin_bar->get_node( $node_id );
                    if ( $node ) {
                        $node->title = $node_settings['title'];
                        $wp_admin_bar->add_node( $node );
                    }
                }
            }
        }
    }
}
