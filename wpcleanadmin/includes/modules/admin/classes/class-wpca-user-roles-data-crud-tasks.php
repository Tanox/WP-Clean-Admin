<?php
/**
 * WPCleanAdmin User Roles Data CRUD Tasks
 *
 * 角色 CRUD 操作方法，公开 API 不变。
 * 主类 `User_Roles_Data` 通过 `use UserRolesDataCrudTasks;` 组合。
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
 * User roles data CRUD tasks
 */
trait UserRolesDataCrudTasks {

    /**
     * Create new role
     *
     * @param string $role_slug
     * @param string $role_name
     * @param array  $capabilities
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
     * @param string $role_slug
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
     * @param string $role_slug
     * @param string $new_role_name
     * @param string $new_role_slug
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
}
