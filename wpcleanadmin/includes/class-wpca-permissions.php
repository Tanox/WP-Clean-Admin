<?php
/**
 * WPCleanAdmin Permissions Class
 *
 * @package WPCleanAdmin
 * @version 1.9.4
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Declare WordPress functions for IDE compatibility
if ( ! function_exists( 'get_current_user_id' ) ) {
    function get_current_user_id() {}
}
if ( ! function_exists( 'get_user_by' ) ) {
    function get_user_by() {}
}
if ( ! function_exists( 'user_can' ) ) {
    function user_can() {}
}
if ( ! function_exists( 'wp_redirect' ) ) {
    function wp_redirect() {}
}

// 引入拆分出的权限模块 trait
require_once __DIR__ . '/class-wpca-permissions-filter-tasks.php';
require_once __DIR__ . '/class-wpca-permissions-restrict-tasks.php';

/**
 * Permissions class
 */
class Permissions {

    use PermissionsFilterTasks;
    use PermissionsRestrictTasks;

    /**
     * Singleton instance
     *
     * @var Permissions
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return Permissions
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
     * Initialize the permissions module
     */
    public function init() {
        // Add permissions hooks
        if ( function_exists( 'add_filter' ) ) {
            \add_filter( 'user_has_cap', array( $this, 'filter_user_capabilities' ), 10, 3 );
        }
    }
}
