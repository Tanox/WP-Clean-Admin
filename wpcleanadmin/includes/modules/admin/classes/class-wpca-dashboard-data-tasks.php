<?php
/**
 * WPCleanAdmin Dashboard Data Tasks
 *
 * 仪表盘数据委托与快捷操作执行，公开 API 不变。
 * 主类 `Dashboard` 通过 `use DashboardDataTasks;` 组合。
 *
 * @package WPCleanAdmin
 * @version 1.9.17
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dashboard data tasks
 */
trait DashboardDataTasks {

    /**
     * Get dashboard statistics (delegated to data handler)
     *
     * @return array
     */
    public function get_dashboard_stats(): array {
        return $this->data->get_dashboard_stats();
    }

    /**
     * Get system information (delegated to data handler)
     *
     * @return array
     */
    public function get_system_info(): array {
        return $this->data->get_system_info();
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
                $cleanup        = new Cleanup();
                $cleanup_result = $cleanup->run_database_cleanup(
                    array(
                        'transients'        => true,
                        'orphaned_postmeta' => true,
                        'orphaned_termmeta' => true,
                    )
                );

                $result['success'] = true;
                $result['message'] = \__( 'Database cleanup completed successfully', WPCA_TEXT_DOMAIN );
                $result['data']    = $cleanup_result;
                break;

            case 'optimize_database':
                // Run database optimization
                $database        = new Database();
                $optimize_result = $database->optimize_database();

                $result['success'] = true;
                $result['message'] = \__( 'Database optimization completed successfully', WPCA_TEXT_DOMAIN );
                $result['data']    = $optimize_result;
                break;
        }

        return $result;
    }
}
