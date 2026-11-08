<?php
/**
 * WPCleanAdmin User Roles Delegation Tasks
 *
 * 角色 CRUD 委托方法（转发至 `User_Roles_Data`），公开 API 不变。
 * 主类 `User_Roles` 通过 `use UserRolesDelegationTasks;` 组合。
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
 * User roles delegation tasks
 */
trait UserRolesDelegationTasks {

    /**
     * Create new role (delegated)
     *
     * @param string $role_slug
     * @param string $role_name
     * @param array  $capabilities
     * @return array
     */
    public function create_role( string $role_slug, string $role_name, array $capabilities = array() ): array {
        return $this->data->create_role( $role_slug, $role_name, $capabilities );
    }

    /**
     * Delete role (delegated)
     *
     * @param string $role_slug
     * @return array
     */
    public function delete_role( $role_slug ) {
        return $this->data->delete_role( $role_slug );
    }

    /**
     * Duplicate role (delegated)
     *
     * @param string $role_slug
     * @param string $new_role_name
     * @param string $new_role_slug
     * @return array
     */
    public function duplicate_role( $role_slug, $new_role_name, $new_role_slug ) {
        return $this->data->duplicate_role( $role_slug, $new_role_name, $new_role_slug );
    }
}
