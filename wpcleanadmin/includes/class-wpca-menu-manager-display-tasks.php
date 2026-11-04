<?php
/**
 * WPCleanAdmin Menu Manager Display Tasks
 *
 * 承载仪表盘小部件与管理栏的显示清理逻辑，从 Menu_Manager 主类抽取。
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
 * 菜单管理器显示清理任务（trait）。
 */
trait MenuManagerDisplayTasks {

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
     * Remove dashboard widgets based on settings
     */
    public function remove_dashboard_widgets_by_settings(): void {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['menu']['dashboard_widgets'] ) && function_exists( '\remove_meta_box' ) ) {
            $widgets_to_remove = $settings['menu']['dashboard_widgets'];

            foreach ( $widgets_to_remove as $widget_id => $remove ) {
                if ( $remove ) {
                    \remove_meta_box( $widget_id, 'dashboard', 'normal' );
                    \remove_meta_box( $widget_id, 'dashboard', 'side' );
                    \remove_meta_box( $widget_id, 'dashboard', 'column3' );
                    \remove_meta_box( $widget_id, 'dashboard', 'column4' );
                }
            }
        }
    }

    /**
     * Clean admin bar
     *
     * @param WP_Admin_Bar $wp_admin_bar Admin bar object
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
     * Clean up admin menu
     */
    public function cleanup_admin_menu(): void {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['menu'] ) ) {
            // Remove dashboard widgets
            if ( isset( $settings['menu']['remove_dashboard_widgets'] ) && $settings['menu']['remove_dashboard_widgets'] ) {
                $this->remove_dashboard_widgets();
            }

            // Simplify admin menu
            if ( isset( $settings['menu']['simplify_admin_menu'] ) && $settings['menu']['simplify_admin_menu'] ) {
                $this->simplify_admin_menu();
            }

            // Clean admin bar
            if ( isset( $settings['general']['clean_admin_bar'] ) && $settings['general']['clean_admin_bar'] && function_exists( 'add_action' ) ) {
                \add_action( 'admin_bar_menu', array( $this, 'clean_admin_bar' ), 999 );
            }
        }
    }
}
