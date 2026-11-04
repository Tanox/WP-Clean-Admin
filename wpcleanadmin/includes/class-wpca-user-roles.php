<?php
/**
 * WPCleanAdmin User Roles Class
 *
 * @package WPCleanAdmin
 * @version 1.9.9
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Declare WordPress functions for IDE compatibility
if ( ! function_exists( 'add_role' ) ) {
    function add_role() {}
}
if ( ! function_exists( 'get_role' ) ) {
    function get_role() {}
}
if ( ! function_exists( 'remove_role' ) ) {
    function remove_role() {}
}
if ( ! function_exists( 'add_action' ) ) {
    function add_action() {}
}

// Declare WP_Roles class for IDE compatibility
if ( ! class_exists( 'WP_Roles' ) ) {
    class WP_Roles {
        public $roles = array();
    }
}

// 引入拆分出的用户角色 trait
require_once __DIR__ . '/class-wpca-user-roles-default-capabilities-tasks.php';
require_once __DIR__ . '/class-wpca-user-roles-management-tasks.php';

/**
 * User_Roles class
 */
class User_Roles {

    use UserRoleDefaultCapabilitiesTasks;
    use UserRoleManagementTasks;

    /**
     * Singleton instance
     *
     * @var User_Roles
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return User_Roles
     */
    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize the user roles module
     */
    public function init() {
        // Add user roles hooks
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'init', array( $this, 'register_custom_roles' ) );
        }
    }

    /**
     * Register custom roles
     *
     * @uses wpca_get_settings() To retrieve plugin settings
     * @uses \add_action() To register init hook
     * @uses \add_role() To register custom WordPress roles
     * @return void
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
}
