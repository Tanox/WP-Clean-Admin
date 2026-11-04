<?php
/**
 * WPCleanAdmin Dashboard Widgets Tasks
 *
 * 承载仪表盘小工具注册与渲染逻辑，从 Dashboard 主类抽取。
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
 * 仪表盘小工具相关任务（trait）。
 */
trait DashboardWidgetsTasks {

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

            \wp_add_dashboard_widget(
                'wpca_diagnostics_widget',
                \__( 'Site Health Status', WPCA_TEXT_DOMAIN ),
                array( $this, 'render_diagnostics_widget' )
            );
        }
    }

    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->get_dashboard_stats();

        ?>        <div class="wpca-dashboard-widget">
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
     * Render diagnostics widget
     */
    public function render_diagnostics_widget() {
        ?>
        <div class="wpca-diagnostics-widget">
            <div class="wpca-diagnostics-header">
                <h4><?php \_e( 'Quick Health Check', WPCA_TEXT_DOMAIN ); ?></h4>
                <button class="button button-secondary wpca-run-diagnostics" style="float:right;margin-top:-28px;">
                    <?php \_e( 'Run Scan', WPCA_TEXT_DOMAIN ); ?>
                </button>
            </div>
            <div class="wpca-diagnostics-results" style="display:none;">
                <div class="wpca-diagnostics-summary">
                    <span class="wpca-diagnostics-status"></span>
                    <span class="wpca-diagnostics-count"></span>
                </div>
                <div class="wpca-diagnostics-list"></div>
            </div>
            <div class="wpca-diagnostics-placeholder">
                <p><?php \_e( 'Click "Run Scan" to check your site health.', WPCA_TEXT_DOMAIN ); ?></p>
            </div>
            <div class="wpca-diagnostics-loading" style="display:none;">
                <p><?php \_e( 'Running diagnostics...', WPCA_TEXT_DOMAIN ); ?></p>
            </div>
        </div>
        <?php
    }
}
