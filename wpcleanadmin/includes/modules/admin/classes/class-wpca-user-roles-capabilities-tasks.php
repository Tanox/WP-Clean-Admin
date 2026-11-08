<?php
/**
 * WPCleanAdmin User Roles Capabilities Tasks
 *
 * 角色注册与能力读写/重置逻辑，公开 API 不变。
 * 主类 `User_Roles` 通过 `use UserRolesCapabilitiesTasks;` 组合。
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * User roles capabilities tasks
 */
trait UserRolesCapabilitiesTasks {

    /**
     * Register custom roles
     *
     * @uses wpca_get_settings() To retrieve plugin settings
     */
    public function register_custom_roles() {
        // Load settings
        $settings = \wpca_get_settings();

        // Register custom roles based on settings
        if ( isset( $settings['user_roles'] ) && isset( $settings['user_roles']['custom_roles'] ) ) {
            foreach ( $settings['user_roles']['custom_roles'] as $role_slug => $role_data ) {
                // Register custom role
                if ( function_exists( '\add_role' ) ) {
                    \add_role( $role_slug, $role_data['name'], $role_data['capabilities'] );
                }
            }
        }
    }

    /**
     * Get user roles
     *
     * @return array User roles
     */
    public function get_user_roles() {
        // Get all user roles
        global $wp_roles;

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new \WP_Roles();
        }

        return $wp_roles->roles;
    }

    /**
     * Update role capabilities
     *
     * @param string $role_slug    Role slug
     * @param array  $capabilities Capabilities to update
     * @return bool Update result
     */
    public function update_role_capabilities( string $role_slug, array $capabilities ): bool {
        // Get role object
        $role = ( function_exists( '\get_role' ) ? \get_role( $role_slug ) : false );

        if ( ! $role ) {
            return false;
        }

        // Update capabilities
        foreach ( $capabilities as $capability => $grant ) {
            if ( $grant ) {
                if ( method_exists( $role, 'add_cap' ) ) {
                    $role->add_cap( $capability );
                }
            } else {
                if ( method_exists( $role, 'remove_cap' ) ) {
                    $role->remove_cap( $capability );
                }
            }
        }

        return true;
    }

    /**
     * Get role capabilities
     *
     * @param string $role_slug Role slug
     * @return array Role capabilities
     */
    public function get_role_capabilities( $role_slug ) {
        // Get role object
        $role = ( function_exists( 'get_role' ) ? \get_role( $role_slug ) : false );

        if ( ! $role ) {
            return array();
        }

        return $role->capabilities;
    }

    /**
     * Reset role capabilities to default
     *
     * @param string $role_slug Role slug
     * @return bool Reset result
     */
    public function reset_role_capabilities( $role_slug ) {
        // Get default capabilities for the role
        $default_capabilities = $this->data->get_default_role_capabilities( $role_slug );

        if ( empty( $default_capabilities ) ) {
            return false;
        }

        // Update role capabilities
        return $this->update_role_capabilities( $role_slug, $default_capabilities );
    }
}
