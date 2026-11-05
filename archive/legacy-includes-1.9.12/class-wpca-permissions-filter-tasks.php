<?php
/**
 * WPCleanAdmin Permissions Filter Tasks
 *
 * 承载用户能力过滤与特性权限判定逻辑，从 Permissions 主类抽取。
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
 * 权限能力过滤与判定相关任务（trait）。
 */
trait PermissionsFilterTasks {

    /**
     * Filter user capabilities
     *
     * @param array $allcaps All capabilities assigned to the user
     * @param array $caps Required capabilities for the capability check
     * @param array $args Arguments passed to the capability check
     * @return array Modified capabilities
     * @uses wpca_get_settings() To retrieve plugin settings
     */
    public function filter_user_capabilities( $allcaps, $caps = array(), $args = array() ) {
        // Load settings
        $settings = \wpca_get_settings();

        // Apply permission filters based on settings
        if ( isset( $settings['permissions'] ) ) {
            // Restrict access to certain features
            if ( isset( $settings['permissions']['restrict_features'] ) && $settings['permissions']['restrict_features'] ) {
                // Restrict access to specific capabilities
                $restricted_caps = array(
                    'manage_options',
                    'edit_theme_options',
                    'install_plugins',
                    'update_plugins',
                    'delete_plugins',
                    'install_themes',
                    'update_themes',
                    'delete_themes',
                    'import',
                    'export'
                );

                // Remove restricted capabilities for non-administrators
                // 注意：$allcaps 中并不包含角色名，不能用于判断管理员身份，
                // 否则会把管理员自身的能力也一并移除，导致后台无法访问。
                if ( ! $this->is_administrator( $args ) ) {
                    foreach ( $restricted_caps as $cap ) {
                        if ( isset( $allcaps[$cap] ) ) {
                            unset( $allcaps[$cap] );
                        }
                    }
                }
            }
        }

        return $allcaps;
    }

    /**
     * Determine whether the capability check belongs to an administrator
     *
     * @param array $args Arguments passed to the `user_has_cap` filter ($args[0] is the user ID)
     * @return bool True when the user has the administrator role
     */
    private function is_administrator( $args = array() ) {
        $user_id = 0;

        if ( is_array( $args ) && isset( $args[0] ) && is_numeric( $args[0] ) ) {
            $user_id = (int) $args[0];
        }

        if ( $user_id <= 0 && function_exists( '\get_current_user_id' ) ) {
            $user_id = (int) \get_current_user_id();
        }

        if ( $user_id <= 0 || ! function_exists( '\get_userdata' ) ) {
            return false;
        }

        $user = \get_userdata( $user_id );
        if ( ! $user || empty( $user->roles ) ) {
            return false;
        }

        return in_array( 'administrator', (array) $user->roles, true );
    }

    /**
     * Check if user has permission to access a feature
     *
     * @param string $feature Feature name
     * @param int $user_id User ID
     * @return bool Permission result
     */
    public function has_feature_permission( string $feature, ?int $user_id = null ): bool {
        // Get user ID if not provided
        if ( $user_id === null ) {
            $user_id = ( function_exists( 'get_current_user_id' ) ? \get_current_user_id() : 0 );
        }

        // Get user object
        $user = ( function_exists( 'get_user_by' ) ? \get_user_by( 'id', $user_id ) : false );
        if ( ! $user ) {
            return false;
        }

        // Load settings
        $settings = \wpca_get_settings();

        // Check if feature is restricted
        if ( isset( $settings['permissions']['feature_restrictions'] ) && isset( $settings['permissions']['feature_restrictions'][$feature] ) ) {
            $restriction = $settings['permissions']['feature_restrictions'][$feature];

            // Check if user has required role
            if ( isset( $restriction['roles'] ) && ! empty( $restriction['roles'] ) ) {
                $user_roles = $user->roles;
                $has_role = array_intersect( $user_roles, $restriction['roles'] );

                if ( empty( $has_role ) ) {
                    return false;
                }
            }

            // Check if user has required capability
            if ( isset( $restriction['capability'] ) && ! empty( $restriction['capability'] ) ) {
                if ( ! ( function_exists( 'user_can' ) && \user_can( $user_id, $restriction['capability'] ) ) ) {
                    return false;
                }
            }
        }

        return true;
    }
}
