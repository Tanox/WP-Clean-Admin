<?php
/**
 * WPCleanAdmin User Roles Management Tasks
 *
 * 承载角色的创建/删除/复制与能力增删逻辑，从 User_Roles 主类抽取。
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
 * 用户角色管理任务（trait）。
 */
trait UserRoleManagementTasks {

    /**
     * Update role capabilities
     *
     * @param string $role_slug Role slug
     * @param array $capabilities Capabilities to update
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
     * Create new role
     *
     * @param string $role_slug Role slug
     * @param string $role_name Role name
     * @param array $capabilities Capabilities
     * @return array Create result
     */
    public function create_role( string $role_slug, string $role_name, array $capabilities = array() ): array {
        $result = array(
            'success' => false,
            'message' => \__( 'Failed to create role', \WPCA_TEXT_DOMAIN )
        );

        // Check if role already exists
        if ( function_exists( 'get_role' ) && \get_role( $role_slug ) ) {
            $result['message'] = \__( 'Role already exists', \WPCA_TEXT_DOMAIN );
            return $result;
        }

        // Create new role
        $role = ( function_exists( 'add_role' ) ? \add_role( $role_slug, $role_name, $capabilities ) : false );

        if ( $role ) {
            $result['success'] = true;
            $result['message'] = \__( 'Role created successfully', \WPCA_TEXT_DOMAIN );
        }

        return $result;
    }

    /**
     * Delete role
     *
     * @param string $role_slug Role slug
     * @return array Delete result
     */
    public function delete_role( $role_slug ) {
        $result = array(
            'success' => false,
            'message' => \__( 'Failed to delete role', \WPCA_TEXT_DOMAIN )
        );

        // Check if role exists
        if ( ! ( function_exists( 'get_role' ) && \get_role( $role_slug ) ) ) {
            $result['message'] = \__( 'Role does not exist', \WPCA_TEXT_DOMAIN );
            return $result;
        }

        // Delete role
        if ( function_exists( '\remove_role' ) && \remove_role( $role_slug ) ) {
            $result['success'] = true;
            $result['message'] = \__( 'Role deleted successfully', \WPCA_TEXT_DOMAIN );
        }

        return $result;
    }

    /**
     * Duplicate role
     *
     * @param string $role_slug Role slug to duplicate
     * @param string $new_role_name New role name
     * @param string $new_role_slug New role slug
     * @return array Duplicate result
     */
    public function duplicate_role( $role_slug, $new_role_name, $new_role_slug ) {
        $result = array(
            'success' => false,
            'message' => \__( 'Failed to duplicate role', \WPCA_TEXT_DOMAIN )
        );

        // Check if source role exists
        $source_role = ( function_exists( 'get_role' ) ? \get_role( $role_slug ) : false );
        if ( ! $source_role ) {
            $result['message'] = \__( 'Source role does not exist', \WPCA_TEXT_DOMAIN );
            return $result;
        }

        // Check if new role already exists
        if ( function_exists( 'get_role' ) && \get_role( $new_role_slug ) ) {
            $result['message'] = \__( 'New role already exists', \WPCA_TEXT_DOMAIN );
            return $result;
        }

        // Create new role with same capabilities
        $new_role = ( function_exists( 'add_role' ) ? \add_role( $new_role_slug, $new_role_name, $source_role->capabilities ) : false );

        if ( $new_role ) {
            $result['success'] = true;
            $result['message'] = \__( 'Role duplicated successfully', \WPCA_TEXT_DOMAIN );
        }

        return $result;
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
        $default_capabilities = $this->get_default_role_capabilities( $role_slug );

        if ( empty( $default_capabilities ) ) {
            return false;
        }

        // Update role capabilities
        return $this->update_role_capabilities( $role_slug, $default_capabilities );
    }
}
