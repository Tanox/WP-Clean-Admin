<?php
/**
 * WPCleanAdmin Dashboard Class
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

require_once __DIR__ . '/class-wpca-dashboard-data.php';
require_once __DIR__ . '/class-wpca-dashboard-widget-tasks.php';
require_once __DIR__ . '/class-wpca-dashboard-data-tasks.php';

/**
 * Dashboard class
 */
class Dashboard {

    use DashboardWidgetTasks;
    use DashboardDataTasks;

    /**
     * Singleton instance
     *
     * @var Dashboard
     */
    private static $instance = null;

    /**
     * 仪表盘数据获取器
     *
     * @var Dashboard_Data
     */
    private $data;

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
        $this->data = new Dashboard_Data();
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
