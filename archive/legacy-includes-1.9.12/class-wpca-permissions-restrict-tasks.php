<?php
/**
 * WPCleanAdmin Permissions Restrict Tasks
 *
 * 承载后台访问限制、页面限制与用户权限查询逻辑，从 Permissions 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.4
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 *
 * @noinspection PhpUndefinedFunctionInspection WordPress functions are available in WP environment
 */

namespace WPCleanAdmin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 后台访问限制与用户权限查询相关任务（trait）。
 */
trait PermissionsRestrictTasks {

    /**
     * Get user permissions
     *
     * @param int $user_id User ID
     * @return array User permissions
     */
    public function get_user_permissions( $user_id = null ) {
        // Get user ID if not provided
        if ( $user_id === null ) {
            $user_id = ( function_exists( 'get_current_user_id' ) ? \get_current_user_id() : 0 );
        }

        // Get user object
        $user = ( function_exists( 'get_user_by' ) ? \get_user_by( 'id', $user_id ) : false );
        if ( ! $user ) {
            return array();
        }

        // Get user capabilities
        $capabilities = $user->allcaps;

        // Get user roles
        $roles = $user->roles;

        // Load settings
        $settings = \wpca_get_settings();

        // Get feature permissions
        $feature_permissions = array();

        if ( isset( $settings['permissions']['feature_restrictions'] ) ) {
            foreach ( $settings['permissions']['feature_restrictions'] as $feature => $restriction ) {
                $feature_permissions[$feature] = $this->has_feature_permission( $feature, $user_id );
            }
        }

        return array(
            'capabilities' => $capabilities,
            'roles' => $roles,
            'feature_permissions' => $feature_permissions
        );
    }

    /**
     * Restrict access to admin pages
     */
    public function restrict_admin_access() {
        // Load settings
        $settings = \wpca_get_settings();

        // Check if admin access restriction is enabled
        if ( isset( $settings['permissions']['restrict_admin_access'] ) && $settings['permissions']['restrict_admin_access'] ) {
            // Check if user has access to admin area
            if ( ! ( function_exists( 'current_user_can' ) && \current_user_can( 'manage_options' ) ) ) {
                // Redirect non-administrators to front-end
                if ( function_exists( 'wp_redirect' ) && function_exists( 'home_url' ) ) {
                    \wp_redirect( \home_url() );
                    exit;
                }
            }
        }
    }

    /**
     * Restrict access to specific admin pages
     */
    public function restrict_specific_admin_pages() {
        // Load settings
        $settings = \wpca_get_settings();

        // Check if specific admin page restriction is enabled
        if ( isset( $settings['permissions']['restrict_specific_pages'] ) && $settings['permissions']['restrict_specific_pages'] ) {
            // Get current admin page
            $current_page = isset( $_GET['page'] ) ? ( function_exists( 'sanitize_text_field' ) ? \sanitize_text_field( $_GET['page'] ) : $_GET['page'] ) : '';

            // Check if current page is restricted
            if ( isset( $settings['permissions']['restricted_pages'] ) && in_array( $current_page, (array) $settings['permissions']['restricted_pages'], true ) ) {
                // Check if user has access to restricted page
                if ( ! ( function_exists( 'current_user_can' ) && \current_user_can( 'manage_options' ) ) ) {
                    // Redirect to admin dashboard
                    if ( function_exists( 'wp_redirect' ) && function_exists( 'admin_url' ) ) {
                        \wp_redirect( \admin_url() );
                        exit;
                    }
                }
            }
        }
    }
}
