<?php
/**
 * WPCleanAdmin User Roles Class
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

require_once __DIR__ . '/class-wpca-user-roles-data.php';
require_once __DIR__ . '/class-wpca-user-roles-capabilities-tasks.php';
require_once __DIR__ . '/class-wpca-user-roles-delegation-tasks.php';

/**
 * User_Roles class
 */
class User_Roles {

    use UserRolesCapabilitiesTasks;
    use UserRolesDelegationTasks;

    /**
     * Singleton instance
     *
     * @var User_Roles
     */
    private static $instance;

    /**
     * 角色数据/操作处理器
     *
     * @var User_Roles_Data
     */
    private $data;

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
        $this->data = new User_Roles_Data();
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
}
