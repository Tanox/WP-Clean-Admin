<?php
/**
 * WPCleanAdmin Resources Class
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-resources-optimization-tasks.php';
require_once __DIR__ . '/class-wpca-resources-stats-tasks.php';
require_once __DIR__ . '/class-wpca-resources-management-tasks.php';

/**
 * Resources class
 */
class Resources {

    use ResourcesOptimizationTasks;
    use ResourcesStatsTasks;
    use ResourcesManagementTasks;

    /**
     * Singleton instance
     *
     * @var Resources
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Resources
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
     * Initialize the resources module
     */
    public function init(): void {
        // Add resources hooks
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'wp_enqueue_scripts', array( $this, 'optimize_frontend_resources' ), 999 );
            \add_action( 'admin_enqueue_scripts', array( $this, 'optimize_admin_resources' ), 999 );
        }
    }
}
