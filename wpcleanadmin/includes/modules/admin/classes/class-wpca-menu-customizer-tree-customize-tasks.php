<?php
/**
 * WPCleanAdmin Menu Customizer Tree Customize Tasks
 *
 * 后台菜单/子菜单项的隐藏、标题、图标与位置定制，公开 API 不变。
 * 主类 `Menu_Customizer_Tree` 通过 `use MenuCustomizerTreeCustomizeTasks;` 组合。
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
 * Menu customizer tree customize tasks
 */
trait MenuCustomizerTreeCustomizeTasks {

    /**
     * Customize admin menu (hide/title/icon/position + order + groups)
     *
     * @global array $menu
     * @global array $submenu
     */
    public function customize_admin_menu(): void {
        global $menu, $submenu;

        $settings = ( function_exists( '\wpca_get_settings' ) ? \wpca_get_settings() : array() );

        // Customize menu items based on settings
        if ( isset( $settings['menu_items'] ) ) {
            foreach ( $settings['menu_items'] as $menu_slug => $menu_settings ) {
                // Hide menu item
                if ( isset( $menu_settings['hidden'] ) && $menu_settings['hidden'] ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            unset( $menu[ $key ] );
                            break;
                        }
                    }
                }

                // Customize menu title
                if ( isset( $menu_settings['title'] ) && ! empty( $menu_settings['title'] ) ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            $menu[ $key ][0] = $menu_settings['title'];
                            break;
                        }
                    }
                }

                // Customize menu icon
                if ( isset( $menu_settings['icon'] ) && ! empty( $menu_settings['icon'] ) ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            $menu[ $key ][6] = $menu_settings['icon'];
                            break;
                        }
                    }
                }

                // Customize menu position
                if ( isset( $menu_settings['position'] ) && is_numeric( $menu_settings['position'] ) ) {
                    foreach ( $menu as $key => $menu_item ) {
                        if ( isset( $menu_item[2] ) && $menu_item[2] === $menu_slug ) {
                            $menu[ $key ][5] = $menu_settings['position'];
                            break;
                        }
                    }
                }
            }
        }

        // Customize submenu items
        if ( isset( $settings['submenu_items'] ) ) {
            foreach ( $settings['submenu_items'] as $parent_slug => $submenu_items ) {
                if ( isset( $submenu[ $parent_slug ] ) ) {
                    foreach ( $submenu_items as $submenu_slug => $submenu_settings ) {
                        // Hide submenu item
                        if ( isset( $submenu_settings['hidden'] ) && $submenu_settings['hidden'] ) {
                            foreach ( $submenu[ $parent_slug ] as $key => $submenu_item ) {
                                if ( isset( $submenu_item[2] ) && $submenu_item[2] === $submenu_slug ) {
                                    unset( $submenu[ $parent_slug ][ $key ] );
                                    break;
                                }
                            }
                        }

                        // Customize submenu title
                        if ( isset( $submenu_settings['title'] ) && ! empty( $submenu_settings['title'] ) ) {
                            foreach ( $submenu[ $parent_slug ] as $key => $submenu_item ) {
                                if ( isset( $submenu_item[2] ) && $submenu_item[2] === $submenu_slug ) {
                                    $submenu[ $parent_slug ][ $key ][0] = $submenu_settings['title'];
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
}
