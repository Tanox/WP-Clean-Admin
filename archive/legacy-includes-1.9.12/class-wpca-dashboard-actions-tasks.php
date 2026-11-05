<?php
/**
 * WPCleanAdmin Dashboard Actions Tasks
 *
 * 承载仪表盘脚本注册与快捷操作逻辑，从 Dashboard 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.5
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
 * 仪表盘脚本与快捷操作相关任务（trait）。
 */
trait DashboardActionsTasks {

    /**
     * Enqueue dashboard scripts and styles
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_dashboard_scripts( string $hook ): void {
        // Only enqueue on dashboard page
        if ( $hook !== 'index.php' ) {
            return;
        }

        // Enqueue dashboard JS
        if ( function_exists( '\wp_enqueue_script' ) ) {
            \wp_enqueue_script(
                'wpca-dashboard',
                WPCA_PLUGIN_URL . 'assets/js/wpca-dashboard.js',
                array( 'jquery' ),
                WPCA_VERSION,
                true
            );
        }

        // Localize script
        if ( function_exists( '\wp_localize_script' ) && function_exists( '\wp_create_nonce' ) && function_exists( '\admin_url' ) ) {
            \wp_localize_script( 'wpca-dashboard', 'wpca_dashboard_vars', array(
                'ajax_url' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'wpca_ajax_nonce' )
            ));
        }
    }

    /**
     * Run quick action
     *
     * @param string $action Action name
     * @return array Action result
     */
    public function run_quick_action( $action ) {
        $result = array(
            'success' => false,
            'message' => \__( 'Invalid action', WPCA_TEXT_DOMAIN )
        );

        switch ( $action ) {
            case 'cleanup_database':
                // Run database cleanup
                $cleanup = \WPCleanAdmin\Cleanup::getInstance();
                $cleanup_result = $cleanup->run_database_cleanup( array(
                    'transients' => true,
                    'orphaned_postmeta' => true,
                    'orphaned_termmeta' => true
                ) );

                $result['success'] = true;
                $result['message'] = \__( 'Database cleanup completed successfully', WPCA_TEXT_DOMAIN );
                $result['data'] = $cleanup_result;
                break;

            case 'optimize_database':
                // Run database optimization
                $database = \WPCleanAdmin\Database::getInstance();
                $optimize_result = $database->optimize_database();

                $result['success'] = true;
                $result['message'] = \__( 'Database optimization completed successfully', WPCA_TEXT_DOMAIN );
                $result['data'] = $optimize_result;
                break;
        }

        return $result;
    }
}
