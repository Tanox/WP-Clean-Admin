<?php
/**
 * WPCleanAdmin Reset Class
 *
 * @package WPCleanAdmin
 * @version 1.9.2
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

// Declare WordPress functions for IDE compatibility
if ( ! function_exists( 'wp_next_scheduled' ) ) {
    function wp_next_scheduled() {}
}
if ( ! function_exists( 'wp_unschedule_event' ) ) {
    function wp_unschedule_event() {}
}
if ( ! function_exists( 'remove_role' ) ) {
    function remove_role() {}
}
if ( ! function_exists( 'delete_option' ) ) {
    function delete_option() {}
}
if ( ! function_exists( 'update_option' ) ) {
    function update_option() {}
}
if ( ! function_exists( 'add_action' ) ) {
    function add_action() {}
}

// 引入拆分出的重置操作 trait
require_once __DIR__ . '/class-wpca-reset-operations-tasks.php';

/**
 * Reset class
 */
class Reset {

    use ResetOperationsTasks;

    /**
     * Singleton instance
     *
     * @var Reset
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return Reset
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
     * Initialize the reset module
     */
    public function init() {
        // Add reset hooks
        \add_action( 'wpca_reset_settings', array( $this, 'reset_settings' ) );
        \add_action( 'wpca_reset_plugin', array( $this, 'reset_plugin' ) );
    }
}
