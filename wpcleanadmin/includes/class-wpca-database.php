<?php
/**
 * WPCleanAdmin Database Class
 *
 * Singleton entry point for database management. Information, optimization,
 * backup, restore and backup-listing behavior is provided by the composed traits.
 *
 * @package WPCleanAdmin
 * @version 1.8.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Database class
 */
class Database {

    use DatabaseInfoTasks;
    use DatabaseBackupTasks;
    use DatabaseRestoreTasks;
    use DatabaseBackupListTasks;

    /**
     * Singleton instance
     *
     * @var Database
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Database
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
     * Initialize the database module
     */
    public function init() {
        // Add database optimization hooks
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'wpca_optimize_database', array( $this, 'optimize_database' ) );
        }
    }
}
