<?php
/**
 * WPCleanAdmin Menu Customizer Structure Tasks
 *
 * 承载菜单/管理栏结构读取与设置导入导出逻辑，从 Menu_Customizer 主类抽取。
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
 * 菜单结构读取与导入导出任务（trait）。
 */
trait MenuCustomizerStructureTasks {

    /**
     * Get admin menu structure
     *
     * @return array Admin menu structure
     */
    public function get_admin_menu_structure() {
        global $menu, $submenu;

        $menu_structure = array();

        // Get top-level menu items
        foreach ( $menu as $key => $menu_item ) {
            if ( ! empty( $menu_item[0] ) && $menu_item[0] !== '-' ) {
                $menu_data = array(
                    'id' => $key,
                    'title' => $menu_item[0],
                    'slug' => $menu_item[2],
                    'capability' => $menu_item[1],
                    'icon' => $menu_item[6],
                    'position' => $menu_item[5],
                    'submenu' => array()
                );

                // Get submenu items
                if ( isset( $submenu[$menu_item[2]] ) ) {
                    foreach ( $submenu[$menu_item[2]] as $submenu_key => $submenu_item ) {
                        $menu_data['submenu'][] = array(
                            'id' => $submenu_key,
                            'title' => $submenu_item[0],
                            'slug' => $submenu_item[2],
                            'capability' => $submenu_item[1]
                        );
                    }
                }

                $menu_structure[] = $menu_data;
            }
        }

        return $menu_structure;
    }

    /**
     * Get admin bar structure
     *
     * @return array Admin bar structure
     */
    public function get_admin_bar_structure() {
        global $wp_admin_bar;

        $admin_bar_structure = array();

        // Get admin bar nodes
        $nodes = $wp_admin_bar->get_nodes();

        if ( ! empty( $nodes ) ) {
            foreach ( $nodes as $node_id => $node ) {
                $admin_bar_structure[] = array(
                    'id' => $node_id,
                    'title' => $node->title,
                    'parent' => $node->parent,
                    'href' => $node->href,
                    'group' => $node->group,
                    'meta' => $node->meta
                );
            }
        }

        return $admin_bar_structure;
    }

    /**
     * Export menu customizer settings
     *
     * @return string Exported settings
     */
    public function export_settings() {
        // Get settings
        $settings = $this->get_settings();

        // Export settings as JSON
        return json_encode( $settings, JSON_PRETTY_PRINT );
    }

    /**
     * Import menu customizer settings
     *
     * @param string $imported_settings Imported settings
     * @return array Import results
     */
    public function import_settings( $imported_settings ) {
        $results = array(
            'success' => false,
            'message' => \__( 'Failed to import settings', WPCA_TEXT_DOMAIN )
        );

        // Decode imported settings
        $settings = json_decode( $imported_settings, true );

        // Check if settings are valid
        if ( is_array( $settings ) ) {
            // Save imported settings
            if ( $this->save_settings( $settings ) ) {
                $results['success'] = true;
                $results['message'] = \__( 'Settings imported successfully', WPCA_TEXT_DOMAIN );
            }
        } else {
            $results['message'] = \__( 'Invalid settings format', WPCA_TEXT_DOMAIN );
        }

        return $results;
    }
}
