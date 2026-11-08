<?php
/**
 * WPCleanAdmin Dashboard Widget Tasks
 *
 * 仪表盘小部件注册/渲染与脚本样式入队，公开 API 不变。
 * 主类 `Dashboard` 通过 `use DashboardWidgetTasks;` 组合。
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
 * Dashboard widget tasks
 */
trait DashboardWidgetTasks {

    /**
     * Register dashboard widgets
     */
    public function register_dashboard_widgets() {
        // Add WPCA dashboard widget
        if ( function_exists( '\wp_add_dashboard_widget' ) ) {
            \wp_add_dashboard_widget(
                'wpca_dashboard_widget',
                \__( 'WP Clean Admin', WPCA_TEXT_DOMAIN ),
                array( $this, 'render_dashboard_widget' )
            );
        }
    }

    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->data->get_dashboard_stats();

        ?>
        <div class="wpca-dashboard-widget">
            <h3><?php \_e( 'Dashboard Overview', WPCA_TEXT_DOMAIN ); ?></h3>
            <div class="wpca-dashboard-stats">
                <div class="wpca-stat-item">
                    <span class="wpca-stat-label"><?php \_e( 'Database Size', WPCA_TEXT_DOMAIN ); ?></span>
                    <span class="wpca-stat-value"><?php echo esc_html( $stats['database_size'] ); ?></span>
                </div>
                <div class="wpca-stat-item">
                    <span class="wpca-stat-label"><?php \_e( 'Transients', WPCA_TEXT_DOMAIN ); ?></span>
                    <span class="wpca-stat-value"><?php echo esc_html( $stats['transients'] ); ?></span>
                </div>
                <div class="wpca-stat-item">
                    <span class="wpca-stat-label"><?php \_e( 'Orphaned Postmeta', WPCA_TEXT_DOMAIN ); ?></span>
                    <span class="wpca-stat-value"><?php echo esc_html( $stats['orphaned_postmeta'] ); ?></span>
                </div>
                <div class="wpca-stat-item">
                    <span class="wpca-stat-label"><?php \_e( 'Orphaned Termmeta', WPCA_TEXT_DOMAIN ); ?></span>
                    <span class="wpca-stat-value"><?php echo esc_html( $stats['orphaned_termmeta'] ); ?></span>
                </div>
            </div>
            <div class="wpca-dashboard-actions">
                <button class="button button-primary wpca-quick-action" data-action="cleanup_database">
                    <?php \_e( 'Quick Database Cleanup', WPCA_TEXT_DOMAIN ); ?>
                </button>
                <button class="button button-secondary wpca-quick-action" data-action="optimize_database">
                    <?php \_e( 'Optimize Database', WPCA_TEXT_DOMAIN ); ?>
                </button>
            </div>
        </div>
        <?php
    }

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
            \wp_localize_script(
                'wpca-dashboard',
                'wpca_dashboard_vars',
                array(
                    'ajax_url' => \admin_url( 'admin-ajax.php' ),
                    'nonce'    => \wp_create_nonce( 'wpca_ajax_nonce' ),
                )
            );
        }
    }
}
