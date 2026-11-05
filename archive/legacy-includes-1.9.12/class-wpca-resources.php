<?php
/**
 * WPCleanAdmin Resources Class
 *
 * @package WPCleanAdmin
 * @version 1.9.3
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的资源模块 trait
require_once __DIR__ . '/class-wpca-resources-optimization-tasks.php';
require_once __DIR__ . '/class-wpca-resources-stats-tasks.php';
require_once __DIR__ . '/class-wpca-resources-operations-tasks.php';

/**
 * Resources class
 */
class Resources {

    use ResourcesOptimizationTasks;
    use ResourcesStatsTasks;
    use ResourcesOperationsTasks;

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
}
