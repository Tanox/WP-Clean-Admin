<?php
/**
 * WPCleanAdmin Dashboard Class
 *
 * @package WPCleanAdmin
 * @version 1.9.5
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的仪表盘模块 trait
require_once __DIR__ . '/class-wpca-dashboard-widgets-tasks.php';
require_once __DIR__ . '/class-wpca-dashboard-stats-tasks.php';
require_once __DIR__ . '/class-wpca-dashboard-actions-tasks.php';

/**
 * Dashboard class
 */
class Dashboard {

    use DashboardWidgetsTasks;
    use DashboardStatsTasks;
    use DashboardActionsTasks;

    /**
     * Singleton instance
     *
     * @var Dashboard
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Dashboard
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
     * Initialize the dashboard module
     */
    public function init() {
        // Register dashboard widgets
        if ( function_exists( '\add_action' ) ) {
            \add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widgets' ) );

            // Enqueue dashboard scripts and styles
            \add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_scripts' ) );
        }
    }
}
